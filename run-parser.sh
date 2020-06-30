#!/bin/sh

cd docker

echo 'Create docker environment'
docker-compose up -d

echo
echo 'Run parsing script'
../app/docker-php.sh -f run.php

echo
echo 'Stop docker containers'
docker-compose down
