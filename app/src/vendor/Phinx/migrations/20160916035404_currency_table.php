<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CurrencyTable extends AbstractMigration
{

    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        // Migration for table currency_code
        $table = $this->table('currency_code');
        $table
            ->addColumn('id', 'integer', array('limit' => 11))
            ->addColumn('country_currency', 'string', array('limit' => 180))
            ->addColumn('currency_code', 'string', array('limit' => 3))
            ->addColumn('code_2000', 'string', array('limit' => 6))
            ->addColumn('arial_unicode_ms', 'string', array('limit' => 6))
            ->addColumn('unicode_decimal', 'string', array('limit' => 25))
            ->addColumn('unicode_hex', 'string', array('limit' => 25))
            ->create();
        
        $this->execute(file_get_contents('app/src/vendor/Phinx/migrations/currency_table.txt'));
    }
}
