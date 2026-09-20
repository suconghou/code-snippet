
const UA_MWEB = 'Mozilla/5.0 (iPad; CPU OS 16_7_10 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1';

const routes = [
    {
        r: [/\/releases\/download\//],
        protocol: "https:",
        port: "443",
        hostname: "github.com",
    },
    {
        r: [/\/[\w\-]{6,12}\.(jpg|webp)$/, /^\/vi(_webp)?\/.+\.(jpg|webp)$/],
        handler: async (url) => {
            const m = url.pathname.match(/\/([\w\-]{6,12})\.(jpg|webp)$/);
            const target = m ? `https://i.ytimg.com/${m[2] === 'webp' ? 'vi_webp' : 'vi'}/${m[1]}/mqdefault.${m[2]}` : `https://i.ytimg.com${url.pathname}`;
            try {
                const r = await fetch(target, { headers: { 'User-Agent': UA_MWEB }, signal: AbortSignal.timeout(8000), });
                const ext = url.pathname.match(/\.(jpg|webp)$/)[1];
                return new Response(r.body, {
                    status: r.status,
                    headers: {
                        'Content-Type': r.headers.get('content-type') || (ext === 'webp' ? 'image/webp' : 'image/jpeg'),
                        'Cache-Control': r.ok ? 'public, max-age=86400' : 'no-store',
                        'access-control-allow-origin': '*',
                    },
                });
            } catch {
                return new Response('thumbnail fetch failed', { status: 502 });
            }
        },
    },
    {
        r: [/.*/],
        protocol: "https:",
        port: "443",
        hostname: "box.wispbyte.org",
        headers: new Headers({})
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
            port: "443",
        }
    }
    if (origin.handler) {
        return origin.handler(url);
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


export default {
    async fetch(request, env, ctx) {
        return handleRequest(request);
    }
}


