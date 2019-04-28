const http = require("http");

const post = (type,uri,start,end) => {
	const options = {
		hostname: "127.0.0.1",
		port: 8099,
		path: "/upload.php",
		method: "POST"
	};
    const req = http.request(options);
    const data = `request_log,type=${type},uri=${uri} start=${start}i,end=${end}i,cost=${end-start}i`;
	req.write(data);
	req.on("error", e => {
		console.error(e);
	});
	req.end();
};

post('zp','/srv/hi',+new Date(),+new Date()+6);
