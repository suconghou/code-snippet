https://github.com/csdoker/csdutils


```
bodyParser(request) {
    return async (max = 8192) => {
        return await new Promise((resolve, reject) => {
            let buf = [], count = 0;
            // let buf = Buffer.alloc(0);
            request.on('error', (err) => {
                reject(err);
            }).on('data', function (data) {
                // buf = Buffer.concat([buf, data]);
                buf.push(data);
                count += data.length;
                if (count > max) {
                    reject('body too large');
                }
                // if (buf.length > max) {
                //     reject('body too large');
                // }
            });
            request.on('end', function () {
                // resolve((buf));
                resolve(Buffer.concat(buf));
            });
        });
    }
}
```


`Buffer.concat` is slow