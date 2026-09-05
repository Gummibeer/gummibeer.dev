import Alpine from 'alpinejs';
import ClipboardJS from 'clipboard';
import Prism from 'prismjs';
import 'prismjs/components/prism-markup-templating';
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-ini';
import 'prismjs/components/prism-scss';
import 'prismjs/plugins/line-numbers/prism-line-numbers';
import twemoji from '@twemoji/api';

let turnstilePromise;

const loadTurnstile = () => {
    if (window.turnstile) {
        return Promise.resolve(window.turnstile);
    }

    turnstilePromise ??= new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
        script.async = true;
        script.defer = true;
        script.onload = () => resolve(window.turnstile);
        script.onerror = reject;
        document.head.appendChild(script);
    });

    return turnstilePromise;
};

Alpine.data('comments', () => ({
    opened: false,
    loading: false,
    success: false,
    error: null,
    widgetId: null,
    async showForm() {
        if (this.opened) {
            return;
        }

        this.opened = true;
        this.loading = true;
        this.error = null;

        try {
            const turnstile = await loadTurnstile();
            const container = this.$refs.form.querySelector('.cf-turnstile');

            this.widgetId = turnstile.render(container, {
                sitekey: container.dataset.sitekey,
                action: container.dataset.action,
            });
        } catch {
            this.opened = false;
            this.error = 'Could not load comment verification. Please try again.';
        } finally {
            this.loading = false;
        }
    },
    async submit(form) {
        const button = form.querySelector('button[type="submit"]');
        const error = form.querySelector('[data-comment-error]');

        button.disabled = true;
        button.textContent = 'Sending…';
        error.classList.add('hidden');
        error.textContent = '';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const payload = await response.json();

            if (!response.ok) {
                error.textContent = payload.errors?.join(' ') ?? 'Could not send your comment. Please check the form and try again.';
                error.classList.remove('hidden');
                window.turnstile?.reset(this.widgetId);

                return;
            }

            this.success = true;
        } catch {
            error.textContent = 'Could not send your comment. Please try again.';
            error.classList.remove('hidden');
            window.turnstile?.reset(this.widgetId);
        } finally {
            button.disabled = false;
            button.textContent = 'Send comment';
        }
    },
}));

Alpine.data('slider', (delay = 3) => ({
    delay: delay * 1000,
    images: [],
    index: 0,
    init() {
        this.images = Array.from(this.$el.getElementsByTagName('img'));
        this.render();
        window.setInterval(() => this.next(), this.delay);
    },
    next() {
        this.index = this.index < this.images.length - 1 ? this.index + 1 : 0;
        this.render();
    },
    render() {
        this.images.forEach((image, index) => {
            image.classList.toggle('hidden', index !== this.index);
        });
    },
}));

window.Alpine = Alpine;
Alpine.start();

Prism.highlightAll();
new ClipboardJS('button[data-clipboard-text]');

window.twemoji = (content) => twemoji.parse(content, { folder: 'svg', ext: '.svg' });
window.twemoji(document.body);
