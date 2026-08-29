# gummibeer.dev redesign prototype

Two static pages recreated from the approved light/yellow mockups:

- `index.html` — homepage
- `article.html` — blog post
- `styles.css` — shared responsive design system
- `app.js` — mobile navigation, theme toggle, copy-link behavior, article TOC highlighting
- `assets/` — cropped image assets from the generated mockup reference

## Run locally

```bash
python3 -m http.server 8080
```

Then open `http://localhost:8080/index.html`.

Fonts are loaded from Google Fonts (`Anton`, `Caveat`, `IBM Plex Mono`, `Inter`). All layout, borders, doodles, icons and article structure are HTML/CSS/SVG rather than flattened screenshots.
