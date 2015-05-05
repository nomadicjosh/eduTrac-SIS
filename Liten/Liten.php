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

class Liten
{

    /**
     * @var string
     */
    public $version = '1.0.3';

    /**
     * @var \Liten\Helper\Set
     */
    public $inst;

    /**
     * @var array[\Liten]
     */
    protected static $_apps = [];

    /**
     * @var string
     */
    protected $_name;

    /**
     * The route patterns and their handling
     * functions.
     * @var array
     */
    protected $_routes = [];

    /**
     * The before middleware route patterns
     * and their handling functions.
     * @var array
     */
    protected $_befores = [];

    /**
     * Error object.
     * @var string
     */
    protected $_error;

    /**
     * Current baseroute, used for (sub)route
     * grouping.
     * @var string
     */
    protected $_baseroute = '';

    /**
     * The Request Method that needs to be handled.
     * @var string
     */
    protected $_method = '';

    public function __construct(array $config = [])
    {
        $this->inst = new \Liten\Helper\Set();
        $this->inst['config'] = array_merge(static::defaultConfig(), $config);
        // Load default request
        $this->inst->singleton('req', function ($c) {
            return new \Liten\Http\Request();
        });
        // Load default response
        $this->inst->singleton('res', function ($c) {
            return new \Liten\Http\Response();
        });
        // Load default view
        $this->inst->singleton('view', function ($c) {
            return new \Liten\View();
        });
        // Load default cookies
        $this->inst->singleton('cookies', function ($c) {
            return new \Liten\Cookies();
        });
        // Make default if first instance
        if (is_null(static::getInstance())) {
            $this->setName('default');
        }
    }

    /**
     * Magic function which calls the object
     * when the shorter object is called.
     */
    public function __get($name)
    {
        return $this->inst->get($name);
    }

    /**
     * Magic function which sets the object
     * when the shorter object is called.
     */
    public function __set($name, $value)
    {
        $this->inst->set($name, $value);
    }

    public function __isset($name)
    {
        return $this->inst->has($name);
    }

    public function __unset($name)
    {
        $this->inst->remove($name);
    }

    /**
     * Get Liten application instance by name.
     * @param  string $name
     * @return \Liten\Liten|null
     */
    public static function getInstance($name = 'default')
    {
        return isset(static::$_apps[$name]) ? static::$_apps[$name] : null;
    }

    /**
     * Set Liten application name
     * @param  string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
        static::$_apps[$name] = $this;
    }

    /**
     * Get Liten application name
     * @return string|null
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get default Liten application configuration
     * @return array
     */
    public static function defaultConfig()
    {
        return [
            // Cookies
            'cookies.lifetime'      => '1440',
            'cookies.path'          => '/',
            'cookies.domain'        => null,
            'cookies.secure'        => false,
            'cookies.httponly'      => false,
            // Secure Cookies
            'cookies.crypt'         => 'sha256',
            'cookies.secret.key'    => '8sh8w82j9s71092iw8usi',
            'cookies.savepath'      => '/tmp/',
            // Directories
            'view_dir'              => APP_PATH . 'views' . DS,
            'layouts_dir'           => APP_PATH . 'views' . DS . '_layouts' . DS,
            'partials_dir'          => APP_PATH . 'views' . DS . '_partials' . DS,
            'routers_dir'           => APP_PATH . 'routers' . DS
        ];
    }

    /**
     * Configure Liten Settings
     *
     * This method defines application settings and acts as a setter and a getter.
     *
     * If only one argument is specified and that argument is a string, the value
     * of the setting identified by the first argument will be returned, or NULL if
     * that setting does not exist.
     *
     * If only one argument is specified and that argument is an associative array,
     * the array will be merged into the existing application settings.
     *
     * If two arguments are provided, the first argument is the name of the setting
     * to be created or updated, and the second argument is the setting value.
     *
     * @param  string|array $name	If a string, the name of the setting to set or retrieve.
     * 								Else an associated array of setting names and values
     * @param  mixed		$value	If name is a string, the value of the setting identified by $name
     * @return mixed				The value of a setting if only one argument is a string
     */
    public function config($name, $value = null)
    {
        $c = $this->inst;
        if (is_array($name)) {
            if (true === $value) {
                $c['config'] = array_merge_recursive($c['config'], $name);
            } else {
                $c['config'] = array_merge($c['config'], $name);
            }
        } elseif (func_num_args() === 1) {
            return isset($c['config'][$name]) ? $c['config'][$name] : null;
        } else {
            $config = $c['config'];
            $config[$name] = $value;
            $c['config'] = $config;
        }
    }

