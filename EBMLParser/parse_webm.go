package main

import (
	"log"
	"math"
)

func main() {
	log.Print(math.Pow(2, 3))
}

func parseVint(data []uint8) (val uint64) {
	for i, l := 0, len(data); i < l; i++ {
		val <<= 8
		val += uint64(data[i])
	}
	return
}
