<?php namespace app\src;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * API for Release and Update Checks
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @license     http://www.edutracerp.com/general/edutrac-erp-commercial-license/ Commercial License
 * @link        http://www.7mediaws.org/
 * @since       4.5.3
 * @package     eduTrac ERP
 * @author      Joshua Parker <josh@7mediaws.org>
 */
class ReleaseAPI
{

    private $_url = 'http://7mws.s3.amazonaws.com/';
    private $_json_url = '';

    public function __construct()
    {
        // Create the stream context
        $context = stream_context_create([
            'http' => [
                'timeout' => 2      // Timeout in seconds
            ]
        ]);

        $this->_json_url = file_get_contents($this->_url . 'erp-release.json', false, $context);
    }

    public function init($value)
    {
        $json = json_decode($this->_json_url, true);

        foreach ($json as $k => $v) {
            return $v[$value];
        }
    }
}
