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

    abstract function read($key, $namespace);

    abstract function create($key, $data, $namespace, $ttl);

    abstract function delete($key, $namespace);
    
    abstract function flush();
    
    abstract function flushNamespace($namespace);

    abstract protected function uniqueKey($key, $namespace);

    abstract protected function _exists($key, $namespace);
    
    abstract protected function _namespace($value);
}
