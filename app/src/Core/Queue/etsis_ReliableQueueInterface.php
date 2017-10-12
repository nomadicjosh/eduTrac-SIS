<?php namespace app\src\Core\Queue;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Reliable queue interface.
 * 
 * Classes implementing this interface preserve the order of messages and
 * guarantee that every item will be executed at least once.
 * 
 * @since       6.3.4
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
interface etsis_ReliableQueueInterface extends etsis_QueueInterface
{
    
}
