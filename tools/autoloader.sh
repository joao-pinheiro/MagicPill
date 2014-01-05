#!/bin/sh

BASEDIR="$(cd "$(dirname "$0")"/../; pwd)"
echo ${BASEDIR}
php ${BASEDIR}/tools/classmap/classmap_generator.php -l "${BASEDIR}" -o "${BASEDIR}/classmap.php" -w
