<?php

namespace app\domain\service;

use App;
use app\domain\model\ProductModel;
use PDO;

class ProductService {
  private static $tableName = 'product';

  /**
   * Сохраняет товар в БД, если находит - обновляет существующий, иначе создает новый
   * @param  ProductModel $product
   * @return bool - результат операции (true - успешно сохранено)
   */
  public static function save(ProductModel $product):bool {
    $isSuccess = false;
    $existingProduct = App::$db->getOne('SELECT id FROM '.self::$tableName.' WHERE code = ?', [['val' => $product->code, 'type' => PDO::PARAM_INT]]);

    if(!empty($existingProduct)) {
      $id = $existingProduct['id'];
      // обновление существующего товара
      $isSuccess = App::$db->query('UPDATE '.self::$tableName.' SET name=:name, code=:code, image=:image, rating=:rating, updated_at=:updated_at WHERE id = :id', [
          ['param' => ':name', 'val' => $product->name, 'type' => PDO::PARAM_STR],
          ['param' => ':code', 'val' => $product->code, 'type' => PDO::PARAM_INT],
          ['param' => ':image', 'val' => $product->image, 'type' => PDO::PARAM_STR],
          ['param' => ':rating', 'val' => $product->rating, 'type' => PDO::PARAM_INT],
          ['param' => ':updated_at', 'val' => date('Y-m-d H:i:s'), 'type' => PDO::PARAM_STR],
          ['param' => ':id', 'val' => $id, 'type' => PDO::PARAM_INT],
        ]);

      echo 'Updated product '.$product->name.PHP_EOL;
    } else {
      // создание нового товара
      $isSuccess = App::$db->query('INSERT INTO '.self::$tableName.' (name,code,image,rating) VALUES (:name,:code,:image,:rating)', [
          ['param' => ':name', 'val' => $product->name, 'type' => PDO::PARAM_STR],
          ['param' => ':code', 'val' => $product->code, 'type' => PDO::PARAM_INT],
          ['param' => ':image', 'val' => $product->image, 'type' => PDO::PARAM_STR],
          ['param' => ':rating', 'val' => $product->rating, 'type' => PDO::PARAM_INT],
        ]);

      echo 'Saved new product '.$product->name.PHP_EOL;
      $id = App::$db->getLastId(); // получить id только что созданного товара
    }

    // сохранение цены
    $product->price->productId = $id;
    PriceService::save($product->price);

    // сохранение параметров товара
    foreach($product->properties as $prop) {
      $prop->productId = $id;
      PropertyService::save($prop);
    }

    return $isSuccess;
  }

  /**
   * Удаление товара из БД
   * @param  ProductModel $product
   * @return bool - результат операции (true - успешно удалено)
   */
  public static function delete(ProductModel $product):bool {
    // т.к. в БД заданы внешние ключи с каскадным удалением, то достаточно одного запроса на удаление товара - связанные сущности удаляются автоматически
    return App::$db->query('DELETE FROM '.self::$tableName.' WHERE id = ?', [['val' => $product->id, 'type' => PDO::PARAM_INT]]);
  }
}
