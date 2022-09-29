#!/bin/bash

#
# use from any container like this:
# netcat cron 8192
#

port=8192

netstat -tnl | fgrep -q " 0.0.0.0:$port " && exit

echo "Self_destruct armed!"
netcat -l -p $port -c 'echo service SELF_DESTRUCT triggered.'

echo "service SELF_DESTRUCT triggered."

kill 1

