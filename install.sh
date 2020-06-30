#!/bin/sh

echo 'Init project'

echo 'Create docker environment'
cd docker
docker-compose build
docker-compose up -d

echo 'Install project dependencies'
../app/composer.sh install

echo 'Run DB migrations'
../app/phinx migrate -e development

echo 'Stop docker'
docker-compose down
