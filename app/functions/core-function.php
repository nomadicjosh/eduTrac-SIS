<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$app = \Liten\Liten::getInstance();

/**
 * eduTrac SIS Core Functions
 *
 * @license GPLv3
 *         
 * @since 3.0.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
const CURRENT_RELEASE = '6.3';
define('RELEASE_TAG', trim(_file_get_contents(BASE_PATH . 'RELEASE')));
define('REQUEST_TIME', $app->req->server['REQUEST_TIME']);
use League\Event\Event;
use PHPBenchmark\HtmlView;
use PHPBenchmark\Monitor;
use PHPBenchmark\MonitorInterface;
use app\src\Core\Exception\Exception;
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\IOException;
use Cascade\Cascade;
use Jenssegers\Date\Date;
use PDOException as ORMException;
use Respect\Validation\Validator as v;

/**
 * Retrieves eduTrac site root url.
 *
 * @since 4.1.9
 * @uses $app->hook->apply_filter() Calls 'base_url' filter.
 *      
 * @return string eduTrac SIS root url.
 */
function get_base_url()
{
    $app = \Liten\Liten::getInstance();
    $url = url('/');
    return $app->hook->apply_filter('base_url', $url);
}

/**
 * Custom make directory function.
 *
 * This function will check if the path is an existing directory,
 * if not, then it will be created with set permissions and also created
 * recursively if needed.
 *
 * @since 6.1.00
 * @param string $path
 *            Path to be created.
 * @return string
 * @throws IOException If session.savepath is not set, path is not writable, or
 * lacks permission to mkdir.
 */
function _mkdir($path)
{
    if ('' == _trim($path)) {
        $message = _t('Invalid directory path: Empty path given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    if (session_save_path() == "") {
        throw new IOException(sprintf(_t('Session savepath is not set correctly. It is currently set to: %s'), session_save_path()));
    }

    if (!is_writable(session_save_path())) {
        throw new IOException(sprintf(_t('"%s" is not writable or etSIS does not have permission to create and write directories and files in this location.'), session_save_path()));
    }

    if (!is_dir($path)) {
        if (!@mkdir($path, 0755, true)) {
            throw new IOException(sprintf(_t('The following directory could not be created: %s'), $path));
        }
    }
}

/**
 * Displays the returned translated text.
 *
 * @since 1.0.0
 * @param type $msgid
 *            The translated string.
 * @param type $domain
 *            Domain lookup for translated text.
 * @return string Translated text according to current locale.
 */
function _t($msgid, $domain = '')
{
    if ($domain !== '') {
        return d__($domain, $msgid);
    } else {
        return d__('edutrac-sis', $msgid);
    }
}

function get_path_info($relative)
{
    $app = \Liten\Liten::getInstance();
    $base = basename(BASE_PATH);
    if (strpos($app->req->server['REQUEST_URI'], DS . $base . $relative) === 0) {
        return $relative;
    } else {
        return $app->req->server['REQUEST_URI'];
    }
}

/**
 * Custom function to use curl, fopen, or use file_get_contents
 * if curl is not available.
 *
 * @since 5.0.1
 * @param string $filename
 *            Resource to read.
 * @param bool $use_include_path
 *            Whether or not to use include path.
 * @param bool $context
 *            Whether or not to use a context resource.
 */
function _file_get_contents($filename, $use_include_path = false, $context = true)
{
    $app = \Liten\Liten::getInstance();

    /**
     * Filter the boolean for include path.
     *
     * @since 6.2.4
     * @var bool $use_include_path
     * @return bool
     */
    $use_include_path = $app->hook->apply_filter('trigger_include_path_search', $use_include_path);

    /**
     * Filter the context resource.
     *
     * @since 6.2.4
     * @var bool $context
     * @return bool
     */
    $context = $app->hook->apply_filter('resource_context', $context);

    $opts = [
        'http' => [
            'timeout' => 360.0
        ]
    ];

    /**
     * Filters the stream context create options.
     *
     * @since 6.2.4
     * @param array $opts Array of options.
     * @return mixed
     */
    $opts = $app->hook->apply_filter('stream_context_create_options', $opts);

    if ($context === true) {
        $context = stream_context_create($opts);
    } else {
        $context = null;
    }

    $result = file_get_contents($filename, $use_include_path, $context);

    if ($result) {
        return $result;
    } else {
        $handle = fopen($filename, "r", $use_include_path, $context);
        $contents = stream_get_contents($handle);
        fclose($handle);
        if ($contents) {
            return $contents;
        } else
        if (!function_exists('curl_init')) {
            return false;
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $filename);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 360);
            $output = curl_exec($ch);
            curl_close($ch);
            if ($output) {
                return $output;
            } else {
                return false;
            }
        }
    }
}

/**
 * Bookmarking initialization function.
 *
 * @since 1.1.3
 */
function benchmark_init()
{
    if (!etsis_file_exists(BASE_PATH . 'config.php', false)) {
        return false;
    }
    if (_h(get_option('enable_benchmark')) == 1) {
        Monitor::instance()
            ->init()
            ->addListener(Monitor::EVENT_SHUT_DOWN, function(Event $evt, MonitorInterface $monitor) {
                $htmlView = new HtmlView();
                echo $htmlView->getView($monitor);
            });
    }
}

/**
 * Resize image function.
 *
 * @since 6.3.0
 * @param int $width Width of the image.
 * @param int $height Height of the image.
 * @param string $target Path to the image.
 */
function resize_image($width, $height, $target)
{
    // takes the larger size of the width and height and applies the formula. Your function is designed to work with any image in any size.
    if ($width > $height) {
        $percentage = ($target / $width);
    } else {
        $percentage = ($target / $height);
    }

    // gets the new value and applies the percentage, then rounds the value
    $new_width = round($width * $percentage);
    $new_height = round($height * $percentage);
    // returns the new sizes in html image tag format...this is so you can plug this function inside an image tag so that it will set the image to the correct size, without putting a whole script into the tag.
    return 'width="' . $new_width . '" height="' . $new_height . '"';
}

// An alternative function of using the echo command.

function _e($string)
{
    echo $string;
}

