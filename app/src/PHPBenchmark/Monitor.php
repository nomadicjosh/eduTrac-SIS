<?php namespace app\src\PHPBenchmark;
if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Benchmarking Monitor Class
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3
 * @link        http://www.7mediaws.org/
 * @since       1.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */


/**
 * Class used to collect benchmark data over a given time
 *
 * @author Victor Jonsson (http://victorjonsson.se)
 * @license MIT
 */
class Monitor {

    /**
     * @var float
     */
    private $startTime;

    /**
     * @var array
     */
    private $snapShots = array();

    /**
     * @var bool
     */
    private $displayAsHTML = false;

    /**
     * @var string
     */
    private $dataTemplateCSS = 'position: fixed; top: 60px; right:40px; box-shadow:0 0 8px #555; background: #FFF; padding: 5px; color:#000 !important; z-index: 9999';

    /**
     */
    public function __construct()
    {
        $this->startTime = self::getMicroTime();
    }

    /**
     * @param string $dataTemplateCSS
     */
    public function setDataTemplateCSS($dataTemplateCSS)
    {
        $this->dataTemplateCSS = $dataTemplateCSS;
    }

    /**
     * @return string
     */
    public function getDataTemplateCSS()
    {
        return $this->dataTemplateCSS;
    }

    /**
     * @param bool $displayAsHTML
     */
    public function init($displayAsHTML=false)
    {
        $this->displayAsHTML = $displayAsHTML;
        register_shutdown_function(array($this, 'shutDown'));
    }

    /**
     * Display benchmark data to browser or log
     */
    public function shutDown()
    {
        $data = $this->getData();
        if( $this->displayAsHTML ) {
            $log = $this->generateHTML($data);
        } else {
            $log = sprintf(
                    '[phpbenchmark time=%f memory=%f files=%d classes=%d]',
                    $data['time'],
                    $data['memory'],
                    $data['files'],
                    $data['classes']
                );
        }

        echo $log;
    }

    /**
     * @param array $data
     * @return string
     */
    private function generateHTML($data)
    {
        $table = '<table><thead><tr style="background:#EEE;"><td style="padding:5px;">&nbsp;</td><td style="padding:5px;">Time</td><td style="padding:5px;">Memory</td><td style="padding:5px;">Files</td><td style="padding:5px; text-align:right">Classes</td><td style="padding:5px; text-align:right">%</td></tr></thead><tbody>';

        $table_row = '<tr><td style="padding:5px 10px 5px 5px">%s</td><td style="padding:5px">%s</td style="padding:5px"><td style="padding:5px">%s</td style="padding:5px"><td style="padding:5px">%s</td><td style="padding:5px; text-align:right">%s</td><td style="padding:5px; text-align:right">%s</td></tr>';

        if( !empty($this->snapShots) ) {
            $last_proc = 0;
            foreach($this->snapShots as $name => $snapshot) {
                $proc = (100 * bcdiv($snapshot['time'], $data['time'], 2));
                $table .= sprintf(
                        $table_row,
                        $name,
                        $snapshot['time'],
                        $snapshot['memory'],
                        $snapshot['files'],
                        $snapshot['classes'],
                        $proc.'% <em style="font-size:70%; color:#777">('.($proc - $last_proc).'%)</em>'
                    );
                $last_proc = $proc;
            }
        }

        $table .= sprintf(
            $table_row,
            'Request finished',
            $data['time'],
            $data['memory'],
            $data['files'],
            $data['classes'],
            '100%' .( isset($last_proc) ? ( ' <em style="font-size:70%; color:#777">('.(100 - $last_proc) .'%)</em>') : '' )
        );

        $table .= '</tbody></table>';

        return sprintf('<div id="php-benchmark-result" style="%s">%s</div>', $this->dataTemplateCSS, $table);
    }

    /**
     * @param string $name
     */
    public function snapShot($name)
    {
        if( empty($this->snapShots) ) {
            $data = array(
                    'time' => $this->startTime,
                    'memory' => 0,
                    'classes' => 0,
                    'files' => 0
                );
        } else {
            $data = current(array_slice($this->snapShots, -1));
        }

        $currentData = $this->getData();

        $this->snapShots[$name] = array(
                            'time' => bcsub(self::getMicroTime(), $this->startTime, 4),
                            'memory' => $currentData['memory'],
                            'files' => $currentData['files'] - $data['files'],
                            'classes' => $currentData['classes'] - $data['classes']
                        );
    }

    /**
     * Get benchmark data at this point.
     * @return array
     */
    public function getData()
    {
        return array(
            'time' => bcsub(self::getMicroTime() , $this->startTime, 4),
            'memory' => round(memory_get_usage() / 1024 / 1024, 4),
            'files' => count(get_included_files()),
            'classes' => count(get_declared_classes())
        );
    }

    /**
     * @return array
     */
    public function snapShots()
    {
        return $this->snapShots;
    }

    /**
     * @var \PHPBenchmark\Monitor
     */
    private static $instance=null;


    /**
     * Singleton instance of this class
     * @return \PHPBenchmark\Monitor
     */
    public static function instance()
    {
        if( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @see http://se2.php.net/manual/en/function.microtime.php#101875
     * @return float
     */
    public static function getMicroTime()
    {
        list($u, $s) = explode(' ', microtime(false));
        return bcadd($u, $s, 7);
    }
}