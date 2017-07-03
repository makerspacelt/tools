#!/bin/bash

set -e
set -x

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

# Github token can be provided in vm.cfg
composer --no-interaction -q config -g github-oauth.github.com d5d9879b14a2c066e08c3fa8dfba19aa31658d49
composer --no-interaction config -g optimize-autoloader true

time composer --no-interaction install

# reminder to edit this file
cowsay -f sheep "Please change develop_init.sh script"

