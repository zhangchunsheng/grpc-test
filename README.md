# grpc-test
grpc test

https://github.com/wechaty/wechaty/issues/1986

https://github.com/njpatel/grpcc

```shell script
protoc --proto_path=./protos \
--php_out=./src/php \
--grpc_out=./src/php \
--plugin=protoc-gen-grpc=./bins/opt/grpc_php_plugin \
./protos/helloworld.proto
```

```shell script
git clone https://github.com/grpc/grpc grpc-master
cd grpc-master/examples/node
npm install
cd dynamic_codegen
node greeter_server.js
```

### swoole
```shell script
cd src/swoole/tools
./generator \
--proto_path=./../src/Grpc/Proto \
--php_out=./../src/Grpc \
--grpc_out=./../src/Grpc \
--plugin=protoc-gen-grpc=/usr/local/bin/grpc_php_plugin \
./../src/Grpc/Proto
```