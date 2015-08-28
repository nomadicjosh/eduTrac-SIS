<?php
namespace app\src;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Student DBObject Class
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */

class Student
{

    private $_ID;
    private $_stuID;
    private $_status;
    private $_stuStatus;
    private $_addDate;
    private $_approvedBy;
    private $_LastUpdate;
    private $_address1;
    private $_address2;
    private $_city;
    private $_state;
    private $_zip;
    private $_phone1;
    private $_email1;
    private $_dob;
    private $_app;
    private $_cache;

    public function __construct()
    {
        $this->_app = \Liten\Liten::getInstance();
        $this->_cache = new \app\src\DBCache;
    }

    /**
     * Load one row into var_class. To use the vars use for exemple echo $class->getVar_name; 
     *
     * @param int $key_row
     * @return mixed
     */
    public function Load_from_key($key_row)
    {
        $stu = $this->_app->db->query("SELECT 
                        a.ID,a.stuID,a.addDate,a.approvedBy,a.LastUpdate,a.status,
                        CASE a.status 
                        WHEN 'A' THEN 'Active' 
                        ELSE 'Inactive' 
                        END AS 'stuStatus', 
                        b.address1,b.address2,b.city,b.state,b.zip,
                        b.phone1,b.email1,c.dob 
                    FROM student a 
                    LEFT JOIN address b ON a.stuID = b.personID 
                    LEFT JOIN person c ON a.stuID = c.personID 
                    WHERE a.stuID = ? 
                    AND b.addressStatus = 'C' 
                    AND (b.endDate = '' OR b.endDate = '0000-00-00')", [$key_row]
        );
        $q = $stu->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        foreach ($q as $row) {
            $this->_ID = $row["ID"];
            $this->_stuID = $row["stuID"];
            $this->_status = $row["status"];
            $this->_stuStatus = $row["stuStatus"];
            $this->_addDate = $row["addDate"];
            $this->_approvedBy = $row["approvedBy"];
            $this->_LastUpdate = $row["LastUpdate"];
            $this->_address1 = $row["address1"];
            $this->_address2 = $row["address2"];
            $this->_city = $row["city"];
            $this->_state = $row["state"];
            $this->_zip = $row["zip"];
            $this->_phone1 = $row["phone1"];
            $this->_email1 = $row["email1"];
            $this->_dob = $row["dob"];
        }
    }

    public function getRestriction()
    {
        $rest = $this->_app->db->query("SELECT 
                        b.rstrCode,a.severity,b.description,c.deptEmail,c.deptPhone,c.deptName,
        				GROUP_CONCAT(DISTINCT b.rstrCode SEPARATOR ',') AS 'Restriction' 
    				FROM restriction a 
					LEFT JOIN restriction_code b ON a.rstrCode = b.rstrCode 
					LEFT JOIN department c ON b.deptCode = c.deptCode 
					WHERE a.endDate <= '0000-00-00' 
					AND a.stuID = ? 
                    AND a.rstrCode <> 'FERPA' 
					GROUP BY a.rstrCode,a.stuID 
					HAVING a.stuID = ?", [ $this->_stuID, $this->_stuID]
        );
        $q = $rest->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        return $q;
    }

    /**
     * @return ID - int(11) unsigned zerofill
     */
    public function getID()
    {
        return $this->_ID;
    }

    /**
     * @return stuID - int(8) unsigned zerofill
     */
    public function getStuID()
    {
        return $this->_stuID;
    }

    /**
     * @return current status
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * @return current status
     */
    public function getStuStatus()
    {
        return $this->_stuStatus;
    }

    /**
     * @return addDate - datetime
     */
    public function getAddDate()
    {
        return $this->_addDate;
    }

    /**
     * @return addedBy - int(8)
     */
    public function getAddedBy()
    {
        return $this->_addedBy;
    }

    /**
     * @return LastUpdate - timestamp
     */
    public function getLastUpdate()
    {
        return $this->_LastUpdate;
    }

    public function getAddress1()
    {
        return $this->_address1;
    }

    public function getAddress2()
    {
        return $this->_address2;
    }

    public function getCity()
    {
        return $this->_city;
    }

    public function getState()
    {
        return $this->_state;
    }

    public function getZip()
    {
        return $this->_zip;
    }

    public function getPhone1()
    {
        return $this->_phone1;
    }

    public function getEmail1()
    {
        return $this->_email1;
    }

    public function getDob()
    {
        return $this->_dob;
    }

