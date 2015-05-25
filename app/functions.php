<?php

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Returns the url based on route.
 */
function url($route)
{
    $app = \Liten\Liten::getInstance();
    $url = $app->req->url_for($route);
    return $url;
}

/**
 * Renders any unwarranted special characters to HTML entities.
 * 
 * @since 1.0.0
 * @param string $str
 * @return mixed
 */
function _h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * @since 1.0.0
 */
function timeAgo($original)
{
    // array of time period chunks
    $chunks = array(
        array(60 * 60 * 24 * 365, 'year'),
        array(60 * 60 * 24 * 30, 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24, 'day'),
        array(60 * 60, 'hour'),
        array(60, 'min'),
        array(1, 'sec'),
    );

    $today = time(); /* Current unix time  */
    $since = $today - $original;

    // $j saves performing the count function each time around the loop
    for ($i = 0, $j = count($chunks); $i < $j; $i++) {

        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];

        // finding the biggest chunk (if the chunk fits, break)
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? '1 ' . $name : "$count {$name}s";

    if ($i + 1 < $j) {
        // now getting the second item
        $seconds2 = $chunks[$i + 1][0];
        $name2 = $chunks[$i + 1][1];

        // add second item if its greater than 0
        if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
            $print .= ($count2 == 1) ? ', 1 ' . $name2 : " $count2 {$name2}s";
        }
    }
    return $print;
}

/**
 * @since 1.0.0
 * @return bool
 */
function remoteFileExists($url)
{
    $curl = curl_init($url);
    //don't fetch the actual page, you only want to check the connection is ok
    curl_setopt($curl, CURLOPT_NOBODY, true);
    //do request
    $result = curl_exec($curl);
    $ret = false;
    //if request did not fail
    if ($result !== false) {
        //if request was ok, check response code
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($statusCode == 200) {
            $ret = true;
        }
    }
    curl_close($curl);
    return $ret;
}

/**
 * Return the file extension of the given filename.
 * 
 * @param  string $filename
 * @return string
 * @since   1.0.0
 */
function get_file_ext($filename)
{
    return pathinfo($filename, PATHINFO_EXTENSION);
}

/**
 * Truncate a string to a specified length without cutting a word off
 *
 * @param   string  $string  The string to truncate
 * @param   int     $length  The length to truncate the string to
 * @param   string  $append  Text to append to the string IF it gets
 *                           truncated, defaults to '...'
 * @return  string
 *
 * @access  public
 * @since   1.0.0
 */
function safe_truncate($string, $length, $append = '...')
{
    $ret = substr($string, 0, $length);
    $last_space = strrpos($ret, ' ');

    if ($last_space !== FALSE && $string != $ret) {
        $ret = substr($ret, 0, $last_space);
    }

    if ($ret != $string) {
        $ret .= $append;
    }

    return $ret;
}

/**
 * Special function for files including
 * 
 * @param string $file
 * @param bool $once
 * @param bool|Closure $show_errors If bool error will be processed, if Closure - only Closure will be called
 * 
 * @return bool
 * @since 1.0.0
 */
function _require($file, $once = false, $show_errors = true)
{
    if (file_exists($file)) {
        if ($once) {
            return require_once $file;
        } else {
            return require $file;
        }
    } elseif (is_bool($show_errors) && $show_errors) {
        $data = debug_backtrace()[0];
        trigger_error("File $file does not exists in $data[file] on line $data[line]", E_USER_ERROR);
    } elseif ($show_errors instanceof \Closure) {
        return (bool) $show_errors();
    }
    return false;
}

/**
 * Special function for files including
 * 
 * @param string $file
 * @param bool $once
 * @param bool|Closure $show_errors If bool error will be processed, if Closure - only Closure will be called
 * 
 * @return bool
 * @since 1.0.0
 */
