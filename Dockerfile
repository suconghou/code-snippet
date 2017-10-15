FROM golang:alpine
MAINTAINER suconghou
COPY index.go .
RUN CGO_ENABLED=0 GOOS=linux GOARCH=amd64 go build -v -o /index -a -ldflags "-s -w" index.go
ENV PORT 8080
EXPOSE 8080
ENTRYPOINT ["/index"]
