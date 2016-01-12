<?php namespace app\src\Core;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Person API: etsis_Person Class
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
final class etsis_Person
{

    /**
     * Person ID.
     *
     * @var int
     */
    public $personID;

    /**
     * Alternative person ID.
     *
     * @var string
     */
    public $altID;

    /**
     * The person's username.
     *
     * @var string
     */
    public $uname;

    /**
     * The person's prefix.
     *
     * @var string
     */
    public $prefix;

    /**
     * The person's person type.
     *
     * @var string
     */
    public $personType;

    /**
     * The person's first name.
     *
     * @var string
     */
    public $fname;

    /**
     * The person's last name.
     *
     * @var string
     */
    public $lname;

    /**
     * The person's middle initial.
     *
     * @var string
     */
    public $mname;

    /**
     * The person's email address.
     *
     * @var string
     */
    public $email;

    /**
     * The person's social security number.
     *
     * @var int
     */
    public $ssn;

    /**
     * The person's date of birth.
     *
     * @var string
     */
    public $dob;

    /**
     * The person's veteran status.
     *
     * @var bool
     */
    public $veteran;

    /**
     * The person's ethnicity.
     *
     * @var string
     */
    public $ethnicity;

    /**
     * The person's gender.
     *
     * @var string
     */
    public $gender;

    /**
     * The person's emergency contact person.
     *
     * @var string
     */
    public $emergency_contact;

    /**
     * The person's emergency contact person phone number.
     *
     * @var string
     */
    public $emergency_contact_phone;

    /**
     * The person's uploaded photo.
     *
     * @var string
     */
    public $photo;

    /**
     * The person's status.
     *
     * @var string
     */
    public $status;

    /**
     * The person's approved date.
     *
     * @var string
     */
    public $approvedDate = '0000-00-00';

    /**
     * The person's approval person.
     *
     * @var int
     */
    public $approvedBy = 1;

    /**
     * The person's last log in date and time.
     */
    public $LastLogin = '0000-00-00 00:00:00';

    /**
     * The person's modified date and time.
     */
    public $LastUpdate = '0000-00-00 00:00:00';

    /**
     * Retrieve etsis_Person instance.
     *
     * @global app $app eduTrac SIS application object.
     *        
     * @param int $person_id
     *            Person ID.
     * @return etsis_Person|false Person array, false otherwise.
     */
    public static function get_instance($person_id)
    {
        global $app;
        
        //$person_id = (int) $person_id;
        
        if (! $person_id) {
            return false;
        }
        
        $q = $app->db->person()->where('personID = ?', $person_id);
        
        $person = etsis_cache_get($person_id, 'person');
        if (empty($person)) {
            $person = $q->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            etsis_cache_add($person_id, $person, 'person');
        }
        
        $a = [];
        
        foreach ($person as $_person) {
            $a[] = $_person;
        }
        
        if (! $_person) {
            return false;
        }
        
        return $_person;
    }

    /**
     * Constructor.
     *
     * @param etsis_Person|object $person
     *            Person object.
     */
    public function __construct($person)
    {
        foreach (get_object_vars($person) as $key => $value) {
            $this->$key = $value;
        }
    }
}
