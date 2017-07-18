<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class FixFields extends AbstractMigration
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
        
        $this->execute("ALTER TABLE `stal` MODIFY COLUMN `startDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `stal` SET `startTerm` = NULL WHERE `startTerm` = '';");
        
        $this->execute("UPDATE `stal` SET `startDate` = NULL WHERE `startDate` = '0000-00-00' OR `startDate` = '';");
        
        $this->execute("ALTER TABLE `sttr` MODIFY COLUMN `stuLoad` CHAR(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `sttr` SET `stuLoad` = NULL WHERE stuLoad = '';");
        
        $this->execute("ALTER TABLE `sttr` MODIFY COLUMN `created` datetime DEFAULT NULL;");
        
        $this->execute("UPDATE `sttr` SET `created` = NULL WHERE `created` = '0000-00-00 00:00:00' OR `created` = '';");
        
        $this->execute("ALTER TABLE `stac` MODIFY COLUMN `startDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `stac` SET `startDate` = NULL WHERE `startDate` = '0000-00-00' OR `startDate` = '';");
        
        $this->execute("UPDATE `stac` SET `endDate` = NULL WHERE `endDate` = '0000-00-00' OR `endDate` = '';");
        
        $this->execute("ALTER TABLE `stac` MODIFY COLUMN `statusDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `stac` SET `statusDate` = NULL WHERE `statusDate` = '0000-00-00' OR `statusDate` = '';");
        
        $this->execute("ALTER TABLE `stac` MODIFY COLUMN `statusTime` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `stac` SET `statusTime` = NULL WHERE `statusTime` = '';");
        
        $this->execute("ALTER TABLE `application` MODIFY COLUMN `applDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `application` SET `applDate` = NULL WHERE `applDate` = '0000-00-00' OR `applDate` = '';");
        
        $this->execute("ALTER TABLE `last_login` MODIFY COLUMN `loginTimeStamp` datetime DEFAULT NULL;");
        
        $this->execute("UPDATE `last_login` SET `loginTimeStamp` = NULL WHERE `loginTimeStamp` = '0000-00-00 00:00:00' OR `loginTimeStamp` = '';");
        
        $this->execute("UPDATE `address` SET `endDate` = NULL WHERE `endDate` = '0000-00-00' OR `endDate` = '';");
        
        $this->execute("UPDATE `perc` SET `endDate` = NULL WHERE `endDate` = '0000-00-00' OR `endDate` = '';");
        
        $this->execute("ALTER TABLE `campaign` MODIFY COLUMN `html` longtext COLLATE utf8mb4_unicode_ci NOT NULL;");
        
        $this->execute("ALTER TABLE `campaign` MODIFY COLUMN `text` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("ALTER TABLE `campaign` MODIFY COLUMN `footer` text COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("ALTER TABLE `campaign` MODIFY COLUMN `attachment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("ALTER TABLE `error` MODIFY COLUMN `string` text COLLATE utf8mb4_unicode_ci NOT NULL;");
        
        $this->execute("ALTER TABLE `gl_account` MODIFY COLUMN `gl_acct_memo` text COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("ALTER TABLE `gl_journal_entry` MODIFY COLUMN `gl_jentry_description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("ALTER TABLE `gl_transaction` MODIFY COLUMN `gl_trans_memo` text COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("ALTER TABLE `list` MODIFY COLUMN `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("ALTER TABLE `list` MODIFY COLUMN `rule` text COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("ALTER TABLE `met_link` MODIFY COLUMN `link_src` text COLLATE utf8mb4_unicode_ci NOT NULL;");
        
        $this->execute("ALTER TABLE `person` MODIFY COLUMN `photo` text COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("ALTER TABLE `template` MODIFY COLUMN `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL;");
        
        $this->execute("ALTER TABLE `tracking_link` MODIFY COLUMN `url` text COLLATE utf8mb4_unicode_ci NOT NULL;");
        
        $this->execute("SET FOREIGN_KEY_CHECKS=1;");
    }
}
