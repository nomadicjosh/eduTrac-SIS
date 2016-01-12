<?php
if (! defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * eduTrac SIS Academic Program Functions
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();

/**
 * Retrieves academic program data given an academic program ID or academic program array.
 *
 * @since 6.2.0
 * @param int|etsis_Acad_Program|null $program
 *            Academic program ID or academic program array.
 * * @param bool $object
 *            If set to true, data will return as an object, else as an array.
 */
function get_acad_program($program, $object = true)
{
    if ($program instanceof \app\src\Core\etsis_Acad_Program) {
        $_program = $program;
    } elseif (is_array($program)) {
        if (empty($program['acadProgID'])) {
            $_program = new \app\src\Core\etsis_Acad_Program($program);
        } else {
            $_program = \app\src\Core\etsis_Acad_Program::get_instance($program['acadProgID']);
        }
    } else {
        $_program = \app\src\Core\etsis_Acad_Program::get_instance($program);
    }
    
    if (! $_program) {
        return null;
    }
    
    if ($object == true) {
        $_program = array_to_object($_program);
    }
    
    return $_program;
}