
async function handleRequest(request) {

    const origin = request.url.indexOf('m3u8') > 1 ? {
        protocol: "https:",
        port: 443,
        hostname: "v2ex.com",
    } : {
        protocol: "https:",
        port: 443,
        hostname: "github.com",
    }
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
    const res = await fetch(url.toString(), request)
    const response = new Response(res.body, res)
    response.headers.set("access-control-allow-origin", "*")
    return response

}


addEventListener('fetch', event => {
    event.respondWith(handleRequest(event.request));
})


