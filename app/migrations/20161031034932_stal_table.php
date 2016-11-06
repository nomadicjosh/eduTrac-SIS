<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class StalTable extends AbstractMigration
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
        $table = $this->table('stal', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', array('signed' => true, 'zerofill' => true, 'identity' => true, 'limit' => 11))
            ->addColumn('code', 'string', ['limit' => 11])
            ->addColumn('name', 'string', ['limit' => 108])
            ->addIndex(['code'])
            ->create();
    }
}
