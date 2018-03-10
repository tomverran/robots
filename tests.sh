#!/usr/bin/env bash
export MSYS_NO_PATHCONV=1

docker build . -t tests
docker run --rm -v `pwd`:/home/robots tests
