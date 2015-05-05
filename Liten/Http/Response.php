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

class Response
{

    /**
     * Property: status
     * Response status 
     */
    protected $_status = 200;

    /**
     * Property: content
     * Response body
     */
    protected $_content;

    /**
     * Property: encoding
     * Response encoding
     */
    protected $_encoding = "UTF-8";

    /**
     * Property: contentType
     * Response type of content
     */
    protected $_contentType = "text/html";

    /**
     * Property: protocol
     * Protocol using for data transfer in our case HTTP
     */
    protected $_protocol = "HTTP/1.1";

    /**
     * Property: headers
     * All of the headers set for response (can clear with clearHeaders() and add with header())
     */
    protected $_headers = [];
    
    public $HTTP = [
        //Informational 1xx
        100 => [
            'information' => [
                'code' => 100,
                'status' => 'Continue',
            ],
        ],
        101 => [
            'information' => [
                'code' => 101,
                'status' => 'Switching Protocols',
            ],
        ],
        //Successful 2xx
        200 => [
            'success' => [
                'code' => 200,
                'status' => 'OK',
            ],
        ],
        201 => [
            'success' => [
                'code' => 201,
                'status' => 'Created',
            ],
        ],
        202 => [
            'success' => [
                'code' => 202,
                'status' => 'Accepted',
            ],
        ],
        203 => [
            'success' => [
                'code' => 203,
                'status' => 'Non-Authoritative Information',
            ],
        ],
        204 => [
            'success' => [
                'code' => 204,
                'status' => 'No Content',
            ],
        ],
        206 => [
            'success' => [
                'code' => 206,
                'status' => 'Partial Content',
            ],
        ],
        //Redirection 3xx
        300 => [
            'redirect' => [
                'code' => 300,
                'status' => 'Multiple Choices',
            ],
        ],
        301 => [
            'redirect' => [
                'code' => 301,
                'status' => 'Moved Permanently',
            ],
        ],
        302 => [
            'redirect' => [
                'code' => 302,
                'status' => 'Found',
            ],
        ],
        307 => [
            'redirect' => [
                'code' => 307,
                'status' => 'Redirect',
            ],
        ],
        400 => [
            'error' => [
                'code' => 400,
                'status' => 'Bad Request',
            ],
        ],
        //Client Error 4xx
        400 => [
            'error' => [
                'code' => 400,
                'status' => 'Bad Request',
            ],
        ],
        401 => [
            'error' => [
                'code' => 401,
                'status' => 'Unathorized',
            ],
        ],
        402 => [
            'error' => [
                'code' => 402,
                'status' => 'Payment Required',
            ],
        ],
        403 => [
            'error' => [
                'code' => 403,
                'status' => 'Forbidden',
            ],
        ],
        404 => [
            'error' => [
                'code' => 404,
                'status' => 'Not Found',
            ],
        ],
        409 => [
            'error' => [
                'code' => 409,
                'status' => 'Conflict',
            ],
        ],
        //Server Error 5xx
        500 => [
            'error' => [
                'code' => 500,
                'status' => 'Internal Server Error',
            ],
        ],
        501 => [
            'error' => [
                'code' => 501,
                'status' => 'Not Implemented',
            ],
        ],
        502 => [
            'error' => [
                'code' => 502,
                'status' => 'Bad Gateway',
            ],
        ],
        503 => [
            'error' => [
                'code' => 503,
                'status' => 'Service Unavailable',
            ],
        ],
        504 => [
            'error' => [
                'code' => 504,
                'status' => 'Gateway Timeout',
            ],
        ],
    ];
    // Mimetypes
    protected $_mime = [
        'txt' => 'text/plain',
        'html' => 'text/html',
        'xhtml' => 'application/xhtml+xml',
        'xml' => 'application/xml',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'csv' => 'text/csv',
        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        // adobe
        'pdf' => 'application/pdf'
    ];

    /**
     * Constructor Function
     */
    public function __construct()
    {
        $this->_protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'http';
    }

    /**
     * Set HTTP header
     */
    public function header($type, $content = null)
    {
        if ($content === null) {
            if (isset($this->_headers[$type])) {
                return $this->_headers[$type];
            }
            return false;
        }
        // Normalize headers to ensure proper case
        for ($tmp = explode("-", $type), $i = 0; $i < count($tmp); $i++) {
            $tmp[$i] = ucfirst($tmp[$i]);
        }

        $type = implode("-", $tmp);
        if ($type == 'Content-Type') {
            if (preg_match('/^(.*);\w*charset\w*=\w*(.*)/', $content, $matches)) {
                $this->_contentType = $matches[1];
                $this->_encoding = $matches[2];
            } else {
                $this->_contentType = $content;
            }
        } else {
            $this->_headers[$type] = $content;
        }
        return;
    }

    /**
     * Get array of all HTTP headers
     */
    public function headers()
    {
        return $this->_headers;
    }

    /**
     * Set HTTP status to return
     */
    public function status($status = null)
    {
        if (null === $status) {
            return $this->_status;
        }
        $this->_status = $status;
        return $this->_status;
    }

    /**
     * Set HTTP encoding to use
     */
    public function encoding($encoding = null)
    {
        if (null === $encoding) {
            return $this->_encoding;
        }
        $this->_encoding = $encoding;
        return $this->_encoding;
    }

    /**
     * Set HTTP response body
     */
    public function content($content = null)
    {
        if (null === $content) {
            return $this->_content;
        }
        $this->_content = $content;
    }

    /**
     * Append new content to HTTP response body
     */
    public function appendContent($content)
    {
        $this->_content .= $content;
    }

    /**
     * Set HTTP content type
     */
    public function contentType($contentType = null)
    {
        if (null == $contentType) {
            return $this->_contentType;
        }
        $this->_contentType = $contentType;
        return $this->_contentType;
    }

    /**
     * Clear any previously set HTTP headers
     */
    public function clearHeaders()
    {
        $this->_headers = [];
        return $this->_headers;
    }

    /**
     * Send HTTP status header
     */
    protected function sendStatus()
    {
        // Send HTTP Header
        header($this->_protocol . " " . $this->_status . " " . $this->_content);
    }

    /**
     * Send all set HTTP headers
     */
    public function sendHeaders()
    {
        if (isset($this->_contentType)) {
            header('Content-Type: ' . $this->_contentType . "; charset=" . $this->_encoding);
        }

        // Send all headers
        foreach ($this->_headers as $key => $value) {
            if (is_null($value)) {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $content) {
                    header($key . ': ' . $content, false);
                }
                continue;
            }

            header($key . ": " . $value);
        }
    }

    /**
     * Send HTTP body content
     */
    public function sendBody()
    {
        echo $this->_content;
    }

    public function _format($format = 'html', $status = 200, $data = null)
    {

        foreach ($this->HTTP[$status] as $key => $value) {
            $content = $value['status'];
        }

        $this->_contentType = $this->_mime[$format];

        $this->_content = $content;
        $this->_status = $status;

        if (!headers_sent()) {
            $this->sendStatus();
            $this->sendHeaders();
        }

        if ($format === 'json' && $data === null) {
            echo json_encode($this->HTTP[$status], JSON_PRETTY_PRINT);
        } elseif ($format === 'json' && $data !== null) {
            echo json_encode($data, JSON_PRETTY_PRINT);
        } else {
            return;
        }
    }
}
