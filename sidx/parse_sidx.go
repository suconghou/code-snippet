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

func (p *parser) parse() error {
	// 前8字节是固定的 box header
	// fmt.Println(string(p.read(8)))
	var version1 = "" // p.read(1)
	fmt.Println(len(version1))
	version := ""  // readUInt32(version1)
	var flags = "" // p.read(17)

	var referenceID = "" // string(p.read(4))
	var timeScale = readUInt32(p.data[16:21])
	var info = map[string]interface{}{
		"version":     version,
		"flags":       flags,
		"referenceID": referenceID,
		"timeScale":   timeScale,
	}
	fmt.Println(info)
	return nil
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
