
const origin = {
    protocol: "https:",
    port: 443,
    hostname: "github.com",
}

async function handleRequest(request) {
    request.cf = {
        cacheEverything: true,
        cacheTtl: 60,
        minify: {
            javascript: true,
            css: true,
            html: true
        },
    }
    const url = new URL(request.url)
    url.hostname = origin.hostname
    if (origin.protocol) {
        url.protocol = origin.protocol
    }
    if (origin.port) {
        url.port = origin.port
    }
    return await fetch(url.toString(), request)
}


addEventListener('fetch', event => {
    event.respondWith(handleRequest(event.request));
})


