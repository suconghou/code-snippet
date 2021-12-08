var crypto = require('crypto');
const fs = require("fs");
const { Buffer } = require('buffer');
const f = "/tmp/395.mp4"


const file = fs.readFileSync(f)

console.info(file.length)


var md5file = crypto.createHash('md5');
md5file.update(file)

console.info(md5file.digest("hex"))



const data = new DataView(file.buffer)
console.info("byteLength", data.byteLength)
console.info(parse_js(data))


// 4字节int转为byte
function intTobytes(n) {
    const a = new Uint8Array(4)
    a[3] = (n >> 24) & 0xFF
    a[2] = (n >> 16) & 0xFF
    a[1] = (n >> 8) & 0xFF
    a[0] = n & 0xFF
    return a
}

// 大小端存储问题，我们倒序一下
function byteToString(uint8Arr) {
    const str = String.fromCharCode.apply(null, uint8Arr);
    return str.split('').reverse().join('');
}

// 传入的是一个dataview
function parse_js(data) {
    // 8字节的header,前4字节是size,后4字节是type
    // 如果size是1，后面8字节作为size
    const infos = [];
    let pos = 0;
    // 即使文件截断，只需要前一部分，我们也能分析出想要的结果
    while (pos < data.byteLength) {
        let headerSize = 8;
        let size = data.getUint32(pos)
        pos += 4
        if (pos >= data.byteLength) {
            break
        }
        const type = data.getUint32(pos)
        pos += 4
        if (pos >= data.byteLength) {
            break
        }
        if (size == 1) {
            // 如果size是1，后面的8字节作为size
            headerSize += 8
            size = Number(data.getBigUint64(pos))
            pos += 8
        }
        const typeStr = byteToString(intTobytes(type))
        infos.push({ type: typeStr, size })
        pos += size - headerSize; // size 是包含头部大小的，所有下一个atom的开始位置是这样计算
        if (pos >= data.byteLength) {
            break
        }
    }
    const init = { start: 0, end: 0 }
    const index = { start: 0, end: 0 }
    let offset = 0;
    for (let i = 0; i < infos.length; i++) {
        const { type, size } = infos[i]
        if (type == 'moov') {
            offset += size;
            init.end = offset - 1 // rang end 应-1，range 0-9 代表10字节
            continue
        }
        if (type == 'sidx') {
            index.start = offset
            offset += size
            index.end = offset - 1 // rang end 应-1，range 0-9 代表10字节
            continue
        }
        offset += size;
    }
    return { infos, init, index };
}



function parse_origin(data) {
    // 8字节的header,前4字节是size,后4字节是type
    // 如果size是1，后面8字节作为size（应该是特大文件才会出现的状况，我们先不考虑）
    const infos = [];
    let pos = 0;
    let size = data.getUint32(pos)
    pos += 4
    let type = data.getUint32(pos)
    pos += 4
    let typeStr = byteToString(intTobytes(type))
    infos.push({ type: typeStr, size })
    console.info(size, type, typeStr)
    // ftpy解析完毕,继续读取下一个atom,接下来是moov
    pos += size - 8; // size 是包含头部大小的，所有下一个atom的开始位置是这样计算
    size = data.getUint32(pos)
    pos += 4
    type = data.getUint32(pos)
    pos += 4
    typeStr = byteToString(intTobytes(type))
    console.info(size, type, typeStr);
    infos.push({ type: typeStr, size })

    // moov 分析完毕，接下来是sidx
    pos += size - 8
    size = data.getUint32(pos)
    pos += 4
    type = data.getUint32(pos)
    pos += 4
    typeStr = byteToString(intTobytes(type))
    console.info(size, type, typeStr, data.byteLength);
    infos.push({ type: typeStr, size })
    // 以上，ftpy,moov,sidx 都已分析完毕，后续不再处理
    return infos;
}