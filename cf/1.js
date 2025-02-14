
const routes = [
    {
        r: [/\/v1beta\//],
        protocol: "https:",
        port: 443,
        hostname: "generativelanguage.googleapis.com",
    },
    {
        r: [/\/releases\/download\//],
        protocol: "https:",
        port: 443,
        hostname: "github.com",
    },
];


async function handleRequest(request) {
    const url = new URL(request.url)
    let origin = routes.find(item => item.r.some(regex => regex.test(url.pathname)));
    if (!origin) {
        const a = url.pathname.split('/')
        if (a.length < 3 || !/^([\w\-]+\.)+\w+$/.test(a[1])) {
            return new Response("Not found", { status: 404 })
        }
        const h = a[1];
        a.splice(1, 1);
        url.pathname = a.join('/');
        origin = {
            hostname: h,
            protocol: "https:",
            port: 443,
        }
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
    url.hostname = origin.hostname
    if (origin.protocol) {
        url.protocol = origin.protocol
    }
    if (origin.port) {
        url.port = origin.port
    }
    const headers = new Headers([...request.headers.entries(), ...Object.entries(origin.headers || {})]);
    const res = await fetch(url.toString(), { redirect: 'follow', headers, method: request.method, body: request.body })
    const response = new Response(res.body, res)
    response.headers.set("access-control-allow-origin", "*")
    return response

}


addEventListener('fetch', event => {
    event.respondWith(handleRequest(event.request));
})


