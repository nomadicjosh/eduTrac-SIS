<?php namespace app\src\Core\NodeQ;

use \app\src\Core\NodeQ\Database as NodeQ;
use \app\src\Core\NodeQ\Helpers;

/**
 * NodeQ
 * 
 * A simple NoSQL library.
 *
 * @license GPLv3
 *         
 * @since 6.2.11
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
class etsis_NodeQ extends NodeQ
{

    public static function dispense($table)
    {
        Helpers\Migrations::dispense($table);

        return self::table($table);
    }
}
