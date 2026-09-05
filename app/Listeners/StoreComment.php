<?php

namespace App\Listeners;

use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Statamic\Events\FormSubmitted;
use Statamic\Facades\Entry;

class StoreComment
{
    public function handle(FormSubmitted $event): void
    {
        if ($event->submission->form()->handle() !== 'comments') {
            return;
        }

        $this->validateTurnstile();

        $post = Entry::find((string) $event->submission->get('post'));

        if (! $post || $post->collection()->handle() !== 'posts') {
            throw ValidationException::withMessages([
                'post' => 'This post does not exist.',
            ]);
        }

        Entry::make()
            ->collection('comments')
            ->slug((string) Str::uuid())
            ->date(CarbonImmutable::now())
            ->published(false)
            ->data([
                'name' => trim((string) $event->submission->get('name')),
                'comment' => trim((string) $event->submission->get('comment')),
                'post' => $post->id(),
            ])
            ->save();
    }

    private function validateTurnstile(): void
    {
        $secret = (string) config('services.turnstile.secret_key');
        $token = (string) request()->input('cf-turnstile-response');

        if ($secret === '' || $token === '') {
            throw ValidationException::withMessages([
                'turnstile' => 'Turnstile verification failed. Please try again.',
            ]);
        }

        try {
            $response = Http::asForm()
                ->connectTimeout(2)
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                ]);
        } catch (ConnectionException) {
            throw ValidationException::withMessages([
                'turnstile' => 'Turnstile verification failed. Please try again.',
            ]);
        }

        if (! $response->successful() || ! $response->json('success') || $response->json('action') !== 'comment') {
            throw ValidationException::withMessages([
                'turnstile' => 'Turnstile verification failed. Please try again.',
            ]);
        }
    }
}
