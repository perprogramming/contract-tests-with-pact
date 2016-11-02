#!/bin/bash

docker rm -f provider &> /dev/null

docker run \
    --name provider \
    -d \
    -w $(pwd) -v $(pwd):$(pwd) \
    php:7.0 php -S 0.0.0.0:80 index.php

rm -rf contracts
mkdir -p contracts
curl -S http://localhost:8080/pacts/provider/my-provider/consumer/my-client/latest > contracts/my-client-my-provider.json

docker run \
    --rm \
    --link provider \
    -e PACT_PROVIDER=my-provider \
    -e PACT_TARGET_HOST=provider \
    -e PACT_TARGET_PORT=80 \
    -e PACT_TARGET_PATH=/ \
    -e PACT_TARGET_PROTOCOL=http \
    -e PACT_PACTS_DIR=/opt/contracts \
    -v $(pwd)/contracts:/opt/contracts \
    -e PACT_PROVIDER_STATE_ENDPOINT=http://provider/provider-state-setup \
    dr.chefkoch.net/pact-provider-verifier-java

docker rm -f provider
rm -rf contracts
