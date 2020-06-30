# Тестовое задание

Проект скачивает товары с интернет магазина Foxtrot (одна или несколько страниц) с последующей записью в базу данных MySQL. Веб-интерфейс в рамках задачи не реализован. Для анализа проделанной работы подключитесь к базе данных при помощи стороннего ПО.

В таблицах будут сохранены следующие данные:
- название товара
- внутренний код товара в магазине Foxtrot
- картинка товара (url)
- рейтинг (от 1 до 5)
- цены: без скидки, со скидкой, цена при покупке в кредит (только одно значение, прописанное в карточке товара в поле "от") и кешбек
- характеристики товара из карточки

При повторном запуске скрипта если товар найден в базе - он обновится. Если изменилась цена, то будет записана новая цена в таблице цен с указанием актуальной даты (на какую дату цена действительна)

# Требования
1. **docker** либо
2. **php 7.4 + php composer + MySQL Server 5.7**
3. **git**, а при запуске под ОС семейства Windows также **gitbash**
4. Для подключения к БД и последующего анализа данных используйте стороннее ПО, например бесплатное ПО:
  - [PhpMyAdmin](https://www.phpmyadmin.net/)
  - [Mysql Workbench](https://www.mysql.com/products/workbench/)
  - [SQLyog](https://github.com/webyog/sqlyog-community/wiki/Downloads)

# Установка
1. Скачайте проект

`git clone https://github.com/JohnRivers/test_task.git .`

## При использовании docker
1. Если Вы используете **docker**, скопируйте файл `docker/.env-EXAMPLE` в `docker/.env` и измените настройки доступа к БД
  - `#SET USER#` - установите имя пользователя
  - `#SET PASSWORD#` - установите пароль
  - `#SET ROOT PASSWORD#` - установите пароль для root
2. Скопируйте файл `app/config/db.php-EXAMPLE` в `app/config/db.php` и измените настройки как в предыдущем пункте
3. В ОС семейства Linux измените группу, чтобы docker контейнеры могли сохранять/изменять данные, для этого в корне проекта выполните:

`sudo chgrp -R docker . && sudo chmod g+w -R .`

*(рекурсивно для всего проекта установит группу владельца **docker** и разрешит для группы создавать/изменять файлы)*
4. Запустите файл `install.sh` из корня проекта

## Без использования docker
1. Скопируйте файл `app/config/db.php-EXAMPLE` в `app/config/db.php` и измените настройки доступа к БД:
  - `host` - укажите адрес сервера MySQL
  - `database` - имя базы данных
  - `user` - задайте имя пользователя (пользователь должен существовать и иметь права доступа к базе)
  - `password` - задайте пароль
  - `port` - если используете нестандартный порт MySQL, укажите используемый
2. Перейдите в папку `app` и выполните команду `composer install` для установки пакетов зависимостей. Команда может отличаться в зависимости от установленных у вас алиасов
3. Запустите миграции БД
  `php vendor/bin/phinx migrate --configuration config/phinx.php -e development`

# Запуск
- При использовании **docker** запустите из корня проекта скрипт `run-parser.sh`
- Без docker запустите из папки `app` команду `php -f run.php`

# Результат парсинга
Для просмотра результатов работы необходимо подключиться к базе данных при помощи любого имеющегося инструмента для работы с СУБД MySQL (см. Требования пункт 4). Для подключения используйте те же настройки доступа, которые Вы указали в файле `app/config/db.php`

- *для запуска **docker** контейнера перейдите в папку `docker` и выполните `docker-compose up -d`*
- *по окончании работы остановить контейнеры можно командой `docker-compose down`*
- *используйте порт **10306** и localhost в качестве имени хоста*
- *проверить, что контейнер с БД запущен,  можно командой `docker ps`*

# Устранение неполадок
- если при запуске docker контейнера возникают ошибки доступа, убедитесь, что для папок `db`, `app/vendor`, `app/log` есть права на запись для группы docker
- если MySQL не может создать базу данных или какую-то отдельную таблицу (ошибка при инициализации) - остановите контейнеры, очистите папку `db`, запустите контейнеры и заново проведите миграции