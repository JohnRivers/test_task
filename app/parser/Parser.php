<?php

namespace app\parser;

use app\components\HttpException;
use app\domain\model\ProductModel;
use app\domain\model\PriceModel;
use app\domain\model\PropertyModel;
use Mervick\CurlHelper;
use DOMDocument;
use DOMXpath;

class Parser {
  private $options;

  /**
   * @param array $options
   */
  public function __construct($options = []) {
    $this->options = $options;
    if(isset($this->options['parse_url'])) {
      if(!is_array($this->options['parse_url'])) {
        $this->options['parse_url'] = (array)$this->options['parse_url'];
      }
    } else {
      $this->options['parse_url'] = [];
    }
  }

  /**
   * Парсит все товары со страниц, указанных в настройках, см. config/parser.php -> parse_url
   * @return array
   */
  public function run() {
    $productCards = [];
    foreach($this->options['parse_url'] as $url) {
      $productCards = array_merge($productCards, $this->parseUrl($url));

      // делать паузы при загрузке данных с нескольких страниц чтобы нас не забанили
      if(isset($this->options['cooldown'])) {
        sleep($this->options['cooldown']);
      }
    }

    $products = [];
    foreach($productCards as $productCard) { // распарсить каждую карточку товара
      $products[] = $this->parseProduct($productCard);
    }

    return $products;
  }

  /**
   * Возвращает список товаров с одной страницы
   * @param  string $url - адрес страницы, которую парсим
   * @return array string
   */
  private function parseUrl($url) {
    echo 'Parsing url: '.$url.PHP_EOL;

    // curl запрос указанной страницы
    $response = CurlHelper::factory($url)->exec();

    if($response['status'] != 200) {
      throw new HttpException('HTTP error', $response['status']);
    }

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($response['content']);

    $xpath = new DOMXpath($dom);
    // получить все карточки товаров: <div class="card"> внутри тега <div class="listing__body-wrap">
    $xpathNodes = $xpath->query('//div[contains(@class, "listing__body-wrap")]/div[contains(@class, "card js-card")]');

    $products = [];
    foreach($xpathNodes as $node) {
      $products[] = $dom->saveHtml($node);
    }

    return $products;
  }

  /**
   * @param  string $html - карточка одного товара
   * @return ProductModel
   */
  private function parseProduct($html) {
    $product = new ProductModel();
    $dom = new DOMDocument();
    // xml encoding="utf-8" - для корректной загрузки кодировки карточки товара
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    $xpath = new DOMXpath($dom);

    // название товара
    $product->name = trim($xpath->query('//a[@class="card__title"]')->item(0)->nodeValue);
    // внутренний код Фокстрот
    $product->code = $xpath->query('//div[contains(@class, "card")]')->item(0)->getAttribute('data-product-id');
    // url картинки товара
    $image = $xpath->query('//div[@class="card__image"]/*/img')->item(0);
    if($image) {
      $product->image = $image->getAttribute('data-src');
    }
    // рейтинг товара
    $product->rating = $xpath->query('//div[@class="card__rating"]/*[contains(@class, "icon_orange")]')->length;

    // добавить цены на товар
    $price = new PriceModel();
    $price->price = $this->getNumberValue($xpath->query('//div[@class="card-price"]')->item(0)->textContent);
    $price->priceOld = $this->getNumberValue($xpath->query('//div[@class="card__price-discount"]/p')->item(0)->textContent);
    $price->priceCredit = $this->getNumberValue($xpath->query('//a[contains(@class, "card__price-trust")]')->item(0)->textContent);
    $price->cashback = $this->getNumberValue($xpath->query('//div[@class="card__price-cashback"]')->item(0)->textContent);
    $price->validDate = date('Y-m-d');
    $product->addPrice($price);

    // добавить характеристики товара
    $xpathNodes = $xpath->query('//table[contains(@class, "prop-main")]/tr');
    // print_r($xpathNodes);
    foreach($xpathNodes as $xpathProperty) {
      $property = new PropertyModel();
      $propHtml = $dom->saveHtml($xpathProperty);
      preg_match('@<td>([^:]+):</td>\s+<td>(.*)</td>@', $propHtml, $props);
      if(isset($props[1]) && isset($props[2])) {
        $property->name = $props[1];
        $property->value = $props[2];
        $product->addProperty($property);
      }
    }

    return $product;
  }

  /**
   * Преобразует строку к числу, удаляя лишние пробелы и символы валюты
   * @param  string
   * @return int
   */
  private function getNumberValue($text):int {
    $text = preg_replace('/[^0-9]/', '', $text);
    return (int)$text;
  }
}
