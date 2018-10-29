#!/bin/sh
set -e

# this file is an extension of the original entry-point from the PHP Docker image
# https://github.com/docker-library/php/blob/master/docker-php-entrypoint

# reading from a named pipe and writing to stdout via "tail -f"
# this workaround prevents logs from being output to console during console commands
# multistage build uses different UIDs and logpipe is transformed into something else than pipe so best way is to recreate it
PIPE=/tmp/log-pipe
rm -rf $PIPE
mkfifo $PIPE
chmod 666 $PIPE
tail -f $PIPE &

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php-fpm "$@"
fi

exec "$@"
