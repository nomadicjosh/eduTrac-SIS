<?php namespace app\src\Cache;

if (! defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS Abstract Cache Class.
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @subpackage Cache
 * @author Joshua Parker <joshmac3@icloud.com>
 */
abstract class etsis_Abstract_Cache
{

    abstract function read($key, $group);

    abstract function create($key, $data, $group, $ttl);

    abstract function delete($key, $group);

    abstract protected function unique_key($key, $group);

    abstract protected function _exists($key, $group);

    abstract function flush();
}