/**
 * Turn all URLs into clickable links.
 * 
 * @since 6.3.0
 * @param string $value
 * @param array  $protocols  http/https, ftp, mail, twitter
 * @param array  $attributes
 * @param string $mode       normal or all
 * @return string
 */
function make_clickable($value, $protocols = ['http', 'mail'], array $attributes = [])
{
    // Link attributes
    $attr = '';
    foreach ($attributes as $key => $val) {
        $attr = ' ' . $key . '="' . htmlentities($val) . '"';
    }

    $links = [];

    // Extract existing links and tags
    $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) {
        return '<' . array_push($links, $match[1]) . '>';
    }, $value);

    // Extract text links for each protocol
    foreach ((array) $protocols as $protocol) {
        switch ($protocol) {
            case 'http':
            case 'https': $value = preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) {
                    if ($match[1])
                        $protocol = $match[1];
                    $link = $match[2] ? : $match[3];
                    return '<' . array_push($links, "<a $attr href=\"$protocol://$link\">$link</a>") . '>';
                }, $value);
                break;
            case 'mail': $value = preg_replace_callback('~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~', function ($match) use (&$links, $attr) {
                    return '<' . array_push($links, "<a $attr href=\"mailto:{$match[1]}\">{$match[1]}</a>") . '>';
                }, $value);
                break;
            case 'twitter': $value = preg_replace_callback('~(?<!\w)[@#](\w++)~', function ($match) use (&$links, $attr) {
                    return '<' . array_push($links, "<a $attr href=\"https://twitter.com/" . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1] . "\">{$match[0]}</a>") . '>';
                }, $value);
                break;
            default: $value = preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) {
                    return '<' . array_push($links, "<a $attr href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>';
                }, $value);
                break;
        }
    }

    // Insert all link
    return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) {
        return $links[$match[1] - 1];
    }, $value);
}

/**
 * Hide menu links by functions and/or by
 * permissions.
 *
 * @since 4.0.4
 */
function hl($f, $p = NULL)
{
    if (function_exists($f)) {
        return ' style="display:none !important;"';
    }
    if ($p !== NULL) {
        return ae($p);
    }
}

/**
 * Function used to check the installation
 * of a particular module.
 * If module exists,
 * unhide it's links throughout the system.
 */
function ml($func)
{
    if (!function_exists($func)) {
        return ' style="display:none !important;"';
    }
}

function _bool($num)
{
    switch ($num) {
        case 1:
            return 'Yes';
            break;
        case 0:
            return 'No';
            break;
    }
}

function translate_addr_status($status)
{
    switch ($status) {
        case 'C':
            return 'Current';
            break;

        case 'I':
            return 'Inactive';
            break;
    }
}

function translate_addr_type($type)
{
    switch ($type) {
        case 'H':
            return 'Home';
            break;

        case 'P':
            return 'Permanent';
            break;

        case 'B':
            return 'Business';
            break;
    }
}

/**
 * Function to help with SQL injection when using SQL terminal
 * and the saved query screens.
 */
function strstra($haystack, $needles = array(), $before_needle = false)
{
    $chr = array();
    foreach ($needles as $needle) {
        $res = strstr($haystack, $needle, $before_needle);
        if ($res !== false)
            $chr[$needle] = $res;
    }
    if (empty($chr))
        return false;
    return min($chr);
}

function print_gzipped_page()
{
    global $HTTP_ACCEPT_ENCODING;
    if (headers_sent()) {
        $encoding = false;
    } elseif (strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false) {
        $encoding = 'x-gzip';
    } elseif (strpos($HTTP_ACCEPT_ENCODING, 'gzip') !== false) {
        $encoding = 'gzip';
    } else {
        $encoding = false;
    }

    if ($encoding) {
        $contents = ob_get_contents();
        ob_end_clean();
        header('Content-Encoding: ' . $encoding);
        print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
        $size = strlen($contents);
        $contents = gzcompress($contents, 9);
        $contents = substr($contents, 0, $size);
        print($contents);
        exit();
    } else {
        ob_end_flush();
        exit();
    }
}

function percent($num_amount, $num_total)
{
    $count1 = $num_amount / $num_total;
    $count2 = $count1 * 100;
    $count = number_format($count2, 0);
    return $count;
}

/**
 * Merge user defined arguments into defaults array.
 *
 * This function is used throughout eduTrac to allow for both string or array
 * to be merged into another array.
 *
 * @since 6.2.0
 * @param string|array $args
 *            Value to merge with $defaults
 * @param array $defaults
 *            Optional. Array that serves as the defaults. Default empty.
 * @return array Merged user defined values with defaults.
 */
function etsis_parse_args($args, $defaults = '')
{
    if (is_object($args)) {
        $r = get_object_vars($args);
    } elseif (is_array($args)) {
        $r = $args;
    } else {
        etsis_parse_str($args, $r);
    }

    if (is_array($defaults)) {
        return array_merge($defaults, $r);
    }

    return $r;
}

function head_release_meta()
{
    echo "<meta name='generator' content='eduTrac SIS " . CURRENT_RELEASE . "'>\n";
}

function foot_release()
{
    if (CURRENT_RELEASE != RELEASE_TAG) {
        $release = "r" . CURRENT_RELEASE . ' (t' . RELEASE_TAG . ')';
    } else {
        $release = "r" . CURRENT_RELEASE;
    }
    return $release;
}

/**
 * Hashes a plain text password.
 *
 * @since 6.2.0
 * @param string $password
 *            Plain text password
 * @return mixed
 */
