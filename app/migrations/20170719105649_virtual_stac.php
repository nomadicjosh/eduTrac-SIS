<?php
use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class VirtualStac extends AbstractMigration
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
        
        $this->execute(
            "INSERT IGNORE INTO sttr (stuID,termCode,acadLevelCode,attCred,compCred,gradePoints,gpa,created) 
            SELECT stac.stuID,stac.termCode,stac.acadLevelCode,COALESCE(SUM(stac.attCred),0),COALESCE(SUM(stac.compCred),0),COALESCE(SUM(stac.gradePoints),0),COALESCE(SUM(stac.gradePoints),0)/COALESCE(SUM(stac.attCred),0),NOW()
            FROM stac 
            LEFT JOIN grade_scale ON stac.grade = grade_scale.grade
            WHERE stac.creditType = 'I' 
            AND grade_scale.count_in_gpa = '1' 
            AND (stac.grade IS NOT NULL OR stac.grade <> '') 
            GROUP BY stac.stuID,stac.termCode,stac.acadLevelCode"
        );
        
        if ($this->hasTable('v_stac')) :
            $this->execute('DROP TABLE IF EXISTS v_stac');
        endif;

        if (!$this->hasTable('v_stac')) :
            $this->execute(
                "CREATE VIEW v_stac AS
                SELECT stac.stuID,stac.acadLevelCode,SUM(stac.attCred) attempted,SUM(stac.compCred) completed,
                SUM(stac.gradePoints) points,SUM(stac.gradePoints)/SUM(stac.attCred) AS gpa
                FROM stac
                LEFT JOIN grade_scale ON stac.grade = grade_scale.grade
                WHERE grade_scale.count_in_gpa = '1'
                AND (stac.grade IS NOT NULL
                OR stac.grade <> '')
                AND stac.creditType = 'I'
                GROUP BY stac.stuID,stac.acadLevelCode;"
            );
        endif;

        if ($this->hasTable('v_scrd')) :
            $this->execute('DROP TABLE IF EXISTS v_scrd');
        endif;

        if (!$this->hasTable('v_scrd')) :
            $this->execute(
                "CREATE VIEW v_scrd AS
                    SELECT 
                        sttr.stuID,sttr.acadLevelCode AS acadLevel,sacp.acadProgCode AS prog,v_stac.attempted,
                        v_stac.completed,v_stac.points,v_stac.gpa,
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
                    JOIN
                        v_stac
                    ON
                        sttr.stuID = v_stac.stuID AND sttr.acadLevelCode = v_stac.acadLevelCode
                    WHERE
                        sttr.stuID = sacp.stuID
                    AND
                        sttr.acadLevelCode = acad_program.acadLevelCode
                    AND
                        sacp.currStatus = 'A'
                    GROUP BY sttr.stuID,sttr.acadLevelCode
                    ORDER BY sttr.stuID;"
            );
        endif;
        
        $this->execute("SET FOREIGN_KEY_CHECKS=1;");
    }
}