function _include($file, $once = false, $show_errors = true)
{
    if (file_exists($file)) {
        if ($once) {
            return include_once $file;
        } else {
            return include $file;
        }
    } elseif (is_bool($show_errors) && $show_errors) {
        $data = debug_backtrace()[0];
        trigger_error("File $file does not exists in $data[file] on line $data[line]", E_USER_WARNING);
    } elseif ($show_errors instanceof \Closure) {
        return (bool) $show_errors();
    }
    return false;
}

/**
 * Special function for files including
 * 
 * @param string $file
 * @param bool|Closure $show_errors If bool error will be processed, if Closure - only Closure will be called
 * 
 * @return bool
 * @since 1.0.0
 */
function _require_once($file, $show_errors = true)
{
    return _require($file, true, $show_errors);
}

/**
 * Special function for files including
 * 
 * @param string $file
 * @param bool|Closure $show_errors If bool error will be processed, if Closure - only Closure will be called
 * 
 * @return bool
 * @since 1.0.0
 */
function _include_once($file, $show_errors = true)
{
    return _include($file, true, $show_errors);
}

/**
 * Validate email.
 * 
 * @param string $email
 * @return string (ok, empty, bad email).
 * @since 1.0.0
 */
function validate_email($email)
{
    if ($email == '')
        return false;
    if (filter_var($email, FILTER_VALIDATE_EMAIL) == false)
        return false;
    else
        return true;
}

/**
 * Formats date to be stored in MySQL database.
 * 
 * @return string
 * @since 1.0.0
 */
function formatDate($date)
{
    $date = strtotime($date);
    $date = date("Y-m-d", $date);

    return $date;
}

/**
 * Removes all whitespace.
 * 
 * @param string $str
 * @return mixed
 * @since 1.0.0
 */
function _trim($str)
{
    return preg_replace('/\s/', '', $str);
}

/**
 * Function used to create a slug associated to an "ugly" string.
 * 
 * @param string $string the string to transform.
 * @return string the resulting slug.
 * @since 1.0.0
 */
function slugify($string)
{
    $table = array(
        'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
        'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
        'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
        'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
        'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
        'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r', '/' => '-', ' ' => '-'
    );
    // -- Remove duplicated spaces
    $stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);
    // -- Returns the slug
    return strtolower(strtr($string, $table));
}

/**
 * @param string $file Filepath
 * @param int $digits Digits to display
 * @return string|bool Size (KB, MB, GB, TB) or boolean
 * @since 1.0.0
 */
function getFilesize($file, $digits = 2)
{
    if (is_file($file)) {
        $fileSize = filesize($file);
        $sizes = array("TB", "GB", "MB", "KB", "B");
        $total = count($sizes);
        while ($total-- && $fileSize > 1024) {
            $fileSize /= 1024;
        }
        return round($fileSize, $digits) . " " . $sizes[$total];
    }
    return false;
}

/**
 * Redirects to another page.
 * 
 * @param string $location The path to redirect to
 * @param int $status Status code to use
 * @return bool False if $location is not set
 * @since 1.0.0
 */
function redirect($location, $status = 302)
{
    if (!$location)
        return false;
    header("Location: $location", true, $status);
}
if (!function_exists('hash_equals')) {

    /**
     * Timing attack safe string comparison
     * 
     * Compares two strings using the same time whether they're equal or not.
     * This function should be used to mitigate timing attacks; for instance, when testing crypt() password hashes.
     * 
     * @param string $known_string The string of known length to compare against
     * @param string $user_string The user-supplied string
     * @return boolean Returns TRUE when the two strings are equal, FALSE otherwise.
     */
    function hash_equals($known_string, $user_string)
    {
        if (func_num_args() !== 2) {
            // handle wrong parameter count as the native implentation
            trigger_error('hash_equals() expects exactly 2 parameters, ' . func_num_args() . ' given', E_USER_WARNING);
            return null;
        }
        if (is_string($known_string) !== true) {
            trigger_error('hash_equals(): Expected known_string to be a string, ' . gettype($known_string) . ' given', E_USER_WARNING);
            return false;
        }
        $known_string_len = strlen($known_string);
        $user_string_type_error = 'hash_equals(): Expected user_string to be a string, ' . gettype($user_string) . ' given'; // prepare wrong type error message now to reduce the impact of string concatenation and the gettype call
        if (is_string($user_string) !== true) {
            trigger_error($user_string_type_error, E_USER_WARNING);
            // prevention of timing attacks might be still possible if we handle $user_string as a string of diffent length (the trigger_error() call increases the execution time a bit)
            $user_string_len = strlen($user_string);
            $user_string_len = $known_string_len + 1;
        } else {
            $user_string_len = $known_string_len + 1;
            $user_string_len = strlen($user_string);
        }
        if ($known_string_len !== $user_string_len) {
            $res = $known_string ^ $known_string; // use $known_string instead of $user_string to handle strings of diffrent length.
            $ret = 1; // set $ret to 1 to make sure false is returned
        } else {
            $res = $known_string ^ $user_string;
            $ret = 0;
        }
        for ($i = strlen($res) - 1; $i >= 0; $i--) {
            $ret |= ord($res[$i]);
        }
        return $ret === 0;
    }
}

