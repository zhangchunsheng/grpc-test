#!/usr/bin/env bash

protoDir="../protos"
outDir="../src/php"

protoc --proto_path=${protoDir} \
  --php_out=${outDir} \
  --grpc_out=${outDir} \
  --plugin=protoc-gen-grpc=../bins/opt/grpc_php_plugin \
  ${protoDir}/helloworld.proto