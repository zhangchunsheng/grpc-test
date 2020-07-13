package main

import (
	"flag"
	"fmt"
	"time"

	balance "github.com/zhangchunsheng/grpc-test/grpc-go-balance/etcd"
	"github.com/zhangchunsheng/grpc-test/grpc-go-balance/example/pb"
	"golang.org/x/net/context"
	"google.golang.org/grpc"
)

var (
	serv = flag.String("service", "hello_service", "service name")
	// reg  = flag.String("reg", "127.0.0.1:8500", "register address")
	reg = flag.String("reg", "http://127.0.0.1:2379", "register address")
)

func main() {
	flag.Parse()
	r := balance.NewResolver(*serv)
	b := grpc.RoundRobin(r)

	conn, err := grpc.Dial(*reg, grpc.WithInsecure(), grpc.WithBalancer(b))
	if err != nil {
		panic(err)
	}

	ticker := time.NewTicker(2 * time.Second)
	for t := range ticker.C {
		client := pb.NewHelloServiceClient(conn)
		resp, err := client.SayHello(context.Background(), &pb.HelloRequest{Greeting: "world"})
		if err == nil {
			fmt.Printf("%v: Reply is %s\n", t, resp.Reply)
		}
	}
}
