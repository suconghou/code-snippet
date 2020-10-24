启动服务端之后再启动客户端大约 2 分钟之后或者直接 kill 掉服务端会报如下错误

```
Error: socket hang up
    at connResetException (internal/errors.js:614:14)
    at Socket.socketOnEnd (_http_client.js:491:23)
    at Socket.emit (events.js:327:22)
    at endReadableNT (_stream_readable.js:1224:12)
    at processTicksAndRejections (internal/process/task_queues.js:84:21) {
  code: 'ECONNRESET'
}
```

为什么在 http client 这一端会报 socket hang up 这个错误，看下 Node.js http client 端源码会发现由于没有得到响应，那么就认为这个 socket 已经结束，因此会在 L440 处触发一个 connResetException('socket hang up') 错误。


```
// https://github.com/nodejs/node/blob/v12.x/lib/_http_client.js#L440
 
function socketOnEnd() {
  const socket = this;
  const req = this._httpMessage;
  const parser = this.parser;
 
  if (!req.res && !req.socket._hadError) {
    // If we don't have a response then we know that the socket
    // ended prematurely and we need to emit an error on the request.
    req.socket._hadError = true;
    req.emit('error', connResetException('socket hang up'));
  }
  if (parser) {
    parser.finish();
    freeParser(parser, req, socket);
  }
  socket.destroy();
}
```