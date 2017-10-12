<?php
use Phinx\Migration\AbstractMigration;

class NotNull extends AbstractMigration
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
        
        $this->execute("ALTER TABLE `last_login` MODIFY COLUMN `loginTimeStamp` datetime DEFAULT NULL;");
        
        $this->execute("UPDATE `last_login` SET loginTimeStamp = NULL WHERE loginTimeStamp = '0000-00-00 00:00:00';");
        
        $this->execute("UPDATE `last_login` SET loginTimeStamp = NULL WHERE loginTimeStamp = '';");
        
        $this->execute("ALTER TABLE `stac` MODIFY COLUMN `startDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `stac` SET startDate = NULL WHERE startDate = '0000-00-00';");
        
        $this->execute("UPDATE `stac` SET startDate = NULL WHERE startDate = '';");
        
        $this->execute("ALTER TABLE `stac` MODIFY COLUMN `endDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `stac` SET endDate = NULL WHERE endDate = '0000-00-00';");
        
        $this->execute("UPDATE `stac` SET endDate = NULL WHERE endDate = '';");
        
        $this->execute("ALTER TABLE `stac` MODIFY COLUMN `statusDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `stac` SET statusDate = NULL WHERE statusDate = '0000-00-00';");
        
        $this->execute("UPDATE `stac` SET statusDate = NULL WHERE statusDate = '';");
        
        $this->execute("ALTER TABLE `stac` MODIFY COLUMN `statusTime` char(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `stac` SET statusTime = NULL WHERE statusTime = '';");
        
        $this->execute("ALTER TABLE `stac` MODIFY COLUMN `attCred` decimal(4,1) NOT NULL DEFAULT '0.0';");
        
        $this->execute("UPDATE `stac` SET grade = NULL WHERE grade = '';");
        
        $this->execute("ALTER TABLE `stal` MODIFY COLUMN `startTerm` char(22) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `stal` SET startTerm = NULL WHERE startTerm = '';");
        
        $this->execute("ALTER TABLE `stal` MODIFY COLUMN `startDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `stal` SET startDate = NULL WHERE startDate = '0000-00-00';");
        
        $this->execute("UPDATE `stal` SET startDate = NULL WHERE startDate = '';");
        
        $this->execute("ALTER TABLE `stal` MODIFY COLUMN `endDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `stal` SET endDate = NULL WHERE endDate = '0000-00-00';");
        
        $this->execute("UPDATE `stal` SET endDate = NULL WHERE endDate = '';");
        
        $this->execute("ALTER TABLE `sttr` MODIFY COLUMN `stuLoad` char(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `sttr` SET stuLoad = NULL WHERE stuLoad = '';");
        
        $this->execute("UPDATE `sttr` SET created = NULL WHERE created = '0000-00-00';");
        
        $this->execute("UPDATE `sttr` SET created = NULL WHERE created = '';");
        
        $this->execute("ALTER TABLE `application` MODIFY COLUMN `applDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `application` SET applDate = NULL WHERE applDate = '0000-00-00';");
        
        $this->execute("UPDATE `application` SET applDate = NULL WHERE applDate = '';");
        
        $this->execute("ALTER TABLE `application` MODIFY COLUMN `startTerm` char(22) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `application` SET startTerm = NULL WHERE startTerm = '';");
        
        $this->execute("ALTER TABLE `application` MODIFY COLUMN `admitStatus` char(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `application` SET admitStatus = NULL WHERE admitStatus = '';");
        
        $this->execute("ALTER TABLE `application` MODIFY COLUMN `exam` char(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `application` SET exam = NULL WHERE exam = '';");
        
        $this->execute("ALTER TABLE `application` MODIFY COLUMN `PSAT_Verbal` char(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `application` SET PSAT_Verbal = NULL WHERE PSAT_Verbal = '';");
        
        $this->execute("ALTER TABLE `application` MODIFY COLUMN `PSAT_Math` char(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `application` SET PSAT_Math = NULL WHERE PSAT_Math = '';");
        
        $this->execute("ALTER TABLE `application` MODIFY COLUMN `SAT_Verbal` char(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `application` SET SAT_Verbal = NULL WHERE SAT_Verbal = '';");
        
        $this->execute("ALTER TABLE `application` MODIFY COLUMN `SAT_Math` char(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `application` SET SAT_Math = NULL WHERE SAT_Math = '';");
        
        $this->execute("ALTER TABLE `application` MODIFY COLUMN `ACT_English` char(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `application` SET ACT_English = NULL WHERE ACT_English = '';");
        
        $this->execute("ALTER TABLE `application` MODIFY COLUMN `ACT_Math` char(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `application` SET ACT_Math = NULL WHERE ACT_Math = '';");
        
        $this->execute("ALTER TABLE `application` MODIFY COLUMN `appl_comments` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `application` SET appl_comments = NULL WHERE appl_comments = '';");
        
        $this->execute("ALTER TABLE `application` MODIFY COLUMN `staff_comments` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `application` SET staff_comments = NULL WHERE staff_comments = '';");
        
        $this->execute("ALTER TABLE `course` MODIFY COLUMN `endDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `course` SET endDate = NULL WHERE endDate = '0000-00-00';");
        
        $this->execute("UPDATE `course` SET endDate = NULL WHERE endDate = '';");
        
        $this->execute("ALTER TABLE `course` MODIFY COLUMN `preReq` text DEFAULT NULL;");
        
        $this->execute("UPDATE `course` SET preReq = NULL WHERE preReq = '';");
        
        $this->execute("ALTER TABLE `perc` MODIFY COLUMN `endDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `perc` SET endDate = NULL WHERE endDate = '0000-00-00';");
        
        $this->execute("UPDATE `perc` SET endDate = NULL WHERE endDate = '';");
        
        $this->execute("ALTER TABLE `student` MODIFY COLUMN `tags` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `student` SET tags = NULL WHERE tags = '';");
        
        $this->execute("ALTER TABLE `person` MODIFY COLUMN `altID` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `person` SET altID = NULL WHERE altID = '';");
        
        $this->execute("ALTER TABLE `person` MODIFY COLUMN `dob` date DEFAULT NULL;");
        
        $this->execute("UPDATE `person` SET dob = NULL WHERE dob = '0000-00-00';");
        
        $this->execute("UPDATE `person` SET dob = NULL WHERE dob = '';");
        
        $this->execute("ALTER TABLE `person` MODIFY COLUMN `tags` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `person` SET tags = NULL WHERE tags = '';");
        
        $this->execute("ALTER TABLE `address` MODIFY COLUMN `endDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `address` SET endDate = NULL WHERE endDate = '0000-00-00';");
        
        $this->execute("UPDATE `address` SET endDate = NULL WHERE endDate = '';");
        
        $this->execute("ALTER TABLE `acad_program` MODIFY COLUMN `endDate` date DEFAULT NULL;");
        
        $this->execute("UPDATE `acad_program` SET endDate = NULL WHERE endDate = '0000-00-00';");
        
        $this->execute("UPDATE `acad_program` SET endDate = NULL WHERE endDate = '';");
        
        $this->execute("ALTER TABLE `gradebook` MODIFY COLUMN `grade` char(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL;");
        
        $this->execute("UPDATE `gradebook` SET grade = NULL WHERE grade = '';");
        
        $this->execute("SET FOREIGN_KEY_CHECKS=1;");

    }
}
