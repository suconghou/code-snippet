var http = require('http');
var proxy = http.createServer(function(request, response) {
    var headers = Object.assign({}, request.headers, {});
    delete headers.host;
    var options = {
        hostname: 'vvv.suconghou.cn',
        method: request.method,
        headers: headers,
        path: request.url
    };
    var req = http.request(options, function(res) {
        response.writeHead(res.statusCode, res.headers);
        res.pipe(response);
    });
    request.pipe(req);
    req.on('error', function(error) {
        console.info(error);
    });
    req.end();
}).listen(18080);