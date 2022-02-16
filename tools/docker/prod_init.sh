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
setPerms "${PROJECT_ROOT}/public/upload/photos"
setPerms "${PROJECT_ROOT}/public/upload/instructions"
setPerms "${PROJECT_ROOT}/public/vendor"

t1=$(date +%s)
t2=0
until mysql -h mysql -u root -prootpassword </dev/null; do
	t2=$(date +%s)
	if [ $(($t2-$t1)) -ge 10 ]; then
		error 'MySQL creation timeout' >&2
		exit
	fi
	sleep 1
done

# Github token can be provided in vm.cfg
composer --no-interaction -q config -g github-oauth.github.com d5d9879b14a2c066e08c3fa8dfba19aa31658d49
composer --no-interaction config -g optimize-autoloader true
composer dump-env prod

time composer --no-interaction install --no-dev --optimize-autoloader
time yarn install --frozen-lock
time grunt

# init database
bin/console doctrine:database:create --if-not-exists
bin/console doctrine:schema:update --force

# reminder to edit this file
cowsay -f sheep "Please change develop_init.sh script"

