<?php namespace app\src\Core;

use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Staff API: etsis_Staff Class
 *
 * @license GPLv3
 *         
 * @since 6.3.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
final class etsis_Staff
{

    /**
     * Application object;
     * 
     * @var object
     */
    public $app;

    /**
     * Primary key id.
     *
     * @var int
     */
    public $id;

    /**
     * Staff ID.
     *
     * @var int
     */
    public $staffID;

    /**
     * School Code.
     *
     * @var string
     */
    public $schoolCode;

    /**
     * Building Code.
     *
     * @var string
     */
    public $buildingCode;

    /**
     * Office Code.
     *
     * @var string
     */
    public $officeCode;

    /**
     * Office Phone.
     *
     * @var string
     */
    public $office_phone;

    /**
     * Department Code.
     *
     * @var string
     */
    public $deptCode;

    /**
     * Staff status.
     *
     * @var string
     */
    public $status;

    /**
     * The staff's add date.
     *
     * @var string
     */
    public $addDate = '0000-00-00';

    /**
     * Alternative staff ID.
     *
     * @var string
     */
    public $altID;

    /**
     * The staff's username.
     *
     * @var string
     */
    public $uname;

    /**
     * The staff's prefix.
     *
     * @var string
     */
    public $prefix;

    /**
     * The staff's person type.
     *
     * @var string
     */
    public $personType;

    /**
     * The staff's first name.
     *
     * @var string
     */
    public $fname;

    /**
     * The staff's last name.
     *
     * @var string
     */
    public $lname;

    /**
     * The staff's middle initial.
     *
     * @var string
     */
    public $mname;

    /**
     * The staff's email address.
     *
     * @var string
     */
    public $email;

    /**
     * The staff's social security number.
     *
     * @var int
     */
    public $ssn;

    /**
     * The staff's date of birth.
     *
     * @var string
     */
    public $dob;

    /**
     * The staff's veteran status.
     *
     * @var bool
     */
    public $veteran;

    /**
     * The staff's ethnicity.
     *
     * @var string
     */
    public $ethnicity;

    /**
     * The staff's gender.
     *
     * @var string
     */
    public $gender;

    /**
     * The staff's emergency contact person.
     *
     * @var string
     */
    public $emergency_contact;

    /**
     * The staff's emergency contact person phone number.
     *
     * @var string
     */
    public $emergency_contact_phone;

    /**
     * The staff's uploaded photo.
     *
     * @var string
     */
    public $photo;

    /**
     * The staff's address.
     *
     * @var string
     */
    public $address1;

    /**
     * The staff's address2.
     *
     * @var string
     */
    public $address2;

    /**
     * The staff's address city.
     *
     * @var string
     */
    public $city;

    /**
     * The staff's address state.
     *
     * @var string
     */
    public $state;

    /**
     * The staff's address zip.
     *
     * @var string
     */
    public $zip;

    /**
     * The staff's country of origin.
     *
     * @var string
     */
    public $country;

    /**
     * The staff's address phone.
     *
     * @var string
     */
    public $phone1;

    /**
     * The staff's address primary email.
     *
     * @var string
     */
    public $email1;

    /**
     * The staff meta jobStatusCode
     *
     * @var string
     */
    public $jobStatusCode;

    /**
     * The staff meta jobID
     *
     * @var int
     */
    public $jobID;

    /**
     * The staff meta supervisor ID.
     * 
     * @var int
     */
    public $supervisorID;

    /**
     * The staff meta staffType.
     * 
     * @var string
     */
    public $staffType;

    /**
     * The staff meta hireDate.
     * 
     * @var string
     */
    public $hireDate;

    /**
     * The staff meta startDate.
     * 
     * @var string
     */
    public $metaStartDate;

    /**
     * The staff meta endDate.
     * 
     * @var string
     */
    public $metaEndDate;

    /**
     * The staff meta addDate.
     * 
     * @var string
     */
    public $metaAddDate;

    /**
     * Retrieve etsis_Staff instance.
     *
     * @global app $app eduTrac SIS application object.
     *        
     * @param int $staff_id
     *            Staff ID.
     * @return etsis_Staff|false Staff array, false otherwise.
     */
    public static function get_instance($staff_id)
    {
        global $app;

        if (!$staff_id) {
            return false;
        }
        try {
            $q = $app->db->staff()
                ->select('staff.id,staff.staffID,staff.addDate AS staffAddDate,department.deptName')
                ->select('CASE WHEN staff.status = "A" THEN "Active" ELSE "Inactive" END AS staffStatus')
                ->select('person.altID,person.uname,person.prefix,person.fname')
                ->select('person.lname,person.mname,person.email,person.personType')
                ->select('person.ssn,person.dob,person.veteran,person.ethnicity')
                ->select('person.gender,person.emergency_contact,person.emergency_contact_phone')
                ->select('person.photo,person.status AS naeStatus,person.approvedDate')
                ->select('person.approvedBy,person.LastUpdate')
                ->select('address.*,job.title,staff.office_phone,staff.officeCode')
                ->select('meta.id AS sMetaID,meta.jobStatusCode,meta.jobID,meta.supervisorID,meta.staffType')
                ->select('meta.hireDate,meta.startDate as metaStartDate,meta.endDate as metaEndDate')
                ->select('meta.addDate as metaAddDate')
                ->_join('person', 'staff.staffID = person.personID')
                ->_join('address', 'staff.staffID = address.personID')
                ->_join('staff_meta', 'staff.staffID = meta.staffID', 'meta')
                ->_join('job', 'meta.jobID = job.id')
                ->_join('department', 'staff.deptCode = department.deptCode')
                ->where('staff.staffID = ?', $staff_id)->_and_()
                ->where('meta.id = (SELECT id FROM staff_meta WHERE staffID = staff.staffID ORDER BY id DESC LIMIT 1)')->_and_()
                ->where('address.addressStatus = "C"')->_and_()
                ->where('(address.endDate IS NULL OR address.endDate <= "0000-00-00")');

            $staff = etsis_cache_get($staff_id, 'staff');
            if (empty($staff)) {
                $staff = $q->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($staff_id, $staff, 'staff');
            }

            $a = [];

            foreach ($staff as $_staff) {
                $a[] = $_staff;
            }

            if (!$_staff) {
                return false;
            }

            return $_staff;
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
     * @param etsis_Staff|object $staff
     *            Staff object.
     */
    public function __construct($staff)
    {
        foreach (get_object_vars($staff) as $key => $value) {
            $this->$key = $value;
        }
    }
}
