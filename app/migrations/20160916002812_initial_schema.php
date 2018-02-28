<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class InitialSchema extends AbstractMigration
{

    /**
     * Initial Schema
     *
     * Creates and populates a new database on first migration.
     * 
     * @since 6.2.10
     */
    public function change()
    {
        $this->execute("SET FOREIGN_KEY_CHECKS=0;");

        $host = $this->adapter->getOption('host');
        $name = $this->adapter->getOption('name');
        $user = $this->adapter->getOption('user');
        $pass = $this->adapter->getOption('pass');

        $NOW = date("Y-m-d H:i:s");

        // Automatically created phinx migration commands for tables from database et
        // Migration for table acad_program
        if (!$this->hasTable('acad_program')) :
            $table = $this->table('acad_program', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('acadProgCode', 'string', ['limit' => 80])
                ->addColumn('acadProgTitle', 'string', ['limit' => 191])
                ->addColumn('programDesc', 'string', ['limit' => 191])
                ->addColumn('currStatus', 'char', ['limit' => 4])
                ->addColumn('statusDate', 'date', [])
                ->addColumn('deptCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('schoolCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('acadYearCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('startDate', 'date', [])
                ->addColumn('endDate', 'date', ['null' => true])
                ->addColumn('degreeCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('ccdCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('majorCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('minorCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('specCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('acadLevelCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('cipCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('locationCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('approvedDate', 'date', [])
                ->addColumn('approvedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['acadProgCode'], ['unique' => true])
                ->addForeignKey('deptCode', 'department', 'deptCode', ['constraint' => 'acad_program_deptCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('locationCode', 'location', 'locationCode', ['constraint' => 'acad_program_locationCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('schoolCode', 'school', 'schoolCode', ['constraint' => 'acad_program_schoolCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('acadYearCode', 'acad_year', 'acadYearCode', ['constraint' => 'acad_program_acadYearCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('degreeCode', 'degree', 'degreeCode', ['constraint' => 'acad_program_degreeCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('ccdCode', 'ccd', 'ccdCode', ['constraint' => 'acad_program_ccdCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('majorCode', 'major', 'majorCode', ['constraint' => 'acad_program_majorCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('minorCode', 'minor', 'minorCode', ['constraint' => 'acad_program_minorCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('specCode', 'specialization', 'specCode', ['constraint' => 'acad_program_specCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('cipCode', 'cip', 'cipCode', ['constraint' => 'acad_program_cipCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('acadLevelCode', 'aclv', 'code', ['constraint' => 'acad_program_acadLevelCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('approvedBy', 'staff', 'staffID', ['constraint' => 'acad_program_approvedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
        endif;


        // Migration for table acad_year
        if (!$this->hasTable('acad_year')) :
            $table = $this->table('acad_year', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('acadYearCode', 'char', ['limit' => 22])
                ->addColumn('acadYearDesc', 'string', ['limit' => 60])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['acadYearCode'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `acad_year` VALUES(1, 'NULL', 'Null', '$NOW');");
        endif;

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

        // Migration for table activity_log
        if (!$this->hasTable('activity_log')) :
            $table = $this->table('activity_log', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('action', 'string', ['limit' => 60])
                ->addColumn('process', 'string', ['limit' => 191])
                ->addColumn('record', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('uname', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('created_at', 'datetime', [])
                ->addColumn('expires_at', 'datetime', [])
                ->create();
        endif;

        // Migration for table address
        if (!$this->hasTable('address')) :
            $table = $this->table('address', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('personID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('address1', 'string', ['limit' => 80])
                ->addColumn('address2', 'string', ['limit' => 80])
                ->addColumn('city', 'string', ['limit' => 60])
                ->addColumn('state', 'char', ['null' => true, 'limit' => 4])
                ->addColumn('zip', 'char', ['null' => true, 'limit' => 10])
                ->addColumn('country', 'char', ['null' => true, 'limit' => 4])
                ->addColumn('addressType', 'char', ['limit' => 4])
                ->addColumn('startDate', 'date', [])
                ->addColumn('endDate', 'date', ['null' => true])
                ->addColumn('addressStatus', 'char', ['limit' => 4])
                ->addColumn('phone1', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('phone2', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('ext1', 'char', ['null' => true, 'limit' => 8])
                ->addColumn('ext2', 'char', ['null' => true, 'limit' => 8])
                ->addColumn('phoneType1', 'char', ['null' => true, 'limit' => 8])
                ->addColumn('phoneType2', 'char', ['null' => true, 'limit' => 8])
                ->addColumn('email1', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('email2', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('addDate', 'date', [])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('personID', 'person', 'personID', ['constraint' => 'address_personID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('addedBy', 'person', 'personID', ['constraint' => 'address_addedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();

            $this->execute("INSERT INTO `address` VALUES(1, 1, '125 Montgomery Street', '#2', 'Cambridge', 'MA', '02140', 'US', 'P', '2013-08-01', 'NULL', 'C', '6718997836', '', '', '', 'CEL', '', 'etsis@campus.com', '', '$NOW', 1, '$NOW');");
        endif;


        // Migration for table application
        if (!$this->hasTable('application')) :
            $table = $this->table('application', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('personID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('acadProgCode', 'string', ['limit' => 80])
                ->addColumn('startTerm', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('admitStatus', 'char', ['null' => true, 'limit' => 4])
                ->addColumn('exam', 'char', ['null' => true, 'limit' => 8])
                ->addColumn('PSAT_Verbal', 'char', ['null' => true, 'limit' => 8])
                ->addColumn('PSAT_Math', 'char', ['null' => true, 'limit' => 8])
                ->addColumn('SAT_Verbal', 'char', ['null' => true, 'limit' => 8])
                ->addColumn('SAT_Math', 'char', ['null' => true, 'limit' => 8])
                ->addColumn('ACT_English', 'char', ['null' => true, 'limit' => 8])
                ->addColumn('ACT_Math', 'char', ['null' => true, 'limit' => 8])
                ->addColumn('applStatus', 'enum', ['values' => ['Pending', 'Under Review', 'Accepted', 'Not Accepted']])
                ->addColumn('applDate', 'date', [])
                ->addColumn('appl_comments', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('staff_comments', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('addDate', 'date', [])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['personID', 'acadProgCode'], ['unique' => true])
                ->addForeignKey('personID', 'person', 'personID', ['constraint' => 'application_personID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('acadProgCode', 'acad_program', 'acadProgCode', ['constraint' => 'application_acadProgCode', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->addForeignKey('startTerm', 'term', 'termCode', ['constraint' => 'application_startTerm', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('addedBy', 'person', 'personID', ['constraint' => 'application_addedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table assignment
        if (!$this->hasTable('assignment')) :
            $table = $this->table('assignment', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('courseSecID', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('facID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('shortName', 'char', ['null' => true, 'limit' => 12])
                ->addColumn('title', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('dueDate', 'date', ['null' => true])
                ->addColumn('addDate', 'date', [])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['id', 'courseSecID'], ['name' => 'assignID'])
                ->addForeignKey('courseSecID', 'course_sec', 'courseSecID', ['constraint' => 'assignment_courseSecID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('facID', 'staff', 'staffID', ['constraint' => 'assignment_facID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('addedBy', 'staff', 'staffID', ['constraint' => 'assignment_staffID', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table billing_table
        if (!$this->hasTable('billing_table')) :
            $table = $this->table('billing_table', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('name', 'string', ['limit' => 191])
                ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00'])
                ->addColumn('type', 'enum', ['default' => 'F', 'values' => ['F', 'T']])
                ->addColumn('termCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('rule', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('status', 'enum', ['default' => 'A', 'values' => ['A', 'I']])
                ->addColumn('addDate', 'date', [])
                ->addForeignKey('termCode', 'term', 'termCode', ['constraint' => 'billing_table_termCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->create();
        endif;


        // Migration for table building
        if (!$this->hasTable('building')) :
            $table = $this->table('building', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('buildingCode', 'char', ['limit' => 22])
                ->addColumn('buildingName', 'string', ['limit' => 191])
                ->addColumn('locationCode', 'char', ['null' => true, 'limit' => 22])
                ->addIndex(['buildingCode'], ['unique' => true])
                ->addForeignKey('locationCode', 'location', 'locationCode', ['constraint' => 'building_locationCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->create();

            $this->execute("INSERT INTO `building` VALUES(1, 'NULL', '', 'NULL');");
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

        // Migration for table ccd
        if (!$this->hasTable('ccd')) :
            $table = $this->table('ccd', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('ccdCode', 'char', ['limit' => 22])
                ->addColumn('ccdName', 'string', ['limit' => 191])
                ->addColumn('addDate', 'date', [])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['ccdCode'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `ccd` VALUES(1, 'NULL', 'Null', '$NOW', '$NOW');");
        endif;

        // Migration for table cip
        if (!$this->hasTable('cip')) :
            $table = $this->table('cip', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('cipCode', 'char', ['limit' => 22])
                ->addColumn('cipName', 'string', ['limit' => 191])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['cipCode'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `cip` VALUES(1, 'NULL', 'Null', '$NOW');");
        endif;

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

        // Migration for table country
        if (!$this->hasTable('country')) :
            $table = $this->table('country', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 5])
                ->addColumn('iso2', 'char', ['null' => true, 'limit' => 4])
                ->addColumn('short_name', 'string', ['default' => '', 'limit' => 80])
                ->addColumn('long_name', 'string', ['default' => '', 'limit' => 191])
                ->addColumn('iso3', 'char', ['null' => true, 'limit' => 6])
                ->addColumn('numcode', 'string', ['null' => true, 'limit' => 8])
                ->addColumn('un_member', 'string', ['null' => true, 'limit' => 12])
                ->addColumn('calling_code', 'string', ['null' => true, 'limit' => 10])
                ->addColumn('cctld', 'char', ['null' => true, 'limit' => 20])
                ->create();

            $this->execute("INSERT INTO `country` VALUES(1, 'AF', 'Afghanistan', 'Islamic Republic of Afghanistan', 'AFG', '004', 'yes', '93', '.af');");
            $this->execute("INSERT INTO `country` VALUES(2, 'AX', 'Aland Islands', '&Aring;land Islands', 'ALA', '248', 'no', '358', '.ax');");
            $this->execute("INSERT INTO `country` VALUES(3, 'AL', 'Albania', 'Republic of Albania', 'ALB', '008', 'yes', '355', '.al');");
            $this->execute("INSERT INTO `country` VALUES(4, 'DZ', 'Algeria', 'People''s Democratic Republic of Algeria', 'DZA', '012', 'yes', '213', '.dz');");
            $this->execute("INSERT INTO `country` VALUES(5, 'AS', 'American Samoa', 'American Samoa', 'ASM', '016', 'no', '1+684', '.as');");
            $this->execute("INSERT INTO `country` VALUES(6, 'AD', 'Andorra', 'Principality of Andorra', 'AND', '020', 'yes', '376', '.ad');");
            $this->execute("INSERT INTO `country` VALUES(7, 'AO', 'Angola', 'Republic of Angola', 'AGO', '024', 'yes', '244', '.ao');");
            $this->execute("INSERT INTO `country` VALUES(8, 'AI', 'Anguilla', 'Anguilla', 'AIA', '660', 'no', '1+264', '.ai');");
            $this->execute("INSERT INTO `country` VALUES(9, 'AQ', 'Antarctica', 'Antarctica', 'ATA', '010', 'no', '672', '.aq');");
            $this->execute("INSERT INTO `country` VALUES(10, 'AG', 'Antigua and Barbuda', 'Antigua and Barbuda', 'ATG', '028', 'yes', '1+268', '.ag');");
            $this->execute("INSERT INTO `country` VALUES(11, 'AR', 'Argentina', 'Argentine Republic', 'ARG', '032', 'yes', '54', '.ar');");
            $this->execute("INSERT INTO `country` VALUES(12, 'AM', 'Armenia', 'Republic of Armenia', 'ARM', '051', 'yes', '374', '.am');");
            $this->execute("INSERT INTO `country` VALUES(13, 'AW', 'Aruba', 'Aruba', 'ABW', '533', 'no', '297', '.aw');");
            $this->execute("INSERT INTO `country` VALUES(14, 'AU', 'Australia', 'Commonwealth of Australia', 'AUS', '036', 'yes', '61', '.au');");
            $this->execute("INSERT INTO `country` VALUES(15, 'AT', 'Austria', 'Republic of Austria', 'AUT', '040', 'yes', '43', '.at');");
            $this->execute("INSERT INTO `country` VALUES(16, 'AZ', 'Azerbaijan', 'Republic of Azerbaijan', 'AZE', '031', 'yes', '994', '.az');");
            $this->execute("INSERT INTO `country` VALUES(17, 'BS', 'Bahamas', 'Commonwealth of The Bahamas', 'BHS', '044', 'yes', '1+242', '.bs');");
            $this->execute("INSERT INTO `country` VALUES(18, 'BH', 'Bahrain', 'Kingdom of Bahrain', 'BHR', '048', 'yes', '973', '.bh');");
            $this->execute("INSERT INTO `country` VALUES(19, 'BD', 'Bangladesh', 'People''s Republic of Bangladesh', 'BGD', '050', 'yes', '880', '.bd');");
            $this->execute("INSERT INTO `country` VALUES(20, 'BB', 'Barbados', 'Barbados', 'BRB', '052', 'yes', '1+246', '.bb');");
            $this->execute("INSERT INTO `country` VALUES(21, 'BY', 'Belarus', 'Republic of Belarus', 'BLR', '112', 'yes', '375', '.by');");
            $this->execute("INSERT INTO `country` VALUES(22, 'BE', 'Belgium', 'Kingdom of Belgium', 'BEL', '056', 'yes', '32', '.be');");
            $this->execute("INSERT INTO `country` VALUES(23, 'BZ', 'Belize', 'Belize', 'BLZ', '084', 'yes', '501', '.bz');");
            $this->execute("INSERT INTO `country` VALUES(24, 'BJ', 'Benin', 'Republic of Benin', 'BEN', '204', 'yes', '229', '.bj');");
            $this->execute("INSERT INTO `country` VALUES(25, 'BM', 'Bermuda', 'Bermuda Islands', 'BMU', '060', 'no', '1+441', '.bm');");
            $this->execute("INSERT INTO `country` VALUES(26, 'BT', 'Bhutan', 'Kingdom of Bhutan', 'BTN', '064', 'yes', '975', '.bt');");
            $this->execute("INSERT INTO `country` VALUES(27, 'BO', 'Bolivia', 'Plurinational State of Bolivia', 'BOL', '068', 'yes', '591', '.bo');");
            $this->execute("INSERT INTO `country` VALUES(28, 'BQ', 'Bonaire, Sint Eustatius and Saba', 'Bonaire, Sint Eustatius and Saba', 'BES', '535', 'no', '599', '.bq');");
            $this->execute("INSERT INTO `country` VALUES(29, 'BA', 'Bosnia and Herzegovina', 'Bosnia and Herzegovina', 'BIH', '070', 'yes', '387', '.ba');");
            $this->execute("INSERT INTO `country` VALUES(30, 'BW', 'Botswana', 'Republic of Botswana', 'BWA', '072', 'yes', '267', '.bw');");
            $this->execute("INSERT INTO `country` VALUES(31, 'BV', 'Bouvet Island', 'Bouvet Island', 'BVT', '074', 'no', 'NONE', '.bv');");
            $this->execute("INSERT INTO `country` VALUES(32, 'BR', 'Brazil', 'Federative Republic of Brazil', 'BRA', '076', 'yes', '55', '.br');");
            $this->execute("INSERT INTO `country` VALUES(33, 'IO', 'British Indian Ocean Territory', 'British Indian Ocean Territory', 'IOT', '086', 'no', '246', '.io');");
            $this->execute("INSERT INTO `country` VALUES(34, 'BN', 'Brunei', 'Brunei Darussalam', 'BRN', '096', 'yes', '673', '.bn');");
            $this->execute("INSERT INTO `country` VALUES(35, 'BG', 'Bulgaria', 'Republic of Bulgaria', 'BGR', '100', 'yes', '359', '.bg');");
            $this->execute("INSERT INTO `country` VALUES(36, 'BF', 'Burkina Faso', 'Burkina Faso', 'BFA', '854', 'yes', '226', '.bf');");
            $this->execute("INSERT INTO `country` VALUES(37, 'BI', 'Burundi', 'Republic of Burundi', 'BDI', '108', 'yes', '257', '.bi');");
            $this->execute("INSERT INTO `country` VALUES(38, 'KH', 'Cambodia', 'Kingdom of Cambodia', 'KHM', '116', 'yes', '855', '.kh');");
            $this->execute("INSERT INTO `country` VALUES(39, 'CM', 'Cameroon', 'Republic of Cameroon', 'CMR', '120', 'yes', '237', '.cm');");
            $this->execute("INSERT INTO `country` VALUES(40, 'CA', 'Canada', 'Canada', 'CAN', '124', 'yes', '1', '.ca');");
            $this->execute("INSERT INTO `country` VALUES(41, 'CV', 'Cape Verde', 'Republic of Cape Verde', 'CPV', '132', 'yes', '238', '.cv');");
            $this->execute("INSERT INTO `country` VALUES(42, 'KY', 'Cayman Islands', 'The Cayman Islands', 'CYM', '136', 'no', '1+345', '.ky');");
            $this->execute("INSERT INTO `country` VALUES(43, 'CF', 'Central African Republic', 'Central African Republic', 'CAF', '140', 'yes', '236', '.cf');");
            $this->execute("INSERT INTO `country` VALUES(44, 'TD', 'Chad', 'Republic of Chad', 'TCD', '148', 'yes', '235', '.td');");
            $this->execute("INSERT INTO `country` VALUES(45, 'CL', 'Chile', 'Republic of Chile', 'CHL', '152', 'yes', '56', '.cl');");
            $this->execute("INSERT INTO `country` VALUES(46, 'CN', 'China', 'People''s Republic of China', 'CHN', '156', 'yes', '86', '.cn');");
            $this->execute("INSERT INTO `country` VALUES(47, 'CX', 'Christmas Island', 'Christmas Island', 'CXR', '162', 'no', '61', '.cx');");
            $this->execute("INSERT INTO `country` VALUES(48, 'CC', 'Cocos (Keeling) Islands', 'Cocos (Keeling) Islands', 'CCK', '166', 'no', '61', '.cc');");
            $this->execute("INSERT INTO `country` VALUES(49, 'CO', 'Colombia', 'Republic of Colombia', 'COL', '170', 'yes', '57', '.co');");
            $this->execute("INSERT INTO `country` VALUES(50, 'KM', 'Comoros', 'Union of the Comoros', 'COM', '174', 'yes', '269', '.km');");
            $this->execute("INSERT INTO `country` VALUES(51, 'CG', 'Congo', 'Republic of the Congo', 'COG', '178', 'yes', '242', '.cg');");
            $this->execute("INSERT INTO `country` VALUES(52, 'CK', 'Cook Islands', 'Cook Islands', 'COK', '184', 'some', '682', '.ck');");
            $this->execute("INSERT INTO `country` VALUES(53, 'CR', 'Costa Rica', 'Republic of Costa Rica', 'CRI', '188', 'yes', '506', '.cr');");
            $this->execute("INSERT INTO `country` VALUES(54, 'CI', 'Cote d''ivoire (Ivory Coast)', 'Republic of C&ocirc;te D''Ivoire (Ivory Coast)', 'CIV', '384', 'yes', '225', '.ci');");
            $this->execute("INSERT INTO `country` VALUES(55, 'HR', 'Croatia', 'Republic of Croatia', 'HRV', '191', 'yes', '385', '.hr');");
            $this->execute("INSERT INTO `country` VALUES(56, 'CU', 'Cuba', 'Republic of Cuba', 'CUB', '192', 'yes', '53', '.cu');");
            $this->execute("INSERT INTO `country` VALUES(57, 'CW', 'Curacao', 'Cura&ccedil;ao', 'CUW', '531', 'no', '599', '.cw');");
            $this->execute("INSERT INTO `country` VALUES(58, 'CY', 'Cyprus', 'Republic of Cyprus', 'CYP', '196', 'yes', '357', '.cy');");
            $this->execute("INSERT INTO `country` VALUES(59, 'CZ', 'Czech Republic', 'Czech Republic', 'CZE', '203', 'yes', '420', '.cz');");
            $this->execute("INSERT INTO `country` VALUES(60, 'CD', 'Democratic Republic of the Congo', 'Democratic Republic of the Congo', 'COD', '180', 'yes', '243', '.cd');");
            $this->execute("INSERT INTO `country` VALUES(61, 'DK', 'Denmark', 'Kingdom of Denmark', 'DNK', '208', 'yes', '45', '.dk');");
            $this->execute("INSERT INTO `country` VALUES(62, 'DJ', 'Djibouti', 'Republic of Djibouti', 'DJI', '262', 'yes', '253', '.dj');");
            $this->execute("INSERT INTO `country` VALUES(63, 'DM', 'Dominica', 'Commonwealth of Dominica', 'DMA', '212', 'yes', '1+767', '.dm');");
            $this->execute("INSERT INTO `country` VALUES(64, 'DO', 'Dominican Republic', 'Dominican Republic', 'DOM', '214', 'yes', '1+809, 8', '.do');");
            $this->execute("INSERT INTO `country` VALUES(65, 'EC', 'Ecuador', 'Republic of Ecuador', 'ECU', '218', 'yes', '593', '.ec');");
            $this->execute("INSERT INTO `country` VALUES(66, 'EG', 'Egypt', 'Arab Republic of Egypt', 'EGY', '818', 'yes', '20', '.eg');");
            $this->execute("INSERT INTO `country` VALUES(67, 'SV', 'El Salvador', 'Republic of El Salvador', 'SLV', '222', 'yes', '503', '.sv');");
            $this->execute("INSERT INTO `country` VALUES(68, 'GQ', 'Equatorial Guinea', 'Republic of Equatorial Guinea', 'GNQ', '226', 'yes', '240', '.gq');");
            $this->execute("INSERT INTO `country` VALUES(69, 'ER', 'Eritrea', 'State of Eritrea', 'ERI', '232', 'yes', '291', '.er');");
            $this->execute("INSERT INTO `country` VALUES(70, 'EE', 'Estonia', 'Republic of Estonia', 'EST', '233', 'yes', '372', '.ee');");
            $this->execute("INSERT INTO `country` VALUES(71, 'ET', 'Ethiopia', 'Federal Democratic Republic of Ethiopia', 'ETH', '231', 'yes', '251', '.et');");
            $this->execute("INSERT INTO `country` VALUES(72, 'FK', 'Falkland Islands (Malvinas)', 'The Falkland Islands (Malvinas)', 'FLK', '238', 'no', '500', '.fk');");
            $this->execute("INSERT INTO `country` VALUES(73, 'FO', 'Faroe Islands', 'The Faroe Islands', 'FRO', '234', 'no', '298', '.fo');");
            $this->execute("INSERT INTO `country` VALUES(74, 'FJ', 'Fiji', 'Republic of Fiji', 'FJI', '242', 'yes', '679', '.fj');");
            $this->execute("INSERT INTO `country` VALUES(75, 'FI', 'Finland', 'Republic of Finland', 'FIN', '246', 'yes', '358', '.fi');");
            $this->execute("INSERT INTO `country` VALUES(76, 'FR', 'France', 'French Republic', 'FRA', '250', 'yes', '33', '.fr');");
            $this->execute("INSERT INTO `country` VALUES(77, 'GF', 'French Guiana', 'French Guiana', 'GUF', '254', 'no', '594', '.gf');");
            $this->execute("INSERT INTO `country` VALUES(78, 'PF', 'French Polynesia', 'French Polynesia', 'PYF', '258', 'no', '689', '.pf');");
            $this->execute("INSERT INTO `country` VALUES(79, 'TF', 'French Southern Territories', 'French Southern Territories', 'ATF', '260', 'no', NULL, '.tf');");
            $this->execute("INSERT INTO `country` VALUES(80, 'GA', 'Gabon', 'Gabonese Republic', 'GAB', '266', 'yes', '241', '.ga');");
            $this->execute("INSERT INTO `country` VALUES(81, 'GM', 'Gambia', 'Republic of The Gambia', 'GMB', '270', 'yes', '220', '.gm');");
            $this->execute("INSERT INTO `country` VALUES(82, 'GE', 'Georgia', 'Georgia', 'GEO', '268', 'yes', '995', '.ge');");
            $this->execute("INSERT INTO `country` VALUES(83, 'DE', 'Germany', 'Federal Republic of Germany', 'DEU', '276', 'yes', '49', '.de');");
            $this->execute("INSERT INTO `country` VALUES(84, 'GH', 'Ghana', 'Republic of Ghana', 'GHA', '288', 'yes', '233', '.gh');");
            $this->execute("INSERT INTO `country` VALUES(85, 'GI', 'Gibraltar', 'Gibraltar', 'GIB', '292', 'no', '350', '.gi');");
            $this->execute("INSERT INTO `country` VALUES(86, 'GR', 'Greece', 'Hellenic Republic', 'GRC', '300', 'yes', '30', '.gr');");
            $this->execute("INSERT INTO `country` VALUES(87, 'GL', 'Greenland', 'Greenland', 'GRL', '304', 'no', '299', '.gl');");
            $this->execute("INSERT INTO `country` VALUES(88, 'GD', 'Grenada', 'Grenada', 'GRD', '308', 'yes', '1+473', '.gd');");
            $this->execute("INSERT INTO `country` VALUES(89, 'GP', 'Guadaloupe', 'Guadeloupe', 'GLP', '312', 'no', '590', '.gp');");
            $this->execute("INSERT INTO `country` VALUES(90, 'GU', 'Guam', 'Guam', 'GUM', '316', 'no', '1+671', '.gu');");
            $this->execute("INSERT INTO `country` VALUES(91, 'GT', 'Guatemala', 'Republic of Guatemala', 'GTM', '320', 'yes', '502', '.gt');");
            $this->execute("INSERT INTO `country` VALUES(92, 'GG', 'Guernsey', 'Guernsey', 'GGY', '831', 'no', '44', '.gg');");
            $this->execute("INSERT INTO `country` VALUES(93, 'GN', 'Guinea', 'Republic of Guinea', 'GIN', '324', 'yes', '224', '.gn');");
            $this->execute("INSERT INTO `country` VALUES(94, 'GW', 'Guinea-Bissau', 'Republic of Guinea-Bissau', 'GNB', '624', 'yes', '245', '.gw');");
            $this->execute("INSERT INTO `country` VALUES(95, 'GY', 'Guyana', 'Co-operative Republic of Guyana', 'GUY', '328', 'yes', '592', '.gy');");
            $this->execute("INSERT INTO `country` VALUES(96, 'HT', 'Haiti', 'Republic of Haiti', 'HTI', '332', 'yes', '509', '.ht');");
            $this->execute("INSERT INTO `country` VALUES(97, 'HM', 'Heard Island and McDonald Islands', 'Heard Island and McDonald Islands', 'HMD', '334', 'no', 'NONE', '.hm');");
            $this->execute("INSERT INTO `country` VALUES(98, 'HN', 'Honduras', 'Republic of Honduras', 'HND', '340', 'yes', '504', '.hn');");
            $this->execute("INSERT INTO `country` VALUES(99, 'HK', 'Hong Kong', 'Hong Kong', 'HKG', '344', 'no', '852', '.hk');");
            $this->execute("INSERT INTO `country` VALUES(100, 'HU', 'Hungary', 'Hungary', 'HUN', '348', 'yes', '36', '.hu');");
            $this->execute("INSERT INTO `country` VALUES(101, 'IS', 'Iceland', 'Republic of Iceland', 'ISL', '352', 'yes', '354', '.is');");
            $this->execute("INSERT INTO `country` VALUES(102, 'IN', 'India', 'Republic of India', 'IND', '356', 'yes', '91', '.in');");
            $this->execute("INSERT INTO `country` VALUES(103, 'ID', 'Indonesia', 'Republic of Indonesia', 'IDN', '360', 'yes', '62', '.id');");
            $this->execute("INSERT INTO `country` VALUES(104, 'IR', 'Iran', 'Islamic Republic of Iran', 'IRN', '364', 'yes', '98', '.ir');");
            $this->execute("INSERT INTO `country` VALUES(105, 'IQ', 'Iraq', 'Republic of Iraq', 'IRQ', '368', 'yes', '964', '.iq');");
            $this->execute("INSERT INTO `country` VALUES(106, 'IE', 'Ireland', 'Ireland', 'IRL', '372', 'yes', '353', '.ie');");
            $this->execute("INSERT INTO `country` VALUES(107, 'IM', 'Isle of Man', 'Isle of Man', 'IMN', '833', 'no', '44', '.im');");
            $this->execute("INSERT INTO `country` VALUES(108, 'IL', 'Israel', 'State of Israel', 'ISR', '376', 'yes', '972', '.il');");
            $this->execute("INSERT INTO `country` VALUES(109, 'IT', 'Italy', 'Italian Republic', 'ITA', '380', 'yes', '39', '.jm');");
            $this->execute("INSERT INTO `country` VALUES(110, 'JM', 'Jamaica', 'Jamaica', 'JAM', '388', 'yes', '1+876', '.jm');");
            $this->execute("INSERT INTO `country` VALUES(111, 'JP', 'Japan', 'Japan', 'JPN', '392', 'yes', '81', '.jp');");
            $this->execute("INSERT INTO `country` VALUES(112, 'JE', 'Jersey', 'The Bailiwick of Jersey', 'JEY', '832', 'no', '44', '.je');");
            $this->execute("INSERT INTO `country` VALUES(113, 'JO', 'Jordan', 'Hashemite Kingdom of Jordan', 'JOR', '400', 'yes', '962', '.jo');");
            $this->execute("INSERT INTO `country` VALUES(114, 'KZ', 'Kazakhstan', 'Republic of Kazakhstan', 'KAZ', '398', 'yes', '7', '.kz');");
            $this->execute("INSERT INTO `country` VALUES(115, 'KE', 'Kenya', 'Republic of Kenya', 'KEN', '404', 'yes', '254', '.ke');");
            $this->execute("INSERT INTO `country` VALUES(116, 'KI', 'Kiribati', 'Republic of Kiribati', 'KIR', '296', 'yes', '686', '.ki');");
            $this->execute("INSERT INTO `country` VALUES(117, 'XK', 'Kosovo', 'Republic of Kosovo', '---', '---', 'some', '381', '');");
            $this->execute("INSERT INTO `country` VALUES(118, 'KW', 'Kuwait', 'State of Kuwait', 'KWT', '414', 'yes', '965', '.kw');");
            $this->execute("INSERT INTO `country` VALUES(119, 'KG', 'Kyrgyzstan', 'Kyrgyz Republic', 'KGZ', '417', 'yes', '996', '.kg');");
            $this->execute("INSERT INTO `country` VALUES(120, 'LA', 'Laos', 'Lao People''s Democratic Republic', 'LAO', '418', 'yes', '856', '.la');");
            $this->execute("INSERT INTO `country` VALUES(121, 'LV', 'Latvia', 'Republic of Latvia', 'LVA', '428', 'yes', '371', '.lv');");
            $this->execute("INSERT INTO `country` VALUES(122, 'LB', 'Lebanon', 'Republic of Lebanon', 'LBN', '422', 'yes', '961', '.lb');");
            $this->execute("INSERT INTO `country` VALUES(123, 'LS', 'Lesotho', 'Kingdom of Lesotho', 'LSO', '426', 'yes', '266', '.ls');");
            $this->execute("INSERT INTO `country` VALUES(124, 'LR', 'Liberia', 'Republic of Liberia', 'LBR', '430', 'yes', '231', '.lr');");
            $this->execute("INSERT INTO `country` VALUES(125, 'LY', 'Libya', 'Libya', 'LBY', '434', 'yes', '218', '.ly');");
            $this->execute("INSERT INTO `country` VALUES(126, 'LI', 'Liechtenstein', 'Principality of Liechtenstein', 'LIE', '438', 'yes', '423', '.li');");
            $this->execute("INSERT INTO `country` VALUES(127, 'LT', 'Lithuania', 'Republic of Lithuania', 'LTU', '440', 'yes', '370', '.lt');");
            $this->execute("INSERT INTO `country` VALUES(128, 'LU', 'Luxembourg', 'Grand Duchy of Luxembourg', 'LUX', '442', 'yes', '352', '.lu');");
            $this->execute("INSERT INTO `country` VALUES(129, 'MO', 'Macao', 'The Macao Special Administrative Region', 'MAC', '446', 'no', '853', '.mo');");
            $this->execute("INSERT INTO `country` VALUES(130, 'MK', 'Macedonia', 'The Former Yugoslav Republic of Macedonia', 'MKD', '807', 'yes', '389', '.mk');");
            $this->execute("INSERT INTO `country` VALUES(131, 'MG', 'Madagascar', 'Republic of Madagascar', 'MDG', '450', 'yes', '261', '.mg');");
            $this->execute("INSERT INTO `country` VALUES(132, 'MW', 'Malawi', 'Republic of Malawi', 'MWI', '454', 'yes', '265', '.mw');");
            $this->execute("INSERT INTO `country` VALUES(133, 'MY', 'Malaysia', 'Malaysia', 'MYS', '458', 'yes', '60', '.my');");
            $this->execute("INSERT INTO `country` VALUES(134, 'MV', 'Maldives', 'Republic of Maldives', 'MDV', '462', 'yes', '960', '.mv');");
            $this->execute("INSERT INTO `country` VALUES(135, 'ML', 'Mali', 'Republic of Mali', 'MLI', '466', 'yes', '223', '.ml');");
            $this->execute("INSERT INTO `country` VALUES(136, 'MT', 'Malta', 'Republic of Malta', 'MLT', '470', 'yes', '356', '.mt');");
            $this->execute("INSERT INTO `country` VALUES(137, 'MH', 'Marshall Islands', 'Republic of the Marshall Islands', 'MHL', '584', 'yes', '692', '.mh');");
            $this->execute("INSERT INTO `country` VALUES(138, 'MQ', 'Martinique', 'Martinique', 'MTQ', '474', 'no', '596', '.mq');");
            $this->execute("INSERT INTO `country` VALUES(139, 'MR', 'Mauritania', 'Islamic Republic of Mauritania', 'MRT', '478', 'yes', '222', '.mr');");
            $this->execute("INSERT INTO `country` VALUES(140, 'MU', 'Mauritius', 'Republic of Mauritius', 'MUS', '480', 'yes', '230', '.mu');");
            $this->execute("INSERT INTO `country` VALUES(141, 'YT', 'Mayotte', 'Mayotte', 'MYT', '175', 'no', '262', '.yt');");
            $this->execute("INSERT INTO `country` VALUES(142, 'MX', 'Mexico', 'United Mexican States', 'MEX', '484', 'yes', '52', '.mx');");
            $this->execute("INSERT INTO `country` VALUES(143, 'FM', 'Micronesia', 'Federated States of Micronesia', 'FSM', '583', 'yes', '691', '.fm');");
            $this->execute("INSERT INTO `country` VALUES(144, 'MD', 'Moldava', 'Republic of Moldova', 'MDA', '498', 'yes', '373', '.md');");
            $this->execute("INSERT INTO `country` VALUES(145, 'MC', 'Monaco', 'Principality of Monaco', 'MCO', '492', 'yes', '377', '.mc');");
            $this->execute("INSERT INTO `country` VALUES(146, 'MN', 'Mongolia', 'Mongolia', 'MNG', '496', 'yes', '976', '.mn');");
            $this->execute("INSERT INTO `country` VALUES(147, 'ME', 'Montenegro', 'Montenegro', 'MNE', '499', 'yes', '382', '.me');");
            $this->execute("INSERT INTO `country` VALUES(148, 'MS', 'Montserrat', 'Montserrat', 'MSR', '500', 'no', '1+664', '.ms');");
            $this->execute("INSERT INTO `country` VALUES(149, 'MA', 'Morocco', 'Kingdom of Morocco', 'MAR', '504', 'yes', '212', '.ma');");
            $this->execute("INSERT INTO `country` VALUES(150, 'MZ', 'Mozambique', 'Republic of Mozambique', 'MOZ', '508', 'yes', '258', '.mz');");
            $this->execute("INSERT INTO `country` VALUES(151, 'MM', 'Myanmar (Burma)', 'Republic of the Union of Myanmar', 'MMR', '104', 'yes', '95', '.mm');");
            $this->execute("INSERT INTO `country` VALUES(152, 'NA', 'Namibia', 'Republic of Namibia', 'NAM', '516', 'yes', '264', '.na');");
            $this->execute("INSERT INTO `country` VALUES(153, 'NR', 'Nauru', 'Republic of Nauru', 'NRU', '520', 'yes', '674', '.nr');");
            $this->execute("INSERT INTO `country` VALUES(154, 'NP', 'Nepal', 'Federal Democratic Republic of Nepal', 'NPL', '524', 'yes', '977', '.np');");
            $this->execute("INSERT INTO `country` VALUES(155, 'NL', 'Netherlands', 'Kingdom of the Netherlands', 'NLD', '528', 'yes', '31', '.nl');");
            $this->execute("INSERT INTO `country` VALUES(156, 'NC', 'New Caledonia', 'New Caledonia', 'NCL', '540', 'no', '687', '.nc');");
            $this->execute("INSERT INTO `country` VALUES(157, 'NZ', 'New Zealand', 'New Zealand', 'NZL', '554', 'yes', '64', '.nz');");
            $this->execute("INSERT INTO `country` VALUES(158, 'NI', 'Nicaragua', 'Republic of Nicaragua', 'NIC', '558', 'yes', '505', '.ni');");
            $this->execute("INSERT INTO `country` VALUES(159, 'NE', 'Niger', 'Republic of Niger', 'NER', '562', 'yes', '227', '.ne');");
            $this->execute("INSERT INTO `country` VALUES(160, 'NG', 'Nigeria', 'Federal Republic of Nigeria', 'NGA', '566', 'yes', '234', '.ng');");
            $this->execute("INSERT INTO `country` VALUES(161, 'NU', 'Niue', 'Niue', 'NIU', '570', 'some', '683', '.nu');");
            $this->execute("INSERT INTO `country` VALUES(162, 'NF', 'Norfolk Island', 'Norfolk Island', 'NFK', '574', 'no', '672', '.nf');");
            $this->execute("INSERT INTO `country` VALUES(163, 'KP', 'North Korea', 'Democratic People''s Republic of Korea', 'PRK', '408', 'yes', '850', '.kp');");
            $this->execute("INSERT INTO `country` VALUES(164, 'MP', 'Northern Mariana Islands', 'Northern Mariana Islands', 'MNP', '580', 'no', '1+670', '.mp');");
            $this->execute("INSERT INTO `country` VALUES(165, 'NO', 'Norway', 'Kingdom of Norway', 'NOR', '578', 'yes', '47', '.no');");
            $this->execute("INSERT INTO `country` VALUES(166, 'OM', 'Oman', 'Sultanate of Oman', 'OMN', '512', 'yes', '968', '.om');");
            $this->execute("INSERT INTO `country` VALUES(167, 'PK', 'Pakistan', 'Islamic Republic of Pakistan', 'PAK', '586', 'yes', '92', '.pk');");
            $this->execute("INSERT INTO `country` VALUES(168, 'PW', 'Palau', 'Republic of Palau', 'PLW', '585', 'yes', '680', '.pw');");
            $this->execute("INSERT INTO `country` VALUES(169, 'PS', 'Palestine', 'State of Palestine (or Occupied Palestinian Territory)', 'PSE', '275', 'some', '970', '.ps');");
            $this->execute("INSERT INTO `country` VALUES(170, 'PA', 'Panama', 'Republic of Panama', 'PAN', '591', 'yes', '507', '.pa');");
            $this->execute("INSERT INTO `country` VALUES(171, 'PG', 'Papua New Guinea', 'Independent State of Papua New Guinea', 'PNG', '598', 'yes', '675', '.pg');");
            $this->execute("INSERT INTO `country` VALUES(172, 'PY', 'Paraguay', 'Republic of Paraguay', 'PRY', '600', 'yes', '595', '.py');");
            $this->execute("INSERT INTO `country` VALUES(173, 'PE', 'Peru', 'Republic of Peru', 'PER', '604', 'yes', '51', '.pe');");
            $this->execute("INSERT INTO `country` VALUES(174, 'PH', 'Phillipines', 'Republic of the Philippines', 'PHL', '608', 'yes', '63', '.ph');");
            $this->execute("INSERT INTO `country` VALUES(175, 'PN', 'Pitcairn', 'Pitcairn', 'PCN', '612', 'no', 'NONE', '.pn');");
            $this->execute("INSERT INTO `country` VALUES(176, 'PL', 'Poland', 'Republic of Poland', 'POL', '616', 'yes', '48', '.pl');");
            $this->execute("INSERT INTO `country` VALUES(177, 'PT', 'Portugal', 'Portuguese Republic', 'PRT', '620', 'yes', '351', '.pt');");
            $this->execute("INSERT INTO `country` VALUES(178, 'PR', 'Puerto Rico', 'Commonwealth of Puerto Rico', 'PRI', '630', 'no', '1+939', '.pr');");
            $this->execute("INSERT INTO `country` VALUES(179, 'QA', 'Qatar', 'State of Qatar', 'QAT', '634', 'yes', '974', '.qa');");
            $this->execute("INSERT INTO `country` VALUES(180, 'RE', 'Reunion', 'R&eacute;union', 'REU', '638', 'no', '262', '.re');");
            $this->execute("INSERT INTO `country` VALUES(181, 'RO', 'Romania', 'Romania', 'ROU', '642', 'yes', '40', '.ro');");
            $this->execute("INSERT INTO `country` VALUES(182, 'RU', 'Russia', 'Russian Federation', 'RUS', '643', 'yes', '7', '.ru');");
            $this->execute("INSERT INTO `country` VALUES(183, 'RW', 'Rwanda', 'Republic of Rwanda', 'RWA', '646', 'yes', '250', '.rw');");
            $this->execute("INSERT INTO `country` VALUES(184, 'BL', 'Saint Barthelemy', 'Saint Barth&eacute;lemy', 'BLM', '652', 'no', '590', '.bl');");
            $this->execute("INSERT INTO `country` VALUES(185, 'SH', 'Saint Helena', 'Saint Helena, Ascension and Tristan da Cunha', 'SHN', '654', 'no', '290', '.sh');");
            $this->execute("INSERT INTO `country` VALUES(186, 'KN', 'Saint Kitts and Nevis', 'Federation of Saint Christopher and Nevis', 'KNA', '659', 'yes', '1+869', '.kn');");
            $this->execute("INSERT INTO `country` VALUES(187, 'LC', 'Saint Lucia', 'Saint Lucia', 'LCA', '662', 'yes', '1+758', '.lc');");
            $this->execute("INSERT INTO `country` VALUES(188, 'MF', 'Saint Martin', 'Saint Martin', 'MAF', '663', 'no', '590', '.mf');");
            $this->execute("INSERT INTO `country` VALUES(189, 'PM', 'Saint Pierre and Miquelon', 'Saint Pierre and Miquelon', 'SPM', '666', 'no', '508', '.pm');");
            $this->execute("INSERT INTO `country` VALUES(190, 'VC', 'Saint Vincent and the Grenadines', 'Saint Vincent and the Grenadines', 'VCT', '670', 'yes', '1+784', '.vc');");
            $this->execute("INSERT INTO `country` VALUES(191, 'WS', 'Samoa', 'Independent State of Samoa', 'WSM', '882', 'yes', '685', '.ws');");
            $this->execute("INSERT INTO `country` VALUES(192, 'SM', 'San Marino', 'Republic of San Marino', 'SMR', '674', 'yes', '378', '.sm');");
            $this->execute("INSERT INTO `country` VALUES(193, 'ST', 'Sao Tome and Principe', 'Democratic Republic of S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'STP', '678', 'yes', '239', '.st');");
            $this->execute("INSERT INTO `country` VALUES(194, 'SA', 'Saudi Arabia', 'Kingdom of Saudi Arabia', 'SAU', '682', 'yes', '966', '.sa');");
            $this->execute("INSERT INTO `country` VALUES(195, 'SN', 'Senegal', 'Republic of Senegal', 'SEN', '686', 'yes', '221', '.sn');");
            $this->execute("INSERT INTO `country` VALUES(196, 'RS', 'Serbia', 'Republic of Serbia', 'SRB', '688', 'yes', '381', '.rs');");
            $this->execute("INSERT INTO `country` VALUES(197, 'SC', 'Seychelles', 'Republic of Seychelles', 'SYC', '690', 'yes', '248', '.sc');");
            $this->execute("INSERT INTO `country` VALUES(198, 'SL', 'Sierra Leone', 'Republic of Sierra Leone', 'SLE', '694', 'yes', '232', '.sl');");
            $this->execute("INSERT INTO `country` VALUES(199, 'SG', 'Singapore', 'Republic of Singapore', 'SGP', '702', 'yes', '65', '.sg');");
            $this->execute("INSERT INTO `country` VALUES(200, 'SX', 'Sint Maarten', 'Sint Maarten', 'SXM', '534', 'no', '1+721', '.sx');");
            $this->execute("INSERT INTO `country` VALUES(201, 'SK', 'Slovakia', 'Slovak Republic', 'SVK', '703', 'yes', '421', '.sk');");
            $this->execute("INSERT INTO `country` VALUES(202, 'SI', 'Slovenia', 'Republic of Slovenia', 'SVN', '705', 'yes', '386', '.si');");
            $this->execute("INSERT INTO `country` VALUES(203, 'SB', 'Solomon Islands', 'Solomon Islands', 'SLB', '090', 'yes', '677', '.sb');");
            $this->execute("INSERT INTO `country` VALUES(204, 'SO', 'Somalia', 'Somali Republic', 'SOM', '706', 'yes', '252', '.so');");
            $this->execute("INSERT INTO `country` VALUES(205, 'ZA', 'South Africa', 'Republic of South Africa', 'ZAF', '710', 'yes', '27', '.za');");
            $this->execute("INSERT INTO `country` VALUES(206, 'GS', 'South Georgia and the South Sandwich Islands', 'South Georgia and the South Sandwich Islands', 'SGS', '239', 'no', '500', '.gs');");
            $this->execute("INSERT INTO `country` VALUES(207, 'KR', 'South Korea', 'Republic of Korea', 'KOR', '410', 'yes', '82', '.kr');");
            $this->execute("INSERT INTO `country` VALUES(208, 'SS', 'South Sudan', 'Republic of South Sudan', 'SSD', '728', 'yes', '211', '.ss');");
            $this->execute("INSERT INTO `country` VALUES(209, 'ES', 'Spain', 'Kingdom of Spain', 'ESP', '724', 'yes', '34', '.es');");
            $this->execute("INSERT INTO `country` VALUES(210, 'LK', 'Sri Lanka', 'Democratic Socialist Republic of Sri Lanka', 'LKA', '144', 'yes', '94', '.lk');");
            $this->execute("INSERT INTO `country` VALUES(211, 'SD', 'Sudan', 'Republic of the Sudan', 'SDN', '729', 'yes', '249', '.sd');");
            $this->execute("INSERT INTO `country` VALUES(212, 'SR', 'Suriname', 'Republic of Suriname', 'SUR', '740', 'yes', '597', '.sr');");
            $this->execute("INSERT INTO `country` VALUES(213, 'SJ', 'Svalbard and Jan Mayen', 'Svalbard and Jan Mayen', 'SJM', '744', 'no', '47', '.sj');");
            $this->execute("INSERT INTO `country` VALUES(214, 'SZ', 'Swaziland', 'Kingdom of Swaziland', 'SWZ', '748', 'yes', '268', '.sz');");
            $this->execute("INSERT INTO `country` VALUES(215, 'SE', 'Sweden', 'Kingdom of Sweden', 'SWE', '752', 'yes', '46', '.se');");
            $this->execute("INSERT INTO `country` VALUES(216, 'CH', 'Switzerland', 'Swiss Confederation', 'CHE', '756', 'yes', '41', '.ch');");
            $this->execute("INSERT INTO `country` VALUES(217, 'SY', 'Syria', 'Syrian Arab Republic', 'SYR', '760', 'yes', '963', '.sy');");
            $this->execute("INSERT INTO `country` VALUES(218, 'TW', 'Taiwan', 'Republic of China (Taiwan)', 'TWN', '158', 'former', '886', '.tw');");
            $this->execute("INSERT INTO `country` VALUES(219, 'TJ', 'Tajikistan', 'Republic of Tajikistan', 'TJK', '762', 'yes', '992', '.tj');");
            $this->execute("INSERT INTO `country` VALUES(220, 'TZ', 'Tanzania', 'United Republic of Tanzania', 'TZA', '834', 'yes', '255', '.tz');");
            $this->execute("INSERT INTO `country` VALUES(221, 'TH', 'Thailand', 'Kingdom of Thailand', 'THA', '764', 'yes', '66', '.th');");
            $this->execute("INSERT INTO `country` VALUES(222, 'TL', 'Timor-Leste (East Timor)', 'Democratic Republic of Timor-Leste', 'TLS', '626', 'yes', '670', '.tl');");
            $this->execute("INSERT INTO `country` VALUES(223, 'TG', 'Togo', 'Togolese Republic', 'TGO', '768', 'yes', '228', '.tg');");
            $this->execute("INSERT INTO `country` VALUES(224, 'TK', 'Tokelau', 'Tokelau', 'TKL', '772', 'no', '690', '.tk');");
            $this->execute("INSERT INTO `country` VALUES(225, 'TO', 'Tonga', 'Kingdom of Tonga', 'TON', '776', 'yes', '676', '.to');");
            $this->execute("INSERT INTO `country` VALUES(226, 'TT', 'Trinidad and Tobago', 'Republic of Trinidad and Tobago', 'TTO', '780', 'yes', '1+868', '.tt');");
            $this->execute("INSERT INTO `country` VALUES(227, 'TN', 'Tunisia', 'Republic of Tunisia', 'TUN', '788', 'yes', '216', '.tn');");
            $this->execute("INSERT INTO `country` VALUES(228, 'TR', 'Turkey', 'Republic of Turkey', 'TUR', '792', 'yes', '90', '.tr');");
            $this->execute("INSERT INTO `country` VALUES(229, 'TM', 'Turkmenistan', 'Turkmenistan', 'TKM', '795', 'yes', '993', '.tm');");
            $this->execute("INSERT INTO `country` VALUES(230, 'TC', 'Turks and Caicos Islands', 'Turks and Caicos Islands', 'TCA', '796', 'no', '1+649', '.tc');");
            $this->execute("INSERT INTO `country` VALUES(231, 'TV', 'Tuvalu', 'Tuvalu', 'TUV', '798', 'yes', '688', '.tv');");
            $this->execute("INSERT INTO `country` VALUES(232, 'UG', 'Uganda', 'Republic of Uganda', 'UGA', '800', 'yes', '256', '.ug');");
            $this->execute("INSERT INTO `country` VALUES(233, 'UA', 'Ukraine', 'Ukraine', 'UKR', '804', 'yes', '380', '.ua');");
            $this->execute("INSERT INTO `country` VALUES(234, 'AE', 'United Arab Emirates', 'United Arab Emirates', 'ARE', '784', 'yes', '971', '.ae');");
            $this->execute("INSERT INTO `country` VALUES(235, 'GB', 'United Kingdom', 'United Kingdom of Great Britain and Nothern Ireland', 'GBR', '826', 'yes', '44', '.uk');");
            $this->execute("INSERT INTO `country` VALUES(236, 'US', 'United States', 'United States of America', 'USA', '840', 'yes', '1', '.us');");
            $this->execute("INSERT INTO `country` VALUES(237, 'UM', 'United States Minor Outlying Islands', 'United States Minor Outlying Islands', 'UMI', '581', 'no', 'NONE', 'NONE');");
            $this->execute("INSERT INTO `country` VALUES(238, 'UY', 'Uruguay', 'Eastern Republic of Uruguay', 'URY', '858', 'yes', '598', '.uy');");
            $this->execute("INSERT INTO `country` VALUES(239, 'UZ', 'Uzbekistan', 'Republic of Uzbekistan', 'UZB', '860', 'yes', '998', '.uz');");
            $this->execute("INSERT INTO `country` VALUES(240, 'VU', 'Vanuatu', 'Republic of Vanuatu', 'VUT', '548', 'yes', '678', '.vu');");
            $this->execute("INSERT INTO `country` VALUES(241, 'VA', 'Vatican City', 'State of the Vatican City', 'VAT', '336', 'no', '39', '.va');");
            $this->execute("INSERT INTO `country` VALUES(242, 'VE', 'Venezuela', 'Bolivarian Republic of Venezuela', 'VEN', '862', 'yes', '58', '.ve');");
            $this->execute("INSERT INTO `country` VALUES(243, 'VN', 'Vietnam', 'Socialist Republic of Vietnam', 'VNM', '704', 'yes', '84', '.vn');");
            $this->execute("INSERT INTO `country` VALUES(244, 'VG', 'Virgin Islands, British', 'British Virgin Islands', 'VGB', '092', 'no', '1+284', '.vg');");
            $this->execute("INSERT INTO `country` VALUES(245, 'VI', 'Virgin Islands, US', 'Virgin Islands of the United States', 'VIR', '850', 'no', '1+340', '.vi');");
            $this->execute("INSERT INTO `country` VALUES(246, 'WF', 'Wallis and Futuna', 'Wallis and Futuna', 'WLF', '876', 'no', '681', '.wf');");
            $this->execute("INSERT INTO `country` VALUES(247, 'EH', 'Western Sahara', 'Western Sahara', 'ESH', '732', 'no', '212', '.eh');");
            $this->execute("INSERT INTO `country` VALUES(248, 'YE', 'Yemen', 'Republic of Yemen', 'YEM', '887', 'yes', '967', '.ye');");
            $this->execute("INSERT INTO `country` VALUES(249, 'ZM', 'Zambia', 'Republic of Zambia', 'ZMB', '894', 'yes', '260', '.zm');");
            $this->execute("INSERT INTO `country` VALUES(250, 'ZW', 'Zimbabwe', 'Republic of Zimbabwe', 'ZWE', '716', 'yes', '263', '.zw');");
        endif;

        // Migration for table course
        if (!$this->hasTable('course')) :
            $table = $this->table('course', ['id' => false, 'primary_key' => 'courseID', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('courseID', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('courseNumber', 'integer', ['limit' => 8])
                ->addColumn('courseCode', 'string', ['limit' => 80])
                ->addColumn('subjectCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('deptCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('courseDesc', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('creditType', 'char', ['default' => 'I', 'limit' => 6])
                ->addColumn('minCredit', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.0'])
                ->addColumn('maxCredit', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.0'])
                ->addColumn('increCredit', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.0'])
                ->addColumn('courseLevelCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('acadLevelCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('courseShortTitle', 'string', ['limit' => 60])
                ->addColumn('courseLongTitle', 'string', ['limit' => 80])
                ->addColumn('preReq', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('printText', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('rule', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('allowAudit', 'enum', ['default' => '0', 'values' => ['1', '0']])
                ->addColumn('allowWaitlist', 'enum', ['default' => '0', 'values' => ['1', '0']])
                ->addColumn('minEnroll', 'integer', ['null' => true, 'limit' => 3])
                ->addColumn('seatCap', 'integer', ['null' => true, 'limit' => 3])
                ->addColumn('startDate', 'date', [])
                ->addColumn('endDate', 'date', ['null' => true])
                ->addColumn('currStatus', 'string', ['limit' => 1])
                ->addColumn('statusDate', 'date', ['null' => true])
                ->addColumn('approvedDate', 'date', ['null' => true])
                ->addColumn('approvedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['courseCode', 'courseLevelCode'])
                ->addForeignKey('subjectCode', 'subject', 'subjectCode', ['constraint' => 'course_subjectCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('deptCode', 'department', 'deptCode', ['constraint' => 'course_deptCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('courseLevelCode', 'crlv', 'code', ['constraint' => 'course_courseLevelCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('acadLevelCode', 'aclv', 'code', ['constraint' => 'course_acadLevelCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('approvedBy', 'staff', 'staffID', ['constraint' => 'course_approvedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table course_sec
        if (!$this->hasTable('course_sec')) :
            $table = $this->table('course_sec', ['id' => false, 'primary_key' => 'courseSecID', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('courseSecID', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('sectionNumber', 'char', ['limit' => 8])
                ->addColumn('courseSecCode', 'string', ['limit' => 80])
                ->addColumn('courseSection', 'string', ['limit' => 80])
                ->addColumn('buildingCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('roomCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('locationCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('courseLevelCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('acadLevelCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('deptCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('facID', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('termCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('courseID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('courseCode', 'string', ['limit' => 80])
                ->addColumn('preReqs', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('secShortTitle', 'string', ['limit' => 60])
                ->addColumn('startDate', 'date', [])
                ->addColumn('endDate', 'date', ['null' => true])
                ->addColumn('startTime', 'string', ['null' => true, 'limit' => 8])
                ->addColumn('endTime', 'string', ['null' => true, 'limit' => 8])
                ->addColumn('dotw', 'string', ['null' => true, 'limit' => 7])
                ->addColumn('minCredit', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.0'])
                ->addColumn('maxCredit', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.0'])
                ->addColumn('increCredit', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.0'])
                ->addColumn('ceu', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.0'])
                ->addColumn('instructorMethod', 'string', ['limit' => 191])
                ->addColumn('instructorLoad', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.0'])
                ->addColumn('contactHours', 'decimal', ['precision' => 4, 'scale' => 1, 'default' => '0.0'])
                ->addColumn('webReg', 'enum', ['default' => '1', 'values' => ['1', '0']])
                ->addColumn('courseFee', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00'])
                ->addColumn('labFee', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00'])
                ->addColumn('materialFee', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00'])
                ->addColumn('secType', 'enum', ['default' => 'ONC', 'values' => ['ONL', 'HB', 'ONC']])
                ->addColumn('currStatus', 'string', ['limit' => 1])
                ->addColumn('statusDate', 'date', [])
                ->addColumn('comment', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('approvedDate', 'date', [])
                ->addColumn('approvedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['courseSection'], ['unique' => true])
                ->addIndex(['courseSecCode', 'currStatus', 'facID', 'courseCode'])
                ->addForeignKey('buildingCode', 'building', 'buildingCode', ['constraint' => 'course_sec_buildingCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('roomCode', 'room', 'roomCode', ['constraint' => 'course_sec_roomCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('locationCode', 'location', 'locationCode', ['constraint' => 'course_sec_locationCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('deptCode', 'department', 'deptCode', ['constraint' => 'course_sec_deptCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('termCode', 'term', 'termCode', ['constraint' => 'course_sec_termCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('courseLevelCode', 'crlv', 'code', ['constraint' => 'course_sec_courseLevelCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('acadLevelCode', 'aclv', 'code', ['constraint' => 'course_sec_acadLevelCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('courseID', 'course', 'courseID', ['constraint' => 'course_sec_courseID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('approvedBy', 'person', 'personID', ['constraint' => 'course_sec_approvedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
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

        // Migration for table degree
        if (!$this->hasTable('degree')) :
            $table = $this->table('degree', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('degreeCode', 'char', ['limit' => 22])
                ->addColumn('degreeName', 'string', ['limit' => 191])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['degreeCode'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `degree` VALUES(1, 'NULL', '', '$NOW');");
        endif;

        // Migration for table department
        if (!$this->hasTable('department')) :
            $table = $this->table('department', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('deptTypeCode', 'char', ['limit' => 22])
                ->addColumn('deptCode', 'char', ['limit' => 22])
                ->addColumn('deptName', 'string', ['limit' => 191])
                ->addColumn('deptEmail', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('deptPhone', 'string', ['null' => true, 'limit' => 80])
                ->addColumn('deptDesc', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['deptCode'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `department` VALUES(1, 'NULL', 'NULL', 'Null', '', '', 'Default', '$NOW');");
        endif;

        // Migration for table error
        if (!$this->hasTable('error')) :
            $table = $this->table('error', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('type', 'integer', ['limit' => 10])
                ->addColumn('time', 'integer', ['limit' => 10])
                ->addColumn('string', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('file', 'string', ['limit' => 191])
                ->addColumn('line', 'integer', ['limit' => 10])
                ->addColumn('addDate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->create();
        endif;

        // Migration for table event
        if (!$this->hasTable('event')) :
            $table = $this->table('event', ['id' => false, 'primary_key' => 'eventID', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('eventID', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('eventType', 'string', ['limit' => 191])
                ->addColumn('catID', 'integer', ['null' => true, 'limit' => 11])
                ->addColumn('requestor', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('roomCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('termCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('title', 'string', ['limit' => 191])
                ->addColumn('description', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('weekday', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_TINY])
                ->addColumn('startDate', 'date', ['null' => true])
                ->addColumn('startTime', 'time', ['null' => true])
                ->addColumn('endTime', 'time', ['null' => true])
                ->addColumn('repeats', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_TINY])
                ->addColumn('repeatFreq', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_TINY])
                ->addColumn('status', 'enum', ['default' => 'A', 'values' => ['A', 'I']])
                ->addColumn('addDate', 'date', [])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['roomCode', 'termCode', 'title', 'weekday', 'startDate', 'startTime', 'endTime'], ['unique' => true])
                ->addForeignKey('catID', 'event_category', 'catID', ['constraint' => 'event_catID', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('requestor', 'person', 'personID', ['constraint' => 'event_requestor', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('roomCode', 'room', 'roomCode', ['constraint' => 'event_roomCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('termCode', 'term', 'termCode', ['constraint' => 'event_termCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('addedBy', 'person', 'personID', ['constraint' => 'event_addedBy', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table event_category
        if (!$this->hasTable('event_category')) :
            $table = $this->table('event_category', ['id' => false, 'primary_key' => 'catID', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('catID', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('cat_name', 'string', ['limit' => 80])
                ->addColumn('bgcolor', 'char', ['default' => '#', 'limit' => 22])
                ->create();

            $this->execute("INSERT INTO `event_category` VALUES(1, 'Course', '#8C7BC6');");
            $this->execute("INSERT INTO `event_category` VALUES(2, 'Meeting', '#00CCFF');");
            $this->execute("INSERT INTO `event_category` VALUES(3, 'Conference', '#E66000');");
            $this->execute("INSERT INTO `event_category` VALUES(4, 'Event', '#61D0AF');");
        endif;

        // Migration for table event_meta
        if (!$this->hasTable('event_meta')) :
            $table = $this->table('event_meta', ['id' => false, 'primary_key' => 'eventMetaID', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('eventMetaID', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('eventID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('roomCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('requestor', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('start', 'datetime', ['null' => true])
                ->addColumn('end', 'datetime', ['null' => true])
                ->addColumn('title', 'string', ['limit' => 191])
                ->addColumn('description', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('addDate', 'date', [])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['eventID', 'roomCode', 'start', 'end', 'title'], ['unique' => true])
                ->addForeignKey('eventID', 'event', 'eventID', ['constraint' => 'event_meta_eventID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('roomCode', 'room', 'roomCode', ['constraint' => 'event_meta_roomCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('requestor', 'person', 'personID', ['constraint' => 'event_meta_requestor', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('addedBy', 'person', 'personID', ['constraint' => 'event_meta_addedBy', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table event_request
        if (!$this->hasTable('event_request')) :
            $table = $this->table('event_request', ['id' => false, 'primary_key' => 'requestID', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('requestID', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('eventType', 'string', ['limit' => 191])
                ->addColumn('catID', 'integer', ['null' => true, 'limit' => 11])
                ->addColumn('requestor', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('roomCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('termCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('title', 'string', ['limit' => 191])
                ->addColumn('description', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('weekday', 'integer', ['limit' => MysqlAdapter::INT_TINY])
                ->addColumn('startDate', 'date', [])
                ->addColumn('endDate', 'date', ['null' => true])
                ->addColumn('startTime', 'time', [])
                ->addColumn('endTime', 'time', [])
                ->addColumn('repeats', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_TINY])
                ->addColumn('repeatFreq', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_TINY])
                ->addColumn('addDate', 'date', [])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['roomCode', 'termCode', 'title', 'weekday', 'startDate', 'startTime', 'endTime'], ['unique' => true])
                ->addForeignKey('requestor', 'person', 'personID', ['constraint' => 'event_request_requestor', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('roomCode', 'room', 'roomCode', ['constraint' => 'event_request_roomCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('termCode', 'term', 'termCode', ['constraint' => 'event_request_termCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table external_course
        if (!$this->hasTable('external_course')) :
            $table = $this->table('external_course', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('courseTitle', 'string', ['limit' => 191])
                ->addColumn('instCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('courseName', 'string', ['limit' => 191])
                ->addColumn('term', 'char', ['limit' => 22])
                ->addColumn('credits', 'decimal', ['precision' => 4, 'scale' => 2])
                ->addColumn('currStatus', 'enum', ['default' => 'A', 'values' => ['A', 'I', 'P', 'O']])
                ->addColumn('statusDate', 'date', [])
                ->addColumn('minGrade', 'char', ['limit' => 6])
                ->addColumn('comments', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('startDate', 'date', [])
                ->addColumn('endDate', 'date', ['null' => true])
                ->addColumn('addDate', 'date', [])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('instCode', 'institution', 'fice_ceeb', ['constraint' => 'external_course_instCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('addedBy', 'staff', 'staffID', ['constraint' => 'external_course_addedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table gl_account
        if (!$this->hasTable('gl_account')) :
            $table = $this->table('gl_account', ['id' => false, 'primary_key' => 'glacctID', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('glacctID', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('gl_acct_number', 'string', ['limit' => 191])
                ->addColumn('gl_acct_name', 'string', ['limit' => 191])
                ->addColumn('gl_acct_type', 'string', ['limit' => 191])
                ->addColumn('gl_acct_memo', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addIndex(['gl_acct_number'], ['unique' => true])
                ->create();
        endif;

        // Migration for table gl_journal_entry
        if (!$this->hasTable('gl_journal_entry')) :
            $table = $this->table('gl_journal_entry', ['id' => false, 'primary_key' => 'jeID', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('jeID', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('gl_jentry_date', 'date', [])
                ->addColumn('gl_jentry_manual_id', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('gl_jentry_title', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('gl_jentry_description', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('gl_jentry_personID', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->create();
        endif;

        // Migration for table gl_transaction
        if (!$this->hasTable('gl_transaction')) :
            $table = $this->table('gl_transaction', ['id' => false, 'primary_key' => 'trID', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('trID', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('jeID', 'integer', ['null' => true])
                ->addColumn('accountID', 'integer', ['null' => true])
                ->addColumn('gl_trans_date', 'date', ['null' => true])
                ->addColumn('gl_trans_memo', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('gl_trans_debit', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true])
                ->addColumn('gl_trans_credit', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true])
                ->addForeignKey('jeID', 'gl_journal_entry', 'jeID', ['constraint' => 'gl_transaction_jeID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table gradebook
        if (!$this->hasTable('gradebook')) :
            $table = $this->table('gradebook', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('assignID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('courseSecID', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('facID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('grade', 'char', ['null' => true, 'limit' => 6])
                ->addColumn('addDate', 'date', [])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['assignID', 'facID', 'stuID'], ['unique' => true])
                ->addForeignKey('assignID', 'assignment', 'id', ['constraint' => 'gradebook_assignID', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->addForeignKey('courseSecID', 'course_sec', 'courseSecID', ['constraint' => 'gradebook_courseSecID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('facID', 'staff', 'staffID', ['constraint' => 'gradebook_facID', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->addForeignKey('stuID', 'student', 'stuID', ['constraint' => 'gradebook_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('addedBy', 'person', 'personID', ['constraint' => 'gradebook_addedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table grade_scale
        if (!$this->hasTable('grade_scale')) :
            $table = $this->table('grade_scale', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('grade', 'char', ['limit' => 6])
                ->addColumn('percent', 'string', ['limit' => 12])
                ->addColumn('points', 'decimal', ['precision' => 6, 'scale' => 2])
                ->addColumn('count_in_gpa', 'enum', ['default' => '0', 'values' => ['1', '0']])
                ->addColumn('status', 'enum', ['default' => '1', 'values' => ['1', '0']])
                ->addColumn('description', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->create();

            $this->execute("INSERT INTO `grade_scale` VALUES(1, 'A+', '97-100', '4.00', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(2, 'A', '93-96', '4.00', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(3, 'A-', '90-92', '3.70', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(4, 'B+', '87-89', '3.30', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(5, 'B', '83-86', '3.00', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(6, 'B-', '80-82', '2.70', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(7, 'P', '80-82', '2.70', '1', '1', 'Minimum for Pass/Fail courses');");
            $this->execute("INSERT INTO `grade_scale` VALUES(8, 'C+', '77-79', '2.30', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(9, 'C', '73-76', '2.00', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(10, 'C-', '70-72', '1.70', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(11, 'D+', '67-69', '1.30', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(12, 'D', '65-66', '1.00', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(13, 'F', 'Below 65', '0.00', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(14, 'I', '0', '0.00', '0', '1', 'Incomplete grades');");
            $this->execute("INSERT INTO `grade_scale` VALUES(15, 'AW', '0', '0.00', '0', '1', '\"AW\" is an administrative grade assigned to students who have attended no more than the first two classes, but who have not officially dropped or withdrawn from the course. Does not count against GPA.');");
            $this->execute("INSERT INTO `grade_scale` VALUES(16, 'NA', '0', '0.00', '0', '1', '\"NA\" is an administrative grade assigned to students who are officially registered for the course and whose name appears on the grade roster, but who have never attended class. Does not count against GPA.');");
            $this->execute("INSERT INTO `grade_scale` VALUES(17, 'W', '0', '0.00', '0', '1', 'Withdrew');");
            $this->execute("INSERT INTO `grade_scale` VALUES(18, 'IP', '90-98', '4.00', '0', '1', 'Incomplete passing');");
        endif;


        // Migration for table graduation_hold
        if (!$this->hasTable('graduation_hold')) :
            $table = $this->table('graduation_hold', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('queryID', 'integer', ['limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('gradDate', 'date', [])
                ->create();
        endif;

        // Migration for table hiatus
        if (!$this->hasTable('hiatus')) :
            $table = $this->table('hiatus', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('code', 'char', ['limit' => 22])
                ->addColumn('startDate', 'date', [])
                ->addColumn('endDate', 'date', ['null' => true])
                ->addColumn('comment', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('addDate', 'date', [])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('stuID', 'student', 'stuID', ['constraint' => 'hiatus_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('addedBy', 'staff', 'staffID', ['constraint' => 'hiatus_addedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table institution
        if (!$this->hasTable('institution')) :
            $table = $this->table('institution', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('fice_ceeb', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('instType', 'char', ['limit' => 10])
                ->addColumn('instName', 'string', ['limit' => 191])
                ->addColumn('city', 'string', ['null' => true, 'limit' => 60])
                ->addColumn('state', 'char', ['null' => true, 'limit' => 4])
                ->addColumn('country', 'char', ['null' => true, 'limit' => 4])
                ->addIndex(['fice_ceeb'])
                ->create();
        endif;

        // Migration for table institution_attended
        if (!$this->hasTable('institution_attended')) :
            $table = $this->table('institution_attended', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('fice_ceeb', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('personID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('fromDate', 'date', [])
                ->addColumn('toDate', 'date', [])
                ->addColumn('major', 'string', ['limit' => 191])
                ->addColumn('degree_awarded', 'char', ['limit' => 22])
                ->addColumn('degree_conferred_date', 'date', [])
                ->addColumn('GPA', 'decimal', ['precision' => 6, 'scale' => 4, 'null' => true])
                ->addColumn('addDate', 'date', [])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['fice_ceeb', 'personID'], ['unique' => true])
                ->addForeignKey('personID', 'person', 'personID', ['constraint' => 'institution_attended_personID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('addedBy', 'staff', 'staffID', ['constraint' => 'institution_attended_addedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table job
        if (!$this->hasTable('job')) :
            $table = $this->table('job', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('pay_grade', 'integer', ['limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('title', 'string', ['limit' => 191])
                ->addColumn('hourly_wage', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true])
                ->addColumn('weekly_hours', 'integer', ['null' => true, 'limit' => 6])
                ->addColumn('attachment', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('addDate', 'date', [])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('addedBy', 'staff', 'staffID', ['constraint' => 'job_addedBy', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();

            $this->execute("INSERT INTO `job` VALUES(1, 1, 'IT Support', '34.00', 40, NULL, '$NOW', 1, '$NOW');");
        endif;


        // Migration for table job_status
        if (!$this->hasTable('job_status')) :
            $table = $this->table('job_status', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('typeCode', 'char', ['limit' => 22])
                ->addColumn('type', 'string', ['limit' => 191])
                ->addIndex(['typeCode'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `job_status` VALUES(1, 'FT', 'Full Time');");
            $this->execute("INSERT INTO `job_status` VALUES(2, 'TQ', 'Three Quarter Time');");
            $this->execute("INSERT INTO `job_status` VALUES(3, 'HT', 'Half Time');");
            $this->execute("INSERT INTO `job_status` VALUES(4, 'CT', 'Contract');");
            $this->execute("INSERT INTO `job_status` VALUES(5, 'PD', 'Per Diem');");
            $this->execute("INSERT INTO `job_status` VALUES(6, 'TFT', 'Temp Full Time');");
            $this->execute("INSERT INTO `job_status` VALUES(7, 'TTQ', 'Temp Three Quarter Time');");
            $this->execute("INSERT INTO `job_status` VALUES(8, 'THT', 'Temp Half Time');");
        endif;

        if (!$this->hasTable('last_login')) :
            $table = $this->table('last_login', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('personID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('loginTimeStamp', 'datetime', [])
                ->addForeignKey('personID', 'person', 'personID', ['constraint' => 'last_login_personID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();
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

        // Migration for table location
        if (!$this->hasTable('location')) :
            $table = $this->table('location', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('locationCode', 'char', ['limit' => 22])
                ->addColumn('locationName', 'string', ['limit' => 191])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['locationCode'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `location` VALUES(1, 'NULL', '', '$NOW');");
        endif;

        // Migration for table major
        if (!$this->hasTable('major')) :
            $table = $this->table('major', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('majorCode', 'char', ['limit' => 22])
                ->addColumn('majorName', 'string', ['limit' => 191])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['majorCode'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `major` VALUES(1, 'NULL', '', '$NOW');");
        endif;

        // Migration for table met_link
        if (!$this->hasTable('met_link')) :
            $table = $this->table('met_link', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('link_title', 'string', ['limit' => 191])
                ->addColumn('link_src', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('status', 'enum', ['values' => ['active', 'inactive']])
                ->addColumn('sort', 'integer', ['limit' => MysqlAdapter::INT_TINY])
                ->create();
        endif;

        // Migration for table met_news
        if (!$this->hasTable('met_news')) :
            $table = $this->table('met_news', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('news_title', 'string', ['limit' => 191])
                ->addColumn('news_slug', 'string', ['limit' => 191])
                ->addColumn('news_content', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('status', 'enum', ['values' => ['draft', 'publish']])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('addDate', 'date', [])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('addedBy', 'person', 'personID', ['constraint' => 'met_news_addedBy', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table met_page
        if (!$this->hasTable('met_page')) :
            $table = $this->table('met_page', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('page_title', 'string', ['limit' => 191])
                ->addColumn('page_slug', 'string', ['limit' => 191])
                ->addColumn('page_content', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('status', 'enum', ['values' => ['draft', 'publish']])
                ->addColumn('sort', 'integer', ['limit' => MysqlAdapter::INT_TINY])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('addDate', 'date', [])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('addedBy', 'person', 'personID', ['constraint' => 'met_page_addedBy', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table minor
        if (!$this->hasTable('minor')) :
            $table = $this->table('minor', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('minorCode', 'char', ['limit' => 22])
                ->addColumn('minorName', 'string', ['limit' => 191])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['minorCode'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `minor` VALUES(1, 'NULL', '', '$NOW');");
        endif;


        // Migration for table options_meta
        if (!$this->hasTable('options_meta')) :
            $table = $this->table('options_meta', ['id' => false, 'primary_key' => 'meta_id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('meta_id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('meta_key', 'string', ['default' => '', 'limit' => 191])
                ->addColumn('meta_value', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
                ->addIndex(['meta_key'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `options_meta` VALUES(1, 'system_email', 'email@gmail.com');");
            $this->execute("INSERT INTO `options_meta` VALUES(2, 'institution_name', 'Institution Name');");
            $this->execute("INSERT INTO `options_meta` VALUES(3, 'cookieexpire', '604800');");
            $this->execute("INSERT INTO `options_meta` VALUES(4, 'cookiepath', '/');");
            $this->execute("INSERT INTO `options_meta` VALUES(5, 'enable_benchmark', '0');");
            $this->execute("INSERT INTO `options_meta` VALUES(6, 'maintenance_mode', '0');");
            $this->execute("INSERT INTO `options_meta` VALUES(7, 'current_term_code', '');");
            $this->execute("INSERT INTO `options_meta` VALUES(8, 'open_registration', '1');");
            $this->execute("INSERT INTO `options_meta` VALUES(9, 'help_desk', 'https://www.edutracsis.com/');");
            $this->execute("INSERT INTO `options_meta` VALUES(10, 'reset_password_text', '<b>eduTrac SIS Password Reset</b><br>Password &amp; Login Information<br><br>You or someone else requested a new password to the eduTrac SIS online system. If you did not request this change, please contact the administrator as soon as possible @ #adminemail#.&nbsp; To log into the eduTrac system, please visit #url# and login with your username and password.<br><br>FULL NAME:&nbsp; #fname# #lname#<br>USERNAME:&nbsp; #uname#<br>PASSWORD:&nbsp; #password#<br><br>If you need further assistance, please read the documentation at #helpdesk#.<br><br>KEEP THIS IN A SAFE AND SECURE LOCATION.<br><br>Thank You,<br>eduTrac SIS Web Team<br>');");
            $this->execute("INSERT INTO `options_meta` VALUES(11, 'api_key', '');");
            $this->execute("INSERT INTO `options_meta` VALUES(12, 'room_request_email', 'request@myschool.edu');");
            $this->execute("INSERT INTO `options_meta` VALUES(13, 'room_request_text', '<p>&nbsp;</p>\r\n<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#F4F3F4\">\r\n<tbody>\r\n<tr>\r\n<td style=\"padding: 15px;\"><center>\r\n<table width=\"550\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td align=\"left\">\r\n<div style=\"border: solid 1px #d9d9d9;\">\r\n<table id=\"header\" style=\"line-height: 1.6; font-size: 12px; font-family: Helvetica, Arial, sans-serif; border: solid 1px #FFFFFF; color: #444;\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td style=\"color: #ffffff;\" colspan=\"2\" valign=\"bottom\" height=\"30\">.</td>\r\n</tr>\r\n<tr>\r\n<td style=\"line-height: 32px; padding-left: 30px;\" valign=\"baseline\"><span style=\"font-size: 32px;\">eduTrac SIS</span></td>\r\n<td style=\"padding-right: 30px;\" align=\"right\" valign=\"baseline\"><span style=\"font-size: 14px; color: #777777;\">Room/Event Reservation Request</span></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id=\"content\" style=\"margin-top: 15px; margin-right: 30px; margin-left: 30px; color: #444; line-height: 1.6; font-size: 12px; font-family: Arial, sans-serif;\" border=\"0\" width=\"490\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td style=\"border-top: solid 1px #d9d9d9;\" colspan=\"2\">\r\n<div style=\"padding: 15px 0;\">Below are the details of a new room request.</div>\r\n<div style=\"padding: 15px 0;\"><strong>Name:</strong> #name#<br /><br /><strong>Email:</strong> #email#<br /><br /><strong>Event Title:</strong> #title#<br /><strong>Description:</strong> #description#<br /><strong>Request Type:</strong> #request_type#<br /><strong>Category:</strong> #category#<br /><strong>Room#:</strong> #room#<br /><strong>Start Date:</strong> #firstday#<br /><strong>End Date:</strong> #lastday#<br /><strong>Start Time:</strong> #sTime#<br /><strong>End Time:</strong> #eTime#<br /><strong>Repeat?:</strong> #repeat#<br /><strong>Occurrence:</strong> #occurrence#<br /><br /><br />\r\n<h3>Legend</h3>\r\n<ul>\r\n<li>Repeat - 1 means yes it is an event that is repeated</li>\r\n<li>Occurrence - 1 = repeats everyday, 7 = repeats weekly, 14 = repeats biweekly</li>\r\n</ul>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id=\"footer\" style=\"line-height: 1.5; font-size: 12px; font-family: Arial, sans-serif; margin-right: 30px; margin-left: 30px;\" border=\"0\" width=\"490\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr style=\"font-size: 11px; color: #999999;\">\r\n<td style=\"border-top: solid 1px #d9d9d9;\" colspan=\"2\">\r\n<div style=\"padding-top: 15px; padding-bottom: 1px;\">Powered by eduTrac SIS</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"color: #ffffff;\" colspan=\"2\" height=\"15\">.</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</center></td>\r\n</tr>\r\n</tbody>\r\n</table>');");
            $this->execute("INSERT INTO `options_meta` VALUES(14, 'room_booking_confirmation_text', '<p>&nbsp;</p>\r\n<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#F4F3F4\">\r\n<tbody>\r\n<tr>\r\n<td style=\"padding: 15px;\"><center>\r\n<table width=\"550\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td align=\"left\">\r\n<div style=\"border: solid 1px #d9d9d9;\">\r\n<table id=\"header\" style=\"line-height: 1.6; font-size: 12px; font-family: Helvetica, Arial, sans-serif; border: solid 1px #FFFFFF; color: #444;\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td style=\"color: #ffffff;\" colspan=\"2\" valign=\"bottom\" height=\"30\">.</td>\r\n</tr>\r\n<tr>\r\n<td style=\"line-height: 32px; padding-left: 30px;\" valign=\"baseline\"><span style=\"font-size: 32px;\">eduTrac SIS</span></td>\r\n<td style=\"padding-right: 30px;\" align=\"right\" valign=\"baseline\"><span style=\"font-size: 14px; color: #777777;\">Room/Event&nbsp;Booking&nbsp;Confirmation</span></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id=\"content\" style=\"margin-top: 15px; margin-right: 30px; margin-left: 30px; color: #444; line-height: 1.6; font-size: 12px; font-family: Arial, sans-serif;\" border=\"0\" width=\"490\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td style=\"border-top: solid 1px #d9d9d9;\" colspan=\"2\">\r\n<div style=\"padding: 15px 0;\">Your room request or event request entitled <strong>#title#</strong> has been booked. If you have any questions or concerns, please email our office at <a href=\"mailto:request@bdci.edu\">request@bdci.edu</a></div>\r\n<div style=\"padding: 15px 0;\">Sincerely,<br />Room Scheduler</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id=\"footer\" style=\"line-height: 1.5; font-size: 12px; font-family: Arial, sans-serif; margin-right: 30px; margin-left: 30px;\" border=\"0\" width=\"490\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr style=\"font-size: 11px; color: #999999;\">\r\n<td style=\"border-top: solid 1px #d9d9d9;\" colspan=\"2\">\r\n<div style=\"padding-top: 15px; padding-bottom: 1px;\">Powered by eduTrac SIS</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"color: #ffffff;\" colspan=\"2\" height=\"15\">.</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</center></td>\r\n</tr>\r\n</tbody>\r\n</table>');");
            $this->execute("INSERT INTO `options_meta` VALUES(15, 'myetsis_welcome_message', '<p>Welcome to the <em>my</em>etSIS campus portal. The <em>my</em>etSIS campus portal&nbsp;is your personalized campus web site at Eastbound University.</p>\r\n<p>If you are a prospective student who is interested in applying to the college, checkout the <a href=\"pages/admissions/\">admissions</a>&nbsp;page for more information.</p>');");
            $this->execute("INSERT INTO `options_meta` VALUES(16, 'contact_phone', '888.888.8888');");
            $this->execute("INSERT INTO `options_meta` VALUES(17, 'contact_email', 'contact@colegio.edu');");
            $this->execute("INSERT INTO `options_meta` VALUES(18, 'mailing_address', '10 Eliot Street, Suite 2\r\nSomerville, MA 02140');");
            $this->execute("INSERT INTO `options_meta` VALUES(19, 'enable_myetsis_portal', '0');");
            $this->execute("INSERT INTO `options_meta` VALUES(20, 'screen_caching', '1');");
            $this->execute("INSERT INTO `options_meta` VALUES(21, 'db_caching', '1');");
            $this->execute("INSERT INTO `options_meta` VALUES(22, 'admissions_email', 'admissions@colegio.edu');");
            $this->execute("INSERT INTO `options_meta` VALUES(23, 'coa_form_text', '<p>Dear Admin,</p>\r\n<p>#name# has submitted a change of address. Please see below for details.</p>\r\n<p><strong>ID:</strong> #id#</p>\r\n<p><strong>Address1:</strong> #address1#</p>\r\n<p><strong>Address2:</strong> #address2#</p>\r\n<p><strong>City:</strong> #city#</p>\r\n<p><strong>State:</strong> #state#</p>\r\n<p><strong>Zip:</strong> #zip#</p>\r\n<p><strong>Country:</strong> #country#</p>\r\n<p><strong>Phone:</strong> #phone#</p>\r\n<p><strong>Email:</strong> #email#</p>\r\n<p>&nbsp;</p>\r\n<p>----<br /><em>This is a system generated email.</em></p>');");
            $this->execute("INSERT INTO `options_meta` VALUES(24, 'enable_myetsis_appl_form', '0');");
            $this->execute("INSERT INTO `options_meta` VALUES(25, 'myetsis_offline_message', 'Please excuse the dust. We are giving the portal a new facelift. Please try back again in an hour.\r\n\r\nSincerely,\r\nIT Department');");
            $this->execute("INSERT INTO `options_meta` VALUES(26, 'curl', '1');");
            $this->execute("INSERT INTO `options_meta` VALUES(27, 'system_timezone', 'America/New_York');");
            $this->execute("INSERT INTO `options_meta` VALUES(28, 'number_of_courses', '3');");
            $this->execute("INSERT INTO `options_meta` VALUES(29, 'account_balance', '');");
            $this->execute("INSERT INTO `options_meta` VALUES(30, 'reg_instructions', '');");
            $this->execute("INSERT INTO `options_meta` VALUES(31, 'etsis_core_locale', 'en_US');");
            $this->execute("INSERT INTO `options_meta` VALUES(32, 'send_acceptance_email', '0');");
            $this->execute("INSERT INTO `options_meta` VALUES(33, 'person_login_details', '<p>Dear #fname#:</p>\r\n<p>An account has just been created for you. Below are your login details.</p>\r\n<p>Username: #uname#</p>\r\n<p>Password: #password#</p>\r\n<p>ID: #id#</p>\r\n<p>Alternate ID:&nbsp;#altID#</p>\r\n<p>You may log into your account at the url below:</p>\r\n<p><a href=\"#url#\">#url#</a></p>');");
            $this->execute("INSERT INTO `options_meta` VALUES(34, 'myetsis_layout', 'default');");
            $this->execute("INSERT INTO `options_meta` VALUES(35, 'open_terms', '');");
            $this->execute("INSERT INTO `options_meta` VALUES(36, 'elfinder_driver', 'elf_local_driver');");
        endif;

        // Migration for table pay_grade
        if (!$this->hasTable('pay_grade')) :
            $table = $this->table('pay_grade', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('grade', 'char', ['limit' => 22])
                ->addColumn('minimum_salary', 'decimal', ['precision' => 10, 'scale' => 2])
                ->addColumn('maximum_salary', 'decimal', ['precision' => 10, 'scale' => 2])
                ->addColumn('addDate', 'date', [])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('addedBy', 'staff', 'staffID', ['constraint' => 'pay_grade_addedBy', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();

            $this->execute("INSERT INTO `pay_grade` VALUES(1, '24', '40000.00', '44999.00', '$NOW', 1, '$NOW');");
        endif;

        // Migration for table payment
        if (!$this->hasTable('payment')) :
            $table = $this->table('payment', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('termCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0'])
                ->addColumn('checkNum', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('paypal_txnID', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('paypal_payment_status', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('paypal_txn_fee', 'decimal', ['precision' => 6, 'scale' => 2, 'default' => '0.00'])
                ->addColumn('paymentTypeID', 'integer', ['limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('comment', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('paymentDate', 'date', [])
                ->addColumn('postedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('postedBy', 'staff', 'staffID', ['constraint' => 'payment_postedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->addForeignKey('stuID', 'student', 'stuID', ['constraint' => 'payment_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('termCode', 'term', 'termCode', ['constraint' => 'payment_termCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table payment_type
        if (!$this->hasTable('payment_type')) :
            $table = $this->table('payment_type', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('type', 'string', ['limit' => 60])
                ->create();

            $this->execute("INSERT INTO `payment_type` VALUES(1, 'Cash');");
            $this->execute("INSERT INTO `payment_type` VALUES(2, 'Check');");
            $this->execute("INSERT INTO `payment_type` VALUES(3, 'Credit Card');");
            $this->execute("INSERT INTO `payment_type` VALUES(4, 'Paypal');");
            $this->execute("INSERT INTO `payment_type` VALUES(5, 'Wire Transfer');");
            $this->execute("INSERT INTO `payment_type` VALUES(6, 'Money Order');");
            $this->execute("INSERT INTO `payment_type` VALUES(7, 'Student Loan');");
            $this->execute("INSERT INTO `payment_type` VALUES(8, 'Grant');");
            $this->execute("INSERT INTO `payment_type` VALUES(9, 'Financial Aid');");
            $this->execute("INSERT INTO `payment_type` VALUES(10, 'Scholarship');");
            $this->execute("INSERT INTO `payment_type` VALUES(11, 'Waiver');");
            $this->execute("INSERT INTO `payment_type` VALUES(12, 'Other');");
        endif;

        // Migration for table permission
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
        endif;

        // Migration for table person
        if (!$this->hasTable('person')) :
            $table = $this->table('person', ['id' => false, 'primary_key' => 'personID', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('personID', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('altID', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('uname', 'char', ['limit' => 80])
                ->addColumn('prefix', 'char', ['limit' => 6])
                ->addColumn('personType', 'char', ['limit' => 8])
                ->addColumn('fname', 'string', ['limit' => 191])
                ->addColumn('lname', 'string', ['limit' => 191])
                ->addColumn('mname', 'char', ['null' => true, 'limit' => 4])
                ->addColumn('email', 'string', ['limit' => 191])
                ->addColumn('ssn', 'integer', ['null' => true, 'limit' => 9])
                ->addColumn('dob', 'date', ['null' => true])
                ->addColumn('veteran', 'enum', ['values' => ['1', '0']])
                ->addColumn('ethnicity', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('gender', 'enum', ['values' => ['M', 'F']])
                ->addColumn('emergency_contact', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('emergency_contact_phone', 'string', ['null' => true, 'limit' => 60])
                ->addColumn('photo', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('password', 'string', ['limit' => 191])
                ->addColumn('status', 'enum', ['default' => 'A', 'values' => ['A', 'I']])
                ->addColumn('tags', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('auth_token', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('approvedDate', 'datetime', [])
                ->addColumn('approvedBy', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastLogin', 'datetime', ['null' => true])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['uname'], ['unique' => true])
                ->addIndex(['personType'])
                ->addForeignKey('approvedBy', 'person', 'personID', ['constraint' => 'person_approvedBy', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->create();

            $this->execute('INSERT INTO `person` (`personID`, `uname`, `password`, `fname`, `lname`, `email`,`personType`,`approvedDate`,`approvedBy`) VALUES (\'\', \'etsis\', \'$P$BAHklrhrmcZMglABG9VF6PB7c1zD5H/\', \'eduTrac\', \'SIS\', \'sis@gmail.com\', \'STA\', \'' . $NOW . '\', \'1\');');
        endif;

        // Migration for table person_perms
        if (!$this->hasTable('person_perms')) :
            $table = $this->table('person_perms', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['signed' => false, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('personID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('permission', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['personID'], ['unique' => true])
                ->addForeignKey('personID', 'person', 'personID', ['constraint' => 'person_perms_personID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table person_roles
        if (!$this->hasTable('person_roles')) :
            $table = $this->table('person_roles', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('personID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('roleID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('addDate', 'datetime', [])
                ->addIndex(['personID', 'roleID'], ['unique' => true])
                ->addForeignKey('personID', 'person', 'personID', ['constraint' => 'person_roles_personID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();

            $this->execute("INSERT INTO `person_roles` VALUES(1, 1, 8, '$NOW');");
        endif;

        // Migration for table plugin
        if (!$this->hasTable('plugin')) :
            $table = $this->table('plugin', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('location', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR])
                ->create();
        endif;

        // Migration for table refund
        if (!$this->hasTable('refund')) :
            $table = $this->table('refund', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('termCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00'])
                ->addColumn('comment', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('refundDate', 'date', [])
                ->addColumn('postedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('postedBy', 'person', 'personID', ['constraint' => 'refund_personID', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->addForeignKey('stuID', 'student', 'stuID', ['constraint' => 'refund_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('termCode', 'term', 'termCode', ['constraint' => 'refund_termCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->create();
        endif;

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
        endif;

        // Migration for table role
        if (!$this->hasTable('role')) :
            $table = $this->table('role', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['signed' => false, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('roleName', 'string', ['limit' => 191])
                ->addColumn('permission', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
                ->addIndex(['roleName'], ['unique' => true])
                ->create();
            $this->execute("INSERT INTO `role` VALUES(8, 'Super Administrator', 'a:87:{i:0;s:13:\"edit_settings\";i:1;s:25:\"access_audit_trail_screen\";i:2;s:27:\"access_sql_interface_screen\";i:3;s:20:\"access_course_screen\";i:4;s:21:\"access_faculty_screen\";i:5;s:20:\"access_parent_screen\";i:6;s:21:\"access_student_screen\";i:7;s:20:\"access_plugin_screen\";i:8;s:18:\"access_role_screen\";i:9;s:24:\"access_permission_screen\";i:10;s:23:\"access_user_role_screen\";i:11;s:29:\"access_user_permission_screen\";i:12;s:28:\"access_email_template_screen\";i:13;s:24:\"access_course_sec_screen\";i:14;s:14:\"add_course_sec\";i:15;s:20:\"access_person_screen\";i:16;s:10:\"add_person\";i:17;s:23:\"access_acad_prog_screen\";i:18;s:13:\"add_acad_prog\";i:19;s:11:\"access_nslc\";i:20;s:23:\"access_error_log_screen\";i:21;s:21:\"access_cronjob_screen\";i:22;s:20:\"access_report_screen\";i:23;s:11:\"add_address\";i:24;s:24:\"access_plugin_admin_page\";i:25;s:25:\"access_save_query_screens\";i:26;s:12:\"access_forms\";i:27;s:17:\"create_stu_record\";i:28;s:17:\"create_fac_record\";i:29;s:17:\"create_par_record\";i:30;s:21:\"reset_person_password\";i:31;s:17:\"register_students\";i:32;s:10:\"access_ftp\";i:33;s:24:\"access_stu_roster_screen\";i:34;s:21:\"access_grading_screen\";i:35;s:22:\"access_bill_tbl_screen\";i:36;s:17:\"add_crse_sec_bill\";i:37;s:11:\"import_data\";i:38;s:10:\"add_course\";i:39;s:12:\"room_request\";i:40;s:19:\"activate_course_sec\";i:41;s:17:\"cancel_course_sec\";i:42;s:26:\"access_institutions_screen\";i:43;s:15:\"add_institution\";i:44;s:25:\"access_application_screen\";i:45;s:18:\"create_application\";i:46;s:19:\"access_staff_screen\";i:47;s:19:\"create_staff_record\";i:48;s:17:\"graduate_students\";i:49;s:20:\"generate_transcripts\";i:50;s:23:\"access_student_accounts\";i:51;s:21:\"access_general_ledger\";i:52;s:13:\"login_as_user\";i:53;s:16:\"access_academics\";i:54;s:17:\"access_financials\";i:55;s:22:\"access_human_resources\";i:56;s:17:\"submit_timesheets\";i:57;s:10:\"access_sql\";i:58;s:18:\"access_person_mgmt\";i:59;s:16:\"access_dashboard\";i:60;s:20:\"access_myetsis_admin\";i:61;s:20:\"manage_myetsis_pages\";i:62;s:20:\"manage_myetsis_links\";i:63;s:19:\"manage_myetsis_news\";i:64;s:16:\"add_myetsis_page\";i:65;s:17:\"edit_myetsis_page\";i:66;s:19:\"delete_myetsis_page\";i:67;s:16:\"add_myetsis_link\";i:68;s:17:\"edit_myetsis_link\";i:69;s:19:\"delete_myetsis_link\";i:70;s:16:\"add_myetsis_news\";i:71;s:17:\"edit_myetsis_news\";i:72;s:19:\"delete_myetsis_news\";i:73;s:18:\"clear_screen_cache\";i:74;s:20:\"clear_database_cache\";i:75;s:24:\"access_myetsis_appl_form\";i:76;s:16:\"edit_myetsis_css\";i:77;s:28:\"edit_myetsis_welcome_message\";i:78;s:25:\"access_communication_mgmt\";i:79;s:14:\"delete_student\";i:80;s:22:\"access_payment_gateway\";i:81;s:9:\"access_ea\";i:82;s:19:\"execute_saved_query\";i:83;s:19:\"submit_final_grades\";i:84;s:21:\"manage_business_rules\";i:85;s:13:\"override_rule\";i:86;s:8:\"send_sms\";}');");
            $this->execute("INSERT INTO `role` VALUES(9, 'Faculty', 'a:18:{i:0;s:21:\"access_student_screen\";i:1;s:24:\"access_course_sec_screen\";i:2;s:23:\"course_sec_inquiry_only\";i:3;s:19:\"course_inquiry_only\";i:4;s:23:\"access_acad_prog_screen\";i:5;s:22:\"acad_prog_inquiry_only\";i:6;s:20:\"address_inquiry_only\";i:7;s:20:\"general_inquiry_only\";i:8;s:20:\"student_inquiry_only\";i:9;s:24:\"access_stu_roster_screen\";i:10;s:21:\"access_grading_screen\";i:11;s:19:\"person_inquiry_only\";i:12;s:19:\"access_staff_screen\";i:13;s:18:\"staff_inquiry_only\";i:14;s:16:\"access_dashboard\";i:15;s:21:\"restrict_edit_profile\";i:16;s:16:\"access_academics\";i:17;s:18:\"access_person_mgmt\";}');");
            $this->execute("INSERT INTO `role` VALUES(10, 'Parent', '');");
            $this->execute("INSERT INTO `role` VALUES(11, 'Student', 'a:1:{i:0;s:21:\"access_student_portal\";}');");
            $this->execute("INSERT INTO `role` VALUES(12, 'Staff', 'a:32:{i:0;s:27:\"access_sql_interface_screen\";i:1;s:20:\"access_course_screen\";i:2;s:21:\"access_student_screen\";i:3;s:28:\"access_email_template_screen\";i:4;s:24:\"access_course_sec_screen\";i:5;s:23:\"course_sec_inquiry_only\";i:6;s:19:\"course_inquiry_only\";i:7;s:20:\"access_person_screen\";i:8;s:23:\"access_acad_prog_screen\";i:9;s:22:\"acad_prog_inquiry_only\";i:10;s:23:\"access_error_log_screen\";i:11;s:20:\"access_report_screen\";i:12;s:20:\"address_inquiry_only\";i:13;s:25:\"access_save_query_screens\";i:14;s:12:\"access_forms\";i:15;s:17:\"create_fac_record\";i:16;s:24:\"access_stu_roster_screen\";i:17;s:22:\"access_bill_tbl_screen\";i:18;s:17:\"add_crse_sec_bill\";i:19;s:11:\"import_data\";i:20;s:19:\"person_inquiry_only\";i:21;s:19:\"access_staff_screen\";i:22;s:18:\"staff_inquiry_only\";i:23;s:19:\"create_staff_record\";i:24;s:16:\"access_dashboard\";i:25;s:23:\"access_student_accounts\";i:26;s:16:\"access_academics\";i:27;s:22:\"access_human_resources\";i:28;s:17:\"submit_timesheets\";i:29;s:10:\"access_sql\";i:30;s:18:\"access_person_mgmt\";i:31;s:22:\"access_payment_gateway\";}');");
        endif;

        // Migration for table role_perms
        if (!$this->hasTable('role_perms')) :
            $table = $this->table('role_perms', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['signed' => false, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('roleID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('permID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('value', 'integer', ['default' => '0', 'limit' => MysqlAdapter::INT_TINY])
                ->addColumn('addDate', 'datetime', [])
                ->addIndex(['roleID', 'permID'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `role_perms` VALUES(156, 11, 92, 1, '2013-09-03 11:30:43');");
            $this->execute("INSERT INTO `role_perms` VALUES(201, 8, 21, 1, '2013-09-03 12:03:29');");
            $this->execute("INSERT INTO `role_perms` VALUES(238, 8, 23, 1, '2013-09-03 12:03:29');");
            $this->execute("INSERT INTO `role_perms` VALUES(268, 8, 22, 1, '2013-09-03 12:04:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(292, 8, 20, 1, '2013-09-03 12:04:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(309, 9, 84, 1, '2013-09-03 12:05:33');");
            $this->execute("INSERT INTO `role_perms` VALUES(310, 9, 107, 1, '2013-09-03 12:05:33');");
            $this->execute("INSERT INTO `role_perms` VALUES(462, 10, 176, 1, '2013-09-03 12:36:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(470, 12, 84, 1, '2013-09-03 12:37:49');");
            $this->execute("INSERT INTO `role_perms` VALUES(471, 12, 107, 1, '2013-09-03 12:37:49');");
            $this->execute("INSERT INTO `role_perms` VALUES(712, 13, 24, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(713, 13, 25, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(714, 13, 156, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(715, 13, 140, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(716, 13, 144, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(717, 13, 164, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(718, 13, 124, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(719, 13, 128, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(720, 13, 116, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(721, 13, 152, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(722, 13, 132, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(723, 13, 136, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(724, 13, 160, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(725, 13, 173, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(726, 13, 29, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(727, 13, 148, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(728, 13, 120, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(729, 13, 33, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(730, 13, 155, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(731, 13, 139, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(732, 13, 143, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(733, 13, 163, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(734, 13, 123, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(735, 13, 127, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(736, 13, 27, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(737, 13, 158, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(738, 13, 142, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(739, 13, 146, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(740, 13, 166, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(741, 13, 126, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(742, 13, 130, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(743, 13, 118, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(744, 13, 154, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(745, 13, 134, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(746, 13, 138, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(747, 13, 162, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(748, 13, 175, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(749, 13, 31, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(750, 13, 150, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(751, 13, 122, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(752, 13, 35, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(753, 13, 115, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(754, 13, 26, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(755, 13, 99, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(756, 13, 157, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(757, 13, 141, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(758, 13, 145, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(759, 13, 165, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(760, 13, 125, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(761, 13, 129, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(762, 13, 117, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(763, 13, 153, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(764, 13, 133, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(765, 13, 137, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(766, 13, 161, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(767, 13, 174, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(768, 13, 30, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(769, 13, 149, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(770, 13, 121, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(771, 13, 34, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(772, 13, 109, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(773, 13, 151, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(774, 13, 131, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(775, 13, 135, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(776, 13, 159, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(777, 13, 172, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(778, 13, 28, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(779, 13, 147, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(780, 13, 119, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(781, 13, 32, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(971, 11, 180, 1, '2013-09-04 04:51:52');");
            $this->execute("INSERT INTO `role_perms` VALUES(993, 9, 89, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(994, 9, 85, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(995, 9, 218, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(996, 9, 223, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(997, 9, 168, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(998, 9, 100, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(999, 9, 79, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1000, 9, 36, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1001, 9, 78, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1002, 9, 74, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1003, 9, 102, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1004, 9, 40, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1005, 9, 101, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1006, 9, 169, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1007, 9, 103, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1008, 9, 44, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1009, 9, 179, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1010, 9, 80, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1011, 9, 180, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1012, 9, 208, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1013, 9, 104, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1014, 9, 48, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(1015, 12, 89, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1016, 12, 85, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1017, 12, 218, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1018, 12, 223, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1019, 12, 100, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1020, 12, 79, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1021, 12, 36, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1022, 12, 78, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1023, 12, 74, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1024, 12, 102, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1025, 12, 40, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1026, 12, 101, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1027, 12, 103, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1028, 12, 44, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1029, 12, 179, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1030, 12, 80, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1031, 12, 180, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1032, 12, 208, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1033, 12, 104, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(1034, 12, 48, 1, '2014-02-13 09:56:35');");
        endif;

        // Migration for table room
        if (!$this->hasTable('room')) :
            $table = $this->table('room', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => 11])
                ->addColumn('roomCode', 'char', ['limit' => 22])
                ->addColumn('buildingCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('roomNumber', 'char', ['limit' => 22])
                ->addColumn('roomCap', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_TINY])
                ->addIndex(['roomCode'], ['unique' => true])
                ->addForeignKey('buildingCode', 'building', 'buildingCode', ['constraint' => 'room_buildingCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->create();

            $this->execute("INSERT INTO `room` VALUES(1, 'NULL', 'NULL', '', 0);");
        endif;

        // Migration for table saved_query
        if (!$this->hasTable('saved_query')) :
            $table = $this->table('saved_query', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('personID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('savedQueryName', 'string', ['limit' => 191])
                ->addColumn('savedQuery', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('purgeQuery', 'enum', ['default' => '0', 'values' => ['0', '1']])
                ->addColumn('shared', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR])
                ->addColumn('createdDate', 'date', [])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('personID', 'person', 'personID', ['constraint' => 'saved_query_personID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table school
        if (!$this->hasTable('school')) :
            $table = $this->table('school', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('schoolCode', 'char', ['limit' => 22])
                ->addColumn('schoolName', 'string', ['limit' => 191])
                ->addColumn('buildingCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['schoolCode'], ['unique' => true])
                ->addForeignKey('buildingCode', 'building', 'buildingCode', ['constraint' => 'school_buildingCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->create();

            $this->execute("INSERT INTO `school` VALUES(1, 'NULL', 'NULL', 'NULL', '$NOW');");
        endif;

        // Migration for table semester
        if (!$this->hasTable('semester')) :
            $table = $this->table('semester', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('acadYearCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('semCode', 'char', ['limit' => 22])
                ->addColumn('semName', 'string', ['limit' => 191])
                ->addColumn('semStartDate', 'date', ['null' => true])
                ->addColumn('semEndDate', 'date', ['null' => true])
                ->addColumn('active', 'enum', ['default' => '1', 'values' => ['1', '0']])
                ->addIndex(['semCode'], ['unique' => true])
                ->addForeignKey('acadYearCode', 'acad_year', 'acadYearCode', ['constraint' => 'semester_acadYearCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->create();

            $this->execute("INSERT INTO `semester` VALUES(1, 'NULL', 'NULL', '', '$NOW', '$NOW', '1');");
        endif;

        // Migration for table specialization
        if (!$this->hasTable('specialization')) :
            $table = $this->table('specialization', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('specCode', 'char', ['limit' => 22])
                ->addColumn('specName', 'string', ['limit' => 191])
                ->addIndex(['specCode'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `specialization` VALUES(1, 'NULL', '');");
        endif;

        // Migration for table staff
        if (!$this->hasTable('staff')) :
            $table = $this->table('staff', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('staffID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('schoolCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('buildingCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('officeCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('office_phone', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('deptCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('status', 'enum', ['default' => 'A', 'values' => ['A', 'I']])
                ->addColumn('tags', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('addDate', 'date', [])
                ->addColumn('approvedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['staffID'], ['unique' => true])
                ->addForeignKey('schoolCode', 'school', 'schoolCode', ['constraint' => 'staff_schoolCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('buildingCode', 'building', 'buildingCode', ['constraint' => 'staff_buildingCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('officeCode', 'room', 'roomCode', ['constraint' => 'staff_officeCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('deptCode', 'department', 'deptCode', ['constraint' => 'staff_deptCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('staffID', 'person', 'personID', ['constraint' => 'staff_staffID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('approvedBy', 'staff', 'staffID', ['constraint' => 'staff_approvedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();

            $this->execute("INSERT INTO `staff` VALUES(1, 1, 'NULL', 'NULL', 'NULL', '', 'NULL', 'A', '', '$NOW', 1, '$NOW');");
        endif;

        // Migration for table staff_meta
        if (!$this->hasTable('staff_meta')) :
            $table = $this->table('staff_meta', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('jobStatusCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('jobID', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('staffID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('supervisorID', 'integer', ['null' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('staffType', 'char', ['limit' => 8])
                ->addColumn('hireDate', 'date', ['null' => true])
                ->addColumn('startDate', 'date', ['null' => true])
                ->addColumn('endDate', 'date', ['null' => true])
                ->addColumn('addDate', 'date', ['null' => true])
                ->addColumn('approvedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('staffID', 'staff', 'staffID', ['constraint' => 'staff_meta_staffID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('supervisorID', 'staff', 'staffID', ['constraint' => 'staff_meta_supervisorID', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('jobStatusCode', 'job_status', 'typeCode', ['constraint' => 'staff_meta_jobStatusCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('jobID', 'job', 'id', ['constraint' => 'staff_meta_jobID', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('approvedBy', 'staff', 'staffID', ['constraint' => 'staff_meta_approvedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();

            $this->execute("INSERT INTO `staff_meta` VALUES(1, 'FT', 1, 1, 1, 'STA', '2013-11-04', '2013-11-18', 'NULL', '$NOW', 1, '$NOW');");
        endif;

        // Migration for table state
        if (!$this->hasTable('state')) :
            $table = $this->table('state', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['signed' => false, 'identity' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('code', 'char', ['limit' => 4])
                ->addColumn('name', 'string', ['limit' => 191])
                ->addIndex(['code'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `state` VALUES(1, 'AL', 'Alabama');");
            $this->execute("INSERT INTO `state` VALUES(2, 'AK', 'Alaska');");
            $this->execute("INSERT INTO `state` VALUES(3, 'AZ', 'Arizona');");
            $this->execute("INSERT INTO `state` VALUES(4, 'AR', 'Arkansas');");
            $this->execute("INSERT INTO `state` VALUES(5, 'CA', 'California');");
            $this->execute("INSERT INTO `state` VALUES(6, 'CO', 'Colorado');");
            $this->execute("INSERT INTO `state` VALUES(7, 'CT', 'Connecticut');");
            $this->execute("INSERT INTO `state` VALUES(8, 'DE', 'Delaware');");
            $this->execute("INSERT INTO `state` VALUES(9, 'DC', 'District of Columbia');");
            $this->execute("INSERT INTO `state` VALUES(10, 'FL', 'Florida');");
            $this->execute("INSERT INTO `state` VALUES(11, 'GA', 'Georgia');");
            $this->execute("INSERT INTO `state` VALUES(12, 'HI', 'Hawaii');");
            $this->execute("INSERT INTO `state` VALUES(13, 'ID', 'Idaho');");
            $this->execute("INSERT INTO `state` VALUES(14, 'IL', 'Illinois');");
            $this->execute("INSERT INTO `state` VALUES(15, 'IN', 'Indiana');");
            $this->execute("INSERT INTO `state` VALUES(16, 'IA', 'Iowa');");
            $this->execute("INSERT INTO `state` VALUES(17, 'KS', 'Kansas');");
            $this->execute("INSERT INTO `state` VALUES(18, 'KY', 'Kentucky');");
            $this->execute("INSERT INTO `state` VALUES(19, 'LA', 'Louisiana');");
            $this->execute("INSERT INTO `state` VALUES(20, 'ME', 'Maine');");
            $this->execute("INSERT INTO `state` VALUES(21, 'MD', 'Maryland');");
            $this->execute("INSERT INTO `state` VALUES(22, 'MA', 'Massachusetts');");
            $this->execute("INSERT INTO `state` VALUES(23, 'MI', 'Michigan');");
            $this->execute("INSERT INTO `state` VALUES(24, 'MN', 'Minnesota');");
            $this->execute("INSERT INTO `state` VALUES(25, 'MS', 'Mississippi');");
            $this->execute("INSERT INTO `state` VALUES(26, 'MO', 'Missouri');");
            $this->execute("INSERT INTO `state` VALUES(27, 'MT', 'Montana');");
            $this->execute("INSERT INTO `state` VALUES(28, 'NE', 'Nebraska');");
            $this->execute("INSERT INTO `state` VALUES(29, 'NV', 'Nevada');");
            $this->execute("INSERT INTO `state` VALUES(30, 'NH', 'New Hampshire');");
            $this->execute("INSERT INTO `state` VALUES(31, 'NJ', 'New Jersey');");
            $this->execute("INSERT INTO `state` VALUES(32, 'NM', 'New Mexico');");
            $this->execute("INSERT INTO `state` VALUES(33, 'NY', 'New York');");
            $this->execute("INSERT INTO `state` VALUES(34, 'NC', 'North Carolina');");
            $this->execute("INSERT INTO `state` VALUES(35, 'ND', 'North Dakota');");
            $this->execute("INSERT INTO `state` VALUES(36, 'OH', 'Ohio');");
            $this->execute("INSERT INTO `state` VALUES(37, 'OK', 'Oklahoma');");
            $this->execute("INSERT INTO `state` VALUES(38, 'OR', 'Oregon');");
            $this->execute("INSERT INTO `state` VALUES(39, 'PA', 'Pennsylvania');");
            $this->execute("INSERT INTO `state` VALUES(40, 'RI', 'Rhode Island');");
            $this->execute("INSERT INTO `state` VALUES(41, 'SC', 'South Carolina');");
            $this->execute("INSERT INTO `state` VALUES(42, 'SD', 'South Dakota');");
            $this->execute("INSERT INTO `state` VALUES(43, 'TN', 'Tennessee');");
            $this->execute("INSERT INTO `state` VALUES(44, 'TX', 'Texas');");
            $this->execute("INSERT INTO `state` VALUES(45, 'UT', 'Utah');");
            $this->execute("INSERT INTO `state` VALUES(46, 'VT', 'Vermont');");
            $this->execute("INSERT INTO `state` VALUES(47, 'VA', 'Virginia');");
            $this->execute("INSERT INTO `state` VALUES(48, 'WA', 'Washington');");
            $this->execute("INSERT INTO `state` VALUES(49, 'WV', 'West Virginia');");
            $this->execute("INSERT INTO `state` VALUES(50, 'WI', 'Wisconsin');");
            $this->execute("INSERT INTO `state` VALUES(51, 'WY', 'Wyoming');");
            $this->execute("INSERT INTO `state` VALUES(52, 'AB', 'Alberta');");
            $this->execute("INSERT INTO `state` VALUES(53, 'BC', 'British Columbia');");
            $this->execute("INSERT INTO `state` VALUES(54, 'MB', 'Manitoba');");
            $this->execute("INSERT INTO `state` VALUES(55, 'NL', 'Newfoundland');");
            $this->execute("INSERT INTO `state` VALUES(56, 'NB', 'New Brunswick');");
            $this->execute("INSERT INTO `state` VALUES(57, 'NS', 'Nova Scotia');");
            $this->execute("INSERT INTO `state` VALUES(58, 'NT', 'Northwest Territories');");
            $this->execute("INSERT INTO `state` VALUES(59, 'NU', 'Nunavut');");
            $this->execute("INSERT INTO `state` VALUES(60, 'ON', 'Ontario');");
            $this->execute("INSERT INTO `state` VALUES(61, 'PE', 'Prince Edward Island');");
            $this->execute("INSERT INTO `state` VALUES(62, 'QC', 'Quebec');");
            $this->execute("INSERT INTO `state` VALUES(63, 'SK', 'Saskatchewan');");
            $this->execute("INSERT INTO `state` VALUES(64, 'YT', 'Yukon Territory');");
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
        endif;

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
        endif;

        // Migration for table stu_acct_bill
        if (!$this->hasTable('stu_acct_bill')) :
            $table = $this->table('stu_acct_bill', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('billID', 'char', ['limit' => 22])
                ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('termCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('authCode', 'string', ['null' => true, 'limit' => 30])
                ->addColumn('stu_comments', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('staff_comments', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('balanceDue', 'enum', ['default' => '1', 'values' => ['1', '0']])
                ->addColumn('postedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('billingDate', 'date', [])
                ->addColumn('billTimeStamp', 'datetime', [])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['billID'], ['unique' => true])
                ->addForeignKey('stuID', 'student', 'stuID', ['constraint' => 'stu_acct_bill_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('termCode', 'term', 'termCode', ['constraint' => 'stu_acct_bill_termCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('postedBy', 'person', 'personID', ['constraint' => 'stu_acct_bill_postedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table stu_acct_fee
        if (!$this->hasTable('stu_acct_fee')) :
            $table = $this->table('stu_acct_fee', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('billID', 'char', ['limit' => 22])
                ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('termCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('type', 'char', ['limit' => 22])
                ->addColumn('description', 'string', ['limit' => 191])
                ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 2])
                ->addColumn('feeDate', 'date', [])
                ->addColumn('feeTimeStamp', 'datetime', [])
                ->addColumn('postedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('billID', 'stu_acct_bill', 'billID', ['constraint' => 'stu_acct_fee_billID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('postedBy', 'person', 'personID', ['constraint' => 'stu_acct_fee_postedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->addForeignKey('stuID', 'student', 'stuID', ['constraint' => 'stu_acct_fee_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('termCode', 'term', 'termCode', ['constraint' => 'stu_acct_fee_termCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table stu_acct_pp
        if (!$this->hasTable('stu_acct_pp')) :
            $table = $this->table('stu_acct_pp', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('termCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('payFrequency', 'enum', ['values' => ['1', '7', '14', '30', '365']])
                ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 2])
                ->addColumn('startDate', 'date', [])
                ->addColumn('endDate', 'date', ['null' => true])
                ->addColumn('comments', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('addDate', 'date', [])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('stuID', 'student', 'stuID', ['constraint' => 'stu_acct_pp_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('termCode', 'term', 'termCode', ['constraint' => 'stu_acct_pp_termCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->addForeignKey('addedBy', 'person', 'personID', ['constraint' => 'stu_acct_pp_addedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table stu_acct_tuition
        if (!$this->hasTable('stu_acct_tuition')) :
            $table = $this->table('stu_acct_tuition', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('termCode', 'char', ['null' => true, 'limit' => 22])
                ->addColumn('total', 'decimal', ['precision' => 10, 'scale' => 2])
                ->addColumn('postedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('tuitionTimeStamp', 'datetime', [])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('postedBy', 'person', 'personID', ['constraint' => 'stu_acct_tuition_postedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->addForeignKey('stuID', 'student', 'stuID', ['constraint' => 'stu_acct_tuition_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('termCode', 'term', 'termCode', ['constraint' => 'stu_acct_tuition_termCode', 'delete' => 'SET_NULL', 'update' => 'CASCADE'])
                ->create();
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
        endif;

        // Migration for table stu_rgn_cart
        if (!$this->hasTable('stu_rgn_cart')) :
            $table = $this->table('stu_rgn_cart', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('courseSecID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('deleteDate', 'date', [])
                ->addIndex(['stuID', 'courseSecID'], ['unique' => true])
                ->create();
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
        endif;

        // Migration for table student
        if (!$this->hasTable('student')) :
            $table = $this->table('student', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('stuID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('status', 'enum', ['default' => 'A', 'values' => ['A', 'I']])
                ->addColumn('tags', 'string', ['null' => true, 'limit' => 191])
                ->addColumn('addDate', 'datetime', [])
                ->addColumn('approvedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['stuID'], ['unique' => true])
                ->addIndex(['status'])
                ->addForeignKey('stuID', 'person', 'personID', ['constraint' => 'student_stuID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('approvedBy', 'person', 'personID', ['constraint' => 'student_approvedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table subject
        if (!$this->hasTable('subject')) :
            $table = $this->table('subject', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('subjectCode', 'char', ['limit' => 22])
                ->addColumn('subjectName', 'string', ['limit' => 191])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addIndex(['subjectCode'], ['unique' => true])
                ->create();

            $this->execute("INSERT INTO `subject` VALUES(1, 'NULL', '', '$NOW');");
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
        endif;

        // Migration for table term
        if (!$this->hasTable('term')) :
            $table = $this->table('term', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('semCode', 'char', ['limit' => 22])
                ->addColumn('termCode', 'char', ['limit' => 22])
                ->addColumn('termName', 'string', ['default' => null, 'limit' => 191])
                ->addColumn('reportingTerm', 'char', ['limit' => 22])
                ->addColumn('dropAddEndDate', 'date', ['null' => true])
                ->addColumn('termStartDate', 'date', ['null' => true])
                ->addColumn('termEndDate', 'date', ['null' => true])
                ->addColumn('active', 'enum', ['default' => '1', 'values' => ['1', '0']])
                ->addIndex(['termCode'], ['unique' => true])
                ->addForeignKey('semCode', 'semester', 'semCode', ['constraint' => 'term_semCode', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->create();

            $this->execute("INSERT INTO `term` VALUES(1, 'NULL', 'NULL', '', '', '$NOW', '$NOW', '$NOW', '1');");
        endif;

        // Migration for table timesheet
        if (!$this->hasTable('timesheet')) :
            $table = $this->table('timesheet', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('employeeID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('jobID', 'integer', ['limit' => MysqlAdapter::INT_REGULAR])
                ->addColumn('workWeek', 'date', ['null' => true])
                ->addColumn('startDateTime', 'datetime', [])
                ->addColumn('endDateTime', 'datetime', ['null' => true])
                ->addColumn('note', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('status', 'enum', ['default' => 'P', 'values' => ['P', 'R', 'A']])
                ->addColumn('addDate', 'string', ['limit' => 80])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('approvedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('employeeID', 'staff', 'staffID', ['constraint' => 'timesheet_employeeID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('addedBy', 'staff', 'staffID', ['constraint' => 'timesheet_addedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
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

        // Migration for table transfer_equivalent
        if (!$this->hasTable('transfer_equivalent')) :
            $table = $this->table('transfer_equivalent', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('extrID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('courseID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('startDate', 'date', [])
                ->addColumn('endDate', 'date', ['null' => true])
                ->addColumn('grade', 'char', ['limit' => 6])
                ->addColumn('comment', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('addDate', 'date', [])
                ->addColumn('LastUpdate', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
                ->addForeignKey('extrID', 'external_course', 'id', ['constraint' => 'transfer_equivalent_extrID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('courseID', 'course', 'courseID', ['constraint' => 'transfer_equivalent_courseID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('addedBy', 'person', 'personID', ['constraint' => 'transfer_equivalent_addedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
        endif;

        // Migration for table transfer_credit
        if (!$this->hasTable('transfer_credit')) :
            $table = $this->table('transfer_credit', ['id' => false, 'primary_key' => 'id', 'encoding' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci']);
            $table
                ->addColumn('id', 'integer', ['identity' => true, 'limit' => MysqlAdapter::INT_BIG])
                ->addColumn('equivID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('stacID', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('addedBy', 'integer', ['limit' => MysqlAdapter::INT_BIG])
                ->addColumn('addDate', 'date', [])
                ->addForeignKey('equivID', 'transfer_equivalent', 'id', ['constraint' => 'transfer_credit_equivID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('stacID', 'stac', 'id', ['constraint' => 'transfer_credit_stacID', 'delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addForeignKey('addedBy', 'person', 'personID', ['constraint' => 'transfer_credit_addedBy', 'delete' => 'RESTRICT', 'update' => 'CASCADE'])
                ->create();
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

        $this->execute("SET FOREIGN_KEY_CHECKS=1;");

        if (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false && !file_exists('.htaccess')) {
            $f = fopen('.htaccess', 'w');
            fclose($f);

            $htaccess_file = <<<EOF
<IfModule mod_rewrite.c>
RewriteEngine On

# Some hosts may require you to use the `RewriteBase` directive.
# If you need to use the `RewriteBase` directive, it should be the
# absolute physical path to the directory that contains this htaccess file.
#
# RewriteBase /

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php [L]
</IfModule>

EOF;
            file_put_contents('.htaccess', $htaccess_file);
        }

        if (!file_exists('config.php')) {
            copy('config.sample.php', 'config.php');
        }
        $file = 'config.php';
        $config = file_get_contents($file);

        $config = str_replace('{product}', 'eduTrac SIS', $config);
        $config = str_replace('{release}', trim(file_get_contents('RELEASE')), $config);
        $config = str_replace('{datenow}', $NOW, $config);
        $config = str_replace('{hostname}', $host, $config);
        $config = str_replace('{database}', $name, $config);
        $config = str_replace('{username}', $user, $config);
        $config = str_replace('{password}', $pass, $config);

        file_put_contents($file, $config);
    }
}
