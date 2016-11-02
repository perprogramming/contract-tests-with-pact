#!/bin/bash

# Install vendor packages for client and provider
for service in client provider; do
    path=$(pwd)/$service
    docker run --rm -w $path -v $path:$path composer/composer update
done
