<?php

namespace app\domain\service;

use App;

class ProductService {
  private static $tableName = 'product';

  public static function findAll() {
    return App::$db->getAll('SELECT * FROM product');
  }

  /**
   * Insert/Update
   */
  public static function save() {}

  public static function delete() {}
}
