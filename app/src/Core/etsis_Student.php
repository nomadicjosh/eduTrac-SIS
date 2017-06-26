<?php namespace app\src\Core;

use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Student API: etsis_Student Class
 *
 * @license GPLv3
 *         
 * @since 6.2.3
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
final class etsis_Student
{

    /**
     * Application object;
     * 
     * @var object
     */
    public $app;

    /**
     * Primary key ID.
     *
     * @var int
     */
    public $id;

    /**
     * Student ID.
     *
     * @var int
     */
    public $stuID;

    /**
     * Student status.
     *
     * @var int
     */
    public $stuStatus;

    /**
     * Person status.
     *
     * @var int
     */
    public $naeStatus;

    /**
     * The student's add date.
     *
     * @var string
     */
    public $stuAddDate = '0000-00-00';

    /**
     * Alternative student ID.
     *
     * @var string
     */
    public $altID;

    /**
     * The student's username.
     *
     * @var string
     */
    public $uname;

    /**
     * The student's prefix.
     *
     * @var string
     */
    public $prefix;

    /**
     * The student's person type.
     *
     * @var string
     */
    public $personType;

    /**
     * The student's first name.
     *
     * @var string
     */
    public $fname;

    /**
     * The student's last name.
     *
     * @var string
     */
    public $lname;

    /**
     * The student's middle initial.
     *
     * @var string
     */
    public $mname;

    /**
     * The student's email address.
     *
     * @var string
     */
    public $email;

    /**
     * The student's social security number.
     *
     * @var int
     */
    public $ssn;

    /**
     * The student's date of birth.
     *
     * @var string
     */
    public $dob;

    /**
     * The student's veteran status.
     *
     * @var bool
     */
    public $veteran;

    /**
     * The student's ethnicity.
     *
     * @var string
     */
    public $ethnicity;

    /**
     * The student's gender.
     *
     * @var string
     */
    public $gender;

    /**
     * The student's emergency contact person.
     *
     * @var string
     */
    public $emergency_contact;

    /**
     * The student's emergency contact person phone number.
     *
     * @var string
     */
    public $emergency_contact_phone;

    /**
     * The student's uploaded photo.
     *
     * @var string
     */
    public $photo;

    /**
     * The student's address.
     *
     * @var string
     */
    public $address1;

    /**
     * The student's address2.
     *
     * @var string
     */
    public $address2;

    /**
     * The student's address city.
     *
     * @var string
     */
    public $city;

    /**
     * The student's address state.
     *
     * @var string
     */
    public $state;

    /**
     * The student's address zip.
     *
     * @var string
     */
    public $zip;

    /**
     * The student's country of origin.
     *
     * @var string
     */
    public $country;

    /**
     * The student's address phone.
     *
     * @var string
     */
    public $phone1;

    /**
     * The student's address primary email.
     *
     * @var string
     */
    public $email1;

    /**
     * The student's header.
     * 
     * @var mixed
     */
    public $stuHeader;

    /**
     * The student's approved date.
     *
     * @var string
     */
    public $approvedDate = '0000-00-00';

    /**
     * The student's approval person.
     *
     * @var int
     */
    public $approvedBy = 1;

    /**
     * The student's last log in date and time.
     */
    public $LastLogin = '0000-00-00 00:00:00';

    /**
     * The student's modified date and time.
     */
    public $LastUpdate = '0000-00-00 00:00:00';

    /**
     * Retrieve etsis_Student instance.
     *
     * @global app $app eduTrac SIS application object.
     *        
     * @param int $stu_id
     *            Student ID.
     * @return etsis_Student|false Student array, false otherwise.
     */
    public static function get_instance($stu_id)
    {
        global $app;

        if (!$stu_id) {
            return false;
        }
        try {
            $q = $app->db->student()
                ->setTableAlias('stu')
                ->select('stu.id AS _ID,stu.stuID,stu.status AS stuStatus,stu.addDate AS stuAddDate')
                ->select('person.altID,person.uname,person.prefix,person.fname')
                ->select('person.lname,person.mname,person.email,person.personType')
                ->select('person.ssn,person.dob,person.veteran,person.ethnicity')
                ->select('person.gender,person.emergency_contact,person.emergency_contact_phone')
                ->select('person.photo,person.status AS naeStatus,person.approvedDate')
                ->select('person.approvedBy,person.LastLogin,person.LastUpdate')
                ->select('address.*')
                ->_join('person', 'stu.stuID = person.personID')
                ->_join('address', 'stu.stuID = address.personID')
                ->where('stu.stuID = ?', $stu_id)->_and_()
                ->where('address.addressStatus = "C"')->_and_()
                ->where('(address.endDate IS NULL OR address.endDate <= "0000-00-00")');

            $stu = etsis_cache_get($stu_id, 'stu');
            if (empty($stu)) {
                $stu = $q->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($stu_id, $stu, 'stu');
            }

            $a = [];

            foreach ($stu as $_stu) {
                $a[] = $_stu;
            }

            if (!$_stu) {
                return false;
            }

            return $_stu;
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }
    }

    /**
     * Constructor.
     *
     * @param etsis_Student|object $stu
     *            Student object.
     */
    public function __construct($stu, \Liten\Liten $liten = null)
    {
        foreach (get_object_vars($stu) as $key => $value) {
            $this->$key = $value;
        }

        $this->app = !empty($liten) ? $liten : \Liten\Liten::getInstance();
    }
}
