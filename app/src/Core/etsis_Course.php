<?php namespace app\src\Core;

use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception;
use PDOException as ORMException;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Course API: etsis_Course Class
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
final class etsis_Course
{

    /**
     * Course courseID.
     *
     * @var int
     */
    public $courseID;

    /**
     * Course number.
     *
     * @var int
     */
    public $courseNumber;

    /**
     * The course's code
     *
     * @var string
     */
    public $courseCode;

    /**
     * The course's subject code.
     *
     * @var string
     */
    public $subjectCode;

    /**
     * The course's department code.
     *
     * @var string
     */
    public $deptCode;

    /**
     * The course description.
     *
     * @var string
     */
    public $courseDesc;

    /**
     * The course's credit type.
     *
     * @var string
     */
    public $creditType;

    /**
     * The course's minimum credits.
     *
     * @var int
     */
    public $minCredit = 0.0;

    /**
     * The course's maximumm credits.
     *
     * @var int
     */
    public $maxCredit = 0.0;

    /**
     * The course's increment of credits.
     *
     * @var int
     */
    public $increCredit = 0.0;

    /**
     * The course's level.
     *
     * @var string
     */
    public $courseLevelCode;

    /**
     * The course's academic level.
     *
     * @var string
     */
    public $acadLevelCode;

    /**
     * The course's short title.
     *
     * @var string
     */
    public $courseShortTitle;

    /**
     * The course's long title.
     *
     * @var string
     */
    public $courseLongTitle;

    /**
     * The course's prerequisite(s).
     *
     * @var array
     */
    public $preReq;

    /**
     * Whether course can be audited.
     *
     * @var bool
     */
    public $allowAudit;

    /**
     * Whether course can be waitlisted.
     *
     * @var bool
     */
    public $allowWaitlist;

    /**
     * Minimum enrollment for course.
     *
     * @var int
     */
    public $minEnroll;

    /**
     * Number of seats in the course.
     *
     * @var int
     */
    public $seatCap;

    /**
     * The course's start date.
     *
     * @var string
     */
    public $startDate = '0000-00-00';

    /**
     * The course's end date.
     *
     * @var string
     */
    public $endDate = '0000-00-00';

    /**
     * The course's current status.
     *
     * @var string
     */
    public $currStatus = 'A';

    /**
     * The course's status date.
     *
     * @var string
     */
    public $statusDate = '0000-00-00';

    /**
     * The course's approved date.
     *
     * @var string
     */
    public $approvedDate = '0000-00-00';

    /**
     * The course's approval person.
     *
     * @var int
     */
    public $approvedBy = 1;

    /**
     * The course's modified date and time.
     */
    public $LastUpdate = '0000-00-00 00:00:00';

    /**
     * Retrieve etsis_Course instance.
     *
     * @global app $app eduTrac SIS application array.
     *        
     * @param int $course_id
     *            Course id.
     * @return etsis_Course|false Course array, false otherwise.
     */
    public static function get_instance($course_id)
    {
        global $app;

        if (!$course_id) {
            return false;
        }
        try {
            $q = $app->db->course()->where('courseID = ?', $course_id);

            $course = etsis_cache_get($course_id, 'crse');
            if (empty($course)) {
                $course = $q->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($course_id, $course, 'crse');
            }

            $a = [];

            foreach ($course as $_course) {
                $a[] = $_course;
            }

            if (!$_course) {
                return false;
            }

            return $_course;
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
     * @param etsis_Course|object $course
     *            Course object.
     */
    public function __construct($course)
    {
        foreach (get_object_vars($course) as $key => $value) {
            $this->$key = $value;
        }
    }
}
