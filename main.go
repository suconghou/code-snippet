package main

import (
	"encoding/json"
	"fmt"
	"io"
	"io/ioutil"
	"net/http"
	"os"

	"github.com/suconghou/libm3u8"
)

// Data  struct {
// 	Hls_url string
// }

type resJSON struct {
	Error int
	Data  interface{}
}

func main() {
	id := os.Args[1]
	url, err := getM3u8URL(id)
	if err != nil {
		fmt.Println(err)
		return
	}
	var i int
	var errtimes int
	nextURL := func() string {
		i++
		if i%5 == 0 {
			u, err := getM3u8URL(id)
			if err != nil {
				errtimes++
				if errtimes > 5 {
					return ""
				}
				return url
			}
			url = u
			return u
		}
		return url
	}
	m, err := libm3u8.NewFromURL(url, nextURL)
	io.Copy(os.Stdout, m.Play())
}

func getM3u8URL(id string) (string, error) {
	u := fmt.Sprintf("https://m.douyu.com/html5/live?roomId=%s", id)
	resp, err := http.Get(u)
	if err != nil {
		return "", err
	}
	defer resp.Body.Close()
	bodyByte, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		return "", err
	}
	var ret resJSON
	err = json.Unmarshal(bodyByte, &ret)
	if err != nil {
		return "", err
	}
	if ret.Error == 0 {
		data := ret.Data.(map[string]interface{})
		if data["hls_url"] != nil {
			return data["hls_url"].(string), nil
		}
	}
	return "", fmt.Errorf(ret.Data.(string))
}
