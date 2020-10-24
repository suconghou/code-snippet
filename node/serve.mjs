import http from 'http'
import fs from 'fs'

const sleep = async (ms) => {
    await new Promise((resolve, reject) => {
        setTimeout(resolve, ms)
    })
}

const path = '/Users/admin/Downloads/yt.mp4'

http.createServer(async (req, res) => {
    console.info(req.url)
    await sleep(5e3)
    const stream = fs.createReadStream(path)
    stream.pipe(res)
}).listen(9092)