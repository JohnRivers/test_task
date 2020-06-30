<?php

namespace app\domain\model;

use App;

class PriceModel {
  private $id;
  private $productId;
  private $price;
  private $priceOld;
  private $priceCredit;
  private $cashback;
  private $validDate;

  public function __get($field) {
    if(property_exists($this, $field)) {
      return $this->$field;
    }

    switch($field) {
      case 'discount':
        return ($this->priceOld - $this->price);
        break;
      default:
        throw new \Exception('Property not found: '.$field);
    }
  }

  public function __set($field, $value) {
    if(property_exists($this, $field)) {
      $this->$field = value;
    } else {
      throw new \Exception('Property not found: '.$field);
    }
  }
}
