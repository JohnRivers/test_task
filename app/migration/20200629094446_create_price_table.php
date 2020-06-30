<?php

use Phinx\Migration\AbstractMigration;

class CreatePriceTable extends AbstractMigration
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
    $table = $this->table('price', ['signed' => false /* id unsigned */]);
    $table->addColumn('product_id', 'integer', ['signed' => false])
      ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2, 'signed' => false, 'comment' => 'цена без скидки'])
      ->addColumn('price_old', 'decimal', ['precision' => 10, 'scale' => 2, 'signed' => false, 'comment' => 'цена со скидкой'])
      ->addColumn('price_credit', 'decimal', ['precision' => 10, 'scale' => 2, 'signed' => false, 'comment' => 'цена в кредит'])
      ->addColumn('cashback', 'decimal', ['precision' => 10, 'scale' => 2, 'signed' => false])
      ->addColumn('valid_date', 'date', ['comment' => 'на какую дату цена действительна'])
      ->addForeignKey('product_id', 'product', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE', 'constraint' => 'fk_product_price'])
      ->create();
  }
}
