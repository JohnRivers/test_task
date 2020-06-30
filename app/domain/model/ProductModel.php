<?php

namespace app\domain\model;

use App;

class ProductModel {
  private $id;
  private $name;
  private $code;
  private $image;

  private $price;

  private $properties;

  public function __get($field) {
    if(property_exists($this, $field)) {
      return $this->$field;
    }
  }

  public function __set($field, $value) {
    switch($field) {
      case 'name':
      case 'image':
      case 'code':
        $this->$field = $value;
        break;
    }
    
  }

  public function getProperties() {
    return $this->properties;
  }

  public function getPropertyByName($propName) {
    if(array_key_exists($propName, $this->properties)) {
      return $this->properties[$propName]->value;
    } else {
      return null;
    }
  }
}
