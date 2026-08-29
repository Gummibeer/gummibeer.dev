import twemoji from "twemoji";
import { marked } from 'marked';
import css from './css.js';

export default (content, confetti) => `<!DOCTYPE html>
<html lang="en">
    <meta charset="utf-8">
    <title>Generated Image</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://gummibeer.dev/css/app.css?id=${new Date().getTime()}">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.15.1/css/all.css">
    <style>${css()}</style>
    <body class="${confetti ? 'confetti' : ''}">
        <div>
            <div class="spacer">
            <div>${twemoji.parse(marked.parse(content), { folder: 'svg', ext: '.svg' })}</div>
        </div>
    </body>
</html>`;
