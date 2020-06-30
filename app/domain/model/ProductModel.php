<?php

namespace app\domain\model;

use App;
use Exception;

class ProductModel {
  private $id;
  private $name;       // название товара
  private $code;       // внутренний код товара Фокстрота
  private $image;      // url изображения товара
  private $rating;     // рейтинг (1-5)

  private $price;      // объект типа PriceModel

  private $properties; // массив объектов типа PropertyModel

  public function __get($field) {
    if(property_exists($this, $field)) {
      return $this->$field;
    } else {
      throw new Exception(__CLASS__.' - Property not found: '.$field);
    }
  }

  public function __set($field, $value) {
    switch($field) {
      case 'name':
      case 'image':
      case 'code':
      case 'rating':
        $this->$field = $value;
        break;
      default:
        throw new Exception(__CLASS__.' - Property not found: '.$field);
        break;
    }
  }

  /**
   * Добавляет именованное свойство
   * @param  PropertyModel $property
   * @return void
   */
  public function addProperty(PropertyModel $property) {
    // ассоциативный массив отсекает дублирование свойств
    $this->properties[$property->name] = $property;
  }

  /**
   * Устанавливает цены на товар (метод для контроля типов)
   * @param  PriceModel $price
   * @return void
   */
  public function addPrice(PriceModel $price) {
    $this->price = $price;
  }

  /**
   * Получить свойство товара по названию свойства
   * @param  string $propName
   * @return string
   */
  public function getPropertyByName(string $propName): string {
    if(array_key_exists($propName, $this->properties)) {
      return $this->properties[$propName]->value;
    } else {
      return '';
    }
  }
}
