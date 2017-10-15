package main

import (
	"fmt"
	"net/http"
	"net/http/httputil"
	"net/url"
	"os"
	"path"
)

var (
	target, _ = url.Parse("https://www.google.com/")
)

func main() {
	http.HandleFunc("/", routeMatch)
	http.ListenAndServe(fmt.Sprintf(":%s", os.Getenv("PORT")), nil)
}

func routeMatch(w http.ResponseWriter, r *http.Request) {
	proxy := &httputil.ReverseProxy{Director: func(req *http.Request) {
		req.Host = target.Host
		req.URL.Scheme = target.Scheme
		req.URL.Host = target.Host
		req.URL.Path = path.Join(target.Path, req.URL.Path)
	}}
	proxy.ServeHTTP(w, r)
}
