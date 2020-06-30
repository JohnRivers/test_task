<?php

namespace app\domain\model;

use App;
use Exception;

class PropertyModel {
  private $id;
  private $productId;
  private $name;      // название свойства
  private $value;     // значение

  public function __get($field) {
    if(property_exists($this, $field)) {
      return $this->$field;
    } else {
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
