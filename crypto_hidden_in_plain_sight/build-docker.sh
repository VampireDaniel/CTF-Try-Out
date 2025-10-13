#!/bin/bash
NAME="hidden_in_plain_sight"
docker rm -f crypto_$NAME
docker build --tag=crypto_$NAME .
if [ -z "$1" ]; then
    DEBUG="--detach"
elif [[ "$1" == "debug" ]]; then
    DEBUG=""
fi
docker run -e SECRET_KEY=$(LC_ALL=C tr -dc 'A-F0-9' < /dev/urandom | head -c32) -p 1337:1337 --rm --name=crypto_$NAME $DEBUG crypto_$NAME
