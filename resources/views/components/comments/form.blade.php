@props(['post'])

<form
    method="POST"
    action="{{ \Statamic\Facades\Form::find('comments')->actionUrl() }}"
    class="space-y-4"
    @submit.prevent="submit($event.currentTarget)"
>
    <input type="hidden" name="post" value="{{ $post->id() }}" />

    <div class="hidden" aria-hidden="true">
        <label>
            Email
            <input type="email" name="email" tabindex="-1" autocomplete="off" />
        </label>
    </div>

    <label class="block">
        <span class="mb-1 block text-sm font-bold">Name</span>
        <input
            type="text"
            name="name"
            maxlength="100"
            required
            class="w-full rounded-1 border-b-2 border-night-10 bg-white px-4 py-2 shadow focus:border-brand focus:outline-none"
        />
    </label>

    <label class="block">
        <span class="mb-1 block text-sm font-bold">Comment</span>
        <textarea
            name="comment"
            rows="6"
            maxlength="5000"
            required
            class="w-full rounded-1 border-b-2 border-night-10 bg-white px-4 py-2 shadow focus:border-brand focus:outline-none"
        ></textarea>
    </label>

    <div data-turnstile></div>

    <p data-comment-error class="hidden text-sm font-bold"></p>

    <button
        type="submit"
        class="rounded-1 bg-brand px-4 py-2 font-bold text-night-0 shadow transition hover:brightness-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand focus-visible:ring-offset-2 disabled:cursor-wait disabled:opacity-50"
        :disabled="loading || widgetId === null"
    >
        Send comment
    </button>
</form>
