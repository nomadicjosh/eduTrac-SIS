<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AclvTable extends AbstractMigration
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
        $table = $this->table('aclv', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', ['signed' => true, 'zerofill' => true, 'identity' => true, 'limit' => 11])
            ->addColumn('code', 'string', ['limit' => 11])
            ->addColumn('name', 'string', ['limit' => 80])
            ->addColumn('grsc', 'string', ['limit' => 6])
            ->addColumn('ht_creds', 'decimal', ['signed' => true, 'zerofill' => true, 'precision' => 4, 'scale' => 1, 'default' => '6.0'])
            ->addColumn('ft_creds', 'decimal', ['signed' => true, 'zerofill' => true, 'precision' => 4, 'scale' => 1, 'default' => '12.0'])
            ->addColumn('ovr_creds', 'decimal', ['signed' => true, 'zerofill' => true, 'precision' => 4, 'scale' => 1, 'default' => '24.0'])
            ->addColumn('grad_level', 'enum', ['default' => 'No', 'values' => ['Yes', 'No']])
            ->addColumn('comp_months', 'integer', ['signed' => true, 'zerofill' => true, 'limit' => MysqlAdapter::INT_TINY])
            ->addIndex(['code'])
            ->create();

        $rows = [
            [
                'code' => 'NA',
                'name' => 'Not Applicable',
                'grad_level' => 'No',
                'comp_months' => 0
            ],
            [
                'code' => 'CE',
                'name' => 'Continuing Education',
                'grad_level' => 'No',
                'comp_months' => 12
            ],
            [
                'code' => 'CTF',
                'name' => 'Certificate',
                'grad_level' => 'No',
                'comp_months' => 24
            ],
            [
                'code' => 'DIP',
                'name' => 'Diploma',
                'grad_level' => 'No',
                'comp_months' => 12
            ],
            [
                'code' => 'UG',
                'name' => 'Undergraduate',
                'grad_level' => 'No',
                'comp_months' => 48
            ],
            [
                'code' => 'GR',
                'name' => 'Graduate',
                'grad_level' => 'Yes',
                'comp_months' => 48
            ],
            [
                'code' => 'PR',
                'name' => 'Professional',
                'grad_level' => 'Yes',
                'comp_months' => 12
            ],
            [
                'code' => 'PhD',
                'name' => 'Doctorate',
                'grad_level' => 'Yes',
                'comp_months' => 12
            ]
        ];
        
        $this->insert('aclv', $rows);
    }
}
