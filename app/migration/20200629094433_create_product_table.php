<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateProductTable extends AbstractMigration
{
  /**
   * Change Method.
   *
   * Write your reversible migrations using this method.
   *
   * More information on writing migrations is available here:
   * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
   *
   * Remember to call "create()" or "update()" and NOT "save()" when working
   * with the Table class.
   */
  public function change()
  {
    $table = $this->table('product', ['signed' => false /* id unsigned */]);
    $table->addColumn('name', 'string', ['limit' => 255])
      ->addColumn('image', 'string', ['limit' => 255, 'null' => true])
      ->addColumn('code', 'integer', ['signed' => false, 'comment' => 'код товара'])
      ->addColumn('rating', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'signed' => false, 'default' => 0, 'comment' => 'рейтинг товара'])
      ->addTimestamps()
      ->create();
  }
}
