<?php

namespace app\components;

use PDO;

class Database {
  const QUERY_FETCH_ALL    = 1;
  const QUERY_FETCH_ONE    = 2;
  const QUERY_FETCH_COLUMN = 3;
  const QUERY_EXECUTE      = 4;

  private $connection;

  public function __construct($options) {
    $pdo_options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

    $run_set_charset = false;
    $set_charset = 'SET NAMES '.$options['charset'];

    $connection_str = 'mysql:host='.$options['host'].';dbname='.$options['database'];
    if(version_compare(PHP_VERSION, '5.3.6', '<')) {
      if(defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
        $pdo_options[PDO::MYSQL_ATTR_INIT_COMMAND] = $set_charset;
      } else {
        $run_set_charset = true;
      }
    } else {
      $connection_str .= ';charset='.$options['charset'];
    }

    $this->connection = new PDO($connection_str, $options['user'], $options['password'], $pdo_options);
    if($run_set_charset) {
      $this->connection->exec($set_charset);
    }
  }

  public function getLastId() {
    return $this->connection->lastInsertId();
  }

  public function getAll($sql, $args = []) {
    return $this->exec(self::QUERY_FETCH_ALL, $sql, $args);
  }

  public function getOne($sql, $args = []) {
    return $this->exec(self::QUERY_FETCH_ONE, $sql, $args);
  }

  public function getCount($sql, $args = []) {
    return $this->exec(self::QUERY_FETCH_COLUMN, $sql, $args);
  }

  public function query($sql, $args = []) {
    return $this->exec(self::QUERY_EXECUTE, $sql, $args);
  }

  private function exec($type, $sql, $args = []) {
    $result = null;
    $statement = $this->connection->prepare($sql);
    if($args) {
      $i = 1;
      foreach($args as $argument) {
        if(!isset($argument['type'])) {
          $argument['type'] = PDO::PARAM_STR;
        }
        if(isset($argument['param'])) {
          $statement->bindParam($argument['param'], $argument['val'], $argument['type']);
        } else {
          $statement->bindParam($i++, $argument['val'], $argument['type']);
        }
      }
    }

    switch($type) {
      case self::QUERY_FETCH_ALL:
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        break;
      case self::QUERY_FETCH_ONE:
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        break;
      case self::QUERY_FETCH_COLUMN:
        $statement->execute();
        $result = $statement->fetchColumn(0);
        break;
      case self::QUERY_EXECUTE:
        $statement->execute();
        $result = $statement->rowCount();
    }
    $statement->closeCursor();

    return $result;
  }
}
