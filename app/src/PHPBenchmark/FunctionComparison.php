<?php namespace app\src\PHPBenchmark;
if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Benchmarking Class Funciton
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
 * Class that can be used to compare the performance between two functions
 *
 * @author Victor Jonsson (http://victorjonsson.se)
 * @license MIT
 */
class FunctionComparison extends \app\src\PHPBenchmark\AbstractFunctionComparison {

    /**
     * @var \Closure
     */
    private $func_a;

    /**
     * @var \Closure
     */
    private $func_b;

    /**
     * @var array
     */
    private $preparations = array('before'=>array('a'=>false, 'b'=>false), 'after'=>array('a'=>false, 'b'=>false));

    /**
     * @param string $name
     * @param \Closure $func
     * @return \PHPBenchmark\FunctionComparison
     */
    public function setFunctionA($name, $func) {
        $this->func_a_name = $name;
        $this->func_a = $func;
        return $this;
    }

    /**
     * @param string $name
     * @param \Closure $func
     * @return \PHPBenchmark\FunctionComparison
     */
    public function setFunctionB($name, $func) {
        $this->func_b_name = $name;
        $this->func_b = $func;
        return $this;
    }

    /**
     * Runs function A
     */
    public function functionA() {
        $f = $this->func_a;
        $f();
    }

    /**
     * Runs function B
     */
    public function functionB() {
        $f = $this->func_b;
        $f();
    }

    /**
     * Add a function to be called before running any of the functions
     * @param string $func Either 'a' or 'b'
     * @param \Closure $callback
     * @return \PHPBenchmark\FunctionComparison
     */
    public function before($func, $callback)
    {
        $this->preparations['before'][$func] = $callback;
        return $this;
    }

    /**
     * Add a function to be called after running any of the functions
     * @param string $func Either 'a' or 'b'
     * @param \Closure $callback
     * @return \PHPBenchmark\FunctionComparison
     */
    public function after($func, $callback)
    {
        $this->preparations['after'][$func] = $callback;
        return $this;
    }

    /**
     * Function called once before running all A tests
     */
    protected function beforeFunctionA()
    {
        if( is_callable($this->preparations['before']['a']) )
            $this->preparations['before']['a']();
    }

    /**
     * Function called once after all A tests have run
     */
    protected function afterFunctionA()
    {
        if( is_callable($this->preparations['after']['a']) )
            $this->preparations['after']['a']();
    }

    /**
     * Function called once before running all B tests
     */
    protected function beforeFunctionB()
    {
        if( is_callable($this->preparations['before']['b']) )
            $this->preparations['before']['b']();
    }

    /**
     * Function called once after all A tests have run
     */
    protected function afterFunctionB()
    {
        if( is_callable($this->preparations['after']['b']) )
            $this->preparations['after']['b']();
    }

    /**
     * @param int $num_runs
     * @return \PHPBenchmark\FunctionComparison
     */
    public static function load($num_runs=500)
    {
        $instance = new self();
        $instance->setNumRuns($num_runs);
        return $instance;
    }

    /**
     * Instantiate and call exec() on all classes implementing AbstractFunctionComparison
     * in given path
     * @param string $path
     */
    public static function runTests( $path )
    {
        foreach(self::findComparisonTests($path) as $test)
            $test->exec();
    }

    /**
     * Find all classes implementing AbstractFunctionComparison in given path
     * @param string $path
     * @return \PHPBenchmark\AbstractFunctionComparison[]
     */
    public static function findComparisonTests( $path )
    {
        $tests = array();
        foreach( glob($path.'/*.php') as $file ) {
            require_once $file;
            $class = pathinfo($file, PATHINFO_FILENAME);
            if( class_exists($class, false) && is_subclass_of($class, '\\eduTrac\\Classes\\Libraries\\PHPBenchmark\\AbstractFunctionComparison') )
                $tests[] = new $class();
        }

        return $tests;
    }
}