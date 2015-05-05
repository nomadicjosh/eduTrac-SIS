<?php namespace app\src\PHPBenchmark;
if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Benchmark Abstract Class
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
 * Abstract class that can be used to compare the performance
 * between two different algorithms
 *
 * @author Victor Jonsson (http://victorjonsson.se)
 * @license MIT
 */
abstract class AbstractFunctionComparison {

    /**
     * @var int
     */
    protected $num_runs = 5000;

    /**
     * Description of function A
     * @var string
     */
    protected $func_a_name;

    /**
     * Description of function B
     * @var string
     */
    protected $func_b_name;

    /**
     * @param bool $html_format[optional=true]
     * @return string|array
     */
    public function run($html_format = true)
    {
        $this->beforeFunctionA();
        $result_a = $this->runTestFunction('functionA', $this->func_a_name);
        $this->afterFunctionA();
        $this->beforeFunctionB();
        $result_b = $this->runTestFunction('functionB', $this->func_b_name);
        $this->afterFunctionB();

        $results = array();
        $results['winner'] = bccomp($result_a['time'], $result_b['time'], 10) == -1 ? $result_a : $result_b;
        $results['looser'] = $results['winner']['function'] == $result_a['function'] ? $result_b : $result_a;
        $results['winner']['times_faster'] = $this->getTimesFaster($results['winner']['time'], $results['looser']['time']).'%';

        return $html_format ? $this->formatTestResult($results):$results;
    }

    /**
     * Will run the test and echo the result. It will display the result
     * in different format depending on if the function was called
     * in command line or browser
     */
    public function exec()
    {
        $is_browser_request = !empty($_SERVER['REMOTE_ADDR']);
        $result = $this->run( $is_browser_request );

        if( $is_browser_request ) {
            echo $result;
        } else {
            print_r($result);
        }
    }

    /**
     * @param string $func_name
     * @param string $desc
     * @return array
     */
    private function runTestFunction($func_name, $desc)
    {
        $start_mem_usage = memory_get_usage();
        $start_time = \app\src\PHPBenchmark\Monitor::getMicroTime();

        for($i=$this->num_runs; $i>0; $i--)
            $this->$func_name();

        $memory = memory_get_usage() - $start_mem_usage;

        return array(
                'function' => $func_name,
                'description' => $desc,
                'mem_usage' => round($memory/1024/1024, 4),
                'time' => bcsub(\app\src\PHPBenchmark\Monitor::getMicroTime(),  $start_time, 4)
            );
    }

    /**
     * @param $results
     * @return string
     */
    private function formatTestResult($results)
    {
        $html = '
        <table border="1" cellspacing="2" cellpadding="4">
            <thead>
                <tr>
                    <td><strong>Function description</strong></td>
                    <td><strong>Execution time (s)</strong></td>
                    <td><strong>Memory usage (mb)</strong></td>
                    <td><strong>Times faster</strong></td>
                </tr>
            </thead>
            <tbody>
                <tr class="winner">
                    <td>%s</td>
                    <td>%f</td>
                    <td>%f</td>
                    <td class="times_faster">%s</td>
                </tr>
                <tr>
                    <td>%s</td>
                    <td>%f</td>
                    <td>%f</td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>';

        $winner = $results['winner'];
        $loser = $results['looser'];

        return sprintf(
            $html,
            htmlentities($winner['description'], ENT_QUOTES, 'UTF-8'),
            $winner['time'],
            $winner['mem_usage'],
            $winner['times_faster'],
            htmlentities($loser['description'], ENT_QUOTES, 'UTF-8'),
            $loser['time'],
            $loser['mem_usage']
        );
    }

    /**
     * @param int $faster
     * @param int $slower
     * @return float
     */
    private function getTimesFaster($faster, $slower)
    {
        return bcdiv(bcsub($slower, $faster, 4), $faster, 2) * 100;
    }

    /**
     * @abstract
     * @return void
     */
    abstract function functionA();

    /**
     * @abstract
     * @return void
     */
    abstract function functionB();

    /**
     * The number of times each function will be called
     * @param int $num_runs
     */
    public function setNumRuns($num_runs)
    {
        $this->num_runs = $num_runs;
    }

    /**
     * The number of times each function will be called
     * @return int
     */
    public function getNumRuns()
    {
        return $this->num_runs;
    }

    /**
     * Function called once before running all A tests
     */
    protected function beforeFunctionA() {}

    /**
     * Function called once after all A tests have run
     */
    protected function afterFunctionA() {}

    /**
     * Function called once before running all B tests
     */
    protected function beforeFunctionB() {}

    /**
     * Function called once after all A tests have run
     */
    protected function afterFunctionB() {}
}