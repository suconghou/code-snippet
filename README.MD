
## proxy website

use golang proxy website

## build

```
CGO_ENABLED=0 GOOS=linux GOARCH=amd64 go build -v -o index -a -ldflags "-s -w" index.go
```
