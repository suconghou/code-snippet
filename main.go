package main

import (
	"bufio"
	"encoding/json"
	"fmt"
	"io"
	"io/ioutil"
	"log"
	"net/http"
	"os"
	"time"

	"github.com/suconghou/libm3u8"
)

var (
	client = &http.Client{Timeout: time.Duration(5) * time.Second}
	mlog   = log.New(os.Stderr, "", log.Lshortfile)
)

type resJSON struct {
	Error int
	Data  interface{}
}

func main() {
	if len(os.Args) <= 1 {
		m := libm3u8.NewReader(bufio.NewScanner(os.Stdin))
		_, err := io.Copy(os.Stdout, m)
		pe(err)
		return
	}
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
		mlog.Print(err)
		return
	}
	m := libm3u8.NewFromURL(func() string { return url })
	if len(os.Args) > 2 {
		_, err = io.Copy(os.Stdout, m.PlayList())
		pe(err)
		return
	}
	_, err = io.Copy(os.Stdout, m.Play())
	pe(err)
}

func playLive(id string) {
	url, err := getM3u8URL(id)
	if err != nil {
		mlog.Print(err)
		return
	}
	var i int
	var errtimes int
	nextURL := func() string {
		i++
		if i%3 == 0 {
			u, err := getM3u8URL(id)
			if err != nil {
				errtimes++
				if errtimes > 3 {
					return ""
				}
				return url
			}
			errtimes = 0
			url = u
			return u
		}
		return url
	}
	m := libm3u8.NewFromURL(nextURL)
	if len(os.Args) > 2 {
		_, err = io.Copy(os.Stdout, m.PlayList())
		pe(err)
		return
	}
	_, err = io.Copy(os.Stdout, m.Play())
	pe(err)
}

func getM3u8URL(id string) (string, error) {
	u := fmt.Sprintf("https://m.douyu.com/html5/live?roomId=%s", id)
	resp, err := client.Get(u)
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
	resp, err := client.Get(u)
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

func pe(err error) {
	if err != nil {
		mlog.Print(err)
	}
}
