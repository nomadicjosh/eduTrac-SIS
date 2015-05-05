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

class Cookies
{

    /**
     * Liten application object
     * @var object|callable
     */
    protected $_app;

    public function __construct(\Liten\Liten $liten = null)
    {
        $this->_app = !empty($liten) ? $liten : \Liten\Liten::getInstance();
    }
    
    /**
     * Generates a random token which is then hashed.
     * 
     * @param type $length
     * @return string
     */
    public function token($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return hash($this->_app->config('cookies.crypt'), $randomString);
    }

    /**
     * Sets a regular cookie
     *
     * @since 1.0.0
     * @return mixed
     * 
     */
    public function set($key, $value, $expires = null)
    {
        return setcookie(
            $key,
            $value,
            ($expires == null ? time() + $this->_app->config('cookies.lifetime') : time() + $expires),
            $this->_app->config('cookies.path'),
            $this->_app->config('cookies.domain'),
            $this->_app->config('cookies.secure'),
            $this->_app->config('cookies.httponly')
        );
    }
    
    /**
     * Retrieves a regular cookie if it is set.
     *
     * @since 1.0.0
     * @return string Returns cookie if valid
     * 
     */
    public function get($key)
    {
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
    }

    /**
     * Set a secure cookie that is saved
     * to the server.
     *
     * @since 1.0.2
     * @return mixed
     * 
     */
    public function setSecureCookie($key, $data, $expires = null)
    {
        $token = $this->token();
        $value = $this->buildCookie($token, $expires);
        
        file_put_contents(
            $this->_app->config('cookies.savepath').'cookies.'.$token,
            $this->_app->hook->maybe_serialize( 
                        [
                            $key => $data,
                            'exp' => ($expires == null ? time() + $this->_app->config('cookies.lifetime') : time() + $expires)
                        ]
                    )
        );
        
        return setcookie(
            $key,
            $value,
            ($expires == null ? time() + $this->_app->config('cookies.lifetime') : time() + $expires),
            $this->_app->config('cookies.path'),
            $this->_app->config('cookies.domain'),
            $this->_app->config('cookies.secure'),
            $this->_app->config('cookies.httponly')
        );
    }
    
    public function getSecureCookie($key)
    {
        $file = $this->_app->config('cookies.savepath').'cookies.'.$this->getCookieVars($key,'data');
        if(file_exists($file)) {
            $data = $this->_app->hook->maybe_unserialize(file_get_contents($file));
            return $data[$key];
        }
        return false;
    }

    /**
     * Unset the cookie
     *
     * @since 1.0.0
     * @return mixed
     * 
     */
    public function remove($key)
    {
        return setcookie(
            $key,
            '',
            time() - (432000 + $this->_app->config('cookies.lifetime')),
            $this->_app->config('cookies.path'),
            $this->_app->config('cookies.domain'),
            $this->_app->config('cookies.secure'),
            $this->_app->config('cookies.httponly')
        );
    }

    /**
     * Generates a hardened cookie string with digest.
     *
     * @param string $data Cookie value: e.g. random token or hash
     */
    public function buildCookie($data, $expires = null)
    {
        $time = ($expires == null ? time() + $this->_app->config('cookies.lifetime') : $expires + time());

        $string = sprintf("exp=%s&data=%s", urlencode($time), urlencode($data));
        $mac = hash_hmac($this->_app->config('cookies.crypt'), $string, $this->_app->config('cookies.secret.key'));
        return $string . '&digest=' . urlencode($mac);
    }

    /**
     * Extracts data from the cookie string.
     * 
     * @param type $key
     * @param type $str
     * @return array
     */
    public function getCookieVars($key, $str)
    {
        $vars = [];
        parse_str($_COOKIE[$key], $vars);
        return $vars[$str];
    }

    /**
     * Extracts the data from the cookie string. 
     * This does not verify the cookie! This is just so you can get the token.
     *
     * @return string Cookie data var
     * */
    public function getCookieData($key)
    {
        return $this->getCookieVars($key, 'data');
    }

    /**
     * Verifies the expiry and MAC for the cookie
     *
     * @param string $cookie String from the client
     * @return bool
     */
    public function verifySecureCookie($key)
    {
        $cookieFile = glob($this->_app->config('cookies.savepath').'cookies.*');
        foreach($cookieFile as $file) {
            if(file_exists($file)) {
                $exp = $this->_app->hook->maybe_unserialize(file_get_contents($file));
            }
        }
        /**
         * If the cookie exists and it is expired, delete it
         * from the server side.
         */
        if(file_exists($file) && $exp['exp'] < time()) {
            unlink($file);
        }
        
        if ($this->getCookieVars($key, 'exp') === null || $this->getCookieVars($key, 'exp') < time()) {
            // The cookie has expired
            return false;
        }

        $mac = sprintf("exp=%s&data=%s", urlencode($this->getCookieVars($key, 'exp')), urlencode($this->getCookieVars($key, 'data')));
        $hash = hash_hmac($this->_app->config('cookies.crypt'), $mac, $this->_app->config('cookies.secret.key'));

        if (!hash_equals($this->getCookieVars($key, 'digest'), $hash)) {
            // The cookie has been compromised
            return false;
        }

        return true;
    }
}
