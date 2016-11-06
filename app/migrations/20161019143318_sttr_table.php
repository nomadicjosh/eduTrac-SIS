<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class SttrTable extends AbstractMigration
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
        $table = $this->table('sttr', ['id' => false]);
        $table
            ->addColumn('stuID', 'integer', ['signed' => true, 'zerofill' => true, 'limit' => MysqlAdapter::INT_BIG])
            ->addColumn('termCode', 'string', ['limit' => 11])
            ->addColumn('acadLevelCode', 'string', ['limit' => 4])
            ->addColumn('attCred', 'decimal', ['signed' => true, 'zerofill' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'])
            ->addColumn('compCred', 'decimal', ['signed' => true, 'zerofill' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'])
            ->addColumn('gradePoints', 'decimal', ['signed' => true, 'zerofill' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'])
            ->addColumn('stuLoad', 'string', ['limit' => 2])
            ->addColumn('gpa', 'decimal', ['signed' => true, 'zerofill' => true, 'precision' => 4, 'scale' => 2, 'default' => '0.00'])
            ->addColumn('created', 'datetime', [])
            ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['stuID', 'termCode', 'acadLevelCode'], ['unique' => true])
            ->addIndex(['termCode'])
            ->addForeignKey('stuID', 'student', 'stuID', ['delete' => 'RESTRICT', 'update' => 'CASCADE'])
            ->addForeignKey('termCode', 'term', 'termCode', ['delete' => 'RESTRICT', 'update' => 'CASCADE'])
            ->create();
        
        $exists = $this->hasTable('stu_term');
        if ($exists) {
            $sql = $this->query('SELECT '
                . 'a.stuID,a.termCode,a.acadLevelCode,a.addDateTime,b.attCred,b.compCred,b.gradePoints,b.termGPA,c.stuLoad'
                . ' FROM stu_term a'
                . ' LEFT JOIN stu_term_gpa b ON a.stuID = b.stuID AND a.termCode = b.termCode AND a.acadLevelCode = b.acadLevelCode'
                . ' LEFT JOIN stu_term_load c ON a.stuID = c.stuID AND a.termCode = c.termCode AND a.acadLevelCode = c.acadLevelCode');
            foreach ($sql as $row) {
                $data = [
                    'stuID' => $row['stuID'],
                    'termCode' => $row['termCode'],
                    'acadLevelCode' => $row['acadLevelCode'],
                    'attCred' => $row['attCred'],
                    'compCred' => $row['compCred'],
                    'gradePoints' => $row['gradePoints'],
                    'stuLoad' => $row['stuLoad'],
                    'gpa' => $row['termGPA'],
                    'created' => $row['addDateTime']
                ];
                $this->insert('sttr', $data);
            }

            $this->execute('DELETE FROM stu_term');
            $this->execute('DELETE FROM stu_term_gpa');
            $this->execute('DELETE FROM stu_term_load');
            $this->dropTable('stu_term');
            $this->dropTable('stu_term_gpa');
            $this->dropTable('stu_term_load');
        }
    }
}
