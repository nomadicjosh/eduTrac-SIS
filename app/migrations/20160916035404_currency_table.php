<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CurrencyTable extends AbstractMigration
{
    public function up()
    {
        // Migration for table currency_code
        $table = $this->table('currency_code', array('id' => false, 'primary_key' => 'id'));
        $table
            ->addColumn('id', 'integer', array('signed' => true, 'zerofill' => true, 'identity' => true, 'limit' => 11))
            ->addColumn('country_currency', 'string', array('limit' => 180))
            ->addColumn('currency_code', 'string', array('limit' => 3))
            ->addColumn('code_2000', 'string', array('limit' => 6))
            ->addColumn('arial_unicode_ms', 'string', array('limit' => 6))
            ->addColumn('unicode_decimal', 'string', array('limit' => 25))
            ->addColumn('unicode_hex', 'string', array('limit' => 25))
            ->create();
        
        $this->execute(file_get_contents('app/migrations/currency_table.txt'));
    }
    
    public function down()
    {
        $this->dropTable('currency_code');
    }
}
