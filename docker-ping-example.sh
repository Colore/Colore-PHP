#!/bin/bash

EXAMPLE=ping

CONF="$(pwd)/docker/${EXAMPLE}.conf"
CMD=""

if [ $# -gt 0 ]; then
    CMD="$@"
fi

docker run \
    -it \
    --rm \
    -p 8765:80 \
    -v $(pwd):/colore \
    -v $(pwd)/docker/apache-colore.conf:/etc/apache2/conf-available/docker-php.conf \
    -v ${CONF}:/etc/apache2/sites-enabled/000-default.conf \
    php:8-apache \
    ${CMD}