function etsis_hash_password($password)
{
    if ('' == _trim($password)) {
        $message = _t('Invalid password: empty password given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    // By default, use the portable hash from phpass
    $hasher = new \app\src\PasswordHash(8, FALSE);

    return $hasher->HashPassword($password);
}

/**
 * Checks a plain text password against a hashed password.
 *
 * @since 6.2.0
 * @param string $password
 *            Plain test password.
 * @param string $hash
 *            Hashed password in the database to check against.
 * @param int $person_id
 *            Person ID.
 * @return mixed
 */
function etsis_check_password($password, $hash, $person_id = '')
{
    $app = \Liten\Liten::getInstance();
    // If the hash is still md5...
    if (strlen($hash) <= 32) {
        $check = ($hash == md5($password));
        if ($check && $person_id) {
            // Rehash using new hash.
            etsis_set_password($password, $person_id);
            $hash = etsis_hash_password($password);
        }
        return $app->hook->apply_filter('check_password', $check, $password, $hash, $person_id);
    }

    // If the stored hash is longer than an MD5, presume the
    // new style phpass portable hash.
    $hasher = new \app\src\PasswordHash(8, FALSE);

    $check = $hasher->CheckPassword($password, $hash);

    return $app->hook->apply_filter('check_password', $check, $password, $hash, $person_id);
}

/**
 * Used by etsis_check_password in order to rehash
 * an old password that was hashed using MD5 function.
 *
 * @since 6.2.0
 * @param string $password
 *            Person password.
 * @param int $person_id
 *            Person ID.
 * @return mixed
 */
function etsis_set_password($password, $person_id)
{
    $app = \Liten\Liten::getInstance();
    try {
        $hash = etsis_hash_password($password);
        $q = $app->db->person();
        $q->password = $hash;
        $q->where('personID = ?', $person_id)->update();
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error($e->getMessage());
        _etsis_flash()->error(_etsis_flash()->notice(409));
    }
}

/**
 * Prints a list of timezones which includes
 * current time.
 *
 * @return array
 */
function generate_timezone_list()
{
    static $regions = array(
        \DateTimeZone::AFRICA,
        \DateTimeZone::AMERICA,
        \DateTimeZone::ANTARCTICA,
        \DateTimeZone::ASIA,
        \DateTimeZone::ATLANTIC,
        \DateTimeZone::AUSTRALIA,
        \DateTimeZone::EUROPE,
        \DateTimeZone::INDIAN,
        \DateTimeZone::PACIFIC
    );

    $timezones = array();
    foreach ($regions as $region) {
        $timezones = array_merge($timezones, \DateTimeZone::listIdentifiers($region));
    }

    $timezone_offsets = array();
    foreach ($timezones as $timezone) {
        $tz = new \DateTimeZone($timezone);
        $timezone_offsets[$timezone] = $tz->getOffset(new DateTime());
    }

    // sort timezone by timezone name
    ksort($timezone_offsets);

    $timezone_list = array();
    foreach ($timezone_offsets as $timezone => $offset) {
        $offset_prefix = $offset < 0 ? '-' : '+';
        $offset_formatted = gmdate('H:i', abs($offset));

        $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

        $t = new \DateTimeZone($timezone);
        $c = new \DateTime(null, $t);
        $current_time = $c->format('g:i A');

        $timezone_list[$timezone] = "(${pretty_offset}) $timezone - $current_time";
    }

    return $timezone_list;
}

/**
 * Get age by birthdate.
 *
 * @since 6.3.0
 * @param string $birthdate
 *            Person's birth date.
 * @return mixed
 */
function get_age($birthdate = '0000-00-00')
{
    $date = new Date($birthdate);
    $age = $date->age;

    if ($birthdate <= '0000-00-00' || $age == \Jenssegers\Date\Date::now()->format('Y')) {
        return _t('Unknown');
    }
    return $age;
}

/**
 * Converts a string into unicode values.
 *
 * @since 4.3
 * @param string $string            
 * @return mixed
 */
function unicoder($string)
{
    $p = str_split(trim($string));
    $new_string = '';
    foreach ($p as $val) {
        $new_string .= '&#' . ord($val) . ';';
    }
    return $new_string;
}

/**
 * Checks against certain keywords when the SQL
 * terminal and saved query screens are used.
 * Helps
 * against database manipulation and SQL injection.
 *
 * @since 1.0.0
 * @return boolean
 */
function forbidden_keyword()
{
    $array = [
        "create",
        "delete",
        "drop table",
        "alter",
        "insert",
        "change",
        "convert",
        "modifies",
        "optimize",
        "purge",
        "rename",
        "replace",
        "revoke",
        "unlock",
        "truncate",
        "anything",
        "svc",
        "write",
        "into",
        "--",
        "1=1",
        "1 = 1",
        "\\",
        "?",
        "'x'",
        "loop",
        "exit",
        "leave",
        "undo",
        "upgrade",
        "html",
        "script",
        "css",
        "x=x",
        "x = x",
        "everything",
        "anyone",
        "everyone",
        "upload",
        "&",
        "&amp;",
        "xp_",
        "$",
        "0=0",
        "0 = 0",
        "X=X",
        "X = X",
        "mysql",
        "'='",
        "XSS",
        "mysql_",
        "die",
        "password",
        "auth_token",
        "alert",
        "img",
        "src",
        "drop tables",
        "drop index",
        "drop database",
        "drop column",
        "show tables in",
        "show databases",
        " in ",
        "slave",
        "hosts",
        "grants",
        "warnings",
        "variables",
        "triggers",
        "privileges",
        "engine",
        "processlist",
        "relaylog",
        "errors",
        "information_schema",
        "mysqldump",
        "hostname",
        "root",
        "use",
        "describe",
        "flush",
        "privileges",
        "mysqladmin",
        "set",
        "quit",
        "-u",
        "-p",
        "load data",
        "backup table",
        "cache index",
        "change master to",
        "commit",
        "drop user",
        "drop view",
        "kill",
        "load index",
        "load table",
        "lock",
        "reset",
        "restore",
        "rollback",
        "savepoint",
        "show character set",
        "show collation",
        "innodb",
        "show table status"
    ];
    return $array;
}

/**
 * The myetSIS welcome message filter.
 *
 * @since 4.3
 */
function the_myetsis_welcome_message()
{
    $app = \Liten\Liten::getInstance();
    $welcome_message = _escape(get_option('myetsis_welcome_message'));
    $welcome_message = $app->hook->apply_filter('the_myetsis_welcome_message', $welcome_message);
    $welcome_message = str_replace(']]>', ']]&gt;', $welcome_message);
    return $welcome_message;
}

/**
 * Returns the template header information
 *
 * @since 6.0.00
 * @param
 *            string (optional) $template_dir loads templates from specified folder
 * @return mixed
 */
function get_templates_header($template_dir = '')
{
    $templates_header = [];
    if ($handle = opendir($template_dir)) {

        while ($file = readdir($handle)) {
            if (is_file($template_dir . $file)) {
                if (strpos($template_dir . $file, '.template.php')) {
                    $fp = fopen($template_dir . $file, 'r');
                    // Pull only the first 8kiB of the file in.
                    $template_data = fread($fp, 8192);
                    fclose($fp);

                    preg_match('|Template Name:(.*)$|mi', $template_data, $name);
                    preg_match('|Template Slug:(.*)$|mi', $template_data, $template_slug);

                    foreach (array(
                    'name',
                    'template_slug'
                    ) as $field) {
                        if (!empty(${$field}))
                            ${$field} = trim(${$field}[1]);
                        else
                            ${$field} = '';
                    }
                    $template_data = array(
                        'filename' => $file,
                        'Name' => $name,
                        'Title' => $name,
                        'Slug' => $template_slug
                    );
                    $templates_header[] = $template_data;
                }
            } else
            if ((is_dir($template_dir . $file)) && ($file != '.') && ($file != '..')) {
                get_templates_header($template_dir . $file . '/');
            }
        }

        closedir($handle);
    }
    return $templates_header;
}

/**
 * Returns the layout header information
 *
 * @since 6.0.00
 * @param
 *            string (optional) $layout_dir loads layouts from specified folder
 * @return mixed
 */
function get_layouts_header($layout_dir = '')
{
    $layouts_header = [];
    if ($handle = opendir($layout_dir)) {

        while ($file = readdir($handle)) {
            if (is_file($layout_dir . $file)) {
                if (strpos($layout_dir . $file, '.layout.php')) {
                    $fp = fopen($layout_dir . $file, 'r');
                    // Pull only the first 8kiB of the file in.
                    $layout_data = fread($fp, 8192);
                    fclose($fp);

                    preg_match('|Layout Name:(.*)$|mi', $layout_data, $name);
                    preg_match('|Layout Slug:(.*)$|mi', $layout_data, $layout_slug);

                    foreach (array(
                    'name',
                    'layout_slug'
                    ) as $field) {
                        if (!empty(${$field}))
                            ${$field} = trim(${$field}[1]);
                        else
                            ${$field} = '';
                    }
                    $layout_data = array(
                        'filename' => $file,
                        'Name' => $name,
                        'Title' => $name,
                        'Slug' => $layout_slug
                    );
                    $layouts_header[] = $layout_data;
                }
            } else
            if ((is_dir($layout_dir . $file)) && ($file != '.') && ($file != '..')) {
                get_layouts_header($layout_dir . $file . '/');
            }
        }

        closedir($handle);
    }
    return $layouts_header;
}

/**
 * Subdomain as directory function uses the subdomain
 * of the install as a directory.
 *
 * @since 6.0.05
 * @return string
 */
function subdomain_as_directory()
{
    $app = \Liten\Liten::getInstance();

    $subdomain = '';
    $domain_parts = explode('.', $app->req->server['SERVER_NAME']);
    if (count($domain_parts) == 3) {
        $subdomain = $domain_parts[0];
    } else {
        $subdomain = 'www';
    }
    return $subdomain;
}

/**
 * Strips out all duplicate values and compact the array.
 *
 * @since 6.0.04
 * @param mixed $a
 *            An array that be compacted.
 * @return mixed
 */
function array_unique_compact($a)
{
    $tmparr = array_unique($a);
    $i = 0;
    foreach ($tmparr as $v) {
        $newarr[$i] = $v;
        $i ++;
    }
    return $newarr;
}

function check_mime_type($file, $mode = 0)
{
    if ('' == _trim($file)) {
        $message = _t('Invalid file: empty file given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    // mode 0 = full check
    // mode 1 = extension check only
    $mime_types = array(
        'txt' => 'text/plain',
        'csv' => 'text/plain',
        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        // adobe
        'pdf' => 'application/pdf',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'docx' => 'application/msword',
        'xlsx' => 'application/vnd.ms-excel',
        'pptx' => 'application/vnd.ms-powerpoint'
    );

    $ext = strtolower(array_pop(explode('.', $file)));

    if (function_exists('mime_content_type') && $mode == 0) {
        $mimetype = mime_content_type($file);
        return $mimetype;
    }

    if (function_exists('finfo_open') && $mode == 0) {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $file);
        finfo_close($finfo);
        return $mimetype;
    } elseif (array_key_exists($ext, $mime_types)) {
        return $mime_types[$ext];
    }
}

/**
 * Check whether variable is an eduTrac SIS Error.
 *
 * Returns true if $object is an object of the \app\src\Core\etsis_Error class.
 *
 * @since 6.1.14
 * @param mixed $object
 *            Check if unknown variable is an \app\src\Core\etsis_Error object.
 * @return bool True, if \app\src\Core\etsis_Error. False, if not \app\src\Core\etsis_Error.
 */
function is_etsis_error($object)
{
    return ($object instanceof \app\src\Core\etsis_Error);
}

/**
 * Check whether variable is an eduTrac SIS Exception.
 *
 * Returns true if $object is an object of the `\app\src\Core\Exception\BaseException` class.
 *
 * @since 6.1.14
 * @param mixed $object
 *            Check if unknown variable is an `\app\src\Core\Exception\BaseException` object.
 * @return bool True, if `\app\src\Core\Exception\BaseException`. False, if not `\app\src\Core\Exception\BaseException`.
 */
function is_etsis_exception($object)
{
    return ($object instanceof \app\src\Core\Exception\BaseException);
}

/**
 * Returns the datetime of when the content of file was changed.
 *
 * @since 6.2.0
 * @param string $file
 *            Absolute path to file.
 */
function file_mod_time($file)
{
    filemtime($file);
}

/**
 * Returns an array of function names in a file.
 *
 * @since 6.2.0
 * @param string $filename
 *            The path to the file.
 * @param bool $sort
 *            If TRUE, sort results by function name.
 */
function get_functions_in_file($filename, $sort = FALSE)
{
    $file = file($filename);
    $functions = [];
    foreach ($file as $line) {
        $line = trim($line);
        if (substr($line, 0, 8) == 'function') {
            $functions[] = strtolower(substr($line, 9, strpos($line, '(') - 9));
        }
    }
    if ($sort) {
        asort($functions);
        $functions = array_values($functions);
    }
    return $functions;
}

/**
 * Checks a given file for any duplicated named user functions.
 *
 * @since 6.2.0
 * @param string $filename            
 */
function is_duplicate_function($filename)
{
    if ('' == _trim($filename)) {
        $message = _t('Invalid file name: empty file name given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    $plugin = get_functions_in_file($filename);
    $functions = get_defined_functions();
    $merge = array_merge($plugin, $functions['user']);
    if (count($merge) !== count(array_unique($merge))) {
        $dupe = array_unique(array_diff_assoc($merge, array_unique($merge)));
        foreach ($dupe as $value) {
            return new \app\src\Core\etsis_Error('duplicate_function_error', sprintf(_t('The following function is already defined elsewhere: <strong>%s</strong>'), $value));
        }
    }
    return false;
}

/**
 * Performs a check within a php script and returns any other files
 * that might have been required or included.
 *
 * @since 6.2.0
 * @param string $filename
 *            PHP script to check.
 */
function etsis_php_check_includes($filename)
{
    if ('' == _trim($filename)) {
        $message = _t('Invalid file name: empty file name given.');
        _incorrectly_called(__FUNCTION__, $message, '6.2.0');
        return;
    }

    // NOTE that any file coming into this function has already passed the syntax check, so
    // we can assume things like proper line terminations
    $includes = [];
    // Get the directory name of the file so we can prepend it to relative paths
    $dir = dirname($filename);

    // Split the contents of $fileName about requires and includes
    // We need to slice off the first element since that is the text up to the first include/require
    $requireSplit = array_slice(preg_split('/require|include/i', _file_get_contents($filename)), 1);

    // For each match
    foreach ($requireSplit as $string) {
        // Substring up to the end of the first line, i.e. the line that the require is on
        $string = substr($string, 0, strpos($string, ";"));

        // If the line contains a reference to a variable, then we cannot analyse it
        // so skip this iteration
        if (strpos($string, "$") !== false) {
            continue;
        }

        // Split the string about single and double quotes
        $quoteSplit = preg_split('/[\'"]/', $string);

        // The value of the include is the second element of the array
        // Putting this in an if statement enforces the presence of '' or "" somewhere in the include
        // includes with any kind of run-time variable in have been excluded earlier
        // this just leaves includes with constants in, which we can't do much about
        if ($include = $quoteSplit[1]) {
            // If the path is not absolute, add the dir and separator
            // Then call realpath to chop out extra separators
            if (strpos($include, ':') === FALSE)
                $include = realpath($dir . DS . $include);

            array_push($includes, $include);
        }
    }

    return $includes;
}

/**
 * Performs a syntax and error check of a given PHP script.
 *
 * @since 6.2.0
 * @param string $filename
 *            PHP script/file to check.
 * @param bool $check_includes
 *            If set to TRUE, will check if other files have been included.
 * @return void|\app\src\Core\Exception\Exception
 * @throws NotFoundException If file does not exist or is not readable.
 * @throws Exception If file contains duplicate function names.
 */
function etsis_php_check_syntax($filename, $check_includes = true)
{
    // If file does not exist or it is not readable, throw an exception
    if (!is_file($filename) || !is_readable($filename)) {
        throw new NotFoundException(sprintf(_t('"%s" is not found or is not a regular file.'), $filename));
    }

    $dupe_function = is_duplicate_function($filename);

    if (is_etsis_error($dupe_function)) {
        return new \app\src\Core\Exception\Exception($dupe_function->get_error_message(), 'php_check_syntax');
    }

    // Sort out the formatting of the filename
    $file_name = realpath($filename);

    // Get the shell output from the syntax check command
    $output = shell_exec('php -l "' . $file_name . '"');

    // Try to find the parse error text and chop it off
    $syntaxError = preg_replace("/Errors parsing.*$/", "", $output, - 1, $count);

    // If the error text above was matched, throw an exception containing the syntax error
    if ($count > 0) {
        return new \app\src\Core\Exception\Exception(trim($syntaxError), 'php_check_syntax');
    }

    // If we are going to check the files includes
    if ($check_includes) {
        foreach (etsis_php_check_includes($file_name) as $include) {
            // Check the syntax for each include
            if (is_file($include)) {
                etsis_php_check_syntax($include);
            }
        }
    }
}

/**
 * Validates a plugin and checks to make sure there are no syntax and/or
 * parsing errors.
 *
 * @since 6.2.0
 * @param string $plugin_name
 *            Name of the plugin file (i.e. moodle.plugin.php).
 */
function etsis_validate_plugin($plugin_name)
{
    $app = \Liten\Liten::getInstance();

    $plugin = str_replace('.plugin.php', '', $plugin_name);

    if (!etsis_file_exists(ETSIS_PLUGIN_DIR . $plugin . DS . $plugin_name, false)) {
        $file = ETSIS_PLUGIN_DIR . $plugin_name;
    } else {
        $file = ETSIS_PLUGIN_DIR . $plugin . DS . $plugin_name;
    }

    $error = etsis_php_check_syntax($file);
    if (is_etsis_exception($error)) {
        _etsis_flash()->error(_t('Plugin could not be activated because it triggered a <strong>fatal error</strong>. <br /><br />') . $error->getMessage());
        return false;
    }

    try {
        if (etsis_file_exists($file)) {
            include_once ($file);
        }
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error(sprintf('FILESTATE[%s]: File not found: %s', $e->getCode(), $e->getMessage()));
    }

    /**
     * Fires before a specific plugin is activated.
     *
     * $pluginName refers to the plugin's
     * name (i.e. moodle.plugin.php).
     *
     * @since 6.1.00
     * @param string $plugin_name
     *            The plugin's base name.
     */
    $app->hook->do_action('activate_plugin', $plugin_name);

    /**
     * Fires as a specifig plugin is being activated.
     *
     * $pluginName refers to the plugin's
     * name (i.e. moodle.plugin.php).
     *
     * @since 6.1.00
     * @param string $plugin_name
     *            The plugin's base name.
     */
    $app->hook->do_action('activate_' . $plugin_name);

    /**
     * Activate the plugin if there are no errors.
     *
     * @since 5.0.0
     * @param string $plugin_name
     *            The plugin's base name.
     */
    activate_plugin($plugin_name);

    /**
     * Fires after a plugin has been activated.
     *
     * $pluginName refers to the plugin's
     * name (i.e. moodle.plugin.php).
     *
     * @since 6.1.06
     * @param string $plugin_name
     *            The plugin's base name.
     */
    $app->hook->do_action('activated_plugin', $plugin_name);
}

/**
 * Single file writable atribute check.
 * Thanks to legolas558.users.sf.net
 *
 * @since 6.2.0
 * @param string $path            
 * @return true
 */
function win_is_writable($path)
{
    // will work in despite of Windows ACLs bug
    // NOTE: use a trailing slash for folders!!!
    // see http://bugs.php.net/bug.php?id=27609
    // see http://bugs.php.net/bug.php?id=30931
    if ($path{strlen($path) - 1} == '/') { // recursively return a temporary file path
        return win_is_writable($path . uniqid(mt_rand()) . '.tmp');
    } elseif (is_dir($path)) {
        return win_is_writable($path . DS . uniqid(mt_rand()) . '.tmp');
    }
    // check tmp file for read/write capabilities
    $rm = etsis_file_exists($path, false);
    $f = fopen($path, 'a');
    if ($f === false) {
        return false;
    }
    fclose($f);
    if (!$rm) {
        unlink($path);
    }
    return true;
}

/**
 * Alternative to PHP's native is_writable function due to a Window's bug.
 *
 * @since 6.2.0
 * @param string $path
 *            Path to check.
 */
function etsis_is_writable($path)
{
    if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
        return win_is_writable($path);
    } else {
        return is_writable($path);
    }
}

/**
 * Takes an array and turns it into an object.
 *
 * @param array $array
 *            Array of data.
 */
function array_to_object(array $array)
{
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = array_to_object($value);
        }
    }
    return (object) $array;
}

/**
 * Strip close comment and close php tags from file headers.
 *
 * @since 6.2.3
 * @param string $str
 *            Header comment to clean up.
 * @return string
 */
function _etsis_cleanup_file_header_comment($str)
{
    return trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $str));
}

