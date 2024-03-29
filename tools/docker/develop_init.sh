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
setPerms "${PROJECT_ROOT}/var/backups"
setPerms "${PROJECT_ROOT}/vendor"
setPerms "${PROJECT_ROOT}/public/upload/photos"
setPerms "${PROJECT_ROOT}/public/upload/photos/thumbnails"
setPerms "${PROJECT_ROOT}/public/upload/photos/preview"
setPerms "${PROJECT_ROOT}/public/upload/instructions"
setPerms "${PROJECT_ROOT}/public/vendor"


# Github token can be provided in vm.cfg
composer --no-interaction -q config -g github-oauth.github.com d5d9879b14a2c066e08c3fa8dfba19aa31658d49
composer --no-interaction config -g optimize-autoloader true

time composer --no-interaction install
time yarn install --frozen-lock
time grunt

# init database
bin/console doctrine:schema:update --force
bin/console doctrine:fixtures:load -n --purge-exclusions=tools_users --purge-exclusions=migration_versions
bin/console doctrine:migrations:migrate -n

xdebug-config dev


# reminder to edit this file
cowsay -f sheep "Please change develop_init.sh script"

