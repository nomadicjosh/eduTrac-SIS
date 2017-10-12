<?php namespace app\src\Core\Queue;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Interface for a garbage collection.
 * 
 * If the eduTrac SIS 'queue' service implements this interface, the
 * garbageCollection() method will be called during master cron.
 * 
 * @since       6.3.4
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
interface etsis_QueueGarbageCollectionInterface
{

    /**
     * Cleans queues of garbage.
     */
    public function garbageCollection();
}