    /**
     * Store a before middleware route and a handling function to be executed when
     * accessed using one of the specified methods
     *
     * @param string $methods Allowed methods, | delimited
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function before($methods, $pattern, $fn)
    {
        $pattern = $this->_baseroute . '/' . trim($pattern, '/');
        $pattern = $this->_baseroute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            $this->_befores[$method][] = [
                'pattern' => $pattern,
                'fn' => $fn
            ];
        }
    }

    /**
     * Store a route and a handling function to be executed when accessed using one
     * of the specified methods
     *
     * @param string $methods Allowed methods, | delimited
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function match($methods, $pattern, $fn)
    {
        $pattern = $this->_baseroute . '/' . trim($pattern, '/');
        $pattern = $this->_baseroute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            $this->_routes[$method][] = [
                'pattern' => $pattern,
                'fn' => $fn
            ];
        }
    }

    /**
     * Shorthand for a route accessed using GET
     *
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function get($pattern, $fn)
    {
        $this->match('GET', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using POST
     *
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function post($pattern, $fn)
    {
        $this->match('POST', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using PATCH
     *
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function patch($pattern, $fn)
    {
        $this->match('PATCH', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using DELETE
     *
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function delete($pattern, $fn)
    {
        $this->match('DELETE', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using PUT
     *
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function put($pattern, $fn)
    {
        $this->match('PUT', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using OPTIONS
     *
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function options($pattern, $fn)
    {
        $this->match('OPTIONS', $pattern, $fn);
    }

    /**
     * A wrapper for routes accessed using GET, POST,
     * PATCH, DELETE, PUT, or OPTIONS
     *
     * @param string $method The HTTP method request
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function route($method, $pattern, $fn)
    {
        $method = strtolower($method);
        return $this->$method($pattern, $fn);
    }

    /**
     * Groups a collection of callables onto a base route
     *
     * @param string $baseroute The route subpattern to group the callables on
     * @param callable $fn The callabled to be called
     */
    public function group($baseroute, $fn)
    {
        // Track current baseroute
        $curBaseroute = $this->_baseroute;
        // Build new baseroute string
        $this->_baseroute .= $baseroute;
        // Call the callable
        call_user_func($fn);
        // Restore original baseroute
        $this->_baseroute = $curBaseroute;
    }

    /**
     * Sets flash message
     */
    public function flash($key, $value)
    {
        $this->cookies->set($key, $value);
    }

