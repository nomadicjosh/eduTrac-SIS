<?php namespace app\src\Core;

use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Academic Program API: etsis_Acad_Program Class
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
final class etsis_Acad_Program
{

    /**
     * Academic program ID.
     *
     * @var int
     */
    public $id;

    /**
     * The academic program code.
     *
     * @var string
     */
    public $acadProgCode;

    /**
     * The academic program title.
     *
     * @var string
     */
    public $acadProgTitle;

    /**
     * The academic program description.
     *
     * @var string
     */
    public $programDesc;

    /**
     * The academic program current status.
     *
     * @var string
     */
    public $currStatus = 'A';

    /**
     * The academic program status date.
     *
     * @var string
     */
    public $statusDate = '0000-00-00';

    /**
     * The academic program department code.
     *
     * @var string
     */
    public $deptCode;

    /**
     * The academic program school code.
     *
     * @var string
     */
    public $schoolCode;

    /**
     * The academic program year code.
     *
     * @var string
     */
    public $acadYearCode;

    /**
     * The academic program start date.
     *
     * @var string
     */
    public $startDate = '0000-00-00';

    /**
     * The academic program end date.
     *
     * @var string
     */
    public $endDate = '0000-00-00';

    /**
     * The academic program degree code.
     *
     * @var string
     */
    public $degreeCode;

    /**
     * The academic program ccd code.
     *
     * @var string
     */
    public $ccdCode;

    /**
     * The academic program major code.
     *
     * @var string
     */
    public $majorCode;

    /**
     * The academic program minor code.
     *
     * @var string
     */
    public $minorCode;

    /**
     * The academic program specialization code.
     *
     * @var string
     */
    public $specCode;

    /**
     * The academic program academic level code.
     *
     * @var string
     */
    public $acadLevelCode;

    /**
     * The academic program cip code.
     *
     * @var string
     */
    public $cipCode;

    /**
     * The academic program location code.
     *
     * @var string
     */
    public $locationCode;

    /**
     * The academic program approval date.
     *
     * @var string
     */
    public $approvedDate = '0000-00-00';

    /**
     * The academic program approval person.
     *
     * @var int
     */
    public $approvedBy = 1;

    /**
     * The person's modified date and time.
     *
     * @var string
     */
    public $LastUpdate = '0000-00-00 00:00:00';

    /**
     * Retrieve etsis_Acad_Program instance.
     *
     * @global app $app eduTrac SIS application object.
     *        
     * @param int $acad_prog_id
     *            Academic Program ID.
     * @return etsis_Acad_Program|false Academic program array, false otherwise.
     */
    public static function get_instance($acad_prog_id)
    {
        global $app;

        if (!$acad_prog_id) {
            return false;
        }
        try {
            $q = $app->db->acad_program()->where('id = ?', $acad_prog_id);

            $acad_prog = etsis_cache_get($acad_prog_id, 'prog');
            if (empty($acad_prog)) {
                $acad_prog = $q->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                etsis_cache_add($acad_prog_id, $acad_prog, 'prog');
            }

            $a = [];

            foreach ($acad_prog as $_acad_prog) {
                $a[] = $_acad_prog;
            }

            if (!$_acad_prog) {
                return false;
            }

            return $_acad_prog;
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
     * @param etsis_Acad_Program|object $acad_prog
     *            Academic program object.
     */
    public function __construct($acad_prog)
    {
        foreach (get_object_vars($acad_prog) as $key => $value) {
            $this->$key = $value;
        }
    }
}
