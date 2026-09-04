import Alpine from 'alpinejs';
import ClipboardJS from 'clipboard';
import Prism from 'prismjs';
import 'prismjs/components/prism-markup-templating';
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-ini';
import 'prismjs/components/prism-scss';
import 'prismjs/plugins/line-numbers/prism-line-numbers';
import twemoji from '@twemoji/api';

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
new ClipboardJS('button[data-clipboard-text], button[data-clipboard-target]');

window.twemoji = (content) => twemoji.parse(content, { folder: 'svg', ext: '.svg' });
window.twemoji(document.body);
