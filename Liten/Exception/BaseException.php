<?php namespace Liten\Exception;

/**
 * Liten - PHP 5 micro framework
 * 
 * @link        http://www.litenframework.com
 * @version     1.0.1
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

interface BaseException
{
    /* Protected methods inherited from Exception class */

    public function getMessage();                 // Exception message 

    public function getCode();                    // User-defined Exception code

    public function getFile();                    // Source filename

    public function getLine();                    // Source line

    public function getTrace();                   // An array of the backtrace()

    public function getTraceAsString();           // Formated string of trace

    /* Overrideable methods inherited from Exception class */

    public function __toString();                 // formated string for display

    public function __construct($message = null, $code = 0);
}
