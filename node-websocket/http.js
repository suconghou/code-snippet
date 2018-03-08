var http = require('http');
var crypto = require('crypto');
var WebSocket = require('./websocket.js');
var proxy = http.createServer(function(request, response) 
{



}).listen(18080);

proxy.on('upgrade',function(req, socket, upgradeHead)
{
	var key = req.headers['sec-websocket-key'];
    key = crypto.createHash("sha1").update(key + "258EAFA5-E914-47DA-95CA-C5AB0DC85B11").digest("base64");
    var headers = 
    [
        'HTTP/1.1 101 Switching Protocols',
        'Upgrade: websocket',
        'Connection: Upgrade',
        'Sec-WebSocket-Accept: ' + key
    ];
    socket.setNoDelay(true);
    socket.write(headers.join("\r\n") + "\r\n\r\n", 'ascii');
    var ws = new WebSocket(socket);
    WebSocket.addConnection(ws);
    ws.onMessage(function(data)
    {
        console.info('http get data',data);
    });

});