/**
 * Retrieve metadata from a file.
 *
 * Searches for metadata in the first 8kB of a file, such as a plugin or layout.
 * Each piece of metadata must be on its own line. Fields can not span multiple
 * lines, the value will get cut at the end of the first line.
 *
 * If the file data is not within that first 8kB, then the author should correct
 * their plugin file and move the data headers to the top.
 *
 * @since 6.2.3
 * @param string $file
 *            Path to the file.
 * @param array $default_headers
 *            List of headers, in the format array('HeaderKey' => 'Header Name').
 * @param string $context
 *            Optional. If specified adds filter hook "extra_{$context}_headers".
 *            Default empty.
 * @return array Array of file headers in `HeaderKey => Header Value` format.
 */
function etsis_get_file_data($file, $default_headers, $context = '')
{
    $app = \Liten\Liten::getInstance();
    // We don't need to write to the file, so just open for reading.
    $fp = fopen($file, 'r');
    // Pull only the first 8kB of the file in.
    $file_data = fread($fp, 8192);
    // PHP will close file handle.
    fclose($fp);
    // Make sure we catch CR-only line endings.
    $file_data = str_replace("\r", "\n", $file_data);
    /**
     * Filter extra file headers by context.
     *
     * The dynamic portion of the hook name, `$context`, refers to
     * the context where extra headers might be loaded.
     *
     * @since 6.2.3
     *       
     * @param array $extra_context_headers
     *            Empty array by default.
     */
    if ($context && $extra_headers = $app->hook->apply_filter("extra_{$context}_headers", [])) {
        $extra_headers = array_combine($extra_headers, $extra_headers); // keys equal values
        $all_headers = array_merge($extra_headers, (array) $default_headers);
    } else {
        $all_headers = $default_headers;
    }
    foreach ($all_headers as $field => $regex) {
        if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $file_data, $match) && $match[1])
            $all_headers[$field] = _etsis_cleanup_file_header_comment($match[1]);
        else
            $all_headers[$field] = '';
    }
    return $all_headers;
}

