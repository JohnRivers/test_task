<?php

namespace app\domain\service;

use App;
use app\domain\model\PriceModel;
use PDO;

class PriceService {
  private static $tableName = 'price';

  /**
   * Сохраняет цену товара
   * @param PriceModel $price
   * @return bool - результат операции (true - успешно сохранено)
   */
  public static function save(PriceModel $price):bool {
    $existingPrice = App::$db->getOne('SELECT id FROM '.self::$tableName.' WHERE product_id = ? AND valid_date = ?', [
        ['val' => $price->productId, 'type' => PDO::PARAM_INT],
        ['val' => $price->validDate, 'type' => PDO::PARAM_STR],
      ]);

    if(!empty($existingPrice)) {
      $id = $existingPrice['id'];
      // обновление существующей цены
      $isSuccess = App::$db->query('UPDATE '.self::$tableName.' SET price=:price, price_old=:price_old, price_credit=:price_credit, cashback=:cashback
                                    WHERE id = :id', [
          ['param' => ':price', 'val' => $price->price, 'type' => PDO::PARAM_INT],
          ['param' => ':price_old', 'val' => $price->priceOld, 'type' => PDO::PARAM_INT],
          ['param' => ':price_credit', 'val' => $price->priceCredit, 'type' => PDO::PARAM_INT],
          ['param' => ':cashback', 'val' => $price->cashback, 'type' => PDO::PARAM_INT],
          ['param' => ':id', 'val' => $id, 'type' => PDO::PARAM_INT],
        ]);
    } else {
      // сохранить новую цену
      $isSuccess = App::$db->query('INSERT INTO '.self::$tableName.' (product_id,price,price_old,price_credit,cashback,valid_date)
                                    VALUES (:product_id,:price,:price_old,:price_credit,:cashback,:valid_date)', [
          ['param' => ':product_id', 'val' => $price->productId, 'type' => PDO::PARAM_INT],
          ['param' => ':price', 'val' => $price->price, 'type' => PDO::PARAM_INT],
          ['param' => ':price_old', 'val' => $price->priceOld, 'type' => PDO::PARAM_INT],
          ['param' => ':price_credit', 'val' => $price->priceCredit, 'type' => PDO::PARAM_INT],
          ['param' => ':cashback', 'val' => $price->cashback, 'type' => PDO::PARAM_INT],
          ['param' => ':valid_date', 'val' => $price->validDate, 'type' => PDO::PARAM_STR],
        ]);
    }

    return $isSuccess;
  }

  /**
   * Удаление цены товара
   * @param PriceModel $price
   * @return bool - результат операции (true - успешно удалено)
   */
  public static function delete(PriceModel $price):bool {
    return App::$db->query('DELETE FROM '.self::$tableName.' WHERE productId = ? AND valid_date = ?', [
        ['val' => $price->productId, 'type' => PDO::PARAM_INT],
        ['val' => $price->validDate, 'type' => PDO::PARAM_STR],
      ]);
  }
}
