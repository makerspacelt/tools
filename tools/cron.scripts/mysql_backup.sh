#!/bin/bash

source .env
source .env.$APP_ENV

user="$(echo $DATABASE_URL | cut -d: -f2 | cut -d/ -f3)"
pass="$(echo $DATABASE_URL | cut -d@ -f1 | cut -d: -f3)"
host="$(echo $DATABASE_URL | cut -d@ -f2 | cut -d: -f1)"
db="$(echo $DATABASE_URL | cut -d/ -f4)"

# new backup
mysqldump --no-tablespaces -h$host -u$user -p$pass $db > $BACKUP_DIR/${db}_$(date +%Y-%m-%d_%H%M).sql

# cleanup old
find $BACKUP_DIR/ -name "${db}_*.sql" | sort -nr | tail +6 | xargs -n1 rm 2>/dev/null

