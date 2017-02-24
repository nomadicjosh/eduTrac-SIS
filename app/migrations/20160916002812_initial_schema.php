<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class InitialSchema extends AbstractMigration
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

        $host = $this->adapter->getOption('host');
        $name = $this->adapter->getOption('name');
        $user = $this->adapter->getOption('user');
        $pass = $this->adapter->getOption('pass');

        $NOW = date("Y-m-d H:i:s");

        // Automatically created phinx migration commands for tables from database et
        // Migration for table acad_program
        if (!$this->hasTable('acad_program')) :
            $table = $this->table('acad_program', array('id' => false, 'primary_key' => 'acadProgID'));
            $table
                ->addColumn('acadProgID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('acadProgCode', 'string', array('limit' => 20))
                ->addColumn('acadProgTitle', 'string', array('limit' => 180))
                ->addColumn('programDesc', 'string', array('limit' => 255))
                ->addColumn('currStatus', 'string', array('limit' => 1))
                ->addColumn('statusDate', 'date', array())
                ->addColumn('deptCode', 'string', array('limit' => 11))
                ->addColumn('schoolCode', 'string', array('limit' => 11))
                ->addColumn('acadYearCode', 'string', array('limit' => 11))
                ->addColumn('startDate', 'date', array())
                ->addColumn('endDate', 'date', array('null' => true))
                ->addColumn('degreeCode', 'string', array('limit' => 11))
                ->addColumn('ccdCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('majorCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('minorCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('specCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('acadLevelCode', 'string', array('limit' => 11))
                ->addColumn('cipCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('locationCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('approvedDate', 'date', array())
                ->addColumn('approvedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('acadProgCode', 'acadLevelCode', 'deptCode', 'schoolCode', 'acadYearCode', 'degreeCode', 'ccdCode', 'majorCode', 'minorCode', 'specCode', 'cipCode', 'locationCode'))
                ->addForeignKey('deptCode', 'department', 'deptCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('locationCode', 'location', 'locationCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('schoolCode', 'school', 'schoolCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('acadYearCode', 'acad_year', 'acadYearCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('degreeCode', 'degree', 'degreeCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('ccdCode', 'ccd', 'ccdCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('majorCode', 'major', 'majorCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('minorCode', 'minor', 'minorCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('specCode', 'specialization', 'specCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('cipCode', 'cip', 'cipCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;


        // Migration for table acad_year
        if (!$this->hasTable('acad_year')) :
            $table = $this->table('acad_year', array('id' => false, 'primary_key' => 'acadYearID'));
            $table
                ->addColumn('acadYearID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('acadYearCode', 'string', array('limit' => 11))
                ->addColumn('acadYearDesc', 'string', array('limit' => 30))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('acadYearCode'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `acad_year` VALUES(1, 'NULL', 'Null', '$NOW');");
        endif;


        // Migration for table activity_log
        if (!$this->hasTable('activity_log')) :
            $table = $this->table('activity_log', array('id' => false, 'primary_key' => 'id'));
            $table
                ->addColumn('id', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('action', 'string', array('limit' => 50))
                ->addColumn('process', 'string', array('limit' => 255))
                ->addColumn('record', 'text', array('null' => true, 'limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('uname', 'string', array('limit' => 180))
                ->addColumn('created_at', 'datetime', array())
                ->addColumn('expires_at', 'datetime', array())
                ->create();
        endif;

        // Migration for table address
        if (!$this->hasTable('address')) :
            $table = $this->table('address', array('id' => false, 'primary_key' => 'addressID'));
            $table
                ->addColumn('addressID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('personID', 'integer', array('limit' => MysqlAdapter::INT_BIG))
                ->addColumn('address1', 'string', array('limit' => 80))
                ->addColumn('address2', 'string', array('limit' => 80))
                ->addColumn('city', 'string', array('limit' => 30))
                ->addColumn('state', 'string', array('limit' => 2))
                ->addColumn('zip', 'string', array('limit' => 10))
                ->addColumn('country', 'string', array('limit' => 2))
                ->addColumn('addressType', 'string', array('limit' => 2))
                ->addColumn('startDate', 'date', array())
                ->addColumn('endDate', 'date', array())
                ->addColumn('addressStatus', 'string', array('limit' => 2))
                ->addColumn('phone1', 'string', array('limit' => 15))
                ->addColumn('phone2', 'string', array('limit' => 15))
                ->addColumn('ext1', 'string', array('limit' => 5))
                ->addColumn('ext2', 'string', array('limit' => 5))
                ->addColumn('phoneType1', 'string', array('limit' => 3))
                ->addColumn('phoneType2', 'string', array('limit' => 3))
                ->addColumn('email1', 'string', array('limit' => 80))
                ->addColumn('email2', 'string', array('limit' => 80))
                ->addColumn('addDate', 'date', array())
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('personID', 'addedBy'))
                ->addForeignKey('personID', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('addedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();

            $this->execute("INSERT INTO `address` VALUES(1, 1, '125 Montgomery Street', '#2', 'Cambridge', 'MA', '02140', 'US', 'P', '2013-08-01', '0000-00-00', 'C', '6718997836', '', '', '', 'CEL', '', 'etsis@campus.com', '', '$NOW', 00000001, '$NOW');");
        endif;


        // Migration for table application
        if (!$this->hasTable('application')) :
            $table = $this->table('application', array('id' => false, 'primary_key' => 'applID'));
            $table
                ->addColumn('applID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('personID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('acadProgCode', 'string', array('limit' => 20))
                ->addColumn('startTerm', 'string', array('limit' => 11))
                ->addColumn('admitStatus', 'string', array('null' => true, 'limit' => 2))
                ->addColumn('exam', 'string', array('limit' => 5))
                ->addColumn('PSAT_Verbal', 'string', array('limit' => 5))
                ->addColumn('PSAT_Math', 'string', array('limit' => 5))
                ->addColumn('SAT_Verbal', 'string', array('limit' => 5))
                ->addColumn('SAT_Math', 'string', array('limit' => 5))
                ->addColumn('ACT_English', 'string', array('limit' => 5))
                ->addColumn('ACT_Math', 'string', array('limit' => 5))
                ->addColumn('applStatus', 'enum', array('values' => array('Pending', 'Under Review', 'Accepted', 'Not Accepted')))
                ->addColumn('applDate', 'date', array())
                ->addColumn('appl_comments', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('staff_comments', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('addDate', 'date', array())
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('personID', 'acadProgCode'), array('unique' => true))
                ->addIndex(array('startTerm', 'addedBy', 'acadProgcode'))
                ->addForeignKey('acadProgCode', 'acad_program', 'acadProgCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('startTerm', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('addedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('personID', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table assignment
        if (!$this->hasTable('assignment')) :
            $table = $this->table('assignment', array('id' => false, 'primary_key' => 'assignID'));
            $table
                ->addColumn('assignID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('courseSecID', 'integer', array('signed' => true, 'null' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('facID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('shortName', 'string', array('limit' => 6))
                ->addColumn('title', 'string', array('limit' => 180))
                ->addColumn('dueDate', 'date', array())
                ->addColumn('addDate', 'date', array())
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('assignID', 'courseSecID'))
                ->addForeignKey('courseSecID', 'course_sec', 'courseSecID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table attendance
        if (!$this->hasTable('attendance')) :
            $table = $this->table('attendance', array('id' => false, 'primary_key' => 'id'));
            $table
                ->addColumn('id', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('courseSecID', 'integer', array('signed' => true, 'null' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('courseSecCode', 'string', array('limit' => 50))
                ->addColumn('stuID', 'integer', array())
                ->addColumn('status', 'string', array('null' => true, 'limit' => 1))
                ->addColumn('date', 'date', array('null' => true))
                ->addIndex(array('courseSecCode', 'stuID', 'date', 'termCode'), array('unique' => true))
                ->addIndex(array('stuID', 'termCode'))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->create();
        endif;

        // Migration for table billing_table
        if (!$this->hasTable('billing_table')) :
            $table = $this->table('billing_table', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('name', 'string', array('limit' => 180))
                ->addColumn('amount', 'decimal', array('signed' => true, 'precision' => 6, 'scale' => 2, 'default' => '0.00'))
                ->addColumn('status', 'enum', array('default' => 'A', 'values' => array('A', 'I')))
                ->addColumn('addDate', 'date', array())
                ->create();
        endif;


        // Migration for table building
        if (!$this->hasTable('building')) :
            $table = $this->table('building', array('id' => false, 'primary_key' => 'buildingID'));
            $table
                ->addColumn('buildingID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('buildingCode', 'string', array('limit' => 11))
                ->addColumn('buildingName', 'string', array('limit' => 180))
                ->addColumn('locationCode', 'string', array('null' => true, 'limit' => 11))
                ->addIndex(array('buildingCode'), array('unique' => true))
                ->addIndex(array('locationCode'))
                ->addForeignKey('locationCode', 'location', 'locationCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();

            $this->execute("INSERT INTO `building` VALUES(1, 'NULL', '', 'NULL');");
        endif;

        // Migration for table ccd
        if (!$this->hasTable('ccd')) :
            $table = $this->table('ccd', array('id' => false, 'primary_key' => 'ccdID'));
            $table
                ->addColumn('ccdID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('ccdCode', 'string', array('limit' => 11))
                ->addColumn('ccdName', 'string', array('limit' => 80))
                ->addColumn('addDate', 'date', array())
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('ccdCode'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `ccd` VALUES(1, 'NULL', 'Null', '$NOW', '$NOW');");
        endif;

        // Migration for table cip
        if (!$this->hasTable('cip')) :
            $table = $this->table('cip', array('id' => false, 'primary_key' => 'cipID'));
            $table
                ->addColumn('cipID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('cipCode', 'string', array('limit' => 11))
                ->addColumn('cipName', 'string', array('limit' => 80))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('cipCode'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `cip` VALUES(1, 'NULL', 'Null', '$NOW');");
        endif;

        // Migration for table country
        if (!$this->hasTable('country')) :
            $table = $this->table('country', array('id' => false, 'primary_key' => 'country_id'));
            $table
                ->addColumn('country_id', 'integer', array('signed' => true, 'identity' => true, 'limit' => 5))
                ->addColumn('iso2', 'char', array('null' => true, 'limit' => 2))
                ->addColumn('short_name', 'string', array('default' => '', 'limit' => 80))
                ->addColumn('long_name', 'string', array('default' => '', 'limit' => 80))
                ->addColumn('iso3', 'char', array('null' => true, 'limit' => 3))
                ->addColumn('numcode', 'string', array('null' => true, 'limit' => 6))
                ->addColumn('un_member', 'string', array('null' => true, 'limit' => 12))
                ->addColumn('calling_code', 'string', array('null' => true, 'limit' => 8))
                ->addColumn('cctld', 'string', array('null' => true, 'limit' => 5))
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
            $table = $this->table('course', array('id' => false, 'primary_key' => 'courseID'));
            $table
                ->addColumn('courseID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('courseNumber', 'integer', array('signed' => true, 'limit' => 6))
                ->addColumn('courseCode', 'string', array('limit' => 25))
                ->addColumn('subjectCode', 'string', array('limit' => 11))
                ->addColumn('deptCode', 'string', array('limit' => 11))
                ->addColumn('courseDesc', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('creditType', 'string', array('default' => 'I', 'limit' => 6))
                ->addColumn('minCredit', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('maxCredit', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('increCredit', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('courseLevelCode', 'string', array('limit' => 5))
                ->addColumn('acadLevelCode', 'string', array('limit' => 4))
                ->addColumn('courseShortTitle', 'string', array('limit' => 25))
                ->addColumn('courseLongTitle', 'string', array('limit' => 60))
                ->addColumn('preReq', 'text', array('limit' => MysqlAdapter::TEXT_REGULAR))
                ->addColumn('allowAudit', 'enum', array('default' => '0', 'values' => array('1', '0')))
                ->addColumn('allowWaitlist', 'enum', array('default' => '0', 'values' => array('1', '0')))
                ->addColumn('minEnroll', 'integer', array('signed' => true, 'limit' => 3))
                ->addColumn('seatCap', 'integer', array('signed' => true, 'limit' => 3))
                ->addColumn('startDate', 'date', array())
                ->addColumn('endDate', 'date', array('null' => true))
                ->addColumn('currStatus', 'string', array('limit' => 1))
                ->addColumn('statusDate', 'date', array('default' => '0000-00-00'))
                ->addColumn('approvedDate', 'date', array('default' => '0000-00-00'))
                ->addColumn('approvedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('courseCode', 'courseLevelCode', 'acadLevelCode', 'approvedBy', 'deptCode', 'subjectCode'))
                ->addForeignKey('subjectCode', 'subject', 'subjectCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('deptCode', 'department', 'deptCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('approvedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table course_sec
        if (!$this->hasTable('course_sec')) :
            $table = $this->table('course_sec', array('id' => false, 'primary_key' => 'courseSecID'));
            $table
                ->addColumn('courseSecID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('sectionNumber', 'string', array('limit' => 5))
                ->addColumn('courseSecCode', 'string', array('limit' => 50))
                ->addColumn('courseSection', 'string', array('limit' => 60))
                ->addColumn('buildingCode', 'string', array('default' => 'NULL', 'limit' => 11))
                ->addColumn('roomCode', 'string', array('default' => 'NULL', 'limit' => 11))
                ->addColumn('locationCode', 'string', array('limit' => 11))
                ->addColumn('courseLevelCode', 'string', array('limit' => 5))
                ->addColumn('acadLevelCode', 'string', array('limit' => 4))
                ->addColumn('deptCode', 'string', array('limit' => 11))
                ->addColumn('facID', 'integer', array('limit' => MysqlAdapter::INT_BIG))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('courseID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('courseCode', 'string', array('limit' => 25))
                ->addColumn('preReqs', 'text', array('limit' => MysqlAdapter::TEXT_REGULAR))
                ->addColumn('secShortTitle', 'string', array('limit' => 60))
                ->addColumn('startDate', 'date', array())
                ->addColumn('endDate', 'date', array())
                ->addColumn('startTime', 'string', array('limit' => 8))
                ->addColumn('endTime', 'string', array('limit' => 8))
                ->addColumn('dotw', 'string', array('limit' => 7))
                ->addColumn('minCredit', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('maxCredit', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('increCredit', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('ceu', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('instructorMethod', 'string', array('limit' => 180))
                ->addColumn('instructorLoad', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('contactHours', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('webReg', 'enum', array('default' => '1', 'values' => array('1', '0')))
                ->addColumn('courseFee', 'decimal', array('signed' => true, 'precision' => 10, 'scale' => 2, 'default' => '0.00'))
                ->addColumn('labFee', 'decimal', array('signed' => true, 'precision' => 10, 'scale' => 2, 'default' => '0.00'))
                ->addColumn('materialFee', 'decimal', array('signed' => true, 'precision' => 10, 'scale' => 2, 'default' => '0.00'))
                ->addColumn('secType', 'enum', array('default' => 'ONC', 'values' => array('ONL', 'HB', 'ONC')))
                ->addColumn('currStatus', 'string', array('limit' => 1))
                ->addColumn('statusDate', 'date', array())
                ->addColumn('comment', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('approvedDate', 'date', array())
                ->addColumn('approvedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('courseSection'), array('unique' => true))
                ->addIndex(array('courseSecCode', 'currStatus', 'approvedBy', 'facID', 'buildingCode', 'roomCode', 'locationCode', 'deptCode', 'termCode', 'courseCode', 'courseID'))
                ->addForeignKey('buildingCode', 'building', 'buildingCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('roomCode', 'room', 'roomCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('locationCode', 'location', 'locationCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('deptCode', 'department', 'deptCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('courseID', 'course', 'courseID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('approvedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table degree
        if (!$this->hasTable('degree')) :
            $table = $this->table('degree', array('id' => false, 'primary_key' => 'degreeID'));
            $table
                ->addColumn('degreeID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('degreeCode', 'string', array('limit' => 11))
                ->addColumn('degreeName', 'string', array('limit' => 180))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('degreeCode'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `degree` VALUES(00000000001, 'NULL', '', '$NOW');");
        endif;

        // Migration for table department
        if (!$this->hasTable('department')) :
            $table = $this->table('department', array('id' => false, 'primary_key' => 'deptID'));
            $table
                ->addColumn('deptID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('deptTypeCode', 'string', array('limit' => 6))
                ->addColumn('deptCode', 'string', array('limit' => 11))
                ->addColumn('deptName', 'string', array('limit' => 180))
                ->addColumn('deptEmail', 'string', array('limit' => 180))
                ->addColumn('deptPhone', 'string', array('limit' => 20))
                ->addColumn('deptDesc', 'string', array('limit' => 255))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('deptCode'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `department` VALUES(1, 'NULL', 'NULL', 'Null', '', '', 'Default', '$NOW');");
        endif;

        // Migration for table email_hold
        if (!$this->hasTable('email_hold')) :
            $table = $this->table('email_hold', array('id' => false, 'primary_key' => 'id'));
            $table
                ->addColumn('id', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('personID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('queryID', 'integer', array('signed' => true, 'limit' => 11))
                ->addColumn('fromName', 'string', array('limit' => 118))
                ->addColumn('fromEmail', 'string', array('limit' => 118))
                ->addColumn('subject', 'string', array('limit' => 118))
                ->addColumn('body', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('processed', 'enum', array('default' => '0', 'values' => array('1', '0')))
                ->addColumn('dateTime', 'datetime', array())
                ->create();
        endif;

        // Migration for table email_queue
        if (!$this->hasTable('email_queue')) :
            $table = $this->table('email_queue', array('id' => false, 'primary_key' => 'id'));
            $table
                ->addColumn('id', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('holdID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('personID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('fromName', 'string', array('limit' => 118))
                ->addColumn('fromEmail', 'string', array('limit' => 118))
                ->addColumn('uname', 'string', array('limit' => 118))
                ->addColumn('email', 'string', array('limit' => 118))
                ->addColumn('fname', 'string', array('limit' => 118))
                ->addColumn('lname', 'string', array('limit' => 118))
                ->addColumn('subject', 'string', array('limit' => 150))
                ->addColumn('body', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('sent', 'enum', array('default' => '0', 'values' => array('1', '0')))
                ->addColumn('sentDate', 'date', array())
                ->create();
        endif;

        // Migration for table email_template
        if (!$this->hasTable('email_template')) :
            $table = $this->table('email_template', array('id' => false, 'primary_key' => 'etID'));
            $table
                ->addColumn('etID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('deptCode', 'string', array('limit' => 11))
                ->addColumn('email_key', 'string', array('limit' => 30))
                ->addColumn('email_name', 'string', array('limit' => 30))
                ->addColumn('email_value', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('deptCode'), array('unique' => true))
                ->addForeignKey('deptCode', 'department', 'deptCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table error
        if (!$this->hasTable('error')) :
            $table = $this->table('error', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('type', 'integer', array('signed' => true, 'limit' => 4))
                ->addColumn('time', 'integer', array('signed' => true, 'limit' => 10))
                ->addColumn('string', 'string', array('limit' => 512))
                ->addColumn('file', 'string', array('limit' => 255))
                ->addColumn('line', 'integer', array('signed' => true, 'limit' => 6))
                ->addColumn('addDate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->create();
        endif;

        // Migration for table event
        if (!$this->hasTable('event')) :
            $table = $this->table('event', array('id' => false, 'primary_key' => 'eventID'));
            $table
                ->addColumn('eventID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('eventType', 'string', array('limit' => 255))
                ->addColumn('catID', 'integer', array('signed' => true, 'limit' => 11))
                ->addColumn('requestor', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('roomCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('termCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('title', 'string', array('limit' => 120))
                ->addColumn('description', 'text', array('null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR))
                ->addColumn('weekday', 'integer', array('signed' => true, 'null' => true, 'limit' => 1))
                ->addColumn('startDate', 'date', array('null' => true))
                ->addColumn('startTime', 'time', array('null' => true))
                ->addColumn('endTime', 'time', array('null' => true))
                ->addColumn('repeats', 'integer', array('signed' => true, 'null' => true, 'limit' => MysqlAdapter::INT_TINY))
                ->addColumn('repeatFreq', 'integer', array('signed' => true, 'null' => true, 'limit' => MysqlAdapter::INT_TINY))
                ->addColumn('status', 'enum', array('default' => 'A', 'values' => array('A', 'I')))
                ->addColumn('addDate', 'date', array())
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('roomCode', 'termCode', 'title', 'weekday', 'startDate', 'startTime', 'endTime'), array('unique' => true))
                ->addIndex(array('termCode', 'requestor', 'addedBy', 'catID'))
                ->addForeignKey('catID', 'event_category', 'catID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('requestor', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('roomCode', 'room', 'roomCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('addedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table event_category
        if (!$this->hasTable('event_category')) :
            $table = $this->table('event_category', array('id' => false, 'primary_key' => 'catID'));
            $table
                ->addColumn('catID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('cat_name', 'string', array('limit' => 30))
                ->addColumn('bgcolor', 'string', array('default' => '#000000', 'limit' => 11))
                ->create();

            $this->execute("INSERT INTO `event_category` VALUES(1, 'Course', '#8C7BC6');");
            $this->execute("INSERT INTO `event_category` VALUES(2, 'Meeting', '#00CCFF');");
            $this->execute("INSERT INTO `event_category` VALUES(3, 'Conference', '#E66000');");
            $this->execute("INSERT INTO `event_category` VALUES(4, 'Event', '#61D0AF');");
        endif;

        // Migration for table event_meta
        if (!$this->hasTable('event_meta')) :
            $table = $this->table('event_meta', array('id' => false, 'primary_key' => 'eventMetaID'));
            $table
                ->addColumn('eventMetaID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('eventID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('roomCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('requestor', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('start', 'datetime', array('null' => true))
                ->addColumn('end', 'datetime', array('null' => true))
                ->addColumn('title', 'string', array('limit' => 120))
                ->addColumn('description', 'text', array('null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR))
                ->addColumn('addDate', 'date', array())
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('eventID', 'roomCode', 'start', 'end', 'title'), array('unique' => true))
                ->addIndex(array('roomCode', 'requestor', 'addedBy'))
                ->addForeignKey('eventID', 'event', 'eventID', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
                ->addForeignKey('roomCode', 'room', 'roomCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('requestor', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('addedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table event_request
        if (!$this->hasTable('event_request')) :
            $table = $this->table('event_request', array('id' => false, 'primary_key' => 'requestID'));
            $table
                ->addColumn('requestID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('eventType', 'string', array('limit' => 255))
                ->addColumn('catID', 'integer', array('signed' => true, 'limit' => 11))
                ->addColumn('requestor', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('roomCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('termCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('title', 'string', array('limit' => 120))
                ->addColumn('description', 'text', array('null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR))
                ->addColumn('weekday', 'integer', array('signed' => true, 'limit' => 1))
                ->addColumn('startDate', 'date', array())
                ->addColumn('endDate', 'date', array())
                ->addColumn('startTime', 'time', array())
                ->addColumn('endTime', 'time', array())
                ->addColumn('repeats', 'integer', array('signed' => true, 'null' => true, 'limit' => MysqlAdapter::INT_TINY))
                ->addColumn('repeatFreq', 'integer', array('signed' => true, 'null' => true, 'limit' => MysqlAdapter::INT_TINY))
                ->addColumn('addDate', 'date', array())
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('roomCode', 'termCode', 'title', 'weekday', 'startDate', 'startTime', 'endTime'), array('unique' => true))
                ->addIndex(array('termCode', 'requestor'))
                ->addForeignKey('requestor', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('roomCode', 'room', 'roomCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table external_course
        if (!$this->hasTable('external_course')) :
            $table = $this->table('external_course', array('id' => false, 'primary_key' => 'extrID'));
            $table
                ->addColumn('extrID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('courseTitle', 'string', array('limit' => 180))
                ->addColumn('instCode', 'string', array('limit' => 11))
                ->addColumn('courseName', 'string', array('limit' => 60))
                ->addColumn('term', 'string', array('limit' => 11))
                ->addColumn('credits', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 2))
                ->addColumn('currStatus', 'enum', array('default' => 'A', 'values' => array('A', 'I', 'P', 'O')))
                ->addColumn('statusDate', 'date', array())
                ->addColumn('minGrade', 'string', array('limit' => 2))
                ->addColumn('comments', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('startDate', 'date', array())
                ->addColumn('endDate', 'date', array())
                ->addColumn('addDate', 'date', array())
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('instCode', 'addedBy'))
                ->addForeignKey('instCode', 'institution', 'fice_ceeb', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('addedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table gl_account
        if (!$this->hasTable('gl_account')) :
            $table = $this->table('gl_account', array('id' => false, 'primary_key' => 'glacctID'));
            $table
                ->addColumn('glacctID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('gl_acct_number', 'string', array('limit' => 200))
                ->addColumn('gl_acct_name', 'string', array('limit' => 200))
                ->addColumn('gl_acct_type', 'string', array('limit' => 200))
                ->addColumn('gl_acct_memo', 'string', array('null' => true, 'limit' => 200))
                ->addIndex(array('gl_acct_number'), array('unique' => true))
                ->create();
        endif;

        // Migration for table gl_journal_entry
        if (!$this->hasTable('gl_journal_entry')) :
            $table = $this->table('gl_journal_entry', array('id' => false, 'primary_key' => 'jeID'));
            $table
                ->addColumn('jeID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('gl_jentry_date', 'date', array())
                ->addColumn('gl_jentry_manual_id', 'string', array('null' => true, 'limit' => 100))
                ->addColumn('gl_jentry_title', 'string', array('null' => true, 'limit' => 100))
                ->addColumn('gl_jentry_description', 'string', array('null' => true, 'limit' => 200))
                ->addColumn('gl_jentry_personID', 'integer', array('signed' => true, 'null' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->create();
        endif;

        // Migration for table gl_transaction
        if (!$this->hasTable('gl_transaction')) :
            $table = $this->table('gl_transaction', array('id' => false, 'primary_key' => 'trID'));
            $table
                ->addColumn('trID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('jeID', 'integer', array('signed' => true, 'null' => true))
                ->addColumn('accountID', 'integer', array('signed' => true, 'null' => true))
                ->addColumn('gl_trans_date', 'date', array('null' => true))
                ->addColumn('gl_trans_memo', 'string', array('null' => true, 'limit' => 400))
                ->addColumn('gl_trans_debit', 'decimal', array('signed' => true, 'precision' => 10, 'scale' => 2, 'null' => true))
                ->addColumn('gl_trans_credit', 'decimal', array('signed' => true, 'precision' => 10, 'scale' => 2, 'null' => true))
                ->addIndex(array('jeID'))
                ->addForeignKey('jeID', 'gl_journal_entry', 'jeID', array('delete' => 'CASCADE', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table grade_scale
        if (!$this->hasTable('grade_scale')) :
            $table = $this->table('grade_scale', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('grade', 'string', array('limit' => 2))
                ->addColumn('percent', 'string', array('limit' => 10))
                ->addColumn('points', 'decimal', array('signed' => true, 'precision' => 6, 'scale' => 2))
                ->addColumn('count_in_gpa', 'enum', array('default' => '0', 'values' => array('1', '0')))
                ->addColumn('status', 'enum', array('default' => '1', 'values' => array('1', '0')))
                ->addColumn('description', 'text', array('limit' => MysqlAdapter::TEXT_REGULAR))
                ->create();

            $this->execute("INSERT INTO `grade_scale` VALUES(00000000001, 'A+', '97-100', '4.00', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000002, 'A', '93-96', '4.00', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000003, 'A-', '90-92', '3.70', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000004, 'B+', '87-89', '3.30', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000005, 'B', '83-86', '3.00', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000006, 'B-', '80-82', '2.70', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000007, 'P', '80-82', '2.70', '1', '1', 'Minimum for Pass/Fail courses');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000008, 'C+', '77-79', '2.30', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000009, 'C', '73-76', '2.00', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000010, 'C-', '70-72', '1.70', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000011, 'D+', '67-69', '1.30', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000012, 'D', '65-66', '1.00', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000013, 'F', 'Below 65', '0.00', '1', '1', '');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000014, 'I', '0', '0.00', '0', '1', 'Incomplete grades');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000015, 'AW', '0', '0.00', '0', '1', '\"AW\" is an administrative grade assigned to students who have attended no more than the first two classes, but who have not officially dropped or withdrawn from the course. Does not count against GPA.');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000016, 'NA', '0', '0.00', '0', '1', '\"NA\" is an administrative grade assigned to students who are officially registered for the course and whose name appears on the grade roster, but who have never attended class. Does not count against GPA.');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000017, 'W', '0', '0.00', '0', '1', 'Withdrew');");
            $this->execute("INSERT INTO `grade_scale` VALUES(00000000018, 'IP', '90-98', '4.00', '0', '1', 'Incomplete passing');");
        endif;


        // Migration for table graduation_hold
        if (!$this->hasTable('graduation_hold')) :
            $table = $this->table('graduation_hold', array('id' => false, 'primary_key' => 'id'));
            $table
                ->addColumn('id', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('queryID', 'integer', array('signed' => true, 'limit' => 11))
                ->addColumn('gradDate', 'date', array())
                ->create();
        endif;

        // Migration for table hiatus
        if (!$this->hasTable('hiatus')) :
            $table = $this->table('hiatus', array('id' => false, 'primary_key' => 'shisID'));
            $table
                ->addColumn('shisID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('shisCode', 'string', array('limit' => 6))
                ->addColumn('startDate', 'date', array())
                ->addColumn('endDate', 'date', array())
                ->addColumn('comment', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('addDate', 'date', array())
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('shisCode', 'stuID', 'addedBy'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table institution
        if (!$this->hasTable('institution')) :
            $table = $this->table('institution', array('id' => false, 'primary_key' => 'institutionID'));
            $table
                ->addColumn('institutionID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('fice_ceeb', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('instType', 'string', array('limit' => 4))
                ->addColumn('instName', 'string', array('limit' => 180))
                ->addColumn('city', 'string', array('limit' => 30))
                ->addColumn('state', 'string', array('limit' => 2))
                ->addColumn('country', 'string', array('limit' => 2))
                ->addIndex(array('fice_ceeb'))
                ->create();
        endif;

        // Migration for table institution_attended
        if (!$this->hasTable('institution_attended')) :
            $table = $this->table('institution_attended', array('id' => false, 'primary_key' => 'instAttID'));
            $table
                ->addColumn('instAttID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('fice_ceeb', 'string', array('limit' => 11))
                ->addColumn('personID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('fromDate', 'date', array())
                ->addColumn('toDate', 'date', array())
                ->addColumn('major', 'string', array('limit' => 255))
                ->addColumn('degree_awarded', 'string', array('limit' => 6))
                ->addColumn('degree_conferred_date', 'date', array())
                ->addColumn('GPA', 'decimal', array('signed' => true, 'precision' => 6, 'scale' => 4, 'null' => true))
                ->addColumn('addDate', 'date', array())
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('fice_ceeb', 'personID'), array('unique' => true))
                ->addIndex(array('personID'))
                ->create();
        endif;

        // Migration for table job
        if (!$this->hasTable('job')) :
            $table = $this->table('job', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('pay_grade', 'integer', array('signed' => true, 'limit' => 11))
                ->addColumn('title', 'string', array('limit' => 180))
                ->addColumn('hourly_wage', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 2, 'null' => true))
                ->addColumn('weekly_hours', 'integer', array('signed' => true, 'null' => true, 'limit' => 4))
                ->addColumn('attachment', 'string', array('null' => true, 'limit' => 255))
                ->addColumn('addDate', 'date', array())
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->create();

            $this->execute("INSERT INTO `job` VALUES(1, 1, 'IT Support', '34.00', 40, NULL, '$NOW', 00000001, '$NOW');");
        endif;


        // Migration for table job_status
        if (!$this->hasTable('job_status')) :
            $table = $this->table('job_status', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('typeCode', 'string', array('limit' => 6))
                ->addColumn('type', 'string', array('limit' => 180))
                ->addIndex(array('typeCode'), array('unique' => true))
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


        // Migration for table location
        if (!$this->hasTable('location')) :
            $table = $this->table('location', array('id' => false, 'primary_key' => 'locationID'));
            $table
                ->addColumn('locationID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('locationCode', 'string', array('limit' => 11))
                ->addColumn('locationName', 'string', array('limit' => 80))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('locationCode'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `location` VALUES(1, 'NULL', '', '$NOW');");
        endif;

        // Migration for table major
        if (!$this->hasTable('major')) :
            $table = $this->table('major', array('id' => false, 'primary_key' => 'majorID'));
            $table
                ->addColumn('majorID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('majorCode', 'string', array('limit' => 11))
                ->addColumn('majorName', 'string', array('limit' => 180))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('majorCode'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `major` VALUES(1, 'NULL', '', '$NOW');");
        endif;

        // Migration for table met_link
        if (!$this->hasTable('met_link')) :
            $table = $this->table('met_link', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('link_title', 'string', array('limit' => 180))
                ->addColumn('link_src', 'string', array('limit' => 255))
                ->addColumn('status', 'enum', array('values' => array('active', 'inactive')))
                ->addColumn('sort', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_TINY))
                ->create();
        endif;

        // Migration for table met_news
        if (!$this->hasTable('met_news')) :
            $table = $this->table('met_news', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('news_title', 'string', array('limit' => 255))
                ->addColumn('news_slug', 'string', array('limit' => 255))
                ->addColumn('news_content', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('status', 'enum', array('values' => array('draft', 'publish')))
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('addDate', 'date', array())
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('addedBy'))
                ->addForeignKey('addedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table met_page
        if (!$this->hasTable('met_page')) :
            $table = $this->table('met_page', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('page_title', 'string', array('limit' => 255))
                ->addColumn('page_slug', 'string', array('limit' => 255))
                ->addColumn('page_content', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('status', 'enum', array('values' => array('draft', 'publish')))
                ->addColumn('sort', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_TINY))
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('addDate', 'date', array())
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('addedBy'))
                ->addForeignKey('addedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table minor
        if (!$this->hasTable('minor')) :
            $table = $this->table('minor', array('id' => false, 'primary_key' => 'minorID'));
            $table
                ->addColumn('minorID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('minorCode', 'string', array('limit' => 11))
                ->addColumn('minorName', 'string', array('limit' => 180))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('minorCode'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `minor` VALUES(1, 'NULL', '', '$NOW');");
        endif;


        // Migration for table options_meta
        if (!$this->hasTable('options_meta')) :
            $table = $this->table('options_meta', array('id' => false, 'primary_key' => 'meta_id'));
            $table
                ->addColumn('meta_id', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('meta_key', 'string', array('default' => '', 'limit' => 60))
                ->addColumn('meta_value', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addIndex(array('meta_key'), array('unique' => true))
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
            $this->execute("INSERT INTO `options_meta` VALUES(10, 'reset_password_text', '<b>eduTrac Password Reset</b><br>Password &amp; Login Information<br><br>You or someone else requested a new password to the eduTrac online system. If you did not request this change, please contact the administrator as soon as possible @ #adminemail#.&nbsp; To log into the eduTrac system, please visit #url# and login with your username and password.<br><br>FULL NAME:&nbsp; #fname# #lname#<br>USERNAME:&nbsp; #uname#<br>PASSWORD:&nbsp; #password#<br><br>If you need further assistance, please read the documentation at #helpdesk#.<br><br>KEEP THIS IN A SAFE AND SECURE LOCATION.<br><br>Thank You,<br>eduTrac Web Team<br>');");
            $this->execute("INSERT INTO `options_meta` VALUES(11, 'api_key', '');");
            $this->execute("INSERT INTO `options_meta` VALUES(12, 'room_request_email', 'request@myschool.edu');");
            $this->execute("INSERT INTO `options_meta` VALUES(13, 'room_request_text', '<p>&nbsp;</p>\r\n<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#F4F3F4\">\r\n<tbody>\r\n<tr>\r\n<td style=\"padding: 15px;\"><center>\r\n<table width=\"550\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td align=\"left\">\r\n<div style=\"border: solid 1px #d9d9d9;\">\r\n<table id=\"header\" style=\"line-height: 1.6; font-size: 12px; font-family: Helvetica, Arial, sans-serif; border: solid 1px #FFFFFF; color: #444;\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td style=\"color: #ffffff;\" colspan=\"2\" valign=\"bottom\" height=\"30\">.</td>\r\n</tr>\r\n<tr>\r\n<td style=\"line-height: 32px; padding-left: 30px;\" valign=\"baseline\"><span style=\"font-size: 32px;\">eduTrac SIS</span></td>\r\n<td style=\"padding-right: 30px;\" align=\"right\" valign=\"baseline\"><span style=\"font-size: 14px; color: #777777;\">Room/Event Reservation Request</span></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id=\"content\" style=\"margin-top: 15px; margin-right: 30px; margin-left: 30px; color: #444; line-height: 1.6; font-size: 12px; font-family: Arial, sans-serif;\" border=\"0\" width=\"490\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td style=\"border-top: solid 1px #d9d9d9;\" colspan=\"2\">\r\n<div style=\"padding: 15px 0;\">Below are the details of a new room request.</div>\r\n<div style=\"padding: 15px 0;\"><strong>Name:</strong> #name#<br /><br /><strong>Email:</strong> #email#<br /><br /><strong>Event Title:</strong> #title#<br /><strong>Description:</strong> #description#<br /><strong>Request Type:</strong> #request_type#<br /><strong>Category:</strong> #category#<br /><strong>Room#:</strong> #room#<br /><strong>Start Date:</strong> #firstday#<br /><strong>End Date:</strong> #lastday#<br /><strong>Start Time:</strong> #sTime#<br /><strong>End Time:</strong> #eTime#<br /><strong>Repeat?:</strong> #repeat#<br /><strong>Occurrence:</strong> #occurrence#<br /><br /><br />\r\n<h3>Legend</h3>\r\n<ul>\r\n<li>Repeat - 1 means yes it is an event that is repeated</li>\r\n<li>Occurrence - 1 = repeats everyday, 7 = repeats weekly, 14 = repeats biweekly</li>\r\n</ul>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id=\"footer\" style=\"line-height: 1.5; font-size: 12px; font-family: Arial, sans-serif; margin-right: 30px; margin-left: 30px;\" border=\"0\" width=\"490\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr style=\"font-size: 11px; color: #999999;\">\r\n<td style=\"border-top: solid 1px #d9d9d9;\" colspan=\"2\">\r\n<div style=\"padding-top: 15px; padding-bottom: 1px;\">Powered by eduTrac SIS</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"color: #ffffff;\" colspan=\"2\" height=\"15\">.</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</center></td>\r\n</tr>\r\n</tbody>\r\n</table>');");
            $this->execute("INSERT INTO `options_meta` VALUES(14, 'room_booking_confirmation_text', '<p>&nbsp;</p>\r\n<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#F4F3F4\">\r\n<tbody>\r\n<tr>\r\n<td style=\"padding: 15px;\"><center>\r\n<table width=\"550\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td align=\"left\">\r\n<div style=\"border: solid 1px #d9d9d9;\">\r\n<table id=\"header\" style=\"line-height: 1.6; font-size: 12px; font-family: Helvetica, Arial, sans-serif; border: solid 1px #FFFFFF; color: #444;\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td style=\"color: #ffffff;\" colspan=\"2\" valign=\"bottom\" height=\"30\">.</td>\r\n</tr>\r\n<tr>\r\n<td style=\"line-height: 32px; padding-left: 30px;\" valign=\"baseline\"><span style=\"font-size: 32px;\">eduTrac SIS</span></td>\r\n<td style=\"padding-right: 30px;\" align=\"right\" valign=\"baseline\"><span style=\"font-size: 14px; color: #777777;\">Room/Event&nbsp;Booking&nbsp;Confirmation</span></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id=\"content\" style=\"margin-top: 15px; margin-right: 30px; margin-left: 30px; color: #444; line-height: 1.6; font-size: 12px; font-family: Arial, sans-serif;\" border=\"0\" width=\"490\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr>\r\n<td style=\"border-top: solid 1px #d9d9d9;\" colspan=\"2\">\r\n<div style=\"padding: 15px 0;\">Your room request or event request entitled <strong>#title#</strong> has been booked. If you have any questions or concerns, please email our office at <a href=\"mailto:request@bdci.edu\">request@bdci.edu</a></div>\r\n<div style=\"padding: 15px 0;\">Sincerely,<br />Room Scheduler</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table id=\"footer\" style=\"line-height: 1.5; font-size: 12px; font-family: Arial, sans-serif; margin-right: 30px; margin-left: 30px;\" border=\"0\" width=\"490\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\r\n<tbody>\r\n<tr style=\"font-size: 11px; color: #999999;\">\r\n<td style=\"border-top: solid 1px #d9d9d9;\" colspan=\"2\">\r\n<div style=\"padding-top: 15px; padding-bottom: 1px;\">Powered by eduTrac SIS</div>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"color: #ffffff;\" colspan=\"2\" height=\"15\">.</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</center></td>\r\n</tr>\r\n</tbody>\r\n</table>');");
            $this->execute("INSERT INTO `options_meta` VALUES(15, 'myet_welcome_message', '<p>Welcome to the <em>my</em>etSIS campus portal. The <em>my</em>etSIS campus portal&nbsp;is your personalized campus web site at Eastbound University.</p>\r\n<p>If you are a prospective student who is interested in applying to the college, checkout the <a href=\"pages/admissions/\">admissions</a>&nbsp;page for more information.</p>');");
            $this->execute("INSERT INTO `options_meta` VALUES(16, 'contact_phone', '888.888.8888');");
            $this->execute("INSERT INTO `options_meta` VALUES(17, 'contact_email', 'contact@colegio.edu');");
            $this->execute("INSERT INTO `options_meta` VALUES(18, 'mailing_address', '10 Eliot Street, Suite 2\r\nSomerville, MA 02140');");
            $this->execute("INSERT INTO `options_meta` VALUES(19, 'enable_myet_portal', '0');");
            $this->execute("INSERT INTO `options_meta` VALUES(20, 'screen_caching', '1');");
            $this->execute("INSERT INTO `options_meta` VALUES(21, 'db_caching', '1');");
            $this->execute("INSERT INTO `options_meta` VALUES(22, 'admissions_email', 'admissions@colegio.edu');");
            $this->execute("INSERT INTO `options_meta` VALUES(23, 'coa_form_text', '<p>Dear Admin,</p>\r\n<p>#name# has submitted a change of address. Please see below for details.</p>\r\n<p><strong>ID:</strong> #id#</p>\r\n<p><strong>Address1:</strong> #address1#</p>\r\n<p><strong>Address2:</strong> #address2#</p>\r\n<p><strong>City:</strong> #city#</p>\r\n<p><strong>State:</strong> #state#</p>\r\n<p><strong>Zip:</strong> #zip#</p>\r\n<p><strong>Country:</strong> #country#</p>\r\n<p><strong>Phone:</strong> #phone#</p>\r\n<p><strong>Email:</strong> #email#</p>\r\n<p>&nbsp;</p>\r\n<p>----<br /><em>This is a system generated email.</em></p>');");
            $this->execute("INSERT INTO `options_meta` VALUES(24, 'enable_myet_appl_form', '0');");
            $this->execute("INSERT INTO `options_meta` VALUES(25, 'myet_offline_message', 'Please excuse the dust. We are giving the portal a new facelift. Please try back again in an hour.\r\n\r\nSincerely,\r\nIT Department');");
            $this->execute("INSERT INTO `options_meta` VALUES(26, 'curl', '1');");
            $this->execute("INSERT INTO `options_meta` VALUES(27, 'system_timezone', 'America/New_York');");
            $this->execute("INSERT INTO `options_meta` VALUES(28, 'number_of_courses', '3');");
            $this->execute("INSERT INTO `options_meta` VALUES(29, 'account_balance', '');");
            $this->execute("INSERT INTO `options_meta` VALUES(30, 'reg_instructions', '');");
            $this->execute("INSERT INTO `options_meta` VALUES(31, 'et_core_locale', 'en_US');");
            $this->execute("INSERT INTO `options_meta` VALUES(32, 'send_acceptance_email', '0');");
            $this->execute("INSERT INTO `options_meta` VALUES(33, 'person_login_details', '<p>Dear #fname#:</p>\r\n<p>An account has just been created for you. Below are your login details.</p>\r\n<p>Username: #uname#</p>\r\n<p>Password: #password#</p>\r\n<p>ID: #id#</p>\r\n<p>Alternate ID:&nbsp;#altID#</p>\r\n<p>You may log into your account at the url below:</p>\r\n<p><a href=\"#url#\">#url#</a></p>');");
            $this->execute("INSERT INTO `options_meta` VALUES(34, 'myet_layout', 'default');");
            $this->execute("INSERT INTO `options_meta` VALUES(35, 'open_terms', '');");
            $this->execute("INSERT INTO `options_meta` VALUES(36, 'elfinder_driver', 'elf_local_driver');");
        endif;

        // Migration for table pay_grade
        if (!$this->hasTable('pay_grade')) :
            $table = $this->table('pay_grade', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('grade', 'string', array('limit' => 10))
                ->addColumn('minimum_salary', 'decimal', array('signed' => true, 'precision' => 10, 'scale' => 2))
                ->addColumn('maximum_salary', 'decimal', array('signed' => true, 'precision' => 10, 'scale' => 2))
                ->addColumn('addDate', 'date', array())
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->create();

            $this->execute("INSERT INTO `pay_grade` VALUES(1, '24', '40000.00', '44999.00', '$NOW', 00000001, '$NOW');");
        endif;

        // Migration for table payment
        if (!$this->hasTable('payment')) :
            $table = $this->table('payment', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('amount', 'decimal', array('signed' => true, 'precision' => 10, 'scale' => 2, 'default' => '0'))
                ->addColumn('checkNum', 'string', array('null' => true, 'limit' => 8))
                ->addColumn('paypal_txnID', 'string', array('null' => true, 'limit' => 255))
                ->addColumn('paypal_payment_status', 'string', array('null' => true, 'limit' => 80))
                ->addColumn('paypal_txn_fee', 'string', array('default' => '0.00', 'limit' => 11))
                ->addColumn('paymentTypeID', 'integer', array('signed' => true, 'limit' => 11))
                ->addColumn('comment', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('paymentDate', 'date', array())
                ->addColumn('postedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('stuID', 'termCode', 'postedBy'))
                ->addForeignKey('postedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table payment_type
        if (!$this->hasTable('payment_type')) :
            $table = $this->table('payment_type', array('id' => false, 'primary_key' => 'ptID'));
            $table
                ->addColumn('ptID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('type', 'string', array('limit' => 30))
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
            $table = $this->table('permission', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => false, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('permKey', 'string', array('limit' => 30))
                ->addColumn('permName', 'string', array('limit' => 80))
                ->addIndex(array('permKey'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `permission` VALUES(00000000000000000017, 'edit_settings', 'Edit Settings');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000018, 'access_audit_trail_screen', 'Audit Trail Logs');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000019, 'access_sql_interface_screen', 'SQL Interface Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000036, 'access_course_screen', 'Course Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000040, 'access_faculty_screen', 'Faculty Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000044, 'access_parent_screen', 'Parent Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000048, 'access_student_screen', 'Student Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000052, 'access_plugin_screen', 'Plugin Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000057, 'access_role_screen', 'Role Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000061, 'access_permission_screen', 'Permission Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000065, 'access_user_role_screen', 'User Role Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000069, 'access_user_permission_screen', 'User Permission Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000073, 'access_email_template_screen', 'Email Template Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000074, 'access_course_sec_screen', 'Course Section Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000075, 'add_course_sec', 'Add Course Section');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000078, 'course_sec_inquiry_only', 'Course Section Inquiry Only');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000079, 'course_inquiry_only', 'Course Inquiry Only');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000080, 'access_person_screen', 'Person Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000081, 'add_person', 'Add Person');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000085, 'access_acad_prog_screen', 'Academic Program Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000086, 'add_acad_prog', 'Add Academic Program');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000089, 'acad_prog_inquiry_only', 'Academic Program Inquiry Only');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000090, 'access_nslc', 'NSLC');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000091, 'access_error_log_screen', 'Error Log Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000092, 'access_student_portal', 'Student Portal');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000093, 'access_cronjob_screen', 'Cronjob Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000097, 'access_report_screen', 'Report Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000098, 'add_address', 'Add Address');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000100, 'address_inquiry_only', 'Address Inquiry Only');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000101, 'general_inquiry_only', 'General Inquiry Only');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000102, 'faculty_inquiry_only', 'Faculty Inquiry Only');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000103, 'parent_inquiry_only', 'Parent Inquiry Only');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000104, 'student_inquiry_only', 'Student Inquiry Only');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000106, 'access_plugin_admin_page', 'Plugin Admin Page');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000108, 'access_save_query_screens', 'Save Query Screens');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000109, 'access_forms', 'Forms');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000110, 'create_stu_record', 'Create Student Record');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000111, 'create_fac_record', 'Create Faculty Record');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000112, 'create_par_record', 'Create Parent Record');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000113, 'reset_person_password', 'Reset Person Password');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000114, 'register_students', 'Register Students');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000167, 'access_ftp', 'FTP');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000168, 'access_stu_roster_screen', 'Access Student Roster Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000169, 'access_grading_screen', 'Grading Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000170, 'access_bill_tbl_screen', 'Billing Table Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000171, 'add_crse_sec_bill', 'Add Course Sec Billing');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000176, 'access_parent_portal', 'Parent Portal');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000177, 'import_data', 'Import Data');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000178, 'add_course', 'Add Course');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000179, 'person_inquiry_only', 'Person Inquiry Only');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000180, 'room_request', 'Room Request');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000201, 'activate_course_sec', 'Activate Course Section');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000202, 'cancel_course_sec', 'Cancel Course Section');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000203, 'access_institutions_screen', 'Access Institutions Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000204, 'add_institution', 'Add Institution');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000205, 'access_application_screen', 'Access Application Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000206, 'create_application', 'Create Application');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000207, 'access_staff_screen', 'Staff Screen');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000208, 'staff_inquiry_only', 'Staff Inquiry Only');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000209, 'create_staff_record', 'Create Staff Record');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000210, 'graduate_students', 'Graduate Students');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000211, 'generate_transcripts', 'Generate Transcripts');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000212, 'access_student_accounts', 'Access Student Accounts');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000213, 'student_account_inquiry_only', 'Student Account Inquiry Only');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000214, 'restrict_edit_profile', 'Restrict Edit Profile');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000215, 'access_general_ledger', 'Access General Ledger');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000216, 'login_as_user', 'Login as User');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000217, 'access_academics', 'Access Academics');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000218, 'access_financials', 'Access Financials');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000219, 'access_human_resources', 'Access Human Resources');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000220, 'submit_timesheets', 'Submit Timesheets');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000221, 'access_sql', 'Access SQL');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000222, 'access_person_mgmt', 'Access Person Management');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000223, 'create_campus_site', 'Create Campus Site');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000224, 'access_dashboard', 'Access Dashboard');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000225, 'access_myet_admin', 'Access myetSIS Admin');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000226, 'manage_myet_pages', 'Manage myetSIS Pages');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000227, 'manage_myet_links', 'Manage myetSIS Links');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000228, 'manage_myet_news', 'Manage myetSIS News');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000229, 'add_myet_page', 'Add myetSIS Page');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000230, 'edit_myet_page', 'Edit myetSIS Page');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000231, 'delete_myet_page', 'Delete myetSIS Page');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000232, 'add_myet_link', 'Add myetSIS Link');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000233, 'edit_myet_link', 'Edit myetSIS Link');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000234, 'delete_myet_link', 'Delete myetSIS Link');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000235, 'add_myet_news', 'Add myetSIS News');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000236, 'edit_myet_news', 'Edit myetSIS News');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000237, 'delete_myet_news', 'Delete myetSIS News');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000238, 'clear_screen_cache', 'Clear Screen Cache');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000239, 'clear_database_cache', 'Clear Database Cache');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000240, 'access_myet_appl_form', 'Access myetSIS Application Form');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000241, 'edit_myet_css', 'Edit myetSIS CSS');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000242, 'edit_myet_welcome_message', 'Edit myetSIS Welcome Message');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000243, 'access_communication_mgmt', 'Access Communication Management');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000244, 'delete_student', 'Delete Student');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000245, 'access_payment_gateway', 'Access Payment Gateway');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000246, 'access_ea', 'Access eduTrac Analytics');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000247, 'execute_saved_query', 'Execute Saved Query');");
            $this->execute("INSERT INTO `permission` VALUES(00000000000000000248, 'submit_final_grades', 'Submit Final Grades');");
        endif;

        // Migration for table person
        if (!$this->hasTable('person')) :
            $table = $this->table('person', array('id' => false, 'primary_key' => 'personID'));
            $table
                ->addColumn('personID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('altID', 'string', array('null' => true, 'limit' => 255))
                ->addColumn('uname', 'string', array('limit' => 80))
                ->addColumn('prefix', 'string', array('limit' => 6))
                ->addColumn('personType', 'string', array('limit' => 3))
                ->addColumn('fname', 'string', array('limit' => 150))
                ->addColumn('lname', 'string', array('limit' => 150))
                ->addColumn('mname', 'string', array('limit' => 2))
                ->addColumn('email', 'string', array('limit' => 150))
                ->addColumn('ssn', 'integer', array('signed' => true, 'limit' => 9))
                ->addColumn('dob', 'date', array())
                ->addColumn('veteran', 'enum', array('values' => array('1', '0')))
                ->addColumn('ethnicity', 'string', array('limit' => 30))
                ->addColumn('nationality', 'string', array('limit' => 11))
                ->addColumn('gender', 'enum', array('values' => array('M', 'F')))
                ->addColumn('emergency_contact', 'string', array('limit' => 150))
                ->addColumn('emergency_contact_phone', 'string', array('limit' => 50))
                ->addColumn('photo', 'string', array('null' => true, 'limit' => 255))
                ->addColumn('password', 'string', array('limit' => 255))
                ->addColumn('status', 'enum', array('default' => 'A', 'values' => array('A', 'I')))
                ->addColumn('auth_token', 'string', array('null' => true, 'limit' => 255))
                ->addColumn('approvedDate', 'datetime', array())
                ->addColumn('approvedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastLogin', 'datetime', array())
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('uname'), array('unique' => true))
                ->addIndex(array('personType', 'approvedBy'))
                ->addForeignKey('approvedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();

            $this->execute('INSERT INTO `person` (`personID`, `uname`, `password`, `fname`, `lname`, `email`,`personType`,`approvedDate`,`approvedBy`) VALUES (\'\', \'etsis\', \'$P$BAHklrhrmcZMglABG9VF6PB7c1zD5H/\', \'eduTrac\', \'SIS\', \'sis@gmail.com\', \'STA\', \'' . $NOW . '\', \'1\');');
        endif;

        // Migration for table person_perms
        if (!$this->hasTable('person_perms')) :
            $table = $this->table('person_perms', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => false, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('personID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('permission', 'text', array('limit' => MysqlAdapter::TEXT_REGULAR))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('personID'), array('unique' => true))
                ->addForeignKey('personID', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table person_roles
        if (!$this->hasTable('person_roles')) :
            $table = $this->table('person_roles', array('id' => false, 'primary_key' => 'rID'));
            $table
                ->addColumn('rID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('personID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('roleID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('addDate', 'datetime', array())
                ->addIndex(array('personID', 'roleID'), array('unique' => true))
                ->addForeignKey('personID', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();

            $this->execute("INSERT INTO `person_roles` VALUES(1, 1, 8, '$NOW');");
        endif;

        // Migration for table plugin
        if (!$this->hasTable('plugin')) :
            $table = $this->table('plugin', array('id' => false, 'primary_key' => 'id'));
            $table
                ->addColumn('id', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('location', 'text', array('limit' => MysqlAdapter::TEXT_REGULAR))
                ->create();
        endif;

        // Migration for table refund
        if (!$this->hasTable('refund')) :
            $table = $this->table('refund', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('amount', 'decimal', array('signed' => true, 'precision' => 10, 'scale' => 2, 'default' => '0.00'))
                ->addColumn('comment', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('refundDate', 'date', array())
                ->addColumn('postedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('stuID', 'termCode', 'postedBy'))
                ->addForeignKey('postedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table restriction_code
        if (!$this->hasTable('restriction_code')) :
            $table = $this->table('restriction_code', array('id' => false, 'primary_key' => 'rstrCodeID'));
            $table
                ->addColumn('rstrCodeID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('rstrCode', 'string', array('limit' => 6))
                ->addColumn('description', 'string', array('limit' => 255))
                ->addColumn('deptCode', 'string', array('limit' => 11))
                ->addIndex(array('rstrCode'), array('unique' => true))
                ->addIndex(array('deptCode'))
                ->addForeignKey('deptCode', 'department', 'deptCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table restriction
        if (!$this->hasTable('restriction')) :
            $table = $this->table('restriction', array('id' => false, 'primary_key' => 'rstrID'));
            $table
                ->addColumn('rstrID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('rstrCode', 'string', array('limit' => 6))
                ->addColumn('severity', 'integer', array('signed' => true, 'limit' => 2))
                ->addColumn('startDate', 'date', array())
                ->addColumn('endDate', 'date', array())
                ->addColumn('comment', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('addDate', 'date', array())
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('rstrCode', 'stuID', 'addedBy'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('rstrCode', 'restriction_code', 'rstrCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('addedBy', 'staff', 'staffID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table role
        if (!$this->hasTable('role')) :
            $table = $this->table('role', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => false, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('roleName', 'string', array('limit' => 20))
                ->addColumn('permission', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addIndex(array('roleName'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `role` VALUES(00000000000000000008, 'Super Administrator', 'a:82:{i:0;s:13:\"edit_settings\";i:1;s:25:\"access_audit_trail_screen\";i:2;s:27:\"access_sql_interface_screen\";i:3;s:20:\"access_course_screen\";i:4;s:20:\"access_parent_screen\";i:5;s:21:\"access_student_screen\";i:6;s:20:\"access_plugin_screen\";i:7;s:18:\"access_role_screen\";i:8;s:24:\"access_permission_screen\";i:9;s:23:\"access_user_role_screen\";i:10;s:29:\"access_user_permission_screen\";i:11;s:28:\"access_email_template_screen\";i:12;s:24:\"access_course_sec_screen\";i:13;s:14:\"add_course_sec\";i:14;s:20:\"access_person_screen\";i:15;s:10:\"add_person\";i:16;s:23:\"access_acad_prog_screen\";i:17;s:13:\"add_acad_prog\";i:18;s:11:\"access_nslc\";i:19;s:23:\"access_error_log_screen\";i:20;s:21:\"access_student_portal\";i:21;s:21:\"access_cronjob_screen\";i:22;s:20:\"access_report_screen\";i:23;s:11:\"add_address\";i:24;s:24:\"access_plugin_admin_page\";i:25;s:25:\"access_save_query_screens\";i:26;s:12:\"access_forms\";i:27;s:17:\"create_stu_record\";i:28;s:17:\"create_fac_record\";i:29;s:17:\"create_par_record\";i:30;s:21:\"reset_person_password\";i:31;s:17:\"register_students\";i:32;s:10:\"access_ftp\";i:33;s:24:\"access_stu_roster_screen\";i:34;s:21:\"access_grading_screen\";i:35;s:22:\"access_bill_tbl_screen\";i:36;s:17:\"add_crse_sec_bill\";i:37;s:20:\"access_parent_portal\";i:38;s:11:\"import_data\";i:39;s:10:\"add_course\";i:40;s:12:\"room_request\";i:41;s:19:\"activate_course_sec\";i:42;s:17:\"cancel_course_sec\";i:43;s:26:\"access_institutions_screen\";i:44;s:15:\"add_institution\";i:45;s:25:\"access_application_screen\";i:46;s:18:\"create_application\";i:47;s:19:\"access_staff_screen\";i:48;s:19:\"create_staff_record\";i:49;s:16:\"access_dashboard\";i:50;s:17:\"graduate_students\";i:51;s:20:\"generate_transcripts\";i:52;s:23:\"access_student_accounts\";i:53;s:21:\"access_general_ledger\";i:54;s:13:\"login_as_user\";i:55;s:16:\"access_academics\";i:56;s:17:\"access_financials\";i:57;s:22:\"access_human_resources\";i:58;s:17:\"submit_timesheets\";i:59;s:10:\"access_sql\";i:60;s:18:\"access_person_mgmt\";i:61;s:22:\"access_payment_gateway\";i:62;s:18:\"create_campus_site\";i:63;s:17:\"access_myet_admin\";i:64;s:17:\"manage_myet_pages\";i:65;s:17:\"manage_myet_links\";i:66;s:16:\"manage_myet_news\";i:67;s:13:\"add_myet_page\";i:68;s:14:\"edit_myet_page\";i:69;s:16:\"delete_myet_page\";i:70;s:13:\"add_myet_link\";i:71;s:14:\"edit_myet_link\";i:72;s:16:\"delete_myet_link\";i:73;s:13:\"add_myet_news\";i:74;s:14:\"edit_myet_news\";i:75;s:16:\"delete_myet_news\";i:76;s:18:\"clear_screen_cache\";i:77;s:20:\"clear_database_cache\";i:78;s:21:\"access_myet_appl_form\";i:79;s:13:\"edit_myet_css\";i:80;s:25:\"edit_myet_welcome_message\";i:81;s:25:\"access_communication_mgmt\";}');");
            $this->execute("INSERT INTO `role` VALUES(00000000000000000009, 'Faculty', 'a:18:{i:0;s:21:\"access_student_screen\";i:1;s:24:\"access_course_sec_screen\";i:2;s:23:\"course_sec_inquiry_only\";i:3;s:19:\"course_inquiry_only\";i:4;s:23:\"access_acad_prog_screen\";i:5;s:22:\"acad_prog_inquiry_only\";i:6;s:20:\"address_inquiry_only\";i:7;s:20:\"general_inquiry_only\";i:8;s:20:\"student_inquiry_only\";i:9;s:24:\"access_stu_roster_screen\";i:10;s:21:\"access_grading_screen\";i:11;s:19:\"person_inquiry_only\";i:12;s:19:\"access_staff_screen\";i:13;s:18:\"staff_inquiry_only\";i:14;s:16:\"access_dashboard\";i:15;s:21:\"restrict_edit_profile\";i:16;s:16:\"access_academics\";i:17;s:18:\"access_person_mgmt\";}');");
            $this->execute("INSERT INTO `role` VALUES(00000000000000000010, 'Parent', '');");
            $this->execute("INSERT INTO `role` VALUES(00000000000000000011, 'Student', 'a:1:{i:0;s:21:\"access_student_portal\";}');");
            $this->execute("INSERT INTO `role` VALUES(00000000000000000012, 'Staff', 'a:32:{i:0;s:27:\"access_sql_interface_screen\";i:1;s:20:\"access_course_screen\";i:2;s:21:\"access_student_screen\";i:3;s:28:\"access_email_template_screen\";i:4;s:24:\"access_course_sec_screen\";i:5;s:23:\"course_sec_inquiry_only\";i:6;s:19:\"course_inquiry_only\";i:7;s:20:\"access_person_screen\";i:8;s:23:\"access_acad_prog_screen\";i:9;s:22:\"acad_prog_inquiry_only\";i:10;s:23:\"access_error_log_screen\";i:11;s:20:\"access_report_screen\";i:12;s:20:\"address_inquiry_only\";i:13;s:25:\"access_save_query_screens\";i:14;s:12:\"access_forms\";i:15;s:17:\"create_fac_record\";i:16;s:24:\"access_stu_roster_screen\";i:17;s:22:\"access_bill_tbl_screen\";i:18;s:17:\"add_crse_sec_bill\";i:19;s:11:\"import_data\";i:20;s:19:\"person_inquiry_only\";i:21;s:19:\"access_staff_screen\";i:22;s:18:\"staff_inquiry_only\";i:23;s:19:\"create_staff_record\";i:24;s:16:\"access_dashboard\";i:25;s:23:\"access_student_accounts\";i:26;s:16:\"access_academics\";i:27;s:22:\"access_human_resources\";i:28;s:17:\"submit_timesheets\";i:29;s:10:\"access_sql\";i:30;s:18:\"access_person_mgmt\";i:31;s:22:\"access_payment_gateway\";}');");
        endif;

        // Migration for table role_perms
        if (!$this->hasTable('role_perms')) :
            $table = $this->table('role_perms', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => false, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('roleID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('permID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('value', 'integer', array('signed' => true, 'default' => '0', 'limit' => MysqlAdapter::INT_TINY))
                ->addColumn('addDate', 'datetime', array())
                ->addIndex(array('roleID', 'permID'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000156, 11, 92, 1, '2013-09-03 11:30:43');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000201, 8, 21, 1, '2013-09-03 12:03:29');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000238, 8, 23, 1, '2013-09-03 12:03:29');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000268, 8, 22, 1, '2013-09-03 12:04:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000292, 8, 20, 1, '2013-09-03 12:04:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000309, 9, 84, 1, '2013-09-03 12:05:33');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000310, 9, 107, 1, '2013-09-03 12:05:33');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000462, 10, 176, 1, '2013-09-03 12:36:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000470, 12, 84, 1, '2013-09-03 12:37:49');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000471, 12, 107, 1, '2013-09-03 12:37:49');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000712, 13, 24, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000713, 13, 25, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000714, 13, 156, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000715, 13, 140, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000716, 13, 144, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000717, 13, 164, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000718, 13, 124, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000719, 13, 128, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000720, 13, 116, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000721, 13, 152, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000722, 13, 132, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000723, 13, 136, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000724, 13, 160, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000725, 13, 173, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000726, 13, 29, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000727, 13, 148, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000728, 13, 120, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000729, 13, 33, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000730, 13, 155, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000731, 13, 139, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000732, 13, 143, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000733, 13, 163, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000734, 13, 123, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000735, 13, 127, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000736, 13, 27, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000737, 13, 158, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000738, 13, 142, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000739, 13, 146, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000740, 13, 166, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000741, 13, 126, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000742, 13, 130, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000743, 13, 118, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000744, 13, 154, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000745, 13, 134, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000746, 13, 138, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000747, 13, 162, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000748, 13, 175, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000749, 13, 31, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000750, 13, 150, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000751, 13, 122, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000752, 13, 35, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000753, 13, 115, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000754, 13, 26, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000755, 13, 99, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000756, 13, 157, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000757, 13, 141, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000758, 13, 145, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000759, 13, 165, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000760, 13, 125, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000761, 13, 129, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000762, 13, 117, 1, '2013-09-03 22:37:31');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000763, 13, 153, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000764, 13, 133, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000765, 13, 137, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000766, 13, 161, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000767, 13, 174, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000768, 13, 30, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000769, 13, 149, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000770, 13, 121, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000771, 13, 34, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000772, 13, 109, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000773, 13, 151, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000774, 13, 131, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000775, 13, 135, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000776, 13, 159, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000777, 13, 172, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000778, 13, 28, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000779, 13, 147, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000780, 13, 119, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000781, 13, 32, 1, '2013-09-03 22:40:18');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000971, 11, 180, 1, '2013-09-04 04:51:52');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000993, 9, 89, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000994, 9, 85, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000995, 9, 218, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000996, 9, 223, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000997, 9, 168, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000998, 9, 100, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000000999, 9, 79, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001000, 9, 36, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001001, 9, 78, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001002, 9, 74, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001003, 9, 102, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001004, 9, 40, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001005, 9, 101, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001006, 9, 169, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001007, 9, 103, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001008, 9, 44, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001009, 9, 179, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001010, 9, 80, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001011, 9, 180, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001012, 9, 208, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001013, 9, 104, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001014, 9, 48, 1, '2014-02-13 09:56:10');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001015, 12, 89, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001016, 12, 85, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001017, 12, 218, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001018, 12, 223, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001019, 12, 100, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001020, 12, 79, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001021, 12, 36, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001022, 12, 78, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001023, 12, 74, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001024, 12, 102, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001025, 12, 40, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001026, 12, 101, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001027, 12, 103, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001028, 12, 44, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001029, 12, 179, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001030, 12, 80, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001031, 12, 180, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001032, 12, 208, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001033, 12, 104, 1, '2014-02-13 09:56:35');");
            $this->execute("INSERT INTO `role_perms` VALUES(00000000000000001034, 12, 48, 1, '2014-02-13 09:56:35');");
        endif;

        // Migration for table room
        if (!$this->hasTable('room')) :
            $table = $this->table('room', array('id' => false, 'primary_key' => 'roomID'));
            $table
                ->addColumn('roomID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('roomCode', 'string', array('limit' => 11))
                ->addColumn('buildingCode', 'string', array('limit' => 11))
                ->addColumn('roomNumber', 'string', array('limit' => 11))
                ->addColumn('roomCap', 'integer', array('signed' => true, 'limit' => 4))
                ->addIndex(array('roomCode'), array('unique' => true))
                ->addIndex(array('buildingCode'))
                ->addForeignKey('buildingCode', 'building', 'buildingCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();

            $this->execute("INSERT INTO `room` VALUES(1, 'NULL', 'NULL', '', 0);");
        endif;

        // Migration for table saved_query
        if (!$this->hasTable('saved_query')) :
            $table = $this->table('saved_query', array('id' => false, 'primary_key' => 'savedQueryID'));
            $table
                ->addColumn('savedQueryID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('personID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('savedQueryName', 'string', array('limit' => 80))
                ->addColumn('savedQuery', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('purgeQuery', 'enum', array('default' => '0', 'values' => array('0', '1')))
                ->addColumn('shared', 'text', array('null' => true, 'limit' => MysqlAdapter::TEXT_REGULAR))
                ->addColumn('createdDate', 'date', array())
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('personID'))
                ->addForeignKey('personID', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table school
        if (!$this->hasTable('school')) :
            $table = $this->table('school', array('id' => false, 'primary_key' => 'schoolID'));
            $table
                ->addColumn('schoolID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('schoolCode', 'string', array('limit' => 11))
                ->addColumn('schoolName', 'string', array('limit' => 180))
                ->addColumn('buildingCode', 'string', array('limit' => 11))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('schoolCode'), array('unique' => true))
                ->addIndex(array('buildingCode'))
                ->addForeignKey('buildingCode', 'building', 'buildingCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();

            $this->execute("INSERT INTO `school` VALUES(00000000001, 'NULL', 'NULL', 'NULL', '$NOW');");
        endif;

        // Migration for table screen
        if (!$this->hasTable('screen')) :
            $table = $this->table('screen', array('id' => false, 'primary_key' => 'id'));
            $table
                ->addColumn('id', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('code', 'string', array('limit' => 6))
                ->addColumn('name', 'string', array('limit' => 255))
                ->addColumn('relativeURL', 'string', array('limit' => 255))
                ->addIndex(array('code'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `screen` VALUES(1, 'SYSS', 'System Settings', 'setting/');");
            $this->execute("INSERT INTO `screen` VALUES(2, 'MPRM', 'Manage Permissions', 'permission/');");
            $this->execute("INSERT INTO `screen` VALUES(3, 'APRM', 'Add Permission', 'permission/add/');");
            $this->execute("INSERT INTO `screen` VALUES(4, 'MRLE', 'Manage Roles', 'role/');");
            $this->execute("INSERT INTO `screen` VALUES(5, 'AUDT', 'Audit Trail', 'audit-trail/');");
            $this->execute("INSERT INTO `screen` VALUES(6, 'SQL', 'SQL Interface', 'sql/');");
            $this->execute("INSERT INTO `screen` VALUES(7, 'ARLE', 'Add Role', 'role/add/');");
            $this->execute("INSERT INTO `screen` VALUES(8, 'SCH', 'School Form', 'form/school/');");
            $this->execute("INSERT INTO `screen` VALUES(9, 'SEM', 'Semester Form', 'form/semester/');");
            $this->execute("INSERT INTO `screen` VALUES(10, 'TERM', 'Term Form', 'form/term/');");
            $this->execute("INSERT INTO `screen` VALUES(11, 'AYR', 'Acad Year Form', 'form/acad-year/');");
            $this->execute("INSERT INTO `screen` VALUES(12, 'CRSE', 'Course', 'crse/');");
            $this->execute("INSERT INTO `screen` VALUES(13, 'DEPT', 'Department Form', 'form/department/');");
            $this->execute("INSERT INTO `screen` VALUES(14, 'CRL', 'Credit Load Form', 'form/credit-load/');");
            $this->execute("INSERT INTO `screen` VALUES(15, 'DEG', 'Degree Form', 'form/degree/');");
            $this->execute("INSERT INTO `screen` VALUES(16, 'MAJR', 'Major Form', 'form/major/');");
            $this->execute("INSERT INTO `screen` VALUES(17, 'MINR', 'Minor Form', 'form/minor/');");
            $this->execute("INSERT INTO `screen` VALUES(18, 'PROG', 'Academic Program', 'program/');");
            $this->execute("INSERT INTO `screen` VALUES(19, 'CCD', 'CCD Form', 'form/ccd/');");
            $this->execute("INSERT INTO `screen` VALUES(20, 'CIP', 'CIP Form', 'form/cip/');");
            $this->execute("INSERT INTO `screen` VALUES(21, 'LOC', 'Location Form', 'form/location/');");
            $this->execute("INSERT INTO `screen` VALUES(22, 'BLDG', 'Building Form', 'form/building/');");
            $this->execute("INSERT INTO `screen` VALUES(23, 'ROOM', 'Room Form', 'form/room/');");
            $this->execute("INSERT INTO `screen` VALUES(24, 'SPEC', 'Specialization From', 'form/specialization/');");
            $this->execute("INSERT INTO `screen` VALUES(25, 'SUBJ', 'Subject Form', 'form/subject/');");
            $this->execute("INSERT INTO `screen` VALUES(26, 'CLYR', 'Class Year Form', 'form/class-year/');");
            $this->execute("INSERT INTO `screen` VALUES(27, 'APRG', 'Add Acad Program', 'program/add/');");
            $this->execute("INSERT INTO `screen` VALUES(28, 'ACRS', 'Add Course', 'crse/add/');");
            $this->execute("INSERT INTO `screen` VALUES(29, 'SECT', 'Course Section', 'sect/');");
            $this->execute("INSERT INTO `screen` VALUES(30, 'RGN', 'Course Registration', 'sect/rgn/');");
            $this->execute("INSERT INTO `screen` VALUES(31, 'NSCP', 'NSLC Purge', 'nslc/purge/');");
            $this->execute("INSERT INTO `screen` VALUES(32, 'NSCS', 'NSLC Setup', 'nslc/setup/');");
            $this->execute("INSERT INTO `screen` VALUES(33, 'NSCX', 'NSLC Extraction', 'nslc/extraction/');");
            $this->execute("INSERT INTO `screen` VALUES(34, 'NSCE', 'NSLC Verification', 'nslc/verification/');");
            $this->execute("INSERT INTO `screen` VALUES(35, 'NSCC', 'NSLC Correction', 'nslc/');");
            $this->execute("INSERT INTO `screen` VALUES(36, 'NSCT', 'NSLC File', 'nslc/file/');");
            $this->execute("INSERT INTO `screen` VALUES(37, 'NAE', 'Name & Address', 'nae/');");
            $this->execute("INSERT INTO `screen` VALUES(38, 'APER', 'Add Person', 'nae/add/');");
            $this->execute("INSERT INTO `screen` VALUES(39, 'SPRO', 'Student Profile', 'stu/');");
            $this->execute("INSERT INTO `screen` VALUES(40, 'FAC', 'Faculty Profile', 'faculty/');");
            $this->execute("INSERT INTO `screen` VALUES(41, 'INST', 'Institution', 'appl/inst/');");
            $this->execute("INSERT INTO `screen` VALUES(42, 'AINST', 'New Institution', 'appl/inst/add/');");
            $this->execute("INSERT INTO `screen` VALUES(43, 'APPL', 'Application', 'appl/');");
            $this->execute("INSERT INTO `screen` VALUES(44, 'BRGN', 'Batch Course Registration', 'sect/brgn/');");
            $this->execute("INSERT INTO `screen` VALUES(45, 'STAF', 'Staff', 'staff/');");
            $this->execute("INSERT INTO `screen` VALUES(46, 'TRAN', 'Transcript', 'stu/tran/');");
            $this->execute("INSERT INTO `screen` VALUES(47, 'SLR', 'Student Load Rules', 'form/student-load-rule/');");
            $this->execute("INSERT INTO `screen` VALUES(48, 'RSTR', 'Restriction Codes', 'form/rstr-code/');");
            $this->execute("INSERT INTO `screen` VALUES(49, 'GRSC', 'Grade Scale', 'form/grade-scale/');");
            $this->execute("INSERT INTO `screen` VALUES(50, 'SROS', 'Student Roster', 'sect/sros/');");
            $this->execute("INSERT INTO `screen` VALUES(51, 'EXTR', 'External Course', 'crse/extr/');");
            $this->execute("INSERT INTO `screen` VALUES(52, 'ATCEQ', 'New Transfer Course Equivalency', 'crse/atceq/');");
            $this->execute("INSERT INTO `screen` VALUES(53, 'TCEQ', 'Transfer Course Equivalency', 'crse/tceq/');");
            $this->execute("INSERT INTO `screen` VALUES(54, 'TCRE', 'Transfer Credit', 'crse/tcre/');");
        endif;

        // Migration for table semester
        if (!$this->hasTable('semester')) :
            $table = $this->table('semester', array('id' => false, 'primary_key' => 'semesterID'));
            $table
                ->addColumn('semesterID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('acadYearCode', 'string', array('limit' => 11))
                ->addColumn('semCode', 'string', array('limit' => 11))
                ->addColumn('semName', 'string', array('limit' => 80))
                ->addColumn('semStartDate', 'date', array('default' => '0000-00-00'))
                ->addColumn('semEndDate', 'date', array('default' => '0000-00-00'))
                ->addColumn('active', 'enum', array('default' => '1', 'values' => array('1', '0')))
                ->addIndex(array('semCode'), array('unique' => true))
                ->addIndex(array('acadYearCode'))
                ->addForeignKey('acadYearCode', 'acad_year', 'acadYearCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();

            $this->execute("INSERT INTO `semester` VALUES(00000000001, 'NULL', 'NULL', '', '$NOW', '$NOW', '1');");
        endif;

        // Migration for table specialization
        if (!$this->hasTable('specialization')) :
            $table = $this->table('specialization', array('id' => false, 'primary_key' => 'specID'));
            $table
                ->addColumn('specID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('specCode', 'string', array('limit' => 11))
                ->addColumn('specName', 'string', array('limit' => 80))
                ->addIndex(array('specCode'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `specialization` VALUES(1, 'NULL', '');");
        endif;

        // Migration for table staff
        if (!$this->hasTable('staff')) :
            $table = $this->table('staff', array('id' => false, 'primary_key' => 'id'));
            $table
                ->addColumn('id', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('staffID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('schoolCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('buildingCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('officeCode', 'string', array('null' => true, 'limit' => 11))
                ->addColumn('office_phone', 'string', array('limit' => 15))
                ->addColumn('deptCode', 'string', array('limit' => 11))
                ->addColumn('status', 'enum', array('default' => 'A', 'values' => array('A', 'I')))
                ->addColumn('addDate', 'date', array())
                ->addColumn('approvedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('staffID'), array('unique' => true))
                ->addIndex(array('approvedBy', 'schoolCode', 'buildingCode', 'officeCode', 'deptCode'))
                ->addForeignKey('schoolCode', 'school', 'schoolCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('buildingCode', 'building', 'buildingCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('officeCode', 'room', 'roomCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('deptCode', 'department', 'deptCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('staffID', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('approvedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();

            $this->execute("INSERT INTO `staff` VALUES(1, 1, 'NULL', 'NULL', 'NULL', '', 'NULL', 'A', '$NOW', 1, '$NOW');");
        endif;

        // Migration for table staff_meta
        if (!$this->hasTable('staff_meta')) :
            $table = $this->table('staff_meta', array('id' => false, 'primary_key' => 'sMetaID'));
            $table
                ->addColumn('sMetaID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('jobStatusCode', 'string', array('limit' => 3))
                ->addColumn('jobID', 'integer', array('signed' => true, 'limit' => 11))
                ->addColumn('staffID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('supervisorID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('staffType', 'string', array('limit' => 3))
                ->addColumn('hireDate', 'date', array())
                ->addColumn('startDate', 'date', array())
                ->addColumn('endDate', 'date', array('null' => true))
                ->addColumn('addDate', 'date', array())
                ->addColumn('approvedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('staffID', 'supervisorID', 'approvedBy', 'jobStatusCode'))
                ->addForeignKey('staffID', 'staff', 'staffID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('supervisorID', 'staff', 'staffID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('jobStatusCode', 'job_status', 'typeCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('approvedBy', 'staff', 'staffID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();

            $this->execute("INSERT INTO `staff_meta` VALUES(1, 'FT', 1, 1, 1, 'STA', '2013-11-04', '2013-11-18', '0000-00-00', '$NOW', 1, '$NOW');");
        endif;

        // Migration for table state
        if (!$this->hasTable('state')) :
            $table = $this->table('state', array('id' => false, 'primary_key' => 'id'));
            $table
                ->addColumn('id', 'integer', array('signed' => false, 'identity' => true, 'limit' => 11))
                ->addColumn('code', 'string', array('limit' => 2))
                ->addColumn('name', 'string', array('limit' => 180))
                ->addIndex(array('code'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `state` VALUES(00000000001, 'AL', 'Alabama');");
            $this->execute("INSERT INTO `state` VALUES(00000000002, 'AK', 'Alaska');");
            $this->execute("INSERT INTO `state` VALUES(00000000003, 'AZ', 'Arizona');");
            $this->execute("INSERT INTO `state` VALUES(00000000004, 'AR', 'Arkansas');");
            $this->execute("INSERT INTO `state` VALUES(00000000005, 'CA', 'California');");
            $this->execute("INSERT INTO `state` VALUES(00000000006, 'CO', 'Colorado');");
            $this->execute("INSERT INTO `state` VALUES(00000000007, 'CT', 'Connecticut');");
            $this->execute("INSERT INTO `state` VALUES(00000000008, 'DE', 'Delaware');");
            $this->execute("INSERT INTO `state` VALUES(00000000009, 'DC', 'District of Columbia');");
            $this->execute("INSERT INTO `state` VALUES(00000000010, 'FL', 'Florida');");
            $this->execute("INSERT INTO `state` VALUES(00000000011, 'GA', 'Georgia');");
            $this->execute("INSERT INTO `state` VALUES(00000000012, 'HI', 'Hawaii');");
            $this->execute("INSERT INTO `state` VALUES(00000000013, 'ID', 'Idaho');");
            $this->execute("INSERT INTO `state` VALUES(00000000014, 'IL', 'Illinois');");
            $this->execute("INSERT INTO `state` VALUES(00000000015, 'IN', 'Indiana');");
            $this->execute("INSERT INTO `state` VALUES(00000000016, 'IA', 'Iowa');");
            $this->execute("INSERT INTO `state` VALUES(00000000017, 'KS', 'Kansas');");
            $this->execute("INSERT INTO `state` VALUES(00000000018, 'KY', 'Kentucky');");
            $this->execute("INSERT INTO `state` VALUES(00000000019, 'LA', 'Louisiana');");
            $this->execute("INSERT INTO `state` VALUES(00000000020, 'ME', 'Maine');");
            $this->execute("INSERT INTO `state` VALUES(00000000021, 'MD', 'Maryland');");
            $this->execute("INSERT INTO `state` VALUES(00000000022, 'MA', 'Massachusetts');");
            $this->execute("INSERT INTO `state` VALUES(00000000023, 'MI', 'Michigan');");
            $this->execute("INSERT INTO `state` VALUES(00000000024, 'MN', 'Minnesota');");
            $this->execute("INSERT INTO `state` VALUES(00000000025, 'MS', 'Mississippi');");
            $this->execute("INSERT INTO `state` VALUES(00000000026, 'MO', 'Missouri');");
            $this->execute("INSERT INTO `state` VALUES(00000000027, 'MT', 'Montana');");
            $this->execute("INSERT INTO `state` VALUES(00000000028, 'NE', 'Nebraska');");
            $this->execute("INSERT INTO `state` VALUES(00000000029, 'NV', 'Nevada');");
            $this->execute("INSERT INTO `state` VALUES(00000000030, 'NH', 'New Hampshire');");
            $this->execute("INSERT INTO `state` VALUES(00000000031, 'NJ', 'New Jersey');");
            $this->execute("INSERT INTO `state` VALUES(00000000032, 'NM', 'New Mexico');");
            $this->execute("INSERT INTO `state` VALUES(00000000033, 'NY', 'New York');");
            $this->execute("INSERT INTO `state` VALUES(00000000034, 'NC', 'North Carolina');");
            $this->execute("INSERT INTO `state` VALUES(00000000035, 'ND', 'North Dakota');");
            $this->execute("INSERT INTO `state` VALUES(00000000036, 'OH', 'Ohio');");
            $this->execute("INSERT INTO `state` VALUES(00000000037, 'OK', 'Oklahoma');");
            $this->execute("INSERT INTO `state` VALUES(00000000038, 'OR', 'Oregon');");
            $this->execute("INSERT INTO `state` VALUES(00000000039, 'PA', 'Pennsylvania');");
            $this->execute("INSERT INTO `state` VALUES(00000000040, 'RI', 'Rhode Island');");
            $this->execute("INSERT INTO `state` VALUES(00000000041, 'SC', 'South Carolina');");
            $this->execute("INSERT INTO `state` VALUES(00000000042, 'SD', 'South Dakota');");
            $this->execute("INSERT INTO `state` VALUES(00000000043, 'TN', 'Tennessee');");
            $this->execute("INSERT INTO `state` VALUES(00000000044, 'TX', 'Texas');");
            $this->execute("INSERT INTO `state` VALUES(00000000045, 'UT', 'Utah');");
            $this->execute("INSERT INTO `state` VALUES(00000000046, 'VT', 'Vermont');");
            $this->execute("INSERT INTO `state` VALUES(00000000047, 'VA', 'Virginia');");
            $this->execute("INSERT INTO `state` VALUES(00000000048, 'WA', 'Washington');");
            $this->execute("INSERT INTO `state` VALUES(00000000049, 'WV', 'West Virginia');");
            $this->execute("INSERT INTO `state` VALUES(00000000050, 'WI', 'Wisconsin');");
            $this->execute("INSERT INTO `state` VALUES(00000000051, 'WY', 'Wyoming');");
            $this->execute("INSERT INTO `state` VALUES(00000000052, 'AB', 'Alberta');");
            $this->execute("INSERT INTO `state` VALUES(00000000053, 'BC', 'British Columbia');");
            $this->execute("INSERT INTO `state` VALUES(00000000054, 'MB', 'Manitoba');");
            $this->execute("INSERT INTO `state` VALUES(00000000055, 'NL', 'Newfoundland');");
            $this->execute("INSERT INTO `state` VALUES(00000000056, 'NB', 'New Brunswick');");
            $this->execute("INSERT INTO `state` VALUES(00000000057, 'NS', 'Nova Scotia');");
            $this->execute("INSERT INTO `state` VALUES(00000000058, 'NT', 'Northwest Territories');");
            $this->execute("INSERT INTO `state` VALUES(00000000059, 'NU', 'Nunavut');");
            $this->execute("INSERT INTO `state` VALUES(00000000060, 'ON', 'Ontario');");
            $this->execute("INSERT INTO `state` VALUES(00000000061, 'PE', 'Prince Edward Island');");
            $this->execute("INSERT INTO `state` VALUES(00000000062, 'QC', 'Quebec');");
            $this->execute("INSERT INTO `state` VALUES(00000000063, 'SK', 'Saskatchewan');");
            $this->execute("INSERT INTO `state` VALUES(00000000064, 'YT', 'Yukon Territory');");
        endif;

        // Migration for table stu_acad_cred
        if (!$this->hasTable('stu_acad_cred')) :
            $table = $this->table('stu_acad_cred', array('id' => false, 'primary_key' => 'stuAcadCredID'));
            $table
                ->addColumn('stuAcadCredID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('courseID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('courseSecID', 'integer', array('signed' => true, 'null' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('courseCode', 'string', array('limit' => 25))
                ->addColumn('courseSecCode', 'string', array('null' => true, 'limit' => 50))
                ->addColumn('sectionNumber', 'string', array('null' => true, 'limit' => 5))
                ->addColumn('courseSection', 'string', array('null' => true, 'limit' => 60))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('reportingTerm', 'string', array('limit' => 5))
                ->addColumn('subjectCode', 'string', array('limit' => 11))
                ->addColumn('deptCode', 'string', array('limit' => 11))
                ->addColumn('shortTitle', 'string', array('limit' => 25))
                ->addColumn('longTitle', 'string', array('limit' => 60))
                ->addColumn('compCred', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1))
                ->addColumn('gradePoints', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 2, 'default' => '0.00'))
                ->addColumn('attCred', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1))
                ->addColumn('ceu', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('status', 'enum', array('default' => 'A', 'values' => array('A', 'N', 'D', 'W', 'C')))
                ->addColumn('statusDate', 'date', array())
                ->addColumn('statusTime', 'string', array('limit' => 10))
                ->addColumn('acadLevelCode', 'string', array('limit' => 4))
                ->addColumn('courseLevelCode', 'string', array('limit' => 5))
                ->addColumn('grade', 'string', array('null' => true, 'limit' => 2))
                ->addColumn('creditType', 'string', array('default' => 'I', 'limit' => 6))
                ->addColumn('startDate', 'date', array())
                ->addColumn('endDate', 'date', array('null' => true))
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('addDate', 'date', array('null' => true))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('stuID', 'courseSecID'), array('unique' => true))
                ->addIndex(array('courseSecCode', 'termCode', 'status', 'courseID', 'courseSecID', 'courseCode', 'courseSection', 'subjectCode', 'deptCode', 'addedBy'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('courseID', 'course', 'courseID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('courseSecID', 'course_sec', 'courseSecID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('courseSecCode', 'course_sec', 'courseSecCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('courseSection', 'course_sec', 'courseSection', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('subjectCode', 'subject', 'subjectCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('deptCode', 'department', 'deptCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('addedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table stu_acad_level
        if (!$this->hasTable('stu_acad_level')) :
            $table = $this->table('stu_acad_level', array('id' => false, 'primary_key' => 'id'));
            $table
                ->addColumn('id', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('acadProgCode', 'string', array('limit' => 20))
                ->addColumn('acadLevelCode', 'string', array('limit' => 4))
                ->addColumn('addDate', 'date', array())
                ->addIndex(array('stuID', 'acadProgCode'), array('unique' => true))
                ->addIndex(array('acadProgCode'))
                ->addForeignKey('acadProgCode', 'acad_program', 'acadProgCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table stu_acct_bill
        if (!$this->hasTable('stu_acct_bill')) :
            $table = $this->table('stu_acct_bill', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('billID', 'string', array('limit' => 11))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('authCode', 'string', array('limit' => 23))
                ->addColumn('stu_comments', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('staff_comments', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('balanceDue', 'enum', array('default' => '1', 'values' => array('1', '0')))
                ->addColumn('postedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('billingDate', 'date', array())
                ->addColumn('billTimeStamp', 'datetime', array())
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('billID'), array('unique' => true))
                ->addIndex(array('stuID', 'termCode', 'postedBy'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('postedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table stu_acct_fee
        if (!$this->hasTable('stu_acct_fee')) :
            $table = $this->table('stu_acct_fee', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('billID', 'string', array('limit' => 11))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('type', 'string', array('limit' => 11))
                ->addColumn('description', 'string', array('limit' => 125))
                ->addColumn('amount', 'decimal', array('signed' => true, 'precision' => 6, 'scale' => 2))
                ->addColumn('feeDate', 'date', array())
                ->addColumn('feeTimeStamp', 'datetime', array())
                ->addColumn('postedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('billID', 'stuID', 'termCode', 'postedBy'))
                ->addForeignKey('billID', 'stu_acct_bill', 'billID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('postedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table stu_acct_pp
        if (!$this->hasTable('stu_acct_pp')) :
            $table = $this->table('stu_acct_pp', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('payFrequency', 'enum', array('values' => array('1', '7', '14', '30', '365')))
                ->addColumn('amount', 'decimal', array('signed' => true, 'precision' => 6, 'scale' => 2))
                ->addColumn('startDate', 'date', array())
                ->addColumn('endDate', 'date', array())
                ->addColumn('comments', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('addDate', 'date', array())
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('stuID', 'termCode', 'addedBy'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('addedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table stu_acct_tuition
        if (!$this->hasTable('stu_acct_tuition')) :
            $table = $this->table('stu_acct_tuition', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('total', 'decimal', array('signed' => true, 'precision' => 6, 'scale' => 2))
                ->addColumn('postedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('tuitionTimeStamp', 'datetime', array())
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('termCode', 'stuID', 'postedBy'))
                ->addForeignKey('postedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table stu_course_sec
        if (!$this->hasTable('stu_course_sec')) :
            $table = $this->table('stu_course_sec', array('id' => false, 'primary_key' => 'id'));
            $table
                ->addColumn('id', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('courseSecID', 'integer', array('signed' => true, 'null' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('courseSecCode', 'string', array('limit' => 50))
                ->addColumn('courseSection', 'string', array('limit' => 60))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('courseCredits', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('ceu', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('regDate', 'date', array('null' => true))
                ->addColumn('regTime', 'string', array('null' => true, 'limit' => 10))
                ->addColumn('status', 'enum', array('default' => 'A', 'values' => array('A', 'N', 'D', 'W', 'C')))
                ->addColumn('statusDate', 'date', array())
                ->addColumn('statusTime', 'string', array('limit' => 10))
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('stuID', 'courseSecID'), array('unique' => true))
                ->addIndex(array('courseSecCode', 'termCode', 'addedBy', 'status', 'courseSecID', 'courseSection'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('courseSecID', 'course_sec', 'courseSecID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('courseSecCode', 'course_sec', 'courseSecCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('courseSection', 'course_sec', 'courseSection', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('addedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table stu_program
        if (!$this->hasTable('stu_program')) :
            $table = $this->table('stu_program', array('id' => false, 'primary_key' => 'stuProgID'));
            $table
                ->addColumn('stuProgID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('advisorID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('catYearCode', 'string', array('limit' => 11))
                ->addColumn('acadProgCode', 'string', array('limit' => 20))
                ->addColumn('currStatus', 'string', array('limit' => 1))
                ->addColumn('eligible_to_graduate', 'enum', array('default' => '0', 'values' => array('1', '0')))
                ->addColumn('antGradDate', 'string', array('null' => true, 'limit' => 8))
                ->addColumn('graduationDate', 'date', array())
                ->addColumn('statusDate', 'date', array())
                ->addColumn('startDate', 'date', array())
                ->addColumn('endDate', 'date', array())
                ->addColumn('comments', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('approvedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('stuID', 'acadProgCode'), array('unique' => true))
                ->addIndex(array('approvedBy', 'acadProgCode', 'currStatus', 'advisorID', 'catYearCode'))
                ->addForeignKey('acadProgCode', 'acad_program', 'acadProgCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('approvedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('advisorID', 'staff', 'staffID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('catYearCode', 'acad_year', 'acadYearCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table stu_rgn_cart
        if (!$this->hasTable('stu_rgn_cart')) :
            $table = $this->table('stu_rgn_cart', array('id' => false));
            $table
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('courseSecID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('deleteDate', 'date', array())
                ->addIndex(array('stuID', 'courseSecID'), array('unique' => true))
                ->create();
        endif;

        // Migration for table stu_term
        if (!$this->hasTable('stu_term')) :
            $table = $this->table('stu_term', array('id' => false));
            $table
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('termCredits', 'decimal', array('signed' => true, 'precision' => 6, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('addDateTime', 'datetime', array())
                ->addColumn('acadLevelCode', 'string', array('limit' => 4))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('stuID', 'termCode', 'acadLevelCode'), array('unique' => true))
                ->addIndex(array('termCode'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table stu_term_gpa
        if (!$this->hasTable('stu_term_gpa')) :
            $table = $this->table('stu_term_gpa', array('id' => false));
            $table
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('acadLevelCode', 'string', array('limit' => 4))
                ->addColumn('attCred', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('compCred', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('gradePoints', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1, 'default' => '0.0'))
                ->addColumn('termGPA', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 2, 'default' => '0.00'))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('stuID', 'termCode', 'acadLevelCode'), array('unique' => true))
                ->addIndex(array('termCode'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table stu_term_load
        if (!$this->hasTable('stu_term_load')) :
            $table = $this->table('stu_term_load', array('id' => false));
            $table
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('stuLoad', 'string', array('limit' => 2))
                ->addColumn('acadLevelCode', 'string', array('limit' => 4))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('stuID', 'termCode', 'acadLevelCode'), array('unique' => true))
                ->addIndex(array('stuLoad', 'termCode'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('termCode', 'term', 'termCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table student
        if (!$this->hasTable('student')) :
            $table = $this->table('student', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('status', 'enum', array('default' => 'A', 'values' => array('A', 'I')))
                ->addColumn('tags', 'string', array('limit' => 255))
                ->addColumn('addDate', 'datetime', array())
                ->addColumn('approvedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('stuID'), array('unique' => true))
                ->addIndex(array('approvedBy', 'status'))
                ->addForeignKey('stuID', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('approvedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table student_load_rule
        if (!$this->hasTable('student_load_rule')) :
            $table = $this->table('student_load_rule', array('id' => false, 'primary_key' => 'slrID'));
            $table
                ->addColumn('slrID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('status', 'string', array('limit' => 1))
                ->addColumn('min_cred', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1))
                ->addColumn('max_cred', 'decimal', array('signed' => true, 'precision' => 4, 'scale' => 1))
                ->addColumn('term', 'string', array('limit' => 255))
                ->addColumn('acadLevelCode', 'string', array('limit' => 255))
                ->addColumn('active', 'enum', array('values' => array('1', '0')))
                ->create();

            $this->execute("INSERT INTO `student_load_rule` VALUES(00000000001, 'F', 12.0, 24.0, 'FA\\\\SP\\\\SU', 'CE\\\\UG\\\\GR\\\\PhD', '1');");
            $this->execute("INSERT INTO `student_load_rule` VALUES(00000000002, 'Q', 9.0, 11.0, 'FA\\\\SP\\\\SU', 'CE\\\\UG\\\\GR\\\\PhD', '1');");
            $this->execute("INSERT INTO `student_load_rule` VALUES(00000000003, 'H', 6.0, 8.0, 'FA\\\\SP\\\\SU', 'CE\\\\UG\\\\GR\\\\PhD', '1');");
            $this->execute("INSERT INTO `student_load_rule` VALUES(00000000004, 'L', 0.0, 5.0, 'FA\\\\SP\\\\SU', 'CE\\\\UG\\\\GR\\\\PhD', '1');");
        endif;

        // Migration for table subject
        if (!$this->hasTable('subject')) :
            $table = $this->table('subject', array('id' => false, 'primary_key' => 'subjectID'));
            $table
                ->addColumn('subjectID', 'integer', array('signed' => true, 'identity' => true, 'limit' => 11))
                ->addColumn('subjectCode', 'string', array('limit' => 11))
                ->addColumn('subjectName', 'string', array('limit' => 180))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('subjectCode'), array('unique' => true))
                ->create();

            $this->execute("INSERT INTO `subject` VALUES(1, 'NULL', '', '$NOW');");
        endif;

        // Migration for table term
        if (!$this->hasTable('term')) :
            $table = $this->table('term', array('id' => false, 'primary_key' => 'termID'));
            $table
                ->addColumn('termID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('semCode', 'string', array('limit' => 11))
                ->addColumn('termCode', 'string', array('limit' => 11))
                ->addColumn('termName', 'string', array('default' => '', 'limit' => 180))
                ->addColumn('reportingTerm', 'string', array('limit' => 5))
                ->addColumn('dropAddEndDate', 'date', array('default' => '0000-00-00'))
                ->addColumn('termStartDate', 'date', array('default' => '0000-00-00'))
                ->addColumn('termEndDate', 'date', array('default' => '0000-00-00'))
                ->addColumn('active', 'enum', array('default' => '1', 'values' => array('1', '0')))
                ->addIndex(array('termCode'), array('unique' => true))
                ->addIndex(array('semCode'))
                ->addForeignKey('semCode', 'semester', 'semCode', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();

            $this->execute("INSERT INTO `term` VALUES(00000000001, 'NULL', 'NULL', '', '', '$NOW', '$NOW', '$NOW', '1');");
        endif;

        // Migration for table timesheet
        if (!$this->hasTable('timesheet')) :
            $table = $this->table('timesheet', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('employeeID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('jobID', 'integer', array('signed' => true, 'limit' => 11))
                ->addColumn('workWeek', 'date', array())
                ->addColumn('startDateTime', 'datetime', array())
                ->addColumn('endDateTime', 'datetime', array())
                ->addColumn('note', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('status', 'enum', array('default' => 'P', 'values' => array('P', 'R', 'A')))
                ->addColumn('addDate', 'string', array('limit' => 20))
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('approvedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('employeeID', 'addedBy'))
                ->addForeignKey('employeeID', 'staff', 'staffID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('addedBy', 'staff', 'staffID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table transfer_equivalent
        if (!$this->hasTable('transfer_equivalent')) :
            $table = $this->table('transfer_equivalent', array('id' => false, 'primary_key' => 'equivID'));
            $table
                ->addColumn('equivID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('extrID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('courseID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('startDate', 'date', array())
                ->addColumn('endDate', 'date', array())
                ->addColumn('grade', 'string', array('limit' => 2))
                ->addColumn('comment', 'text', array('limit' => MysqlAdapter::TEXT_LONG))
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('addDate', 'date', array())
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('extrID', 'courseID', 'addedBy'))
                ->addForeignKey('extrID', 'external_course', 'extrID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('courseID', 'course', 'courseID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('addedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table transfer_credit
        if (!$this->hasTable('transfer_credit')) :
            $table = $this->table('transfer_credit', array('id' => false, 'primary_key' => 'ID'));
            $table
                ->addColumn('ID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('equivID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('stuAcadCredID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('addDate', 'date', array())
                ->addIndex(array('equivID', 'stuAcadCredID', 'addedBy'))
                ->addForeignKey('equivID', 'transfer_equivalent', 'equivID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('stuAcadCredID', 'stu_acad_cred', 'stuAcadCredID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('addedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        // Migration for table gradebook
        if (!$this->hasTable('gradebook')) :
            $table = $this->table('gradebook', array('id' => false, 'primary_key' => 'gbID'));
            $table
                ->addColumn('gbID', 'integer', array('signed' => true, 'identity' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('assignID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('courseSecID', 'integer', array('signed' => true, 'null' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('facID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('stuID', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('grade', 'string', array('limit' => 2))
                ->addColumn('addDate', 'date', array())
                ->addColumn('addedBy', 'integer', array('signed' => true, 'limit' => MysqlAdapter::INT_BIG))
                ->addColumn('LastUpdate', 'timestamp', array('default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'))
                ->addIndex(array('assignID', 'facID', 'stuID'), array('unique' => true))
                ->addIndex(array('assignID', 'courseSecID', 'facID', 'stuID', 'addedBy'))
                ->addForeignKey('assignID', 'assignment', 'assignID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('courseSecID', 'course_sec', 'courseSecID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('facID', 'staff', 'staffID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('stuID', 'student', 'stuID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->addForeignKey('addedBy', 'person', 'personID', array('delete' => 'RESTRICT', 'update' => 'CASCADE'))
                ->create();
        endif;

        $this->execute("SET FOREIGN_KEY_CHECKS=1;");

        if (!file_exists('config.php')) {
            copy('config.sample.php', 'config.php');
        }
        $file = 'config.php';
        $config = file_get_contents($file);

        $config = str_replace('{product}', 'eduTrac SIS', $config);
        $config = str_replace('{release}', trim(file_get_contents('RELEASE')), $config);
        $config = str_replace('{datenow}', date('Y-m-d h:m:s'), $config);
        $config = str_replace('{hostname}', $host, $config);
        $config = str_replace('{database}', $name, $config);
        $config = str_replace('{username}', $user, $config);
        $config = str_replace('{password}', $pass, $config);

        file_put_contents($file, $config);
    }
}
