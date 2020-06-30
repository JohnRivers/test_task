<?php

$db_config = require('db.php');

return
[
  'paths' => [
    'migrations' => 'migration',
  ],
  'environments' => [
    'default_migration_table' => 'migration',
    'default_environment' => 'development',
    'development' => [
      'adapter' => 'mysql',
      'host'    => $db_config['host'],
      'name'    => $db_config['database'],
      'user'    => $db_config['user'],
      'pass'    => $db_config['password'],
      'port'    => $db_config['port'],
      'charset' => $db_config['charset'],
    ],
  ],
  'version_order' => 'creation'
];
