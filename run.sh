#!/bin/sh

docker rm -f qrhotel
docker run -it --name qrhotel -p 81:80 -v$(pwd):/root/work -w /root/work/public ebusiness/php:alpine sh