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
setPerms "${PROJECT_ROOT}/var/log"
setPerms "${PROJECT_ROOT}/var/sessions"
setPerms "${PROJECT_ROOT}/vendor"
setPerms "${PROJECT_ROOT}/public/vendor"
setPerms "${PROJECT_ROOT}/actions"

setPerms "${PROJECT_ROOT}/../upload/photos"
setPerms "${PROJECT_ROOT}/../upload/instructions"

setPerms "${PROJECT_ROOT}/../backups"

sed -i 's/APP_ENV=dev/APP_ENV=prod/' .env
cp -f ../.env.prod .env.prod

set +x
t1=$(date +%s)
t2=0
echo -n "Waiting for mysql to get up ... "
until mysql -h mysql -u project -pproject -e 'SELECT (true)'; do
	t2=$(date +%s)
	if [ $(($t2-$t1)) -ge 100 ]; then
	echo 'MySQL init timeout' >&2
		exit
	fi
	sleep 1
done
set -x

# Github token can be provided in vm.cfg
composer --no-interaction -q config -g github-oauth.github.com d5d9879b14a2c066e08c3fa8dfba19aa31658d49
composer --no-interaction config -g optimize-autoloader true

time composer --no-interaction install --no-dev --optimize-autoloader
time yarn install --frozen-lock
time grunt

composer dump-env prod

# init database
bin/console doctrine:database:create --if-not-exists
bin/console doctrine:schema:update --force
bin/console doctrine:migrations:migrate -n


