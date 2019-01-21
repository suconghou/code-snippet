const http = require("http");
const proxy = http
	.createServer(function(request, response) {
		fwd(request, response);
	})
	.listen(9090);

const fwd = (request, response) => {
	const headers = request.headers;
	delete headers.host;
	const options = {
		hostname: "git.ourwill.cn",
		method: request.method,
		headers: headers,
		path: request.url
	};
	var req = http.request(options, res => {
		response.writeHead(res.statusCode, res.headers);
		res.pipe(response);
	});
	request.pipe(req);
	req.on("error", error => {
		console.info(error);
		response.end();
	});
};
