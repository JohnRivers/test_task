<?php

return [
  // адрес страницы или список адресов, которые нужно распарсить (можно задать массив адресов)
  'parse_url' => 'https://www.foxtrot.com.ua/ru/shop/led_televizory_smart-tv.html',
  // задержка в секундах между запросами (если несколько страниц)
  'cooldown' => 2,
  // на какой email слать уведомления если сервер вернул HTTP error 503
  'notify_email' => 'test@example.com',
  //шаблон для всех сообщений об ошибках
  'exception_template' => '[%TIME%] %MESSAGE%',
  // шаблон для ошибок, если HTTP код ответа не 200
  'http_error_template' => '[%TIME%] (CODE: %HTTP_CODE%) %MESSAGE%',
];
