<?php

use Phinx\Migration\AbstractMigration;

class CreatePropertyTable extends AbstractMigration
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
    $table = $this->table('property', ['signed' => false /* id unsigned */]);
    $table->addColumn('product_id', 'integer', ['signed' => false])
      ->addColumn('name', 'string', ['limit' => 255])
      ->addColumn('val', 'string', ['limit' => 255])
      ->addForeignKey('product_id', 'product', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE', 'constraint' => 'fk_product_property'])
      ->create();
  }
}
