#!/bin/bash

docker run \
    --name pact-broker-database \
    -d \
    -e POSTGRES_PASSWORD=p4ssw0rd \
    postgres:9.6.1

docker run \
    --name pact-broker \
    -d \
    --link pact-broker-database:database \
    -p 8080:80 \
    -e PACT_BROKER_DATABASE_HOST=database \
    -e PACT_BROKER_DATABASE_NAME=postgres \
    -e PACT_BROKER_DATABASE_USERNAME=postgres \
    -e PACT_BROKER_DATABASE_PASSWORD=p4ssw0rd \
    dius/pact_broker:latest