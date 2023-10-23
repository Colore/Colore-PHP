#!/bin/bash

EXAMPLE=openswoole

CONF="$(pwd)/docker/${EXAMPLE}.conf"
CMD=/colore/examples/${EXAMPLE}/server.php

if [ $# -gt 0 ]; then
    CMD="$@"
fi

docker run \
    -it \
    --rm \
    -p 9501:9501 \
    -v $(pwd):/colore \
    openswoole/swoole \
    ${CMD}
