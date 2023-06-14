#!/bin/bash

docker build -f Apache.Dockerfile -t receipts-processor:latest . && \
docker build -f Memcached.Dockerfile -t receipt-cache:latest . && \
docker run -d -P --name receipt-cache receipts-cache:latest && \
docker run -d --name receipts --link receipt-cache:latest -p 8080:80 receipts-processor:latest