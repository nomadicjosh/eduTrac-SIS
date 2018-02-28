<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class DbSchemaUpdate extends AbstractMigration
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
        $this->execute("SET FOREIGN_KEY_CHECKS=0;");

        if (!$this->hasTable('aclv') || !$this->hasTable('campaign') || !$this->hasTable('v_scrd')) :

            if (!$this->hasTable('aclv')) :
                $table = $this->table('aclv', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['signed' => true, 'identity' => true, 'limit' => 11])
                    ->addColumn('code', 'char', ['limit' => 22])
                    ->addColumn('name', 'string', ['limit' => 80])
                    ->addColumn('grsc', 'string', ['null' => true, 'limit' => 6])
                    ->addColumn('ht_creds', 'decimal', ['signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '6.0'])
                    ->addColumn('ft_creds', 'decimal', ['signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '12.0'])
                    ->addColumn('ovr_creds', 'decimal', ['signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '24.0'])
                    ->addColumn('grad_level', 'enum', ['default' => 'No', 'values' => ['Yes', 'No']])
                    ->addColumn('comp_months', 'integer', ['signed' => true, 'limit' => MysqlAdapter::INT_TINY])
                    ->addIndex(['code'], ['unique' => true])
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
            endif;

            $this->execute("ALTER TABLE billing_table ADD COLUMN `type` enum('F','T') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'F' AFTER `amount`;");

            $this->execute("ALTER TABLE billing_table ADD COLUMN `termCode` char(22) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `type`;");

            $this->execute("ALTER TABLE billing_table ADD COLUMN `rule` text COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `termCode`;");

            if (!$this->hasTable('clas')) :
                $table = $this->table('clas', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['signed' => true, 'identity' => true, 'limit' => 11])
                    ->addColumn('code', 'char', ['limit' => 22])
                    ->addColumn('name', 'string', ['limit' => 80])
                    ->addColumn('acadLevelCode', 'char', ['limit' => 22])
                    ->addIndex(['code'])
                    ->addForeignKey('acadLevelCode', 'aclv', 'code', ['constraint' => 'clas_acadLevelCode', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                    ->create();
            endif;

            if (!$this->hasTable('crlv')) :
                $table = $this->table('crlv', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['signed' => true, 'identity' => true, 'limit' => 11])
                    ->addColumn('code', 'char', ['limit' => 22])
                    ->addColumn('name', 'string', ['limit' => 80])
                    ->addColumn('status', 'enum', ['default' => 'A', 'values' => ['A', 'I']])
                    ->addIndex(['code'], ['unique' => true])
                    ->create();

                $rows = [
                    [
                        'code' => '100',
                        'name' => '100 Course Level',
                        'status' => 'A'
                    ],
                    [
                        'code' => '200',
                        'name' => '200 Course Level',
                        'status' => 'A'
                    ],
                    [
                        'code' => '300',
                        'name' => '300 Course Level',
                        'status' => 'A'
                    ],
                    [
                        'code' => '400',
                        'name' => '400 Course Level',
                        'status' => 'A'
                    ],
                    [
                        'code' => '500',
                        'name' => '500 Course Level',
                        'status' => 'A'
                    ],
                    [
                        'code' => '600',
                        'name' => '600 Course Level',
                        'status' => 'A'
                    ],
                    [
                        'code' => '700',
                        'name' => '700 Course Level',
                        'status' => 'A'
                    ],
                    [
                        'code' => '800',
                        'name' => '800 Course Level',
                        'status' => 'A'
                    ],
                    [
                        'code' => '900',
                        'name' => '900 Course Level',
                        'status' => 'A'
                    ]
                ];

                $this->insert('crlv', $rows);
            endif;

            $this->execute("ALTER TABLE `course` MODIFY COLUMN `preReq` text COLLATE utf8mb4_unicode_ci DEFAULT NULL;");

            $this->execute("ALTER TABLE `course` ADD COLUMN `printText` text COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `preReq`;");

            $this->execute("ALTER TABLE `course` ADD COLUMN `rule` text COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `printText`;");

            $this->execute("UPDATE `course` set `courseDesc` = REPLACE(`courseDesc`,'\\r\\n', char(10));");

            $this->execute("ALTER TABLE `course_sec` MODIFY COLUMN `courseSecCode` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL;");

            $this->execute("ALTER TABLE `course_sec` MODIFY COLUMN `courseSection` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL;");

            $this->execute("ALTER TABLE `course_sec` MODIFY COLUMN `courseCode` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL;");

            $this->execute("UPDATE `course_sec` set `comment` = REPLACE(`comment`,'\\r\\n', char(10));");

            $this->execute("UPDATE `acad_program` set `programDesc` = REPLACE(`programDesc`,'\\r\\n', char(10));");

            $this->execute("UPDATE `application` set `appl_comments` = REPLACE(`appl_comments`,'\\r\\n', char(10));");

            $this->execute("UPDATE `application` set `staff_comments` = REPLACE(`staff_comments`,'\\r\\n', char(10));");

            $this->execute("UPDATE `met_news` set `news_content` = REPLACE(`news_content`,'\\r\\n', char(10));");

            $this->execute("UPDATE `met_page` set `page_content` = REPLACE(`page_content`,'\\r\\n', char(10));");

            $this->execute("UPDATE `saved_query` set `savedQuery` = REPLACE(`savedQuery`,'\\r\\n', char(10));");

            $this->execute("UPDATE `payment` set `comment` = REPLACE(`comment`,'\\r\\n', char(10));");

            $this->execute("UPDATE `refund` set `comment` = REPLACE(`comment`,'\\r\\n', char(10));");

            $this->execute("UPDATE `stu_acct_bill` set `stu_comments` = REPLACE(`stu_comments`,'\\r\\n', char(10));");

            $this->execute("UPDATE `stu_acct_bill` set `staff_comments` = REPLACE(`staff_comments`,'\\r\\n', char(10));");

            $this->execute("UPDATE `stu_acct_pp` set `comments` = REPLACE(`comments`,'\\r\\n', char(10));");

            $this->execute("UPDATE `options_meta` SET `meta_key` = replace(meta_key, 'myet_', 'myetsis_')");

            $this->execute("UPDATE `options_meta` SET `meta_key` = replace(meta_key, 'et_core_', 'etsis_core_')");

            $this->execute("UPDATE `options_meta` set `meta_value` = REPLACE(`meta_value`,'\\r\\n', char(10));");

            // Migration for table restriction => perc
            if (!$this->hasTable('perc')) :
                $table = $this->table('perc', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('personID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('code', 'char', ['limit' => 22])
                    ->addColumn('severity', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_TINY])
                    ->addColumn('startDate', 'date', [])
                    ->addColumn('endDate', 'date', ['null' => true])
                    ->addColumn('comment', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                    ->addColumn('addDate', 'date', [])
                    ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                    ->addForeignKey('personID', 'person', 'personID', ['constraint' => 'perc_personID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->addForeignKey('code', 'rest', 'code', ['constraint' => 'perc_code', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->addForeignKey('addedBy', 'staff', 'staffID', ['constraint' => 'perc_staffID', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                    ->create();

                $sql = $this->query("SELECT * FROM restriction");
                foreach ($sql as $row) {
                    $data = [
                        'personID' => $row['personID'],
                        'code' => $row['code'],
                        'severity' => $row['severity'],
                        'startDate' => $row['startDate'],
                        'endDate' => $row['endDate'],
                        'comment' => $row['comment'],
                        'addDate' => $row['addDate'],
                        'addedBy' => $row['addedBy']
                    ];
                    $this->insert('perc', $data);
                }
            endif;

            $this->execute("UPDATE `perc` set `comment` = REPLACE(`comment`,'\\r\\n', char(10));");

            $this->execute("ALTER TABLE `person` ADD COLUMN `tags` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `status`;");

            // Migration for table restriction_code => rest
            if (!$this->hasTable('rest')) :
                $table = $this->table('rest', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                    ->addColumn('code', 'char', ['limit' => 22])
                    ->addColumn('description', 'string', ['limit' => 191])
                    ->addColumn('deptCode', 'char', ['null' => true, 'limit' => 22])
                    ->addIndex(['code'], ['unique' => true])
                    ->addForeignKey('deptCode', 'department', 'deptCode', ['constraint' => 'rest_deptCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->create();

                $sql = $this->query("SELECT * FROM restriction_code");
                foreach ($sql as $row) {
                    $data = [
                        'code' => $row['code'],
                        'description' => $row['description'],
                        'deptCode' => $row['deptCode']
                    ];
                    $this->insert('rest', $data);
                }
            endif;

            // Migration for table stu_acad_cred => stac
            if (!$this->hasTable('stac')) :
                $table = $this->table('stac', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('courseID', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('courseSecID', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('courseCode', 'string', ['limit' => 80])
                    ->addColumn('courseSecCode', 'string', ['null' => true, 'limit' => 80])
                    ->addColumn('sectionNumber', 'char', ['null' => true, 'limit' => 8])
                    ->addColumn('courseSection', 'string', ['null' => true, 'limit' => 80])
                    ->addColumn('termCode', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('reportingTerm', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('subjectCode', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('deptCode', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('shortTitle', 'char', ['limit' => 60])
                    ->addColumn('longTitle', 'char', ['limit' => 80])
                    ->addColumn('compCred', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.00'])
                    ->addColumn('gradePoints', 'decimal', ['precision' => 4, 'scale' => 2, 'default' => '0.00'])
                    ->addColumn('attCred', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.00'])
                    ->addColumn('ceu', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.0'])
                    ->addColumn('status', 'enum', ['default' => 'A', 'values' => ['A', 'N', 'D', 'W', 'C']])
                    ->addColumn('statusDate', 'date', [])
                    ->addColumn('statusTime', 'char', ['limit' => 10])
                    ->addColumn('acadLevelCode', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('courseLevelCode', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('grade', 'char', ['null' => true, 'limit' => 6])
                    ->addColumn('creditType', 'char', ['default' => 'I', 'limit' => 4])
                    ->addColumn('startDate', 'date', [])
                    ->addColumn('endDate', 'date', ['null' => true])
                    ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('addDate', 'date', ['null' => true])
                    ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                    ->addIndex(['stuID', 'courseSecID'], ['unique' => true])
                    ->addIndex(['status', 'courseCode'])
                    ->addForeignKey('stuID', 'student', 'stuID', ['constraint' => 'stac_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->addForeignKey('courseID', 'course', 'courseID', ['constraint' => 'stac_courseID', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('courseSecID', 'course_sec', 'courseSecID', ['constraint' => 'stac_courseSecID', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('courseSecCode', 'course_sec', 'courseSecCode', ['constraint' => 'stac_courseSecCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('courseSection', 'course_sec', 'courseSection', ['constraint' => 'stac_courseSection', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('termCode', 'term', 'termCode', ['constraint' => 'stac_termCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('subjectCode', 'subject', 'subjectCode', ['constraint' => 'stac_subjectCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('deptCode', 'department', 'deptCode', ['constraint' => 'stac_deptCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('acadLevelCode', 'aclv', 'code', ['constraint' => 'stac_acadLevelCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('courseLevelCode', 'crlv', 'code', ['constraint' => 'stac_courseLevelCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('addedBy', 'person', 'personID', ['constraint' => 'stac_addedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                    ->create();

                $sql = $this->query("SELECT * FROM stu_acad_cred");
                foreach ($sql as $row) {
                    $data = [
                        'stuID' => $row['stuID'],
                        'courseID' => $row['courseID'],
                        'courseSecID' => $row['courseSecID'],
                        'courseCode' => $row['courseCode'],
                        'courseSecCode' => $row['courseSecCode'],
                        'sectionNumber' => $row['sectionNumber'],
                        'courseSection' => $row['courseSection'],
                        'termCode' => $row['termCode'],
                        'reportingTerm' => $row['reportingTerm'],
                        'subjectCode' => $row['subjectCode'],
                        'deptCode' => $row['deptCode'],
                        'shortTitle' => $row['shortTitle'],
                        'longTitle' => $row['longTitle'],
                        'compCred' => $row['compCred'],
                        'gradePoints' => $row['gradePoints'],
                        'attCred' => $row['attCred'],
                        'ceu' => $row['ceu'],
                        'status' => $row['status'],
                        'statusDate' => $row['statusDate'],
                        'statusTime' => $row['statusTime'],
                        'acadLevelCode' => $row['acadLevelCode'],
                        'courseLevelCode' => $row['courseLevelCode'],
                        'grade' => $row['grade'],
                        'creditType' => $row['creditType'],
                        'startDate' => $row['startDate'],
                        'endDate' => $row['endDate'],
                        'addedBy' => $row['addedBy'],
                        'addDate' => $row['addDate'],
                    ];
                    $this->insert('stac', $data);
                }
            endif;

            $this->execute("ALTER TABLE `staff` ADD COLUMN `tags` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `status`;");

            // Migration for table stu_acad_level
            if (!$this->hasTable('stal')) :
                $table = $this->table('stal', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('acadProgCode', 'string', ['limit' => 80])
                    ->addColumn('acadLevelCode', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('currentClassLevel', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('enrollmentStatus', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('acadStanding', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('gpa', 'decimal', ['null' => true, 'precision' => 4, 'scale' => 2, 'default' => '0.00'])
                    ->addColumn('startTerm', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('startDate', 'date', [])
                    ->addColumn('endDate', 'date', ['null' => true])
                    ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                    ->addIndex(['stuID', 'acadProgCode'], ['unique' => true])
                    ->addForeignKey('acadProgCode', 'acad_program', 'acadProgCode', ['constraint' => 'stal_acadProgCode', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->addForeignKey('acadLevelCode', 'aclv', 'code', ['constraint' => 'stal_acadLevelCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('currentClassLevel', 'clas', 'code', ['constraint' => 'stal_currentClassLevel', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('startTerm', 'term', 'termCode', ['constraint' => 'stal_startTerm', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('stuID', 'student', 'stuID', ['constraint' => 'stal_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->create();

                $sql = $this->query("SELECT * FROM stu_acad_level");
                foreach ($sql as $row) {
                    $data = [
                        'stuID' => $row['stuID'],
                        'acadProgCode' => $row['acadProgCode'],
                        'acadLevelCode' => $row['acadLevelCode'],
                        'startDate' => $row['addDate']
                    ];
                    $this->insert('stal', $data);
                }
            endif;

            // Migration for table stu_course_sec => stcs
            if (!$this->hasTable('stcs')) :
                $table = $this->table('stcs', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('courseSecID', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('courseSecCode', 'string', ['null' => true, 'limit' => 80])
                    ->addColumn('courseSection', 'string', ['null' => true, 'limit' => 80])
                    ->addColumn('termCode', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('courseCredits', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.0'])
                    ->addColumn('ceu', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.0'])
                    ->addColumn('regDate', 'date', ['null' => true])
                    ->addColumn('regTime', 'string', ['null' => true, 'limit' => 10])
                    ->addColumn('status', 'enum', ['default' => 'A', 'values' => ['A', 'N', 'D', 'W', 'C']])
                    ->addColumn('statusDate', 'date', [])
                    ->addColumn('statusTime', 'string', ['limit' => 10])
                    ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                    ->addIndex(['stuID', 'courseSecID'], ['unique' => true])
                    ->addIndex(['status'])
                    ->addForeignKey('stuID', 'student', 'stuID', ['constraint' => 'stcs_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->addForeignKey('courseSecID', 'course_sec', 'courseSecID', ['constraint' => 'stcs_courseSecID', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('courseSecCode', 'course_sec', 'courseSecCode', ['constraint' => 'stcs_courseSecCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('courseSection', 'course_sec', 'courseSection', ['constraint' => 'stcs_courseSection', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('termCode', 'term', 'termCode', ['constraint' => 'stcs_termCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('addedBy', 'person', 'personID', ['constraint' => 'stcs_addedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                    ->create();

                $sql = $this->query("SELECT * FROM stu_course_sec");
                foreach ($sql as $row) {
                    $data = [
                        'stuID' => $row['stuID'],
                        'courseSecID' => $row['courseSecID'],
                        'courseSecCode' => $row['courseSecCode'],
                        'courseSection' => $row['courseSection'],
                        'termCode' => $row['termCode'],
                        'courseCredits' => $row['courseCredits'],
                        'ceu' => $row['ceu'],
                        'regDate' => $row['regDate'],
                        'regTime' => $row['regTime'],
                        'status' => $row['status'],
                        'statusDate' => $row['statusDate'],
                        'statusTime' => $row['statusTime'],
                        'addedBy' => $row['addedBy']
                    ];
                    $this->insert('stcs', $data);
                }
            endif;

            // Migration for table student_program => sacp
            if (!$this->hasTable('sacp')) :
                $table = $this->table('sacp', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('advisorID', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('catYearCode', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('acadProgCode', 'string', ['limit' => 80])
                    ->addColumn('currStatus', 'char', ['limit' => 4])
                    ->addColumn('eligible_to_graduate', 'enum', ['default' => '0', 'values' => ['1', '0']])
                    ->addColumn('antGradDate', 'char', ['null' => true, 'limit' => 12])
                    ->addColumn('graduationDate', 'date', ['null' => true])
                    ->addColumn('statusDate', 'date', [])
                    ->addColumn('startDate', 'date', [])
                    ->addColumn('endDate', 'date', ['null' => true])
                    ->addColumn('comments', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                    ->addColumn('approvedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                    ->addIndex(['stuID', 'acadProgCode'], ['unique' => true])
                    ->addIndex(['currStatus'])
                    ->addForeignKey('acadProgCode', 'acad_program', 'acadProgCode', ['constraint' => 'sacp_acadProgCode', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->addForeignKey('stuID', 'student', 'stuID', ['constraint' => 'sacp_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->addForeignKey('approvedBy', 'person', 'personID', ['constraint' => 'sacp_approvedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                    ->addForeignKey('advisorID', 'staff', 'staffID', ['constraint' => 'sacp_advisorID', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('catYearCode', 'acad_year', 'acadYearCode', ['constraint' => 'sacp_catYearCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->create();

                $sql = $this->query("SELECT * FROM stu_program");
                foreach ($sql as $row) {
                    $data = [
                        'stuID' => $row['stuID'],
                        'advisorID' => $row['advisorID'],
                        'catYearCode' => $row['catYearCode'],
                        'acadProgCode' => $row['acadProgCode'],
                        'currStatus' => $row['currStatus'],
                        'eligible_to_graduate' => $row['eligible_to_graduate'],
                        'antGradDate' => $row['antGradDate'],
                        'graduationDate' => $row['graduationDate'],
                        'statusDate' => $row['statusDate'],
                        'startDate' => $row['startDate'],
                        'endDate' => $row['endDate'],
                        'comments' => $row['comments'],
                        'approvedBy' => $row['approvedBy']
                    ];
                    $this->insert('sacp', $data);
                }
            endif;

            // Migration for table stu_term => sttr
            if (!$this->hasTable('sttr')) :
                $table = $this->table('sttr', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('termCode', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('acadLevelCode', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('attCred', 'decimal', ['null' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'])
                    ->addColumn('compCred', 'decimal', ['null' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'])
                    ->addColumn('gradePoints', 'decimal', ['null' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'])
                    ->addColumn('stuLoad', 'char', ['limit' => 4])
                    ->addColumn('gpa', 'decimal', ['null' => true, 'precision' => 4, 'scale' => 2, 'default' => '0.00'])
                    ->addColumn('created', 'datetime', [])
                    ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                    ->addIndex(['stuID', 'termCode', 'acadLevelCode'], ['unique' => true])
                    ->addForeignKey('stuID', 'student', 'stuID', ['constraint' => 'sttr_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->addForeignKey('termCode', 'term', 'termCode', ['constraint' => 'sttr_termCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('acadLevelCode', 'aclv', 'code', ['constraint' => 'sttr_acadLevelCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->create();

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
            endif;

            if ($this->hasTable('permission')) :
                $this->execute('DROP TABLE IF EXISTS permission');
            endif;

            if (!$this->hasTable('permission')) :
                $table = $this->table('permission', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['signed' => false, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('permKey', 'string', ['limit' => 60])
                    ->addColumn('permName', 'string', ['limit' => 191])
                    ->addIndex(['permKey'], ['unique' => true])
                    ->create();

                $this->execute("INSERT INTO `permission` VALUES(17, 'edit_settings', 'Edit Settings');");
                $this->execute("INSERT INTO `permission` VALUES(18, 'access_audit_trail_screen', 'Audit Trail Logs');");
                $this->execute("INSERT INTO `permission` VALUES(19, 'access_sql_interface_screen', 'SQL Interface Screen');");
                $this->execute("INSERT INTO `permission` VALUES(36, 'access_course_screen', 'Course Screen');");
                $this->execute("INSERT INTO `permission` VALUES(40, 'access_faculty_screen', 'Faculty Screen');");
                $this->execute("INSERT INTO `permission` VALUES(44, 'access_parent_screen', 'Parent Screen');");
                $this->execute("INSERT INTO `permission` VALUES(48, 'access_student_screen', 'Student Screen');");
                $this->execute("INSERT INTO `permission` VALUES(52, 'access_plugin_screen', 'Plugin Screen');");
                $this->execute("INSERT INTO `permission` VALUES(57, 'access_role_screen', 'Role Screen');");
                $this->execute("INSERT INTO `permission` VALUES(61, 'access_permission_screen', 'Permission Screen');");
                $this->execute("INSERT INTO `permission` VALUES(65, 'access_user_role_screen', 'User Role Screen');");
                $this->execute("INSERT INTO `permission` VALUES(69, 'access_user_permission_screen', 'User Permission Screen');");
                $this->execute("INSERT INTO `permission` VALUES(73, 'access_email_template_screen', 'Email Template Screen');");
                $this->execute("INSERT INTO `permission` VALUES(74, 'access_course_sec_screen', 'Course Section Screen');");
                $this->execute("INSERT INTO `permission` VALUES(75, 'add_course_sec', 'Add Course Section');");
                $this->execute("INSERT INTO `permission` VALUES(78, 'course_sec_inquiry_only', 'Course Section Inquiry Only');");
                $this->execute("INSERT INTO `permission` VALUES(79, 'course_inquiry_only', 'Course Inquiry Only');");
                $this->execute("INSERT INTO `permission` VALUES(80, 'access_person_screen', 'Person Screen');");
                $this->execute("INSERT INTO `permission` VALUES(81, 'add_person', 'Add Person');");
                $this->execute("INSERT INTO `permission` VALUES(85, 'access_acad_prog_screen', 'Academic Program Screen');");
                $this->execute("INSERT INTO `permission` VALUES(86, 'add_acad_prog', 'Add Academic Program');");
                $this->execute("INSERT INTO `permission` VALUES(89, 'acad_prog_inquiry_only', 'Academic Program Inquiry Only');");
                $this->execute("INSERT INTO `permission` VALUES(90, 'access_nslc', 'NSLC');");
                $this->execute("INSERT INTO `permission` VALUES(91, 'access_error_log_screen', 'Error Log Screen');");
                $this->execute("INSERT INTO `permission` VALUES(92, 'access_student_portal', 'Student Portal');");
                $this->execute("INSERT INTO `permission` VALUES(93, 'access_cronjob_screen', 'Cronjob Screen');");
                $this->execute("INSERT INTO `permission` VALUES(97, 'access_report_screen', 'Report Screen');");
                $this->execute("INSERT INTO `permission` VALUES(98, 'add_address', 'Add Address');");
                $this->execute("INSERT INTO `permission` VALUES(100, 'address_inquiry_only', 'Address Inquiry Only');");
                $this->execute("INSERT INTO `permission` VALUES(101, 'general_inquiry_only', 'General Inquiry Only');");
                $this->execute("INSERT INTO `permission` VALUES(102, 'faculty_inquiry_only', 'Faculty Inquiry Only');");
                $this->execute("INSERT INTO `permission` VALUES(103, 'parent_inquiry_only', 'Parent Inquiry Only');");
                $this->execute("INSERT INTO `permission` VALUES(104, 'student_inquiry_only', 'Student Inquiry Only');");
                $this->execute("INSERT INTO `permission` VALUES(106, 'access_plugin_admin_page', 'Plugin Admin Page');");
                $this->execute("INSERT INTO `permission` VALUES(108, 'access_save_query_screens', 'Save Query Screens');");
                $this->execute("INSERT INTO `permission` VALUES(109, 'access_forms', 'Forms');");
                $this->execute("INSERT INTO `permission` VALUES(110, 'create_stu_record', 'Create Student Record');");
                $this->execute("INSERT INTO `permission` VALUES(111, 'create_fac_record', 'Create Faculty Record');");
                $this->execute("INSERT INTO `permission` VALUES(112, 'create_par_record', 'Create Parent Record');");
                $this->execute("INSERT INTO `permission` VALUES(113, 'reset_person_password', 'Reset Person Password');");
                $this->execute("INSERT INTO `permission` VALUES(114, 'register_students', 'Register Students');");
                $this->execute("INSERT INTO `permission` VALUES(167, 'access_ftp', 'FTP');");
                $this->execute("INSERT INTO `permission` VALUES(168, 'access_stu_roster_screen', 'Access Student Roster Screen');");
                $this->execute("INSERT INTO `permission` VALUES(169, 'access_grading_screen', 'Grading Screen');");
                $this->execute("INSERT INTO `permission` VALUES(170, 'access_bill_tbl_screen', 'Billing Table Screen');");
                $this->execute("INSERT INTO `permission` VALUES(171, 'add_crse_sec_bill', 'Add Course Sec Billing');");
                $this->execute("INSERT INTO `permission` VALUES(176, 'access_parent_portal', 'Parent Portal');");
                $this->execute("INSERT INTO `permission` VALUES(177, 'import_data', 'Import Data');");
                $this->execute("INSERT INTO `permission` VALUES(178, 'add_course', 'Add Course');");
                $this->execute("INSERT INTO `permission` VALUES(179, 'person_inquiry_only', 'Person Inquiry Only');");
                $this->execute("INSERT INTO `permission` VALUES(180, 'room_request', 'Room Request');");
                $this->execute("INSERT INTO `permission` VALUES(201, 'activate_course_sec', 'Activate Course Section');");
                $this->execute("INSERT INTO `permission` VALUES(202, 'cancel_course_sec', 'Cancel Course Section');");
                $this->execute("INSERT INTO `permission` VALUES(203, 'access_institutions_screen', 'Access Institutions Screen');");
                $this->execute("INSERT INTO `permission` VALUES(204, 'add_institution', 'Add Institution');");
                $this->execute("INSERT INTO `permission` VALUES(205, 'access_application_screen', 'Access Application Screen');");
                $this->execute("INSERT INTO `permission` VALUES(206, 'create_application', 'Create Application');");
                $this->execute("INSERT INTO `permission` VALUES(207, 'access_staff_screen', 'Staff Screen');");
                $this->execute("INSERT INTO `permission` VALUES(208, 'staff_inquiry_only', 'Staff Inquiry Only');");
                $this->execute("INSERT INTO `permission` VALUES(209, 'create_staff_record', 'Create Staff Record');");
                $this->execute("INSERT INTO `permission` VALUES(210, 'graduate_students', 'Graduate Students');");
                $this->execute("INSERT INTO `permission` VALUES(211, 'generate_transcripts', 'Generate Transcripts');");
                $this->execute("INSERT INTO `permission` VALUES(212, 'access_student_accounts', 'Access Student Accounts');");
                $this->execute("INSERT INTO `permission` VALUES(213, 'student_account_inquiry_only', 'Student Account Inquiry Only');");
                $this->execute("INSERT INTO `permission` VALUES(214, 'restrict_edit_profile', 'Restrict Edit Profile');");
                $this->execute("INSERT INTO `permission` VALUES(215, 'access_general_ledger', 'Access General Ledger');");
                $this->execute("INSERT INTO `permission` VALUES(216, 'login_as_user', 'Login as User');");
                $this->execute("INSERT INTO `permission` VALUES(217, 'access_academics', 'Access Academics');");
                $this->execute("INSERT INTO `permission` VALUES(218, 'access_financials', 'Access Financials');");
                $this->execute("INSERT INTO `permission` VALUES(219, 'access_human_resources', 'Access Human Resources');");
                $this->execute("INSERT INTO `permission` VALUES(220, 'submit_timesheets', 'Submit Timesheets');");
                $this->execute("INSERT INTO `permission` VALUES(221, 'access_sql', 'Access SQL');");
                $this->execute("INSERT INTO `permission` VALUES(222, 'access_person_mgmt', 'Access Person Management');");
                $this->execute("INSERT INTO `permission` VALUES(223, 'create_campus_site', 'Create Campus Site');");
                $this->execute("INSERT INTO `permission` VALUES(224, 'access_dashboard', 'Access Dashboard');");
                $this->execute("INSERT INTO `permission` VALUES(225, 'access_myetsis_admin', 'Access myetSIS Admin');");
                $this->execute("INSERT INTO `permission` VALUES(226, 'manage_myetsis_pages', 'Manage myetSIS Pages');");
                $this->execute("INSERT INTO `permission` VALUES(227, 'manage_myetsis_links', 'Manage myetSIS Links');");
                $this->execute("INSERT INTO `permission` VALUES(228, 'manage_myetsis_news', 'Manage myetSIS News');");
                $this->execute("INSERT INTO `permission` VALUES(229, 'add_myetsis_page', 'Add myetSIS Page');");
                $this->execute("INSERT INTO `permission` VALUES(230, 'edit_myetsis_page', 'Edit myetSIS Page');");
                $this->execute("INSERT INTO `permission` VALUES(231, 'delete_myetsis_page', 'Delete myetSIS Page');");
                $this->execute("INSERT INTO `permission` VALUES(232, 'add_myetsis_link', 'Add myetSIS Link');");
                $this->execute("INSERT INTO `permission` VALUES(233, 'edit_myetsis_link', 'Edit myetSIS Link');");
                $this->execute("INSERT INTO `permission` VALUES(234, 'delete_myetsis_link', 'Delete myetSIS Link');");
                $this->execute("INSERT INTO `permission` VALUES(235, 'add_myetsis_news', 'Add myetSIS News');");
                $this->execute("INSERT INTO `permission` VALUES(236, 'edit_myetsis_news', 'Edit myetSIS News');");
                $this->execute("INSERT INTO `permission` VALUES(237, 'delete_myetsis_news', 'Delete myetSIS News');");
                $this->execute("INSERT INTO `permission` VALUES(238, 'clear_screen_cache', 'Clear Screen Cache');");
                $this->execute("INSERT INTO `permission` VALUES(239, 'clear_database_cache', 'Clear Database Cache');");
                $this->execute("INSERT INTO `permission` VALUES(240, 'access_myetsis_appl_form', 'Access myetSIS Application Form');");
                $this->execute("INSERT INTO `permission` VALUES(241, 'edit_myetsis_css', 'Edit myetSIS CSS');");
                $this->execute("INSERT INTO `permission` VALUES(242, 'edit_myetsis_welcome_message', 'Edit myetSIS Welcome Message');");
                $this->execute("INSERT INTO `permission` VALUES(243, 'access_communication_mgmt', 'Access Marketing');");
                $this->execute("INSERT INTO `permission` VALUES(244, 'delete_student', 'Delete Student');");
                $this->execute("INSERT INTO `permission` VALUES(245, 'access_payment_gateway', 'Access Payment Gateway');");
                $this->execute("INSERT INTO `permission` VALUES(246, 'access_ea', 'Access etSIS Analytics');");
                $this->execute("INSERT INTO `permission` VALUES(247, 'access_gradebook', 'Access Gradebook');");
                $this->execute("INSERT INTO `permission` VALUES(248, 'execute_saved_query', 'Execute Saved Query');");
                $this->execute("INSERT INTO `permission` VALUES(249, 'submit_final_grades', 'Submit Final Grades');");
                $this->execute("INSERT INTO `permission` VALUES(250, 'manage_business_rules', 'Manage Business Rules');");
                $this->execute("INSERT INTO `permission` VALUES(251, 'override_rule', 'Override Rule');");
            endif;

            $this->execute("UPDATE `person_perms` SET `permission` = replace(permission, 'myet_', 'myetsis_')");
            
            $this->execute("UPDATE `role` SET `permission` = 'a:87:{i:0;s:13:\"edit_settings\";i:1;s:25:\"access_audit_trail_screen\";i:2;s:27:\"access_sql_interface_screen\";i:3;s:20:\"access_course_screen\";i:4;s:21:\"access_faculty_screen\";i:5;s:20:\"access_parent_screen\";i:6;s:21:\"access_student_screen\";i:7;s:20:\"access_plugin_screen\";i:8;s:18:\"access_role_screen\";i:9;s:24:\"access_permission_screen\";i:10;s:23:\"access_user_role_screen\";i:11;s:29:\"access_user_permission_screen\";i:12;s:28:\"access_email_template_screen\";i:13;s:24:\"access_course_sec_screen\";i:14;s:14:\"add_course_sec\";i:15;s:20:\"access_person_screen\";i:16;s:10:\"add_person\";i:17;s:23:\"access_acad_prog_screen\";i:18;s:13:\"add_acad_prog\";i:19;s:11:\"access_nslc\";i:20;s:23:\"access_error_log_screen\";i:21;s:21:\"access_cronjob_screen\";i:22;s:20:\"access_report_screen\";i:23;s:11:\"add_address\";i:24;s:24:\"access_plugin_admin_page\";i:25;s:25:\"access_save_query_screens\";i:26;s:12:\"access_forms\";i:27;s:17:\"create_stu_record\";i:28;s:17:\"create_fac_record\";i:29;s:17:\"create_par_record\";i:30;s:21:\"reset_person_password\";i:31;s:17:\"register_students\";i:32;s:10:\"access_ftp\";i:33;s:24:\"access_stu_roster_screen\";i:34;s:21:\"access_grading_screen\";i:35;s:22:\"access_bill_tbl_screen\";i:36;s:17:\"add_crse_sec_bill\";i:37;s:11:\"import_data\";i:38;s:10:\"add_course\";i:39;s:12:\"room_request\";i:40;s:19:\"activate_course_sec\";i:41;s:17:\"cancel_course_sec\";i:42;s:26:\"access_institutions_screen\";i:43;s:15:\"add_institution\";i:44;s:25:\"access_application_screen\";i:45;s:18:\"create_application\";i:46;s:19:\"access_staff_screen\";i:47;s:19:\"create_staff_record\";i:48;s:17:\"graduate_students\";i:49;s:20:\"generate_transcripts\";i:50;s:23:\"access_student_accounts\";i:51;s:21:\"access_general_ledger\";i:52;s:13:\"login_as_user\";i:53;s:16:\"access_academics\";i:54;s:17:\"access_financials\";i:55;s:22:\"access_human_resources\";i:56;s:17:\"submit_timesheets\";i:57;s:10:\"access_sql\";i:58;s:18:\"access_person_mgmt\";i:59;s:16:\"access_dashboard\";i:60;s:20:\"access_myetsis_admin\";i:61;s:20:\"manage_myetsis_pages\";i:62;s:20:\"manage_myetsis_links\";i:63;s:19:\"manage_myetsis_news\";i:64;s:16:\"add_myetsis_page\";i:65;s:17:\"edit_myetsis_page\";i:66;s:19:\"delete_myetsis_page\";i:67;s:16:\"add_myetsis_link\";i:68;s:17:\"edit_myetsis_link\";i:69;s:19:\"delete_myetsis_link\";i:70;s:16:\"add_myetsis_news\";i:71;s:17:\"edit_myetsis_news\";i:72;s:19:\"delete_myetsis_news\";i:73;s:18:\"clear_screen_cache\";i:74;s:20:\"clear_database_cache\";i:75;s:24:\"access_myetsis_appl_form\";i:76;s:16:\"edit_myetsis_css\";i:77;s:28:\"edit_myetsis_welcome_message\";i:78;s:25:\"access_communication_mgmt\";i:79;s:14:\"delete_student\";i:80;s:22:\"access_payment_gateway\";i:81;s:9:\"access_ea\";i:82;s:19:\"execute_saved_query\";i:83;s:19:\"submit_final_grades\";i:84;s:21:\"manage_business_rules\";i:85;s:13:\"override_rule\";i:86;s:8:\"send_sms\";}' WHERE id = '8'");

            if ($this->hasTable('screen')) :
                $this->execute('DROP TABLE IF EXISTS screen');
            endif;

            if ($this->hasTable('student_load_rule')) :
                $this->execute('DROP TABLE IF EXISTS student_load_rule');
            endif;

            if ($this->hasTable('v_stld')) :
                $this->execute('DROP VIEW IF EXISTS v_stld');
            endif;

            if (!$this->hasTable('v_stld')) :
                $this->execute(
                    "CREATE VIEW v_stld AS
                    SELECT
                        a.stuID,a.acadLevelCode,a.stuLoad
                    FROM
                        sttr a
                    WHERE
                        a.created = (SELECT MAX(b.created) FROM sttr b WHERE a.stuID = b.stuID AND a.acadLevelCode = b.acadLevelCode)
                    GROUP BY
                        a.stuID,a.acadLevelCode"
                );
            endif;

            if ($this->hasTable('v_sttr')) :
                $this->execute('DROP VIEW IF EXISTS v_sttr');
            endif;

            if (!$this->hasTable('v_sttr')) :
                $this->execute(
                    "CREATE VIEW v_sttr AS
                    SELECT
                        x.stuID,x.acadLevelCode,x.termCode
                    FROM
                        sttr x
                    WHERE
                        x.created = (SELECT MIN(y.created) FROM sttr y WHERE x.stuID = y.stuID AND x.acadLevelCode = y.acadLevelCode)
                    GROUP BY
                        x.stuID,x.acadLevelCode"
                );
            endif;

            if ($this->hasTable('v_scrd')) :
                $this->execute('DROP VIEW IF EXISTS v_scrd');
            endif;

            if (!$this->hasTable('v_scrd')) :
                $this->execute(
                    "CREATE VIEW v_scrd AS
                    SELECT 
                        sttr.stuID,sttr.acadLevelCode AS acadLevel,sacp.acadProgCode AS prog,SUM(sttr.attCred) attempted,SUM(sttr.compCred) completed,SUM(sttr.gradePoints) points,SUM(sttr.gpa)/COUNT(*) gpa,
                        v_stld.stuLoad AS enrollmentStatus,sacp.catYearCode AS year,v_sttr.termCode AS term
                    FROM
                        sttr
                    JOIN
                        sacp
                    ON
                        sttr.stuID = sacp.stuID
                    JOIN
                        acad_program
                    ON
                        sacp.acadProgCode = acad_program.acadProgCode
                    JOIN
                        v_stld
                    ON
                        sttr.stuID = v_stld.stuID AND sttr.acadLevelCode = v_stld.acadLevelCode
                    JOIN
                        v_sttr
                    ON
                        sttr.stuID = v_sttr.stuID AND sttr.acadLevelCode = v_sttr.acadLevelCode
                    WHERE
                        sttr.stuID = sacp.stuID
                    AND
                        sttr.acadLevelCode = acad_program.acadLevelCode
                    AND
                        sacp.currStatus = 'A'
                    AND
                        (sttr.compCred > '0.00' AND sttr.gradePoints > '0.00')
                    GROUP BY sttr.stuID,sttr.acadLevelCode
                    ORDER BY sttr.stuID;"
                );
            endif;

            if ($this->hasTable('v_rgn')) :
                $this->execute('DROP VIEW IF EXISTS v_rgn');
            endif;

            if (!$this->hasTable('v_rgn')) :
                $this->execute(
                    "CREATE VIEW v_rgn AS
                    SELECT
                        courseID AS crseID,courseSecID AS sectID,courseSection AS section,termCode AS term,longTitle AS title,SUM(attCred) AS attempted,SUM(compCred) AS completed,SUM(gradePoints) AS points,acadLevelCode AS acadLevel,COUNT(stuID) AS registrations
                    FROM
                        stac
                    WHERE
                        status IN('A','N')
                    GROUP BY
                        courseSecID"
                );
            endif;

            if ($this->hasTable('v_sacp')) :
                $this->execute('DROP VIEW IF EXISTS v_sacp');
            endif;

            if (!$this->hasTable('v_sacp')) :
                $this->execute(
                    "CREATE VIEW v_sacp AS
                    SELECT 
                        sacp.stuID,sacp.advisorID AS advisor,sacp.catYearCode AS catalog,sacp.acadProgCode as prog,aclv.code AS acadLevel,
                        major.majorCode AS major,minor.minorCode AS minor,ccd.ccdCode AS ccd, specialization.specCode as specialization,
                        degree.degreeCode AS degree,department.deptCode as department,v_scrd.term,sacp.currStatus AS status,
                        sacp.antGradDate,sacp.graduationDate,sacp.startDate,sacp.endDate
                    FROM
                        sacp
                    JOIN
                        acad_program
                    ON
                        sacp.acadProgCode = acad_program.acadProgCode
                    JOIN
                        aclv
                    ON
                        acad_program.acadLevelCode = aclv.code
                    JOIN
                        major
                    ON
                        acad_program.majorCode = major.majorCode
                    JOIN
                        minor
                    ON
                        acad_program.minorCode = minor.minorCode
                    JOIN
                        ccd
                    ON
                        acad_program.ccdCode = ccd.ccdCode
                    JOIN
                        specialization
                    ON
                        acad_program.specCode = specialization.specCode
                    JOIN
                        degree
                    ON
                        acad_program.degreeCode = degree.degreeCode
                    JOIN
                        department
                    ON
                        acad_program.deptCode = department.deptCode
                    LEFT JOIN
                        v_scrd
                    ON
                        sacp.stuID = v_scrd.stuID
                    GROUP BY sacp.stuID,sacp.acadProgCode
                    ORDER BY sacp.stuID;"
                );
            endif;

            if (!$this->hasTable('last_login')) :
                $table = $this->table('last_login', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('personID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('loginTimeStamp', 'datetime', [])
                    ->addForeignKey('personID', 'person', 'personID', ['constraint' => 'last_login_personID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->create();

                $this->execute("INSERT INTO `last_login` (`personID`,`loginTimeStamp`) SELECT `personID`,`LastLogin` FROM person;");
            endif;

            if ($this->hasTable('list')) :
                $this->execute("DROP TABLE IF EXISTS list;");
            endif;

            if (!$this->hasTable('list')) :
                $table = $this->table('list', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('code', 'string', ['limit' => 191])
                    ->addColumn('name', 'string', ['limit' => 191])
                    ->addColumn('description', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                    ->addColumn('rule', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                    ->addColumn('created', 'datetime', [])
                    ->addColumn('owner', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('deptCode', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('status', 'enum', ['default' => 'open', 'values' => ['open', 'closed']])
                    ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                    ->addForeignKey('owner', 'staff', 'staffID', ['constraint' => 'list_owner', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('deptCode', 'department', 'deptCode', ['constraint' => 'list_deptCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->create();
            endif;

            if ($this->hasTable('campaign')) :
                $this->execute("DROP TABLE IF EXISTS campaign;");
            endif;

            if (!$this->hasTable('campaign')) :
                $table = $this->table('campaign', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('subject', 'string', ['limit' => 191])
                    ->addColumn('from_name', 'string', ['limit' => 191])
                    ->addColumn('from_email', 'string', ['limit' => 191])
                    ->addColumn('html', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
                    ->addColumn('text', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                    ->addColumn('footer', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                    ->addColumn('attachment', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                    ->addColumn('status', 'enum', ['default' => 'ready', 'values' => ['ready', 'processing', 'paused', 'sent']])
                    ->addColumn('sendstart', 'datetime', ['null' => true])
                    ->addColumn('sendfinish', 'datetime', ['null' => true])
                    ->addColumn('recipients', 'integer', ['default' => '0', 'limit' => MysqlAdapter::INT_REGULAR])
                    ->addColumn('viewed', 'integer', ['default' => '0', 'limit' => MysqlAdapter::INT_REGULAR])
                    ->addColumn('bounces', 'integer', ['default' => '0', 'limit' => MysqlAdapter::INT_REGULAR])
                    ->addColumn('archive', 'enum', ['default' => '0', 'values' => ['1', '0']])
                    ->addColumn('owner', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('deptCode', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('addDate', 'datetime', [])
                    ->addColumn('last_queued', 'datetime', ['null' => true])
                    ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                    ->addForeignKey('owner', 'staff', 'staffID', ['constraint' => 'campaign_owner', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('deptCode', 'department', 'deptCode', ['constraint' => 'campaign_deptCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->create();
            endif;

            if ($this->hasTable('campaign_list')) :
                $this->execute("DROP TABLE IF EXISTS campaign_list;");
            endif;

            if (!$this->hasTable('campaign_list')) :
                $table = $this->table('campaign_list', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('cid', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('lid', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addForeignKey('cid', 'campaign', 'id', ['constraint' => 'campaign_list_cid', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->addForeignKey('lid', 'list', 'id', ['constraint' => 'campaign_list_lid', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->create();
            endif;

            if ($this->hasTable('template')) :
                $this->execute("DROP TABLE IF EXISTS template;");
            endif;

            if (!$this->hasTable('template')) :
                $table = $this->table('template', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                    ->addColumn('name', 'string', ['limit' => 191])
                    ->addColumn('description', 'string', ['null' => true, 'limit' => 191])
                    ->addColumn('content', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
                    ->addColumn('owner', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('deptCode', 'char', ['null' => true, 'limit' => 22])
                    ->addColumn('addDate', 'date', [])
                    ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                    ->addForeignKey('owner', 'staff', 'staffID', ['constraint' => 'template_owner', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->addForeignKey('deptCode', 'department', 'deptCode', ['constraint' => 'template_deptCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                    ->create();

                $sql = $this->query("SELECT * FROM email_template");
                foreach ($sql as $row) {
                    $data = [
                        'name' => $row['email_name'],
                        'content' => $row['email_value'],
                        'owner' => 1,
                        'deptCode' => $row['deptCode'],
                        'addDate' => date('Y-m-d')
                    ];
                    $this->insert('template', $data);

                    $this->execute("UPDATE `template` set `content` = REPLACE(`content`,'\\r\\n', char(10));");
                }
            endif;

            if ($this->hasTable('tracking')) :
                $this->execute("DROP TABLE IF EXISTS tracking;");
            endif;

            if (!$this->hasTable('tracking')) :
                $table = $this->table('tracking', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('cid', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('sid', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('first_open', 'datetime', ['null' => true])
                    ->addColumn('viewed', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                    ->addForeignKey('cid', 'campaign', 'id', ['constraint' => 'tracking_cid', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->addForeignKey('sid', 'person', 'personID', ['constraint' => 'tracking_sid', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->create();
            endif;

            if ($this->hasTable('tracking_link')) :
                $this->execute("DROP TABLE IF EXISTS tracking_link;");
            endif;

            if (!$this->hasTable('tracking_link')) :
                $table = $this->table('tracking_link', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
                $table
                    ->addColumn('id', 'integer', ['signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('cid', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('sid', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                    ->addColumn('source', 'string', ['limit' => 191])
                    ->addColumn('medium', 'string', ['limit' => 191])
                    ->addColumn('url', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR])
                    ->addColumn('clicked', 'integer', ['limit' => MysqlAdapter::INT_REGULAR])
                    ->addColumn('addDate', 'datetime', [])
                    ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                    ->addForeignKey('cid', 'campaign', 'id', ['constraint' => 'tracking_link_cid', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->addForeignKey('sid', 'person', 'personID', ['constraint' => 'tracking_link_sid', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                    ->create();
            endif;

            $this->execute(
                "
                ALTER TABLE `acad_program`
                    ADD CONSTRAINT `acad_program_acadLevelCode` FOREIGN KEY (`acadLevelCode`) REFERENCES `aclv` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `acad_program_acadYearCode` FOREIGN KEY (`acadYearCode`) REFERENCES `acad_year` (`acadYearCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `acad_program_approvedBy` FOREIGN KEY (`approvedBy`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `acad_program_ccdCode` FOREIGN KEY (`ccdCode`) REFERENCES `ccd` (`ccdCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `acad_program_cipCode` FOREIGN KEY (`cipCode`) REFERENCES `cip` (`cipCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `acad_program_degreeCode` FOREIGN KEY (`degreeCode`) REFERENCES `degree` (`degreeCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `acad_program_deptCode` FOREIGN KEY (`deptCode`) REFERENCES `department` (`deptCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `acad_program_locationCode` FOREIGN KEY (`locationCode`) REFERENCES `location` (`locationCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `acad_program_majorCode` FOREIGN KEY (`majorCode`) REFERENCES `major` (`majorCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `acad_program_minorCode` FOREIGN KEY (`minorCode`) REFERENCES `minor` (`minorCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `acad_program_schoolCode` FOREIGN KEY (`schoolCode`) REFERENCES `school` (`schoolCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `acad_program_specCode` FOREIGN KEY (`specCode`) REFERENCES `specialization` (`specCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                
                ALTER TABLE `address`
                    ADD CONSTRAINT `address_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `address_personID` FOREIGN KEY (`personID`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `application`
                    ADD CONSTRAINT `application_acadProgCode` FOREIGN KEY (`acadProgCode`) REFERENCES `acad_program` (`acadProgCode`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `application_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `application_personID` FOREIGN KEY (`personID`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `application_startTerm` FOREIGN KEY (`startTerm`) REFERENCES `term` (`termCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `assignment`
                    ADD CONSTRAINT `assignment_courseSecID` FOREIGN KEY (`courseSecID`) REFERENCES `course_sec` (`courseSecID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `assignment_facID` FOREIGN KEY (`facID`) REFERENCES `staff` (`staffID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `assignment_staffID` FOREIGN KEY (`addedBy`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE;
                    
                ALTER TABLE `billing_table`
                    ADD CONSTRAINT `billing_table_termCode` FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `building`
                    ADD CONSTRAINT `building_locationCode` FOREIGN KEY (`locationCode`) REFERENCES `location` (`locationCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `course`
                    ADD CONSTRAINT `course_acadLevelCode` FOREIGN KEY (`acadLevelCode`) REFERENCES `aclv` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `course_approvedBy` FOREIGN KEY (`approvedBy`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `course_courseLevelCode` FOREIGN KEY (`courseLevelCode`) REFERENCES `crlv` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `course_deptCode` FOREIGN KEY (`deptCode`) REFERENCES `department` (`deptCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `course_subjectCode` FOREIGN KEY (`subjectCode`) REFERENCES `subject` (`subjectCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `course_sec`
                    ADD CONSTRAINT `course_sec_acadLevelCode` FOREIGN KEY (`acadLevelCode`) REFERENCES `aclv` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `course_sec_approvedBy` FOREIGN KEY (`approvedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `course_sec_buildingCode` FOREIGN KEY (`buildingCode`) REFERENCES `building` (`buildingCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `course_sec_courseID` FOREIGN KEY (`courseID`) REFERENCES `course` (`courseID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `course_sec_courseLevelCode` FOREIGN KEY (`courseLevelCode`) REFERENCES `crlv` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `course_sec_deptCode` FOREIGN KEY (`deptCode`) REFERENCES `department` (`deptCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `course_sec_locationCode` FOREIGN KEY (`locationCode`) REFERENCES `location` (`locationCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `course_sec_roomCode` FOREIGN KEY (`roomCode`) REFERENCES `room` (`roomCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `course_sec_termCode` FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `event`
                    ADD CONSTRAINT `event_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `event_catID` FOREIGN KEY (`catID`) REFERENCES `event_category` (`catID`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `event_requestor` FOREIGN KEY (`requestor`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `event_roomCode` FOREIGN KEY (`roomCode`) REFERENCES `room` (`roomCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `event_termCode` FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `event_meta`
                    ADD CONSTRAINT `event_meta_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `event_meta_eventID` FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `event_meta_requestor` FOREIGN KEY (`requestor`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `event_meta_roomCode` FOREIGN KEY (`roomCode`) REFERENCES `room` (`roomCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `event_request`
                    ADD CONSTRAINT `event_request_requestor` FOREIGN KEY (`requestor`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `event_request_roomCode` FOREIGN KEY (`roomCode`) REFERENCES `room` (`roomCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `event_request_termCode` FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `external_course`
                    ADD CONSTRAINT `external_course_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `external_course_instCode` FOREIGN KEY (`instCode`) REFERENCES `institution` (`fice_ceeb`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `gl_transaction`
                    ADD CONSTRAINT `gl_transaction_jeID` FOREIGN KEY (`jeID`) REFERENCES `gl_journal_entry` (`jeID`) ON DELETE CASCADE ON UPDATE CASCADE;
  
                ALTER TABLE `gradebook`
                    ADD CONSTRAINT `gradebook_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `gradebook_assignID` FOREIGN KEY (`assignID`) REFERENCES `assignment` (`id`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `gradebook_courseSecID` FOREIGN KEY (`courseSecID`) REFERENCES `course_sec` (`courseSecID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `gradebook_facID` FOREIGN KEY (`facID`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `gradebook_stuID` FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `hiatus`
                    ADD CONSTRAINT `hiatus_staffID` FOREIGN KEY (`addedBy`) REFERENCES `staffID` (`staffID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `hiatus_stuID` FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `institution_attended`
                    ADD CONSTRAINT `institution_attended_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `institution_attended_personID` FOREIGN KEY (`personID`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `job`
                    ADD CONSTRAINT `job_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `staff` (`staffID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `met_news`
                    ADD CONSTRAINT `met_news_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `met_page`
                    ADD CONSTRAINT `met_page_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `payment`
                    ADD CONSTRAINT `payment_postedBy` FOREIGN KEY (`postedBy`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `payment_stuID` FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `payment_termCode` FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `pay_grade`
                    ADD CONSTRAINT `pay_grade_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `staff` (`staffID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `person`
                    ADD CONSTRAINT `person_approvedBy` FOREIGN KEY (`approvedBy`) REFERENCES `person` (`personID`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `person_perms`
                    ADD CONSTRAINT `person_perms_personID` FOREIGN KEY (`personID`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `person_roles`
                    ADD CONSTRAINT `person_roles_personID` FOREIGN KEY (`personID`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `refund`
                    ADD CONSTRAINT `refund_personID` FOREIGN KEY (`postedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `refund_stuID` FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `refund_termCode` FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `room`
                    ADD CONSTRAINT `room_buildingCode` FOREIGN KEY (`buildingCode`) REFERENCES `building` (`buildingCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `saved_query`
                    ADD CONSTRAINT `saved_query_personID` FOREIGN KEY (`personID`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `school`
                    ADD CONSTRAINT `school_buildingCode` FOREIGN KEY (`buildingCode`) REFERENCES `building` (`buildingCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `semester`
                    ADD CONSTRAINT `semester_acadYearCode` FOREIGN KEY (`acadYearCode`) REFERENCES `acad_year` (`acadYearCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `staff`
                    ADD CONSTRAINT `staff_approvedBy` FOREIGN KEY (`approvedBy`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `staff_buildingCode` FOREIGN KEY (`buildingCode`) REFERENCES `building` (`buildingCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `staff_deptCode` FOREIGN KEY (`deptCode`) REFERENCES `department` (`deptCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `staff_officeCode` FOREIGN KEY (`officeCode`) REFERENCES `room` (`roomCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `staff_schoolCode` FOREIGN KEY (`schoolCode`) REFERENCES `school` (`schoolCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `staff_staffID` FOREIGN KEY (`staffID`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `staff_meta`
                    ADD CONSTRAINT `staff_meta_approvedBy` FOREIGN KEY (`approvedBy`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `staff_meta_jobID` FOREIGN KEY (`jobID`) REFERENCES `job` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `staff_meta_jobStatusCode` FOREIGN KEY (`jobStatusCode`) REFERENCES `job_status` (`typeCode`) ON DELETE SET NULL ON UPDATE CASCADE,
                    ADD CONSTRAINT `staff_meta_staffID` FOREIGN KEY (`staffID`) REFERENCES `staff` (`staffID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `staff_meta_supervisorID` FOREIGN KEY (`supervisorID`) REFERENCES `staff` (`staffID`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `student`
                    ADD CONSTRAINT `student_approvedBy` FOREIGN KEY (`approvedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `student_stuID` FOREIGN KEY (`stuID`) REFERENCES `person` (`personID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `stu_acct_bill`
                    ADD CONSTRAINT `stu_acct_bill_postedBy` FOREIGN KEY (`postedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `stu_acct_bill_stuID` FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `stu_acct_bill_termCode` FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `stu_acct_fee`
                    ADD CONSTRAINT `stu_acct_fee_billID` FOREIGN KEY (`billID`) REFERENCES `stu_acct_bill` (`billID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `stu_acct_fee_postedBy` FOREIGN KEY (`postedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `stu_acct_fee_stuID` FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `stu_acct_fee_termCode` FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `stu_acct_pp`
                    ADD CONSTRAINT `stu_acct_pp_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `stu_acct_pp_stuID` FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `stu_acct_pp_termCode` FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `stu_acct_tuition`
                    ADD CONSTRAINT `stu_acct_tuition_postedBy` FOREIGN KEY (`postedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `stu_acct_tuition_stuID` FOREIGN KEY (`stuID`) REFERENCES `student` (`stuID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `stu_acct_tuition_termCode` FOREIGN KEY (`termCode`) REFERENCES `term` (`termCode`) ON DELETE SET NULL ON UPDATE CASCADE;
                    
                ALTER TABLE `term`
                    ADD CONSTRAINT `term_semCode` FOREIGN KEY (`semCode`) REFERENCES `semester` (`semCode`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `timesheet`
                    ADD CONSTRAINT `timesheet_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `staff` (`staffID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `timesheet_employeeID` FOREIGN KEY (`employeeID`) REFERENCES `staff` (`staffID`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `transfer_credit`
                    ADD CONSTRAINT `transfer_credit_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `transfer_credit_equivID` FOREIGN KEY (`equivID`) REFERENCES `transfer_equivalent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `transfer_credit_stacID` FOREIGN KEY (`stacID`) REFERENCES `stac` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
                    
                ALTER TABLE `transfer_equivalent`
                    ADD CONSTRAINT `transfer_equivalent_addedBy` FOREIGN KEY (`addedBy`) REFERENCES `person` (`personID`) ON UPDATE CASCADE,
                    ADD CONSTRAINT `transfer_equivalent_courseID` FOREIGN KEY (`courseID`) REFERENCES `course` (`courseID`) ON DELETE CASCADE ON UPDATE CASCADE,
                    ADD CONSTRAINT `transfer_equivalent_extrID` FOREIGN KEY (`extrID`) REFERENCES `external_course` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

            "
            );

        endif;

        $this->execute("SET FOREIGN_KEY_CHECKS=1;");
    }
}
