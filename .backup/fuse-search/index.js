import Fuse from 'fuse.js/dist/fuse.basic.esm';
const _ = require('lodash');

async function handleRequest(request) {
    const requestUrl = new URL(request.url);
    const query = requestUrl.searchParams.get('q');

    const url = new URL('https://gummibeer.dev/blog/search.json');
    url.searchParams.set('t', Date.now());

    try {
        const response = await fetch(url.toString());
        const docs = await response.json();

        const fuse = new Fuse(docs, {
            includeScore: true,
            minMatchCharLength: 3,
            keys: ['title', 'description', 'categories', 'content'],
        });

        const results = _(fuse.search(query))
            .map(result => {
                _.unset(result, 'refIndex');
                _.unset(result, 'item.content');
                _.unset(result, 'item.categories');

                return result;
            })
            .values();

        return new Response(JSON.stringify(results), {
            status: 200,
            headers: {
                'Access-Control-Allow-Origin': '*',
            },
        });
    } catch (e) {
        return new Response(JSON.stringify({ error: e.message }), { status: 500 });
    }
}

addEventListener('fetch', event => {
    event.respondWith(handleRequest(event.request));
});
