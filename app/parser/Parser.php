<?php

namespace app\parser;

use app\components\HttpException;
use app\domain\model\ProductModel;
use Mervick\CurlHelper;
use \DOMDocument;
use \DOMXpath;

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
   * @return array
   */
  private function parseUrl($url) {
    echo 'Parsing url: '.$url.PHP_EOL;

    $response = CurlHelper::factory($url)->exec();

    if($response['status'] != 200) {
      throw new HttpException('HTTP error', $response['status']);
    }

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($response['content']);

    $xpath = new DOMXpath($dom);
    // получить все карточки товаров: <div class="card"> внутри тега <div class="listing__body-wrap">
    $xpathNodes = $xpath->query('//div[contains(@class, "listing__body-wrap")]/div[contains(@class, "card")]');

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
    $xpath = new DOMXpath($html);

    //$product->name = $xpath->query('//a[@class=card__title]')->nodeValue;

    print_r($xpath->query('//a[@class=card__title]'));

    return $product;
  }
}
