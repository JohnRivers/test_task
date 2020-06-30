<?php

namespace app\components;

use Exception;
use App;

/**
 * Обработка HTTP ошибок
 */
class HttpException extends Exception {
  public function __construct($message = null, $code = 0) {
    // если указан шаблон сообщений для http ошибок - отформатировать сообщение
    if(isset(App::$parserConfig['http_error_template'])) {
      $message = str_replace(['%TIME%', '%HTTP_CODE%', '%MESSAGE%'], [date('Y-m-d H:i:s'), $code, $message], App::$parserConfig['http_error_template']);
    }
    
    parent::__construct($message, $code);

    // если указан email для уведомлений, отправить 503-ю ошибку на email
    if($code == 503 && isset(App::$parserConfig['emailNotify'])) {
      mail(App::$parserConfig['emailNotify'], 'Test task: Http error code 503', $message);
    }
  }
}
