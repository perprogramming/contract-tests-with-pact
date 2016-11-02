#!/bin/bash

docker rm -f pact-mock &> /dev/null

docker run \
    --name pact-mock \
    -d \
    -v /tmp/contracts:/opt/contracts \
    madkom/pact-mock-service

docker run \
    --rm \
    --link pact-mock:pact-mock \
    --link pact-broker:pact-broker \
    -e PACT_MOCK_HOST=pact-mock:1234 \
    -e PACT_BROKER_HOST=pact-broker:80 \
    -v /tmp/contracts:/opt/contracts \
    -v $(pwd):$(pwd) \
    -w $(pwd) \
    php:7.0 vendor/bin/phpunit src/

docker rm -f pact-mock