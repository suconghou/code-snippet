package main

import (
	"bytes"
	"crypto/md5"
	"encoding/binary"
	"fmt"
	"io/ioutil"
)

func main() {
	err := parse()
	if err != nil {
		fmt.Println(err)
	}
}

func parse() error {
	bs, err := ioutil.ReadFile("/Users/admin/Downloads/140.mp4")
	if err != nil {
		return err
	}
	data := bs[668:904]
	// 包含668 但不包含904,所以236字节
	fmt.Println(len(data))
	fmt.Printf("%x\n", md5sum(data))
	var p = &parser{data, 0}
	fmt.Println(p.parse())
	return nil
}

type parser struct {
	data  []byte
	index int
}

func (p *parser) read(n int) []byte {
	var r = p.data[p.index : p.index+n]
	p.index += n
	return r
}

func (p *parser) readUInt8() uint8 {
	return readUInt8(p.read(1))
}

func (p *parser) readUInt16() uint16 {
	return readUInt16(p.read(2))
}

func (p *parser) readUInt32() uint32 {
	return readUInt32(p.read(4))
}
func (p *parser) readUInt64() uint64 {
	return readUInt64(p.read(8))
}

func (p *parser) parse() map[string]interface{} {
	// 前8字节是固定的 box header,略过
	p.read(8)
	var (
		versionAndFlags          = p.readUInt32()
		version                  = versionAndFlags >> 24
		flags                    = versionAndFlags & 0xFFFFFF
		referenceID              = p.readUInt32()
		timeScale                = p.readUInt32()
		earliestPresentationTime uint64
		firstOffset              uint64
	)
	if version == 0 {
		earliestPresentationTime = uint64(p.readUInt32())
		firstOffset = uint64(p.readUInt32())
	} else {
		earliestPresentationTime = p.readUInt64()
		firstOffset = p.readUInt64()
	}
	var (
		_              = p.read(2)
		referenceCount = p.readUInt16()
		i              uint16
		references     = map[uint16]interface{}{}
		offset         = uint32(firstOffset)
		time           = earliestPresentationTime
	)

	for i = 0; i < referenceCount; i++ {
		// 由于reference_type的值都是0,最最终结果无影响,所以下面可以直接将$t转为10进制
		referenceType := 0
		referenceSize := p.readUInt32()

		subsegmentDuration := p.readUInt32()

		// 下面是 starts_with_SAP, SAP_type, SAP_delta_time 没用到,这里忽略掉
		p.read(4)

		startRange := offset
		endRange := offset + referenceSize - 1

		references[i] = map[string]interface{}{
			"referenceType":      referenceType,
			"referenceSize":      referenceSize,
			"subsegmentDuration": subsegmentDuration,
			"durationSec":        float32(subsegmentDuration) / float32(timeScale),
			"startTimeSec":       float32(time) / float32(timeScale),
			"startRange":         startRange,
			"endRange":           endRange,
		}
		offset += referenceSize
		time += uint64(subsegmentDuration)
	}
	var info = map[string]interface{}{
		"version":     version,
		"flags":       flags,
		"referenceID": referenceID,
		"timeScale":   timeScale,
		"references":  references,
	}
	return info
}

func readUInt8(data []byte) (ret uint8) {
	buf := bytes.NewBuffer(data)
	binary.Read(buf, binary.BigEndian, &ret)
	return
}

func readUInt16(data []byte) (ret uint16) {
	buf := bytes.NewBuffer(data)
	binary.Read(buf, binary.BigEndian, &ret)
	return
}

func readUInt32(data []byte) (ret uint32) {
	buf := bytes.NewBuffer(data)
	binary.Read(buf, binary.BigEndian, &ret)
	return
}

func readUInt64(data []byte) (ret uint64) {
	buf := bytes.NewBuffer(data)
	binary.Read(buf, binary.BigEndian, &ret)
	return
}

func md5sum(b []byte) []byte {
	h := md5.New()
	h.Write(b)
	return h.Sum(nil)
}
