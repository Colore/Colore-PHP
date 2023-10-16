#!/bin/bash

docker run \
    -it \
    --rm \
    -p 8765:80 \
    -v $(pwd):/colore \
    -v $(pwd)/docker/docker-php.conf:/etc/apache2/conf-available/docker-php.conf \
    -v $(pwd)/docker/000-default.conf:/etc/apache2/sites-enabled/000-default.conf \
    php:8-apache \
    $@
