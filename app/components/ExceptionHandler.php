<?php

namespace app\components;

use App;
use Throwable;

/**
 * Обработка всех исключений по проекту
 */
class ExceptionHandler {
  public static function handle(Throwable $exception) {
    self::writeLog($exception);

    echo json_encode(['error' => $exception->getMessage()]).PHP_EOL;
  }

  /**
   * Записывает сообщение об ошибке в лог-файл
   */
  private static function writeLog(Throwable $exception) {
    $log = fopen('log/'.date('Y-m-d').'_exceptions.log', 'a');
    
    $message = $exception->getMessage();
    // если указан шаблон сообщений для общих ошибок - отформатировать сообщение
    if(!($exception instanceof HttpException) && isset(App::$parserConfig['exception_template'])) {
      $message = str_replace(['%TIME%', '%MESSAGE%'], [date('Y-m-d H:i:s'), $message], App::$parserConfig['exception_template']);
    }

    fwrite($log, $message.PHP_EOL);
    fclose($log);
  }
}