/**
 * Parses the plugin contents to retrieve plugin's metadata.
 *
 * The metadata of the plugin's data searches for the following in the plugin's
 * header. All plugin data must be on its own line. For plugin description, it
 * must not have any newlines or only parts of the description will be displayed
 * and the same goes for the plugin data. The below is formatted for printing.
 *
 * /*
 * Plugin Name: Name of Plugin
 * Plugin URI: Link to plugin information
 * Description: Plugin Description
 * Author: Plugin author's name
 * Author URI: Link to the author's web site
 * Version: Plugin version value.
 * Text Domain: Optional. Unique identifier, should be same as the one used in
 * load_plugin_textdomain()
 *
 * The first 8kB of the file will be pulled in and if the plugin data is not
 * within that first 8kB, then the plugin author should correct their plugin
 * and move the plugin data headers to the top.
 *
 * The plugin file is assumed to have permissions to allow for scripts to read
 * the file. This is not checked however and the file is only opened for
 * reading.
 *
 * @since 6.2.3
 *       
 * @param string $plugin_file
 *            Path to the plugin file
 * @param bool $markup
 *            Optional. If the returned data should have HTML markup applied.
 *            Default true.
 * @param bool $translate
 *            Optional. If the returned data should be translated. Default true.
 * @return array {
 *         Plugin data. Values will be empty if not supplied by the plugin.
 *        
 *         @type string $Name Name of the plugin. Should be unique.
 *         @type string $Title Title of the plugin and link to the plugin's site (if set).
 *         @type string $Description Plugin description.
 *         @type string $Author Author's name.
 *         @type string $AuthorURI Author's website address (if set).
 *         @type string $Version Plugin version.
 *         @type string $TextDomain Plugin textdomain.
 *         @type string $DomainPath Plugins relative directory path to .mo files.
 *         @type bool $Network Whether the plugin can only be activated network-wide.
 *         }
 */
