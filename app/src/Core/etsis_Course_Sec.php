<?php namespace app\src\Core;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Course Section API: etsis_Course_Sec Class
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
final class etsis_Course_Sec
{
    /**
     * Course Section ID.
     * 
     * @var int
     */
    public $sectSecID;
    
    /**
     * Course section number.
     * 
     * @var int
     */
    public $sectionNumber;
    
    /**
     * The course section code
     * 
     * @var string
     */
    public $sectSecCode;
    
    /**
     * The course section.
     *
     * @var string
     */
    public $sectSection;
    
    /**
     * The course section's building code.
     * 
     * @var string
     */
    public $buildingCode;
    
    /**
     * The course section's room code.
     * 
     * @var string
     */
    public $roomCode;
    
    /**
     * The course section's location code.
     * 
     * @var string
     */
    public $locationCode;
    
    /**
     * The course section's level.
     *
     * @var string
     */
    public $sectLevelCode;
    
    /**
     * The course section's academic level.
     *
     * @var string
     */
    public $acadLevelCode;
    
    /**
     * The course section's department code.
     * 
     * @var string
     */
    public $deptCode;
    
    /**
     * The course section's faculty ID.
     * 
     * @var int
     */
    public $facID;
    
    /**
     * The course section's term code.
     * 
     * @var string
     */
    public $termCode;
    
    /**
     * The course section's course ID.
     * 
     * @var int
     */
    public $sectID;
    
    /**
     * The course section's course code.
     * 
     * @var string
     */
    public $sectCode;
    
    /**
     * The course section's prerequisite(s).
     *
     * @var array
     */
    public $preReqs;
    
    /**
     * The course section's short title.
     * 
     * @var string
     */
    public $secShortTitle;
    
    /**
     * The course section's start date.
     *
     * @var string
     */
    public $startDate = '0000-00-00';
    
    /**
     * The course section's end date.
     *
     * @var string
     */
    public $endDate = '0000-00-00';
    
    /**
     * The course section's start time.
     * 
     * @var string
     */
    public $startTime = '00:00 AM';
    
    /**
     * The course section's end time.
     * 
     * @var string
     */
    public $endTime = '00:00 AM';
    
    /**
     * The course section's meeting days.
     * 
     * @var array
     */
    public $dotw;
    
    /**
     * The course section's minimum credits.
     *
     * @var int
     */
    public $minCredit = 0.0;
    
    /**
     * The course section's maximumm credits.
     *
     * @var int
     */
    public $maxCredit = 0.0;
    
    /**
     * The course section's increment of credits.
     *
     * @var int
     */
    public $increCredit = 0.0;
    
    /**
     * The course section's continuing education units.
     * 
     * @var int
     */
    public $ceu = 0.0;
    
    /**
     * The course section's instructor method.
     * 
     * @var string
     */
    public $instructorMethod;
    
    /**
     * The course section's instructor load.
     *
     * @var int
     */
    public $instructorLoad = 0.0;
    
    /**
     * The course section's contact hours.
     * 
     * @var int
     */
    public $contactHours = 0.0;
    
    /**
     * The course section's web registration.
     *
     * @var bool
     */
    public $webReg;
    
    /**
     * The course section's course fee.
     *
     * @var int
     */
    public $sectFee = 0.00;
    
    /**
     * The course section's lab fee.
     *
     * @var int
     */
    public $labFee = 0.00;
    
    /**
     * The course section's material fee.
     *
     * @var int
     */
    public $materialFee = 0.00;
    
    /**
     * The course section's type.
     *
     * @var int
     */
    public $secType = 'ONC';
    
    /**
     * The course section's current status.
     *
     * @var string
     */
    public $currStatus = 'A';
    
    /**
     * The course section's status date.
     * 
     * @var string
     */
    public $statusDate = '0000-00-00';
    
    /**
     * The course section's comments.
     *
     * @var string
     */
    public $comment;
    
    /**
     * The course section's approved date.
     * 
     * @var string
     */
    public $approvedDate = '0000-00-00';
    
    /**
     * The course section's approval person.
     * 
     * @var int
     */
    public $approvedBy = 1;
    
    /**
     * The course section's modified date and time.
     */
    public $LastUpdate = '0000-00-00 00:00:00';
    
    /**
     * Retrieve etsis_Course_Sec instance.
     *
     * @global app $app eduTrac SIS application array.
     *
     * @param int $sect_id Course Section ID.
     * @return etsis_Course_Sec|false Course section array, false otherwise.
     */
    public static function get_instance($sect_id)
    {
        global $app;
        
        //$sect_id = (int) $sect_id;
        
        if (! $sect_id) {
            return false;
        }
        
        $q = $app->db->course_sec()->where('courseSecID = ?', $sect_id);
        
        $sect = etsis_cache_get($sect_id, 'sect');
        if (empty($sect)) {
            $sect = $q->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            etsis_cache_add($sect_id, $sect, 'sect');
        }
        
        $a = [];
        
        foreach ($sect as $_sect) {
            $a[] = $_sect;
        }
        
        if (! $_sect) {
            return false;
        }
        
        return $_sect;
    }

    /**
     * Constructor.
     *
     * @param etsis_Course_Sec|object $section
     *            Course section object.
     */
    public function __construct($section)
    {
        foreach (get_object_vars($section) as $key => $value) {
            $this->$key = $value;
        }
    }
}
