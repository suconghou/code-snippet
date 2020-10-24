const http = require('http');
const opts = {
    hostname: '127.0.0.1',
    port: 3020,
    path: '/timeout',
    method: 'GET',
};

http.get(opts, (res) => {
    let rawData = '';
    res.on('data', (chunk) => { rawData += chunk; });
    res.on('end', () => {
        try {
            console.log(rawData);
        } catch (e) {
            console.error(e.message);
        }
    });
}).on('error', err => {
    console.error(err);
});

 