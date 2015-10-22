<?php namespace Liten;

/**
 * Liten - PHP 5 micro framework
 * 
 * @link        http://www.litenframework.com
 * @version     1.0.0
 * @package		Liten
 * 
 * The MIT License (MIT)
 * Copyright (c) 2015 Joshua Parker
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

class View
{

    /**
     * Liten application object
     * 
     * @var object/callable 
     */
    protected $_app;

    /**
     * Data available to the view templates
     * @var $data
     */
    protected $_data;

    /**
     * The view being extended.
     * 
     * @var string
     */
    protected $_extendedView;

    /**
     * Store the contents of blocks.
     * 
     * @var array
     */
    protected $_blocks;

    /**
     * The currently opened (started) block.
     * 
     * @var string
     */
    protected $_openBlock;

    /**
     * View template extension
     * @var $ext
     */
    public $ext = '.php';

    /**
     * Store the path to the views folder.
     * 
     * @var string
     */
    public $_viewPath;

    public function __construct(\Liten\Liten $liten = null)
    {
        $this->_app = !empty($liten) ? $liten : \Liten\Liten::getInstance();
        $this->_viewPath = $this->_app->config('view_dir');
    }

    /**
     * Display view
     *
     * This method echoes the rendered view to the current output buffer
     *
     * @param  string   $template   Pathname of view file relative to views directory
     * @param  array    $data       Any additonal data to be passed to the view.
     */
    public function display($viewName, $data = null)
    {
        echo $this->render($viewName, $data);
    }

    /**
     * Render the given view.
     * 
     * @param  string 	$viewName
     * @param  array 	$data
     * @return string
     */
    public function render($viewName, $data = null)
    {
        $this->_data = $data;

        $view = $this->load($viewName);

        $view = ($this->_extendedView) ? $this->load($this->_extendedView) : $view;

        return $view;
    }

    /**
     * Load the given view and return the contents.
     *
     * @param  string 	$viewName
     * @return string
     */
    public function load($viewName)
    {
        $viewPath = $this->_viewPath . $viewName . $this->ext;

        try {
            if (!file_exists($viewPath)) {
                throw new \Liten\Exception\ViewException("The view $viewPath does not exist.");
            }
        } catch (\Liten\Exception\ViewException $e) {
            echo "Caught ViewException ('{$e->getMessage()}') <br />";
        }

        $tmpl = $this;

        $extends = function($view) use ($tmpl) {
            $tmpl->extend($view);
        };

        $block = function($name) use ($tmpl) {
            $tmpl->block($name);
        };

        $end = function() use ($tmpl) {
            echo $tmpl->stop();
        };

        $show = function($view) use ($tmpl) {
            echo $tmpl->show($view);
        };

        $include = function($view) use ($tmpl) {
            echo $tmpl->partial($view);
        };

        ob_start();

        if ($this->_data !== null)
            extract($this->_data);
        require($viewPath);

        return ob_get_clean();
    }

    /**
     * Extend a parent View.
     *
     * @param  string 	$viewName
     * @return void
     */
    public function extend($viewName)
    {
        $this->_extendedView = $viewName;
    }

    /**
     * Include a partial view.
     * 
     * @param  string 	$viewName
     * @return void
     */
    public function partial($viewName)
    {
        return $this->load($viewName);
    }

    /**
     * Start a new block.
     * 
     * @param  string 	$name
     * @return void
     */
    public function block($name)
    {
        $this->_openBlock = $name;
        ob_start();
    }

    /**
     * Close a section and return the buffered contents.
     *
     * @return string
     */
    public function stop()
    {
        $name = $this->_openBlock;

        $buffer = ob_get_clean();

        if (!isset($this->_blocks[$name])) {
            $this->_blocks[$name] = $buffer;
        } elseif (isset($this->_blocks[$name])) {
            $this->_blocks[$name] = str_replace('@parent', $buffer, $this->_blocks[$name]);
        }

        return $this->_blocks[$name];
    }

    /**
     * Show the contents of a block.
     *
     * @param  string 	$name
     * @return string
     */
    public function show($name)
    {
        if (isset($this->_blocks[$name])) {
            return $this->_blocks[$name];
        }
    }
}