function get_plugin_data($plugin_file, $markup = true, $translate = true)
{
    $default_headers = array(
        'Name' => 'Plugin Name',
        'PluginURI' => 'Plugin URI',
        'Version' => 'Version',
        'Description' => 'Description',
        'Author' => 'Author',
        'AuthorURI' => 'Author URI',
        'TextDomain' => 'Text Domain'
    );
    $plugin_data = etsis_get_file_data($plugin_file, $default_headers, 'plugin');
    if ($markup || $translate) {
        $plugin_data = _get_plugin_data_markup_translate($plugin_file, $plugin_data, $markup, $translate);
    } else {
        $plugin_data['Title'] = $plugin_data['Name'];
        $plugin_data['AuthorName'] = $plugin_data['Author'];
    }
    return $plugin_data;
}

/**
 * Hide fields function.
 * 
 * Hides or unhides fields based on html element.
 * 
 * @param string $element .
 * @return string
 */
function etsis_field_css_class($element)
{
    $app = \Liten\Liten::getInstance();

    if (_h(get_option($element)) == 'hide') {
        return $app->hook->apply_filter('field_css_class', " $element");
    }
}

/**
 * A wrapper for htmLawed which is a set of functions
 * for html purifier
 *
 * @since 5.0
 * @param string $str            
 * @return mixed
 */
