<?php

use app\components\Database;
use app\domain\service\ProductService;
use app\parser\Parser;

// подключение автозагрузки классов по namespace (PSR-4)
require_once('vendor/autoload.php');

class App {
  public static $db;
  public static $parserConfig;

  public static function init() {
    set_exception_handler('app\\components\\ExceptionHandler::handle');
    self::$parserConfig = require('config/parser.php'); // сохранить отдельно настройки парсера для использования при обработке http ошибок
    self::$db = new Database(require('config/db.php'));
  }

  public static function run() {
    echo 'Application started '.date('Y-m-d H:i:s').PHP_EOL;
    $start_time = microtime(true);

    // загрузка данных с указанных страниц, см. config/parser.php -> parse_url
    $parser = new Parser(self::$parserConfig);
    $products = $parser->run();

    foreach($products as $product) {
      // сохранение товаров в БД, обновление существующих
      ProductService::save($product);
    }
    echo 'Saved '.count($products).' products'.PHP_EOL;

    echo 'Application ended '.date('Y-m-d H:i:s').PHP_EOL;
    $end_time = microtime(true);
    echo 'Execution time: '.round($end_time - $start_time) .' sec'.PHP_EOL;
  }
}
