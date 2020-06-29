package main

func parseVint(data []uint8) (val uint64) {
	for i, l := 0, len(data); i < l; i++ {
		val <<= 8
		val += uint64(data[i])
	}
	return
}