function _escape($t, $C = 1, $S = [])
{
    return htmLawed($t, $C, $S);
}

/**
 * Converts seconds to time format.
 * 
 * @since 6.2.11
 * @param numeric $seconds
 */
function etsis_seconds_to_time($seconds)
{
    $ret = "";

    /** get the days */
    $days = intval(intval($seconds) / (3600 * 24));
    if ($days > 0) {
        $ret .= "$days days ";
    }

    /** get the hours */
    $hours = (intval($seconds) / 3600) % 24;
    if ($hours > 0) {
        $ret .= "$hours hours ";
    }

    /** get the minutes */
    $minutes = (intval($seconds) / 60) % 60;
    if ($minutes > 0) {
        $ret .= "$minutes minutes ";
    }

    /** get the seconds */
    $seconds = intval($seconds) % 60;
    if ($seconds > 0) {
        $ret .= "$seconds seconds";
    }

    return $ret;
}

/**
 * Checks whether a file or directory exists.
 * 
 * @since 6.3.0
 * @param string $filename Path to the file or directory.
 * @param bool $throw Determines whether to do a simple check or throw an exception.
 * @return boolean <b>TRUE</b> if the file or directory specified by
 * <i>$filename</i> exists; <b>FALSE</b> otherwise.
 * @throws NotFoundException If file does not exist.
 */
function etsis_file_exists($filename, $throw = true)
{
    if (!file_exists($filename)) {
        if ($throw == true) {
            throw new NotFoundException(sprintf(_t('"%s" does not exist.'), $filename));
        }
        return false;
    }
    return true;
}

function get_screen($screen)
{
    $acronym = [
        'SYSS' => 'setting',
        'MPRM' => 'permission',
        'APRM' => 'permission/add',
        'MRLE' => 'role',
        'AUDT' => 'audit-trail',
        'SQL' => 'sql',
        'SCH' => 'form/school',
        'SEM' => 'form/semester',
        'TERM' => 'form/term',
        'AYR' => 'form/acad-year',
        'CRSE' => 'crse',
        'DEPT' => 'form/department',
        'DEG' => 'form/degree',
        'MAJR' => 'form/major',
        'MINR' => 'form/minor',
        'PROG' => 'program',
        'CCD' => 'form/ccd',
        'CIP' => 'form/cip',
        'LOC' => 'form/location',
        'BLDG' => 'form/building',
        'ROOM' => 'form/room',
        'SPEC' => 'form/specialization',
        'SUBJ' => 'form/subject',
        'APRG' => 'program/add',
        'ACRS' => 'crse/add',
        'SECT' => 'sect',
        'RGN' => 'sect/rgn',
        'NAE' => 'nae',
        'APER' => 'nae/add',
        'SPRO' => 'stu',
        'INST' => 'app/inst',
        'AINST' => 'app/inst/add',
        'APPL' => 'appl',
        'BRGN' => 'sect/brgn',
        'STAF' => 'staff',
        'TRAN' => 'stu/tran',
        'RSTR' => 'form/rest',
        'GRSC' => 'form/grade-scale',
        'SROS' => 'sect/sros',
        'EXTR' => 'crse/extr',
        'ATCEQ' => 'crse/atceq',
        'TCEQ' => 'crse/tceq',
        'TCRE' => 'crse/tcre',
        'RLDE' => 'rlde',
        'ACLV' => 'form/aclv',
        'MRKT' => 'mrkt',
        'REST' => 'form/rest'
    ];
    return $acronym[$screen];
}

/**
 * Add the template to the message body.
 *
 * Looks for {content} into the template and replaces it with the message.
 *
 * @since 6.3.0
 * @param string $body The message to templatize.
 * @return string $email The email surrounded by template.
 */
function set_email_template($body)
{
    $app = \Liten\Liten::getInstance();

    $tpl = _file_get_contents(APP_PATH . 'views/setting/tpl/email_alert.tpl');

    $template = $app->hook->apply_filter('email_template', $tpl);

    return str_replace('{content}', $body, $template);
}

/**
 * Replace variables in the template.
 *
 * @since 6.3.0
 * @param string $template Template with variables.
 * @return string Template with variables replaced.
 */
function template_vars_replacement($template)
{
    $app = \Liten\Liten::getInstance();

    $var_array = [
        'institution_name' => _h(get_option('institution_name')),
        'address' => _h(get_option('mailing_address'))
    ];

    $to_replace = $app->hook->apply_filter('email_template_tags', $var_array);

    foreach ($to_replace as $tag => $var) {
        $template = str_replace('{' . $tag . '}', $var, $template);
    }

    return $template;
}

/**
 * Process the HTML version of the text.
 *
 * @since 6.3.0
 * @param string $text
 * @param string $title
 * @return string
 */
