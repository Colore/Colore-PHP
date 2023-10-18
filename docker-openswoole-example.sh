#!/bin/bash

docker run \
    -it \
    --rm \
    -p 9501:9501 \
    -v $(pwd):/colore \
    openswoole/swoole \
    $@
