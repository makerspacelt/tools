#!/bin/bash

set -e

PROJECT_ROOT="$(dirname $(dirname $(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)))"

echo "PROJECT ROOT: ${PROJECT_ROOT}"
cd "${PROJECT_ROOT}"


function setPerms {
	mkdir -p $1
	sudo setfacl -R  -m m:rwx -m u:33:rwX -m u:1000:rwX $1
	sudo setfacl -dR -m m:rwx -m u:33:rwX -m u:1000:rwX $1
}

echo -e '\n## Setting up permissions ... '
setPerms "${PROJECT_ROOT}/var/cache"
setPerms "${PROJECT_ROOT}/var/logs"
setPerms "${PROJECT_ROOT}/var/sessions"

# run actual tests
vendor/bin/simple-phpunit --log-junit test_unit.xml
if [ "$?" == "0" ]
then
	echo "OK - tests passed"
	exit 0
else
	echo "CRITICAL - tests FAILED"
	exit -1
fi


