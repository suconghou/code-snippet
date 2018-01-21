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

type resJSON struct {
	Error int
	Data  interface{}
}

func main() {
	id := os.Args[1]
	if len(id) > 12 {
		playVideo(id)
		return
	}
	playLive(id)
}

func playVideo(vid string) {
	url, err := getVideoM3U8URL(vid)
	if err != nil {
		fmt.Fprintln(os.Stderr, err)
		return
	}
	m, err := libm3u8.NewFromURL(url, nil)
	if err == nil {
		io.Copy(os.Stdout, m.Play())
	} else {
		fmt.Fprintln(os.Stderr, err)
	}
}

func playLive(id string) {
	url, err := getM3u8URL(id)
	if err != nil {
		fmt.Fprintln(os.Stderr, err)
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
	if err == nil {
		io.Copy(os.Stdout, m.Play())
	} else {
		fmt.Fprintln(os.Stderr, err)
	}
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

func getVideoM3U8URL(vid string) (string, error) {
	u := fmt.Sprintf("https://vmobile.douyu.com/video/getInfo?vid=%s", vid)
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
		if data["video_url"] != nil {
			return data["video_url"].(string), nil
		}
	}
	return "", fmt.Errorf(ret.Data.(string))
}
