#!/bin/bash
docker rm -f web_masquerade
docker build --tag=web_masquerade .
docker run -p 1337:1337 -it --rm --name=web_masquerade web_masquerade