/**
 	* Outputs the html checked attribute.
 	*
 	* Compares the first two arguments and if identical marks as checked
 	*
 	*
 	* @param mixed $checked One of the values to compare
 	* @param mixed $current (true) The other value to compare if not just true
 	* @param bool $echo Whether to echo or just return the string
 	* @return string html attribute or empty string
 	*/
 	if( !function_exists('checked') ) {
 		
	 	function checked( $checked, $current = true, $echo = true ) {
			return checked_selected_helper( $checked, $current, $echo, 'checked' );
		}
	
	}

	/**
 	* Outputs the html selected attribute.
 	*
 	* Compares the first two arguments and if identical marks as selected
 	*
 	*
 	* @param mixed $selected One of the values to compare
 	* @param mixed $current (true) The other value to compare if not just true
 	* @param bool $echo Whether to echo or just return the string
 	* @return string html attribute or empty string
 	*/
 	if( !function_exists('selected') ) {
 		
	 	function selected( $selected, $current = true, $echo = true ) {
			return checked_selected_helper( $selected, $current, $echo, 'selected' );
		}
	
	}

	/**
 	* Outputs the html disabled attribute.
 	*
 	* Compares the first two arguments and if identical marks as disabled
 	*
 	*
 	* @param mixed $disabled One of the values to compare
 	* @param mixed $current (true) The other value to compare if not just true
 	* @param bool $echo Whether to echo or just return the string
 	* @return string html attribute or empty string
 	*/
 	if( !function_exists('disabled') ) {
 		
		function disabled( $disabled, $current = true, $echo = true ) {
			return checked_selected_helper( $disabled, $current, $echo, 'disabled' );
		}
	
	}

	/**
 	* Private helper function for checked, selected, and disabled.
 	*
 	* Compares the first two arguments and if identical marks as $type
 	*
 	* @access private
 	*
 	* @param any $helper One of the values to compare
 	* @param any $current (true) The other value to compare if not just true
 	* @param bool $echo Whether to echo or just return the string
 	* @param string $type The type of checked|selected|disabled we are doing
 	* @return string html attribute or empty string
 	*/
 	if( !function_exists('checked_selected_helper') ) {
 		
	 	function checked_selected_helper( $helper, $current, $echo, $type ) {
			if ( $helper === $current )
				$result = " $type='$type'";
			else
				$result = '';
	
			if ( $echo )
				echo $result;
	
			return $result;
		}
	
	}
    
    function _filter_input_int($_globals, $variable_name = '') {
        return filter_input(strtoupper($_globals), $variable_name, FILTER_SANITIZE_NUMBER_INT);
    }
    
    function _filter_input_string($_globals, $variable_name = '') {
        return filter_input(strtoupper($_globals), $variable_name, FILTER_SANITIZE_STRING);
    }
    
    function _filter_input_array($_globals, $args = FILTER_SANITIZE_STRING, $bool = true) {
        return filter_input_array(strtoupper($_globals), $args, $bool);
    }
