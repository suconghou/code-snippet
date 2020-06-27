var crypto = require('crypto');
const fs = require("fs");
const { Buffer } = require('buffer');
const f = "/Users/admin/Downloads/140.mp4"


const file = fs.readFileSync(f)

console.info(file.length)


var md5file = crypto.createHash('md5');
md5file.update(file)

console.info(md5file.digest("hex"))

const start = 668;

const maxlen = 236;

let data1 = file.slice(start, start + maxlen);

var md5sum = crypto.createHash('md5');
md5sum.update(data1)
console.info(md5sum.digest("hex"))
console.info("parse len", data1.length)
// console.info(parse_node(data1))


const data = new DataView(data1.buffer,start,maxlen)
console.info("byteLength",data.byteLength)
console.info(parse_js(data))




// 传入的是一个buffer
function parse_node(data) {
    let pos = 8; // 跳过8字节header
    const versionAndFlags = data.readUInt32BE(pos)
    const version = versionAndFlags >>> 24; // version 只是高8位
    const flags = versionAndFlags & 0xFFFFFF
    pos += 4
    const referenceId = data.readUInt32BE(pos);
    pos += 4
    const timeScale = data.readUInt32BE(pos);
    pos += 4
    let earliest_presentation_time = 0, first_offset = 0;
    if (version == 0) {
        earliest_presentation_time = data.readUInt32BE(pos)
        pos += 4
        first_offset = data.readUInt32BE(pos)
        pos += 4
    } else {
        earliest_presentation_time = data.readBigUInt64BE(pos)
        pos += 8
        first_offset = data.readBigUInt64BE(pos)
        pos += 8
    }
    // skip reserved
    pos += 2
    const reference_count = data.readUInt16BE(pos)
    pos += 2;

    const references = [];
    let time = earliest_presentation_time;
    let offset = first_offset;
    for (let i = 0; i < reference_count; i++) {
        const reference_type = 0;
        const reference_size = data.readUInt32BE(pos)
        pos += 4;
        const subsegment_duration = data.readUInt32BE(pos)
        pos += 4
        // 下面是 starts_with_SAP, SAP_type, SAP_delta_time 没用到,这里忽略掉
        pos += 4
        const startRange = offset
        const endRange = offset + reference_size - 1;
        references.push({
            "reference_type": reference_type,
            "reference_size": reference_size,
            "subsegment_duration": subsegment_duration,
            "durationSec": subsegment_duration / timeScale,
            "startTimeSec": time / timeScale,
            "startRange": startRange,
            "endRange": endRange,
        })
        offset += reference_size
        time += subsegment_duration
    }

    return {
        "version": version,
        "flag": flags,
        "referenceId": referenceId,
        "timeScale": timeScale,
        "earliest_presentation_time": earliest_presentation_time,
        "first_offset": first_offset,
        "reference_count": reference_count,
        "references": references,
    }

}

// 传入的是一个dataview
function parse_js(data) {
    let pos = 8; // 跳过8字节header
    const versionAndFlags = data.getUint32(pos)
    const version = versionAndFlags >>> 24; // version 只是高8位
    const flags = versionAndFlags & 0xFFFFFF
    pos += 4
    const referenceId = data.getUint32(pos);
    pos += 4
    const timeScale = data.getUint32(pos);
    pos += 4
    console.info(version, flags, timeScale, referenceId)
    let earliest_presentation_time = 0, first_offset = 0;
    if (version == 0) {
        earliest_presentation_time = data.getUint32(pos)
        pos += 4
        first_offset = data.getUint32(pos)
        pos += 4
    } else {
        earliest_presentation_time = data.getBigUint64(pos)
        pos += 8
        first_offset = data.getBigUint64(pos)
        pos += 8
    }
    // skip reserved
    pos += 2
    const reference_count = data.getUint16(pos)
    pos += 2;

    const references = [];
    let time = earliest_presentation_time;
    let offset = first_offset;
    for (let i = 0; i < reference_count; i++) {
        const reference_type = 0;
        const reference_size = data.getUint32(pos)
        pos += 4;
        const subsegment_duration = data.getUint32(pos)
        pos += 4
        // 下面是 starts_with_SAP, SAP_type, SAP_delta_time 没用到,这里忽略掉
        pos += 4
        const startRange = offset
        const endRange = offset + reference_size - 1;
        references.push({
            "reference_type": reference_type,
            "reference_size": reference_size,
            "subsegment_duration": subsegment_duration,
            "durationSec": subsegment_duration / timeScale,
            "startTimeSec": time / timeScale,
            "startRange": startRange,
            "endRange": endRange,
        })
        offset += reference_size
        time += subsegment_duration
    }
    return {
        "version": version,
        "flag": flags,
        "referenceId": referenceId,
        "timeScale": timeScale,
        "earliest_presentation_time": earliest_presentation_time,
        "first_offset": first_offset,
        "reference_count": reference_count,
        "references": references,
    }
}


