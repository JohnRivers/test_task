<?php

namespace app\domain\service;

use App;
use app\domain\model\PropertyModel;
use PDO;

class PropertyService {
  private static $tableName = 'property';

  /**
   * Сохраняет одно свойство товара
   * @param PropertyModel $property
   * @return bool - результат операции (true - успешно сохранено)
   */
  public static function save(PropertyModel $property):bool {
    $existingProperty = App::$db->getOne('SELECT id FROM '.self::$tableName.' WHERE product_id = ? AND name = ?', [
        ['val' => $property->productId, 'type' => PDO::PARAM_INT],
        ['val' => $property->name, 'type' => PDO::PARAM_STR],
      ]);

    if(!empty($existingProperty)) {
      $id = $existingProperty['id'];
      // обновление свойства товара
      $isSuccess = App::$db->query('UPDATE '.self::$tableName.' SET val=:value WHERE id = :id', [
          ['param' => ':value', 'val' => $property->value, 'type' => PDO::PARAM_STR],
          ['param' => ':id', 'val' => $id, 'type' => PDO::PARAM_INT],
        ]);
    } else {
      // добавление нового свойства товара
      $isSuccess = App::$db->query('INSERT INTO '.self::$tableName.' (product_id,name,val) VALUES (:product_id,:name,:value)', [
          ['param' => ':product_id', 'val' => $property->productId, 'type' => PDO::PARAM_INT],
          ['param' => ':name', 'val' => $property->name, 'type' => PDO::PARAM_STR],
          ['param' => ':value', 'val' => $property->value, 'type' => PDO::PARAM_STR],
        ]);
    }

    return $isSuccess;
  }

  /**
   * Удаляет указанное свойство товара
   * @param PropertyModel $property
   * @return bool - результат операции (true - успешно удалено)
   */
  public static function delete(PropertyModel $property):bool {
    return App::$db->query('DELETE FROM '.self::$tableName.' WHERE productId = ? AND name = ?', [
        ['val' => $property->productId, 'type' => PDO::PARAM_INT],
        ['val' => $property->name, 'type' => PDO::PARAM_STR],
      ]);
  }
}
