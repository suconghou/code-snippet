addEventListener("fetch", (event) => {
    event.respondWith(
        handleRequest(event.request).catch(
            (err) => new Response(err.stack, { status: 500 })
        )
    );
});

async function handleRequest(request) {
    return new Response("hello", {
        headers: { "Content-Type": "text/plain" },
    });
}