function process_email_html($text, $title)
{
    $app = \Liten\Liten::getInstance();

    // Convert URLs to links
    $links = make_clickable($text);

    // Add template to message
    $template = set_email_template($links);

    // Replace title tag with $title.
    $body = str_replace('{title}', $title, $template);

    // Replace variables in email
    $message = $app->hook->apply_filter('email_template_body', template_vars_replacement($body));

    return $message;
}

/**
 * Retrieve the domain name.
 * 
 * @since 6.3.0
 * @return string
 */
function get_domain_name()
{
    $app = \Liten\Liten::getInstance();

    $server_name = strtolower($app->req->server['SERVER_NAME']);
    if (substr($server_name, 0, 4) == 'www.') {
        $server_name = substr($server_name, 4);
    }
    return $server_name;
}

/**
 * SQL Like operator in PHP.
 * 
 * Returns true if match else false.
 * 
 * @since 6.3.0
 * @param string $pattern
 * @param string $subject
 * @return bool
 */
function php_like($pattern, $subject)
{
    $match = str_replace('%', '.*', preg_quote($pattern, '/'));
    return (bool) preg_match("/^{$match}$/i", $subject);
}

/**
 * Url shortening function.
 * 
 * @since 6.3.0
 * @param string $url URL
 * @param int $length Characters to check against.
 * @return string
 */
function etsis_url_shorten($url, $length = 80)
{
    if (strlen($url) > $length) {
        $strlen = $length - 30;
        $first = substr($url, 0, $strlen);
        $last = substr($url, -15);
        $short_url = $first . "[ ... ]" . $last;
        return $short_url;
    } else {
        return $url;
    }
}

/**
 * Adds label to the cron's status.
 * 
 * @since 6.3.0
 * @param string $status
 * @return string
 */
function etsis_cron_status_label($status)
{
    $label = [
        1 => 'label-success',
        0 => 'label-danger'
    ];

    return $label[$status];
}

/**
 * Redirects to another page.
 * 
 * @since 6.3.0
 * @param string $location The path to redirect to
 * @param int $status Status code to use
 * @return bool False if $location is not set
 */
function etsis_redirect($location, $status = 302)
{
    $app = \Liten\Liten::getInstance();
    /**
     * Filters the redirect location.
     *
     * @since 6.3.0
     *
     * @param string $location The path to redirect to.
     * @param int    $status   Status code to use.
     */
    $_location = $app->hook->apply_filter('etsis_redirect', $location, $status);
    /**
     * Filters the redirect status code.
     *
     * @since 6.3.0
     *
     * @param int    $status   Status code to use.
     * @param string $_location The path to redirect to.
     */
    $_status = $app->hook->apply_filter('etsis_redirect_status', $status, $_location);

    if (!$_location)
        return false;

    header("Location: $_location", true, $_status);
    return true;
}

/**
 * Retrieves a modified URL query string.
 * 
 * @since 6.3.0
 * @param string $key A query variable key.
 * @param string $value A query variable value, or a URL to act upon.
 * @param string $url A URL to act upon.
 * @return string
 */
function add_query_arg($key, $value, $url)
{
    $app = \Liten\Liten::getInstance();
    $uri = parse_url($url);
    $query = isset($uri['query']) ? $uri['query'] : '';
    parse_str($query, $params);
    $params[$key] = $value;
    $query = http_build_query($params);
    $result = '';
    if ($uri['scheme']) {
        $result .= $uri['scheme'] . ':';
    }
    if ($uri['host']) {
        $result .= '//' . $uri['host'];
    }
    if ($uri['port']) {
        $result .= $app->hook->apply_filter('query_arg_port', ':' . $uri['port']);
    }
    if ($uri['path']) {
        $result .= $uri['path'];
    }
    if ($query) {
        $result .= '?' . $query;
    }
    return $result;
}

/**
 * Retrieves the login url.
 * 
 * @since 6.3.0
 * @param string $redirect Path to redirect to on log in.
 * @return string
 */
function etsis_login_url($redirect = '')
{
    $app = \Liten\Liten::getInstance();
    $login_url = get_base_url() . 'login' . '/';

    if (!empty($redirect)) {
        $login_url = add_query_arg('redirect_to', $redirect, $login_url);
    }

    /**
     * Validates & protects redirect url against XSS attacks.
     * 
     * @since 6.3.0
     */
    if (!empty($redirect) && !v::filterVar(FILTER_VALIDATE_URL)->validate($redirect)) {
        $login_url = get_base_url() . 'login' . '/';
    }
    /**
     * Filters the login URL.
     *
     * @since 6.3.0
     *
     * @param string $login_url    The login URL. Not HTML-encoded.
     * @param string $redirect     The path to redirect to on login, if supplied.
     */
    return $app->hook->apply_filter('login_url', $login_url, $redirect);
}

/**
 * Create a backup of etSIS install.
 * 
 * @since 6.3.0
 * @param type $source Path/directory to zip.
 * @param type $destination Target for zipped file.
 * @return mixed
 */
function etsis_system_backup($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new \ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true) {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file) {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
                continue;

            $file = realpath($file);

            if (is_dir($file) === true) {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            } else if (is_file($file) === true) {
                $zip->addFromString(str_replace($source . '/', '', $file), _file_get_contents($file));
            }
        }
    } else if (is_file($source) === true) {
        $zip->addFromString(basename($source), _file_get_contents($source));
    }

    return $zip->close();
}

/**
 * Used to retrieve values within a range.
 * 
 * @since 6.3.0
 * @param mixed $val
 * @param mixed $min
 * @param mixed $max
 * @return boolean
 */
function etsis_between($val, $min, $max)
{
    return ($val - $min) * ($val - $max) <= 0;
}

/**
 * Checks if a variable is null. If not null, check if integer or string.
 * 
 * @since 6.3.4
 * @param string|int $var   Variable to check.
 * @return string|int|null Returns null if empty or a string or an integer.
 */
function if_null($var)
{
    $_var = ctype_digit($var) ? (int) $var : (string) $var;
    return $var === '' ? NULL : $_var;
}

/**
 * If variable is not null, return it.
 * 
 * @since 6.3.4
 * @param string|int $var   Variable to check.
 * @return string|int|null Returns false if NULL or a string or an integer.
 */
function if_not_null($var)
{
    return $var !== NULL ? _escape($var) : false;
}
