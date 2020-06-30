#!/bin/sh

echo 'Init project'

echo 'Create docker environment'
cd docker
docker-compose build
docker-compose up -d

# задержка для инициализации mysql docker контейнера
sleep 5

cd ../app
echo 'Install project dependencies'
./composer.sh install

echo 'Run DB migrations'
./phinx.sh migrate -e development

echo 'Stop docker'
cd ../docker
docker-compose down
