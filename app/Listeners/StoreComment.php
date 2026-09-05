<?php

namespace App\Listeners;

use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laragear\Turnstile\Exceptions\InvalidChallengeException;
use Laragear\Turnstile\Facades\Turnstile;
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

        $post = Entry::query()
            ->where('collection', 'posts')
            ->where('id', (string) $event->submission->get('post'))
            ->firstOrFail();

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
        $token = (string) request()->input('cf-turnstile-response');

        if ($token === '') {
            $this->throwTurnstileValidationException();
        }

        try {
            $challenge = Turnstile::getChallenge($token, save: false);
        } catch (ConnectionException|RequestException|InvalidChallengeException) {
            $this->throwTurnstileValidationException();
        }

        if (! $challenge->successful || $challenge->isNotAction('comment')) {
            $this->throwTurnstileValidationException();
        }
    }

    private function throwTurnstileValidationException(): never
    {
        throw ValidationException::withMessages([
            'turnstile' => 'Turnstile verification failed. Please try again.',
        ]);
    }
}
