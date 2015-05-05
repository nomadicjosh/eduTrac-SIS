<?php namespace Liten\Http;

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

class Request
{

    /**
     * Enable of disable SSL
     * @var bool
     */
    public $_ssl = false;

    /**
     * Global server variables
     * @var bool
     */
    public $server = [];

    /**
     * URI prefix
     * @var string
     */
    public $prefix;

    /**
     * Constructor Function
     */
    public function __construct()
    {
        $this->server = $_SERVER;
    }

    /**
     * Enable SSL.
     */
    public function enableSSL()
    {
        $this->_ssl = true;
    }

    /**
     * Disable SSL.
     */
    public function disableSSL()
    {
        $this->_ssl = false;
    }

    public function getMethod()
    {
        return $this->server['REQUEST_METHOD'];
    }

    /**
     * Determine if the incoming method
     * request is POST.
     */
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * Determine if the incoming method
     * request is GET.
     */
    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * Determine if the incoming method
     * request is PUT.
     */
    public function isPut()
    {
        return $this->getMethod() === 'PUT';
    }

    /**
     * Determine if the incoming method
     * request is DELETE.
     */
    public function isDelete()
    {
        return $this->getMethod() === 'DELETE';
    }

    /**
     * Determine if the incoming method
     * request is PATCH.
     */
    public function isPatch()
    {
        return $this->getMethod() === 'PATCH';
    }

    /**
     * Determine if the incoming method
     * request is HEAD.
     */
    public function isHead()
    {
        return $this->getMethod() === 'HEAD';
    }

    /**
     * Determine if the incoming method
     * request is OPTIONS.
     */
    public function isOptions()
    {
        return $this->getMethod() === 'OPTIONS';
    }

    /**
     * Get Host
     */
    public function getHost()
    {
        return $this->server['HTTP_HOST'];
    }

    /**
     * Return protocol based on the
     * SSL setting.
     */
    public function protocol()
    {
        if ($this->_ssl === true) {
            return 'https://';
        }
        return 'http://';
    }

    /**
     * Returns url based on protocol, route and prefix.
     */
    public function url_for($route)
    {
        $base = $this->server['HTTP_HOST'] . $this->server['SCRIPT_NAME'];
        $index = str_replace('index.php', '', $base);
        $url = rtrim($index, '/');

        return $this->protocol() . (isset($this->prefix) ? $this->prefix . '.' : '') . $url . $route;
    }

    /**
     * Returns GET variable if set
     * @param string $caller
     */
    public function _get($caller)
    {
        if (isset($_GET[$caller])) {
            return $_GET[$caller];
        }
    }

    /**
     * Returns POST variable if set
     * @param string $caller
     */
    public function _post($caller)
    {
        if (isset($_POST[$caller])) {
            return $_POST[$caller];
        }
    }
}