    /**
     * Get all request headers
     * @return array The request headers
     */
    public function getRequestHeaders()
    {
        // getallheaders available, use that
        if (function_exists('getallheaders'))
            return getallheaders();

        // getallheaders not available: manually extract 'm
        $headers = [];
        foreach ($this->req->server as $name => $value) {
            if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace([' ', 'Http'], ['-', 'HTTP'], ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    /**
     * Get the request method used, taking overrides into account
     * @return string The Request method to handle
     */
    public function getRequestMethod()
    {
        // Take the method as found in $_SERVER
        $method = $this->req->server['REQUEST_METHOD'];
        // If it's a HEAD request override it to being GET and prevent any output, as per HTTP Specification
        // @url http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4
        if ($this->req->server['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        }
        // If it's a POST request, check for a method override header
        else if ($this->req->server['REQUEST_METHOD'] == 'POST') {
            $headers = $this->getRequestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], ['PUT', 'DELETE', 'PATCH'])) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }
        return $method;
    }

    /**
     * Execute the router: Loop all defined before middlewares and routes, and execute the handling function if a mactch was found
     *
     * @param object|callable $callback Function to be executed after a matching route was handled (= after router middleware)
     */
    public function run($callback = null)
    {
        // Define which method we need to handle
        $this->_method = $this->getRequestMethod();
        // Handle all before middlewares
        if (isset($this->_befores[$this->_method]))
            $this->handle($this->_befores[$this->_method]);
        // Handle all routes
        $numHandled = 0;
        if (isset($this->_routes[$this->_method]))
            $numHandled = $this->handle($this->_routes[$this->_method], true);
        // If no route was handled, trigger the 404 (if any)
        if ($numHandled == 0) {
            if ($this->_error && is_callable($this->_error))
                call_user_func($this->_error);
            else
                header($this->req->server['SERVER_PROTOCOL'] . ' 404 Not Found');
        }
        // If a route was handled, perform the finish callback (if any)
        else {
            if ($callback)
                $callback();
        }
        // If it originally was a HEAD request, clean up after ourselves by emptying the output buffer
        if ($this->req->server['REQUEST_METHOD'] == 'HEAD')
            ob_end_clean();
    }

    /**
     * Set the Error handling function
     * @param object|callable $fn The function to be executed
     */
    public function setError($fn)
    {
        $this->_error = $fn;
    }

    /**
     * Handle a a set of routes: if a match is found, execute the relating handling function
     * @param array $routes Collection of route patterns and their handling functions
     * @param boolean $quitAfterRun Does the handle function need to quit after one route was matched?
     * @return int The number of routes handled
     */
    private function handle($routes, $quitAfterRun = false)
    {
        // Counter to keep track of the number of routes we've handled
        $numHandled = 0;
        // The current page URL
        $uri = $this->getCurrentUri();
        // Loop all routes
        foreach ($routes as $route) {
            // we have a match!
            if (preg_match_all('#^' . $route['pattern'] . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {
                // Rework matches to only contain the matches, not the orig string
                $matches = array_slice($matches, 1);
                // Extract the matched URL parameters (and only the parameters)
                $params = array_map(function($match, $index) use ($matches) {
                    // We have a following parameter: take the substring from the current param position until the next one's position (thank you PREG_OFFSET_CAPTURE)
                    if (isset($matches[$index + 1]) && isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                        return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                    }

                    // We have no following parameters: return the whole lot
                    else {
                        return (isset($match[0][0]) ? trim($match[0][0], '/') : null);
                    }
                }, $matches, array_keys($matches));
                // call the handling function with the URL parameters
                call_user_func_array($route['fn'], $params);
                // yay!
                $numHandled++;
                // If we need to quit, then quit
                if ($quitAfterRun)
                    break;
            }
        }
        // Return the number of routes handled
        return $numHandled;
    }

    /**
     * Prefix a URL path with the given hostname. By default it will append
     * BASE_URL value. When not running in PROD it will also append BASE_URL
     * value.
     * @param string $resourcePath
     * @param string $hostType
     * @return string
     */
    public function prefixHost($resourcePath, $hostType = null)
    {
        if ($hostType == BASE_URL || is_null($hostType) || APP_ENV != 'PROD') {
            return "//" . $this->req->server['HTTP_HOST'] . $resourcePath;
        }
        return "//" . $hostType . $resourcePath;
    }

    /**
     * Define the current relative URI
     * @return string
     */
    private function getCurrentUri()
    {
        // Get the current Request URI and remove rewrite basepath from it (= allows one to run the router in a subfolder)
        $basepath = implode('/', array_slice(explode('/', $this->req->server['SCRIPT_NAME']), 0, -1)) . '/';
        $uri = substr($this->req->server['REQUEST_URI'], strlen($basepath));
        // Don't take query params into account on the URL
        if (strstr($uri, '?'))
            $uri = substr($uri, 0, strpos($uri, '?'));
        // Remove trailing slash + enforce a slash at the start
        $uri = '/' . trim($uri, '/');
        return $uri;
    }
}
