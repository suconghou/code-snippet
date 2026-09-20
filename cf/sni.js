
const routes = [
    {
        r: [/gemini.suconghou.cn/],
        protocol: "https:",
        port: "443",
        hostname: "generativelanguage.googleapis.com",
    },
    {
        r: [/.*/],
        protocol: "https:",
        port: "443",
        hostname: "github.com",
    },
];


async function handleRequest(request) {
    const url = new URL(request.url)
    const origin = routes.find(item => item.r.some(regex => regex.test(url.hostname)));
    url.hostname = origin.hostname
    if (origin.protocol) {
        url.protocol = origin.protocol
    }
    if (origin.port) {
        url.port = origin.port
    }
    request.redirect = 'follow'
    return await fetch(url.toString(), request)
}


export default {
    async fetch(request, env, ctx) {
        return handleRequest(request);
    }
}


