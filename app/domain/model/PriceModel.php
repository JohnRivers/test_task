<?php

namespace app\domain\model;

use App;
use Exception;

class PriceModel {
  private $id;
  private $productId;
  private $price;       // новая цена со скидкой
  private $priceOld;    // старая цена без скидки
  private $priceCredit; // цена при покупке в кредит
  private $cashback;    // кешбек при оплате картой
  private $validDate;   // дата, на которую цена действительна

  public function __get($field) {
    if(property_exists($this, $field)) {
      return $this->$field;
    }

    switch($field) {
      case 'discount': // для получения скидки вычесть из старой цены новую, в БД поле не хранится
        if(!$this->priceOld) {
          return 0; // если старая цена не указана, значит скидки на товар нет
        } else {
          return ($this->priceOld - $this->price);
        }
        break;
      default:
        throw new Exception(__CLASS__.' - Property not found: '.$field);
    }
  }

  public function __set($field, $value) {
    if(property_exists($this, $field)) {
      $this->$field = $value;
    } else {
      throw new Exception(__CLASS__.' - Property not found: '.$field);
    }
  }
}
