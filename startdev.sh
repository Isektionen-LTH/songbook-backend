#!/bin/sh

docker run -it --rm -v $(pwd):/app -v songbook:/var/lib/mysql -p 80:80 --name songbook songbook