    public function getStuHeader()
    {

        ?>

        <!-- List Widget -->
        <div class="relativeWrap">
            <div class="widget">
                <div class="widget-head">
                    <h4 class="heading glyphicons user"><i></i><?= get_name(_h($this->getStuID())); ?></h4>&nbsp;&nbsp;
                    <?php if(!isset($_COOKIE['SWITCH_USERBACK']) && _h($this->getStuID()) != get_persondata('personID')) : ?>
                    <span<?=ae('login_as_user');?> class="label label-inverse"><a href="<?=url('/');?>switchUserTo/<?=_h($this->getStuID());?>/"><font color="#FFFFFF"><?= _t('Switch To'); ?></font></a></span>
                    <?php endif; ?>
                    <?php if(get_persondata('personID') == $this->getStuID() && !hasPermission('access_dashboard')) : ?>
                    <a href="<?= url('/'); ?>profile/" class="heading pull-right"><?= _h($this->getStuID()); ?></a>
                    <?php else : ?>
                    <a href="<?=url('/');?>stu/<?=_h($this->getStuID());?>/" class="heading pull-right"><?=_h($this->getStuID());?></a>
                    <?php endif; ?>
                </div>
                <div class="widget-body">
                    <!-- 3 Column Grid / One Third -->
                    <div class="row">

                        <!-- One Third Column -->
                        <div class="col-md-2">
                            <?= getSchoolPhoto($this->getStuID(), $this->getEmail1(), '90'); ?>
                        </div>
                        <!-- // One Third Column END -->

                        <!-- One Third Column -->
                        <div class="col-md-3">
                            <p><?= _h($this->getAddress1()); ?> <?= _h($this->getAddress2()); ?></p>
                            <p><?= _h($this->getCity()); ?> <?= _h($this->getState()); ?> <?= _h($this->getZip()); ?></p>
                            <p><strong><?= _t('Phone:'); ?></strong> <?= _h($this->getPhone1()); ?></p>
                        </div>
                        <!-- // One Third Column END -->

                        <!-- One Third Column -->
                        <div class="col-md-3">
                            <p><strong><?= _t('Email:'); ?></strong> <a href="mailto:<?= _h($this->getEmail1()); ?>"><?= _h($this->getEmail1()); ?></a></p>
                            <p><strong><?= _t('Birth Date:'); ?></strong> <?= (_h($this->getDob()) > '0000-00-00' ? date('D, M d, o', strtotime(_h($this->getDob()))) : ''); ?></p>
                            <p><strong><?= _t('Status:'); ?></strong> <?= _h($this->getStuStatus()); ?></p>
                        </div>
                        <!-- // One Third Column END -->

                        <!-- One Third Column -->
                        <div class="col-md-3">
                            <p><strong><?= _t('FERPA:'); ?></strong> <?= is_ferpa(_h($this->getStuID())); ?> 
                                    <?php if (is_ferpa(_h($this->getStuID())) == 'Yes') : ?>
                                    <a href="#FERPA" data-toggle="modal"><img style="vertical-align:top !important;" src="<?= url('/'); ?>static/common/theme/images/exclamation.png" /></a>
                                    <?php else : ?>
                                    <a href="#FERPA" data-toggle="modal"><img style="vertical-align:top !important;" src="<?= url('/'); ?>static/common/theme/images/information.png" /></a>
                                <?php endif; ?>
                            </p>
                            <p><strong><?= _t('Restriction(s):'); ?></strong> 
                                <?php $prefix = ''; foreach ($this->getRestriction() as $v) : ?>
                                    <?=$prefix;?><span data-toggle="popover" data-title="<?= _h($v['description']); ?>" data-content="Contact: <?= _h($v['deptName']); ?> <?= (_h($v['deptEmail']) != '') ? ' | ' . $v['deptEmail'] : ''; ?><?= (_h($v['deptPhone']) != '') ? ' | ' . $v['deptPhone'] : ''; ?><?= (_h($v['severity']) == 99) ? _t(' | Restricted from registering for courses.') : ''; ?>" data-placement="bottom"><a href="#"><?= _h($v['Restriction']); ?></a></span>
                                <?php $prefix = ', '; endforeach; ?>
                            </p>
                            <p><strong><?= _t('Entry Date:'); ?></strong> <?= date('D, M d, o', strtotime(_h($this->getAddDate()))); ?></p>
                        </div>
                        <!-- // One Third Column END -->

                    </div>
                    <!-- // 3 Column Grid / One Third END -->
                </div>
            </div>
        </div>
        <!-- // List Widget END -->
        
        <!-- Modal -->
        <div class="modal fade" id="FERPA">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal heading -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="modal-title"><?=_t( 'Family Educational Rights and Privacy Act (FERPA)' );?></h3>
                    </div>
                    <!-- // Modal heading END -->
                    <!-- Modal body -->
                    <div class="modal-body">
                        <p><?=_t('"FERPA gives parents certain rights with respect to their children\'s education records. 
                        These rights transfer to the student when he or she reaches the age of 18 or attends a school beyond 
                        the high school level. Students to whom the rights have transferred are \'eligible students.\'"');?></p>
                        <p><?=_t('If the FERPA restriction states "Yes", then the student has requested that none of their 
                        information be given out without their permission. To get a better understanding of FERPA, visit 
                        the U.S. DOE\'s website @ ') . 
                        '<a href="http://www2.ed.gov/policy/gen/guid/fpco/ferpa/index.html">http://www2.ed.gov/policy/gen/guid/fpco/ferpa/index.html</a>.';?></p>
                    </div>
                    <!-- // Modal body END -->
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <a href="#" class="btn btn-default" data-dismiss="modal"><?=_t( 'Close' );?></a> 
                    </div>
                    <!-- // Modal footer END -->
                </div>
            </div>
        </div>
        <!-- // Modal END -->

        <?php
    }
}
