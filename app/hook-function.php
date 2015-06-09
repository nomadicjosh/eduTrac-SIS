<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * eduTrac Hooks Helper
 *
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();

/**
 * Includes and loads all activated plugins.
 *
 * @since 1.0.0
 */
$app->hook->{'load_activated_plugins'}(APP_PATH . 'plugins/');

/**
 * Includes and loads all available modules.
 *
 * @since 5.0
 */
$app->module->{'load_installed_modules'}(APP_PATH . 'modules/');

/**
 * An action called to add the plugin's link
 * to the menu structure.
 *
 * @since 1.0.0
 * @uses do_action() Calls 'admin_menu' hook.
 */
$app->hook->{'do_action'}('admin_menu');

/**
 * An action called to add custom page links
 * to menu structure.
 *
 * @since 4.2.0
 * @uses do_action() Calls 'custom_plugin_page' hook.
 */
$app->hook->{'do_action'}('custom_plugin_page');

/**
 * An action called to create db tables needed
 * for a plugin
 *
 * @since 4.2.0
 * @uses do_action() Calls 'create_db_table' hook.
 */
$app->hook->{'do_action'}('create_db_table');

/**
 * An action called to add component/companion pages
 * for a plugin. These are typically pages that users
 * will access to interact with a particular plugin.
 *
 * @since 4.2.1
 * @uses do_action() Calls 'component_menu' hook.
 */
$app->hook->{'do_action'}('component_menu');

$parsecode_tags = array();

function clean_pre($matches)
{
    if (is_array($matches))
        $text = $matches[1] . $matches[2] . "</pre>";
    else
        $text = $matches;

    $text = str_replace('<br />', '', $text);
    $text = str_replace('<p>', "\n", $text);
    $text = str_replace('</p>', '', $text);

    return $text;
}
if (!function_exists('add_parsecode')) {

    function add_parsecode($tag, $func)
    {
        global $parsecode_tags;

        if (is_callable($func))
            $parsecode_tags[$tag] = $func;
    }
}

/**
 * Removes hook for parsecode.
 *
 * @since 1.0
 * @uses $parsecode_tags
 *
 * @param string $tag parsecode tag to remove hook for.
 */
function remove_parsecode($tag)
{
    global $parsecode_tags;

    unset($parsecode_tags[$tag]);
}

/**
 * Clear all parsecodes.
 *
 * This function is simple, it clears all of the parsecode tags by replacing the
 * parsecodes global by a empty array. This is actually a very efficient method
 * for removing all parsecodes.
 *
 * @since 1.0
 * @uses $parsecode_tags
 */
function remove_all_parsecodes()
{
    global $parsecode_tags;

    $parsecode_tags = array();
}

/**
 * Search content for parsecodes and filter parsecodes through their hooks.
 *
 * If there are no parsecode tags defined, then the content will be returned
 * without any filtering. This might cause issues when plugins are disabled but
 * the parsecode will still show up in the post or content.
 *
 * @since 1.0
 * @uses $parsecode_tags
 * @uses get_parsecode_regex() Gets the search pattern for searching parsecodes.
 *
 * @param string $content Content to search for parsecodes
 * @return string Content with parsecodes filtered out.
 */
function do_parsecode($content)
{
    global $parsecode_tags;

    if (empty($parsecode_tags) || !is_array($parsecode_tags))
        return $content;

    $pattern = get_parsecode_regex();
    return preg_replace_callback("/$pattern/s", 'do_parsecode_tag', $content);
}

/**
 * Retrieve the parsecode regular expression for searching.
 *
 * The regular expression combines the parsecode tags in the regular expression
 * in a regex class.
 *
 * The regular expression contains 6 different sub matches to help with parsing.
 *
 * 1 - An extra [ to allow for escaping parsecodes with double [[]]
 * 2 - The parsecode name
 * 3 - The parsecode argument list
 * 4 - The self closing /
 * 5 - The content of a parsecode when it wraps some content.
 * 6 - An extra ] to allow for escaping parsecodes with double [[]]
 *
 * @since 1.0
 * @uses $parsecode_tags
 *
 * @return string The parsecode search regular expression
 */
function get_parsecode_regex()
{
    global $parsecode_tags;
    $tagnames = array_keys($parsecode_tags);
    $tagregexp = join('|', array_map('preg_quote', $tagnames));

    // WARNING! Do not change this regex without changing do_parsecode_tag() and strip_parsecode_tag()
    return
        '\\['                              // Opening bracket
        . '(\\[?)'                           // 1: Optional second opening bracket for escaping parsecodes: [[tag]]
        . "($tagregexp)"                     // 2: parsecode name
        . '\\b'                              // Word boundary
        . '('                                // 3: Unroll the loop: Inside the opening parsecode tag
        . '[^\\]\\/]*'                   // Not a closing bracket or forward slash
        . '(?:'
        . '\\/(?!\\])'               // A forward slash not followed by a closing bracket
        . '[^\\]\\/]*'               // Not a closing bracket or forward slash
        . ')*?'
        . ')'
        . '(?:'
        . '(\\/)'                        // 4: Self closing tag ...
        . '\\]'                          // ... and closing bracket
        . '|'
        . '\\]'                          // Closing bracket
        . '(?:'
        . '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing parsecode tags
        . '[^\\[]*+'             // Not an opening bracket
        . '(?:'
        . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing parsecode tag
        . '[^\\[]*+'         // Not an opening bracket
        . ')*+'
        . ')'
        . '\\[\\/\\2\\]'             // Closing parsecode tag
        . ')?'
        . ')'
        . '(\\]?)';                          // 6: Optional second closing brocket for escaping parsecodes: [[tag]]
}

/**
 * Regular Expression callable for do_parsecode() for calling parsecode hook.
 * @see get_parsecode_regex for details of the match array contents.
 *
 * @since 1.0
 * @access private
 * @uses $parsecode_tags
 *
 * @param array $m Regular expression match array
 * @return mixed False on failure.
 */
function do_parsecode_tag($m)
{
    global $parsecode_tags;

    // allow [[foo]] syntax for escaping a tag
    if ($m[1] == '[' && $m[6] == ']') {
        return substr($m[0], 1, -1);
    }

    $tag = $m[2];
    $attr = parsecode_parse_atts($m[3]);

    if (isset($m[5])) {
        // enclosing tag - extra parameter
        return $m[1] . call_user_func($parsecode_tags[$tag], $attr, $m[5], $tag) . $m[6];
    } else {
        // self-closing tag
        return $m[1] . call_user_func($parsecode_tags[$tag], $attr, NULL, $tag) . $m[6];
    }
}

/**
 * Retrieve all attributes from the parsecodes tag.
 *
 * The attributes list has the attribute name as the key and the value of the
 * attribute as the value in the key/value pair. This allows for easier
 * retrieval of the attributes, since all attributes have to be known.
 *
 * @since 1.0
 *
 * @param string $text
 * @return array List of attributes and their value.
 */
function parsecode_parse_atts($text)
{
    $atts = array();
    $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
    $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
    if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
        foreach ($match as $m) {
            if (!empty($m[1]))
                $atts[strtolower($m[1])] = stripcslashes($m[2]);
            elseif (!empty($m[3]))
                $atts[strtolower($m[3])] = stripcslashes($m[4]);
            elseif (!empty($m[5]))
                $atts[strtolower($m[5])] = stripcslashes($m[6]);
            elseif (isset($m[7]) and strlen($m[7]))
                $atts[] = stripcslashes($m[7]);
            elseif (isset($m[8]))
                $atts[] = stripcslashes($m[8]);
        }
    } else {
        $atts = ltrim($text);
    }
    return $atts;
}

/**
 * Combine user attributes with known attributes and fill in defaults when needed.
 *
 * The pairs should be considered to be all of the attributes which are
 * supported by the caller and given as a list. The returned attributes will
 * only contain the attributes in the $pairs list.
 *
 * If the $atts list has unsupported attributes, then they will be ignored and
 * removed from the final returned list.
 *
 * @since 1.0
 *
 * @param array $pairs Entire list of supported attributes and their defaults.
 * @param array $atts User defined attributes in parsecode tag.
 * @return array Combined and filtered attribute list.
 */
function parsecode_atts($pairs, $atts)
{
    $atts = (array) $atts;
    $out = array();
    foreach ($pairs as $name => $default) {
        if (array_key_exists($name, $atts))
            $out[$name] = $atts[$name];
        else
            $out[$name] = $default;
    }
    return $out;
}

/**
 * Remove all parsecode tags from the given content.
 *
 * @since 1.0
 * @uses $parsecode_tags
 *
 * @param string $content Content to remove parsecode tags.
 * @return string Content without parsecode tags.
 */
function strip_parsecodes($content)
{
    global $parsecode_tags;

    if (empty($parsecode_tags) || !is_array($parsecode_tags))
        return $content;

    $pattern = get_parsecode_regex();

    return preg_replace_callback("/$pattern/s", 'strip_parsecode_tag', $content);
}

function strip_parsecode_tag($m)
{
    // allow [[foo]] syntax for escaping a tag
    if ($m[1] == '[' && $m[6] == ']') {
        return substr($m[0], 1, -1);
    }

    return $m[1] . $m[6];
}
$app->hook->{'add_filter'}('the_custom_page_content', 'do_parsecode', 11); // AFTER tt_autop()

function et_autop($pee, $br = 1)
{

    if (trim($pee) === '')
        return '';
    $pee = $pee . "\n"; // just to make things a little easier, pad the end
    $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
    // Space things out a little
    $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|option|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
    $pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
    $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
    $pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
    if (strpos($pee, '<object') !== false) {
        $pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee); // no pee inside object/embed
        $pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
    }
    $pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
    // make paragraphs, including one at the end
    $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
    $pee = '';
    foreach ($pees as $tinkle)
        $pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
    $pee = preg_replace('|<p>\s*</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
    $pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);
    $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
    $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
    $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
    $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
    $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
    $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
    if ($br) {
        $pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', '_autop_newline_preservation_helper', $pee);
        $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
        $pee = str_replace('<TTPreserveNewline />', "\n", $pee);
    }
    $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
    $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
    if (strpos($pee, '<pre') !== false)
        $pee = preg_replace_callback('!(<pre[^>]*>)(.*?)</pre>!is', 'clean_pre', $pee);
    $pee = preg_replace("|\n</p>$|", '</p>', $pee);

    return $pee;
}

function _autop_newline_preservation_helper($matches)
{
    return str_replace("\n", "<TTPreserveNewline />", $matches[0]);
}

function parsecode_unautop($pee)
{
    global $parsecode_tags;

    if (empty($parsecode_tags) || !is_array($parsecode_tags)) {
        return $pee;
    }

    $tagregexp = join('|', array_map('preg_quote', array_keys($parsecode_tags)));

    $pattern = '/'
        . '<p>'                              // Opening paragraph
        . '\\s*+'                            // Optional leading whitespace
        . '('                                // 1: The parsecode
        . '\\['                          // Opening bracket
        . "($tagregexp)"                 // 2: parsecode name
        . '\\b'                          // Word boundary
        // Unroll the loop: Inside the opening parsecode tag
        . '[^\\]\\/]*'                   // Not a closing bracket or forward slash
        . '(?:'
        . '\\/(?!\\])'               // A forward slash not followed by a closing bracket
        . '[^\\]\\/]*'               // Not a closing bracket or forward slash
        . ')*?'
        . '(?:'
        . '\\/\\]'                   // Self closing tag and closing bracket
        . '|'
        . '\\]'                      // Closing bracket
        . '(?:'                      // Unroll the loop: Optionally, anything between the opening and closing parsecode tags
        . '[^\\[]*+'             // Not an opening bracket
        . '(?:'
        . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing parsecode tag
        . '[^\\[]*+'         // Not an opening bracket
        . ')*+'
        . '\\[\\/\\2\\]'         // Closing parsecode tag
        . ')?'
        . ')'
        . ')'
        . '\\s*+'                            // optional trailing whitespace
        . '<\\/p>'                           // closing paragraph
        . '/s';

    return preg_replace($pattern, '$1', $pee);
}

function init()
{
    $app = \Liten\Liten::getInstance();
    $app->hook->{'do_action'}('init');
}

function admin_head()
{
    $app = \Liten\Liten::getInstance();
    $app->hook->{'do_action'}('admin_head');
}

function myet_head()
{
    $app = \Liten\Liten::getInstance();
    $app->hook->{'do_action'}('myet_head');
}

function footer()
{
    $app = \Liten\Liten::getInstance();
    $app->hook->{'do_action'}('footer');
}

function release()
{
    $app = \Liten\Liten::getInstance();
    $app->hook->{'do_action'}('release');
}

function dashboard_top_widgets()
{
    $app = \Liten\Liten::getInstance();
    $app->hook->{'do_action'}('dashboard_top_widgets');
}

function dashboard_right_widgets()
{
    $app = \Liten\Liten::getInstance();
    $app->hook->{'do_action'}('dashboard_right_widgets');
}

/**
 * Shows number of active students.
 *
 * @since 4.0.0
 */
function dashboard_student_count()
{
    $app = \Liten\Liten::getInstance();
    $stu = $app->db->query("SELECT COUNT(stuID) as count FROM student WHERE status = 'A'");
    $q = $stu->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    $a = [];
    foreach ($q as $r) {
        $a[] = $r;
    }
    $stuCount = '<div class="col-md-4">';
    $stuCount .= '<a href="#" class="widget-stats widget-stats-1 widget-stats-inverse">';
    $stuCount .= '<span class="glyphicons group"><i></i><span class="txt">' . _t('Active Students') . '</span></span>';
    $stuCount .= '<div class="clearfix"></div>';
    $stuCount .= '<span class="count">' . $r['count'] . '</span>';
    $stuCount .= '</a>';
    $stuCount .= '</div>';
    echo $app->hook->{'apply_filter'}('dashboard_student_count', $stuCount);
}

/**
 * Shows number of active courses.
 *
 * @since 4.0.0
 */
function dashboard_course_count()
{
    $app = \Liten\Liten::getInstance();
    $count = $app->db->query('SELECT COUNT(courseID) as count FROM course WHERE currStatus = "A" AND endDate = "0000-00-00"');
    $q = $count->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    $a = [];
    foreach ($q as $r) {
        $a[] = $r;
    }
    $crseCount = '<div class="col-md-4">';
    $crseCount .= '<a href="#" class="widget-stats widget-stats-1 widget-stats-inverse">';
    $crseCount .= '<span class="glyphicons book"><i></i><span class="txt">' . _t('Active Courses') . '</span></span>';
    $crseCount .= '<div class="clearfix"></div>';
    $crseCount .= '<span class="count">' . $r['count'] . '</span>';
    $crseCount .= '</a>';
    $crseCount .= '</div>';
    echo $app->hook->{'apply_filter'}('dashboard_course_count', $crseCount);
}

/**
 * Shows number of active academic programs.
 *
 * @since 4.0.0
 */
function dashboard_acadProg_count()
{
    $app = \Liten\Liten::getInstance();
    $count = $app->db->query('SELECT COUNT(acadProgID) FROM acad_program WHERE currStatus = "A" AND endDate = "0000-00-00"');
    $q = $count->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    $a = [];
    foreach ($q as $r) {
        $a[] = $r;
    }
    $progCount = '<div class="col-md-4">';
    $progCount .= '<a href="#" class="widget-stats widget-stats-1 widget-stats-inverse">';
    $progCount .= '<span class="glyphicons keynote"><i></i><span class="txt">' . _t('Active Programs') . '</span></span>';
    $progCount .= '<div class="clearfix"></div>';
    $progCount .= '<span class="count">' . $r['COUNT(acadProgID)'] . '</span>';
    $progCount .= '</a>';
    $progCount .= '</div>';
    echo $app->hook->{'apply_filter'}('dashboard_acadProg_count', $progCount);
}

/**
 * Shows update message when a new release of 
 * eduTrac SIS is available.
 *
 * @since 4.0.0
 */
function show_update_message()
{
    $app = \Liten\Liten::getInstance();
    $acl = new \app\src\ACL(get_persondata('personID'));
    if ($acl->userHasRole(8)) {
        if (RELEASE_TAG < \app\src\ReleaseAPI::inst()->releaseTag()) {
            $alert = '<div class="alerts alerts-warn center">';
            $alert .= _file_get_contents(\app\src\ReleaseAPI::inst()->getNotice());
            $alert .= '</div>';
        }
    }
    return $app->hook->{'apply_filter'}('update_message', $alert);
}

/**
 * Retrieves eduTrac site root url.
 *
 * @since 4.1.9
 * @uses apply_filter() Calls 'base_url' filter.
 *
 * @return string eduTrac root url.
 */
function get_base_url()
{
    $app = \Liten\Liten::getInstance();
    $url = url('/');
    return $app->hook->{'apply_filter'}('base_url', $url);
}

/**
 * Retrieve javascript directory uri.
 *
 * @since 4.1.9
 * @uses apply_filter() Calls 'javascript_directory_uri' filter.
 *
 * @return string eduTrac javascript url.
 */
function get_javascript_directory_uri()
{
    $app = \Liten\Liten::getInstance();
    $directory = 'static/assets/components';
    $javascript_root_uri = get_base_url();
    $javascript_dir_uri = "$javascript_root_uri$directory/";
    return $app->hook->{'apply_filter'}('javascript_directory_uri', $javascript_dir_uri, $javascript_root_uri, $directory);
}

/**
 * Retrieve less directory uri.
 *
 * @since 4.1.9
 * @uses apply_filter() Calls 'less_directory_uri' filter.
 *
 * @return string eduTrac less url.
 */
function get_less_directory_uri()
{
    $app = \Liten\Liten::getInstance();
    $directory = 'static/assets/less';
    $less_root_uri = get_base_url();
    $less_dir_uri = "$less_root_uri$directory/";
    return $app->hook->{'apply_filter'}('less_directory_uri', $less_dir_uri, $less_root_uri, $directory);
}

/**
 * Retrieve css directory uri.
 *
 * @since 4.1.9
 * @uses apply_filter() Calls 'css_directory_uri' filter.
 *
 * @return string eduTrac css url.
 */
function get_css_directory_uri()
{
    $app = \Liten\Liten::getInstance();
    $directory = 'static/assets/css';
    $css_root_uri = get_base_url();
    $css_dir_uri = "$css_root_uri$directory/";
    return $app->hook->{'apply_filter'}('css_directory_uri', $css_dir_uri, $css_root_uri, $directory);
}

/**
 * Parses a string into variables to be stored in an array.
 *
 * Uses {@link http://www.php.net/parse_str parse_str()}
 *
 * @since 4.2.0
 * @param string $string The string to be parsed.
 * @param array $array Variables will be stored in this array.
 */
function et_parse_str($string, &$array)
{
    $app = \Liten\Liten::getInstance();
    parse_str($string, $array);
    /**
     * Filter the array of variables derived from a parsed string.
     *
     * @since 4.2.0
     * @param array $array The array populated with variables.
     */
    $array = $app->hook->{'apply_filter'}('et_parse_str', $array);
}

/**
 * Frontend portal site title.
 *
 * @since 4.3
 * @uses apply_filter() Calls 'met_title' filter.
 *
 * @return string eduTrac frontend site title.
 */
function get_met_title()
{
    $app = \Liten\Liten::getInstance();
    $title = '<em>' . _t('my') . '</em>' . ( 'eduTrac' );
    return $app->hook->{'apply_filter'}('met_title', $title);
}

/**
 * Frontend portal footer powered by and release.
 *
 * @since 4.3
 * @uses apply_filter() Calls 'met_footer_release' filter.
 *
 * @return mixed.
 */
function get_met_footer_release()
{
    $app = \Liten\Liten::getInstance();
    if (CURRENT_RELEASE != RELEASE_TAG) {
        $release = _t('Powered by eduTrac SIS r') . CURRENT_RELEASE . ' (t' . RELEASE_TAG . ')';
    } else {
        $release = _t('Powered by eduTrac SIS r') . CURRENT_RELEASE;
    }
    return $app->hook->{'apply_filter'}('met_footer_release', $release);
}

/**
 * Frontend portal footer title.
 *
 * @since 4.3
 * @uses apply_filter() Calls 'met_footer_title' filter.
 *
 * @return string
 */
function get_met_footer_title()
{
    $app = \Liten\Liten::getInstance();
    $title = '<em>' . _t('my') . '</em>' . ( 'eduTrac' );
    return $app->hook->{'apply_filter'}('met_footer_title', $title);
}

/**
 * Address type select: shows general list of address types and
 * if $typeCode is not NULL, shows the address type attached 
 * to a particular record.
 * 
 * @since 1.0.0
 * @param string $typeCode
 * @return string Returns the record key if selected is true.
 */
function address_type_select($typeCode = NULL)
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="addressType" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
            <option value="">&nbsp;</option>
            <option value="B"' . selected($typeCode, 'B', false) . '>Business</option>
            <option value="H"' . selected($typeCode, 'H', false) . '>Home/Mailing</option>
            <option value="P"' . selected($typeCode, 'P', false) . '>Permanent</option>
            </select>';
    return $app->hook->{'apply_filter'}('address_type', $select, $typeCode);
}

/**
 * Department Type select: shows general list of department types and
 * if $typeCode is not NULL, shows the department type attached 
 * to a particular record.
 * 
 * @since 1.0.0
 * @param string $typeCode
 * @return string Returns the record key if selected is true.
 */
function dept_type_select($typeCode = NULL)
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="deptTypeCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
            <option value="">&nbsp;</option>
            <option value="ADMIN"' . selected($typeCode, 'ADMIN', false) . '>' . _t('Administrative') . '</option>
            <option value="ACAD"' . selected($typeCode, 'ACAD', false) . '>' . _t('Academic') . '</option>
            </select>';
    return $app->hook->{'apply_filter'}('dept_type', $select, $typeCode);
}

/**
 * Acad Level select: shows general list of academic levels and
 * if $status is not NULL, shows the academic level attached 
 * to a particular record.
 * 
 * @since 1.0.0
 * @param string $status
 * @return string Returns the record status if selected is true.
 */
function address_status_select($status = NULL)
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="addressStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
			<option value="">&nbsp;</option>
	    	<option value="C"' . selected($status, 'C', false) . '>Current</option>
			<option value="I"' . selected($status, 'I', false) . '>Inactive</option>
		    </select>';
    return $app->hook->{'apply_filter'}('address_status', $select, $status);
}

/**
 * Acad Level select: shows general list of academic levels and
 * if $levelCode is not NULL, shows the academic level attached 
 * to a particular record.
 * 
 * @since 1.0.0
 * @param string $levelCode
 * @return string Returns the record key if selected is true.
 */
function acad_level_select($levelCode = null, $readonly = null, $required = '')
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="acadLevelCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"' . $readonly . $required . '>
            <option value="">&nbsp;</option>
            <option value="NA"' . selected($levelCode, 'NA', false) . '>N/A Not Applicable</option>
            <option value="CE"' . selected($levelCode, 'CE', false) . '>CE Continuing Education</option>
            <option value="UG"' . selected($levelCode, 'UG', false) . '>UG Undergraduate</option>
            <option value="GR"' . selected($levelCode, 'GR', false) . '>GR Graduate</option>
            <option value="PhD"' . selected($levelCode, 'PhD', false) . '>PhD Doctorate</option>
            </select>';
    return $app->hook->{'apply_filter'}('acad_level', $select, $levelCode);
}

/**
 * Fee acad Level select: shows general list of academic levels and
 * if $levelCode is not NULL, shows the academic level attached 
 * to a particular record.
 * 
 * @since 4.1.7
 * @param string $levelCode
 * @return string Returns the record key if selected is true.
 */
function fee_acad_level_select($levelCode = null)
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="acadLevelCode" class="form-control">
            <option value="">&nbsp;</option>
            <option value="NA"' . selected($levelCode, 'NA', false) . '>N/A Not Applicable</option>
            <option value="CE"' . selected($levelCode, 'CE', false) . '>CE Continuing Education</option>
            <option value="UG"' . selected($levelCode, 'UG', false) . '>UG Undergraduate</option>
            <option value="GR"' . selected($levelCode, 'GR', false) . '>GR Graduate</option>
            <option value="PhD"' . selected($levelCode, 'PhD', false) . '>PhD Doctorate</option>
            </select>';
    return $app->hook->{'apply_filter'}('fee_acad_level', $select, $levelCode);
}

/**
 * Status dropdown: shows general list of statuses and
 * if $status is not NULL, shows the current status 
 * for a particular record.
 * 
 * @since 1.0.0
 * @param string $status
 * @return string Returns the record key if selected is true.
 */
function status_select($status = NULL, $readonly = '')
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="currStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"'.$readonly.' required>
    			<option value="">&nbsp;</option>
    	    	<option value="A"' . selected($status, 'A', false) . '>A Active</option>
    	    	<option value="I"' . selected($status, 'I', false) . '>I Inactive</option>
    			<option value="P"' . selected($status, 'P', false) . '>P Pending</option>
    			<option value="O"' . selected($status, 'O', false) . '>O Obsolete</option>
		        </select>';
    return $app->hook->{'apply_filter'}('status', $select, $status);
}

/**
 * Course section select: shows general list of statuses and
 * if $status is not NULL, shows the current status 
 * for a particular course section record.
 * 
 * @since 1.0.0
 * @param string $status
 * @return string Returns the record key if selected is true.
 */
function course_sec_status_select($status = NULL, $readonly = '')
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="currStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required' . $readonly . '>
    			<option value="">&nbsp;</option>
    	    	<option' . dopt('activate_course_sec') . ' value="A"' . selected($status, 'A', false) . '>A Active</option>
    	    	<option value="I"' . selected($status, 'I', false) . '>I Inactive</option>
    			<option value="P"' . selected($status, 'P', false) . '>P Pending</option>
    			<option' . dopt('cancel_course_sec') . ' value="C"' . selected($status, 'C', false) . '>C Cancel</option>
    			<option value="O"' . selected($status, 'O', false) . '>O Obsolete</option>
		        </select>';
    return $app->hook->{'apply_filter'}('status', $select, $status);
}

/**
 * Person type select: shows general list of person types and
 * if $type is not NULL, shows the person type 
 * for a particular person record.
 * 
 * @since 1.0.0
 * @param string $type
 * @return string Returns the record type if selected is true.
 */
function person_type_select($type = NULL)
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="personType" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="FAC"' . selected($type, 'FAC', false) . '>FAC Faculty</option>
                <option value="ADJ"' . selected($type, 'ADJ', false) . '>ADJ Adjunct</option>
                <option value="STA"' . selected($type, 'STA', false) . '>STA Staff</option>
                <option value="APL"' . selected($type, 'APL', false) . '>APL Applicant</option>
                <option value="STU"' . selected($type, 'STU', false) . '>STU Student</option>
                </select>';
    return $app->hook->{'apply_filter'}('person_type', $select, $type);
}

/**
 * Course Level dropdown: shows general list of course levels and
 * if $levelCode is not NULL, shows the course level attached 
 * to a particular record.
 * 
 * @since 1.0.0
 * @param string $levelCode
 * @return string Returns the record key if selected is true.
 */
function course_level_select($levelCode = NULL, $readonly = null)
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="courseLevelCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required' . $readonly . '>
			<option value="">&nbsp;</option>
	    	<option value="100"' . selected($levelCode, '100', false) . '>100 Course Level</option>
			<option value="200"' . selected($levelCode, '200', false) . '>200 Course Level</option>
			<option value="300"' . selected($levelCode, '300', false) . '>300 Course Level</option>
			<option value="400"' . selected($levelCode, '400', false) . '>400 Course Level</option>
			<option value="500"' . selected($levelCode, '500', false) . '>500 Course Level</option>
			<option value="600"' . selected($levelCode, '600', false) . '>600 Course Level</option>
			<option value="700"' . selected($levelCode, '700', false) . '>700 Course Level</option>
			<option value="800"' . selected($levelCode, '800', false) . '>800 Course Level</option>
			<option value="900"' . selected($levelCode, '900', false) . '>900 Course Level</option>
		    </select>';
    return $app->hook->{'apply_filter'}('course_level', $select, $levelCode);
}

/**
 * Instructor method select: shows general list of instructor methods and
 * if $method is not NULL, shows the instructor method 
 * for a particular course section.
 * 
 * @since 1.0.0
 * @param string $method
 * @return string Returns the record method if selected is true.
 */
function instructor_method($method = NULL)
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="instructorMethod" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="LEC"' . selected($method, 'LEC', false) . '>' . _t('LEC Lecture') . '</option>
                <option value="LAB"' . selected($method, 'LAB', false) . '>' . _t('LAB Lab') . '</option>
                <option value="SEM"' . selected($method, 'SEM', false) . '>' . _t('SEM Seminar') . '</option>
                <option value="LL"' . selected($method, 'LL', false) . '>' . _t('LL Lecture + Lab') . '</option>
                <option value="LS"' . selected($method, 'LS', false) . '>' . _t('LS Lecture + Seminar') . '</option>
                <option value="SL"' . selected($method, 'SL', false) . '>' . _t('SL Seminar + Lab') . '</option>
                <option value="LLS"' . selected($method, 'LLS', false) . '>' . _t('LLS Lecture + Lab + Seminar') . '</option>
                </select>';
    return $app->hook->{'apply_filter'}('instructor_method', $select, $method);
}

/**
 * Student Course section status select: shows general list of course sec statuses and
 * if $status is not NULL, shows the status 
 * for a particular student course section record.
 * 
 * @since 1.0
 * @param string $status
 * @return string Returns the record status if selected is true.
 */
function stu_course_sec_status_select($status = NULL, $readonly = '')
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="status" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required' . $readonly . '>
                <option value="">&nbsp;</option>
                <option value="A"' . selected($status, 'A', false) . '>' . _t('A Add') . '</option>
                <option value="N"' . selected($status, 'N', false) . '>' . _t('N New') . '</option>
                <option value="D"' . selected($status, 'D', false) . '>' . _t('D Drop') . '</option>
                <option value="W"' . selected($status, 'W', false) . '>' . _t('W Withdrawn') . '</option>
                <option value="C"' . selected($status, 'C', false) . '>' . _t('C Cancelled') . '</option>
                </select>';
    return $app->hook->{'apply_filter'}('course_sec_status', $select, $status);
}

/**
 * Student program status select: shows general list of student 
 * statuses and if $status is not NULL, shows the status 
 * for a particular student program record.
 * 
 * @since 1.0.0
 * @param string $status
 * @return string Returns the record status if selected is true.
 */
function stu_prog_status_select($status = NULL)
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="currStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="A"' . selected($status, 'A', false) . '>' . _t('A Active') . '</option>
                <option value="P"' . selected($status, 'P', false) . '>' . _t('P Potential') . '</option>
                <option value="W"' . selected($status, 'W', false) . '>' . _t('W Withdrawn') . '</option>
                <option value="C"' . selected($status, 'C', false) . '>' . _t('C Changed Mind') . '</option>
                <option value="G"' . selected($status, 'G', false) . '>' . _t('G Graduated') . '</option>
                </select>';
    return $app->hook->{'apply_filter'}('stu_prog_status', $select, $status);
}

/**
 * Credit type select: shows general list of credit types and
 * if $type is not NULL, shows the credit type 
 * for a particular course or course section record.
 * 
 * @since 1.0.0
 * @param string $type
 * @return string Returns the record type if selected is true.
 */
function credit_type($type = NULL)
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="status" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="I"' . selected($status, 'I', false) . '>' . _t('I Institutional') . '</option>
                <option value="TR"' . selected($status, 'TR', false) . '>' . _t('TR Transfer') . '</option>
                <option value="AP"' . selected($status, 'AP', false) . '>' . _t('AP Advanced Placement') . '</option>
                <option value="X"' . selected($status, 'X', false) . '>' . _t('X Exempt') . '</option>
                <option value="T"' . selected($status, 'T', false) . '>' . _t('T Test') . '</option>
                </select>';
    return $app->hook->{'apply_filter'}('course_sec_status', $select, $status);
}

/**
 * Class year select: shows general list of class years and
 * if $year is not NULL, shows the class year
 * for a particular student.
 * 
 * @since 1.0.0
 * @param string $year
 * @return string Returns the record year if selected is true.
 */
function class_year($year = NULL)
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="classYear" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                <option value="">&nbsp;</option>
                <option value="FR"' . selected($year, 'FR', false) . '>' . _t('FR Freshman') . '</option>
                <option value="SO"' . selected($year, 'SO', false) . '>' . _t('SO Sophomore') . '</option>
                <option value="JR"' . selected($year, 'JR', false) . '>' . _t('JR Junior') . '</option>
                <option value="SR"' . selected($year, 'SR', false) . '>' . _t('SR Senior') . '</option>
                <option value="UG"' . selected($year, 'UG', false) . '>' . _t('UG Undergraduate Student') . '</option>
                <option value="GR"' . selected($year, 'GR', false) . '>' . _t('GR Grad Student') . '</option>
                <option value="PhD"' . selected($year, 'PhD', false) . '>' . _t('PhD PhD Student') . '</option>
                </select>';
    return $app->hook->{'apply_filter'}('class_year', $select, $year);
}

/**
 * Grading scale: shows general list of letter grades and
 * if $grade is not NULL, shows the grade
 * for a particular student course section record
 * 
 * @since 1.0.0
 * @param string $grade
 * @return string Returns the stu_course_sec grade if selected is true.
 */
function grading_scale($grade = NULL)
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="grade[]" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>' . "\n";
    $select .= '<option value="">&nbsp;</option>' . "\n";
    $scale = $app->db->query('SELECT * FROM grade_scale WHERE status = "1"');
    $q = $scale->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    foreach ($q as $r) {
        $select .= '<option value="' . _h($r['grade']) . '"' . selected($grade, _h($r['grade']), false) . '>' . _h($r['grade']) . '</option>' . "\n";
    }
    $select .= '</select>';
    return $app->hook->{'apply_filter'}('grading_scale', $select, $grade);
}

function grades($id, $aID)
{
    $app = \Liten\Liten::getInstance();
    $grade = $app->db->query('SELECT * FROM gradebook WHERE stuID = ? AND assignID = ?', [ $id, $aID]);
    $q = $grade->find(function($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });
    $array = [];
    foreach ($q as $r) {
        $array[] = $r;
    }
    $select = grading_scale(_h($r['grade']));
    return $app->hook->{'apply_filter'}('grades', $select);
}

/**
 * Admit status: shows general list of admission statuses and
 * if $status is not NULL, shows the admit status
 * for a particular applicant.
 * 
 * @since 1.0.0
 * @param string $status
 * @return string Returns the application admit status if selected is true.
 */
function admit_status_select($status = NULL)
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="admitStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                <option value="">&nbsp;</option>
                <option value="FF"' . selected($status, 'FF', false) . '>' . _t('FF First Time Freshman') . '</option>
                <option value="TR"' . selected($status, 'TR', false) . '>' . _t('TR Transfer') . '</option>
                <option value="RA"' . selected($status, 'RA', false) . '>' . _t('RA Readmit') . '</option>
                <option value="NA"' . selected($status, 'NA', false) . '>' . _t('NA Non-Applicable') . '</option>
                </select>';
    return $app->hook->{'apply_filter'}('admit_status', $select, $status);
}

/**
 * General Ledger type select: shows general list of general 
 * ledger types and if $type is not NULL, shows the general 
 * ledger type for a particular general ledger record.
 * 
 * @since 1.1.5
 * @param string $type
 * @return string Returns the record type if selected is true.
 */
function general_ledger_type_select($type = NULL)
{
    $app = \Liten\Liten::getInstance();
    $select = '<select name="gl_acct_type" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                <option value="">&nbsp;</option>
                <option value="' . _t('Asset') . '"' . selected($type, _t('Asset'), false) . '>' . _t('Asset') . '</option>
                <option value="' . _t('Liability') . '"' . selected($type, _t('Liability'), false) . '>' . _t('Liability') . '</option>
                <option value="' . _t('Equity') . '"' . selected($type, _t('Equity'), false) . '>' . _t('Equity') . '</option>
                <option value="' . _t('Revenue') . '"' . selected($type, _t('Revenue'), false) . '>' . _t('Revenue') . '</option>
                <option value="' . _t('Expense') . '"' . selected($type, _t('Expense'), false) . '>' . _t('Expense') . '</option>
                </select>';
    return $app->hook->{'apply_filter'}('general_ledger_type', $select, $type);
}

function get_user_avatar($email, $s = 80, $class = '', $d = 'mm', $r = 'g', $img = false)
{
    $app = \Liten\Liten::getInstance();
    $url = 'http://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=200&d=$d&r=$r";
    $avatarsize = getimagesize($url);
    $avatar = '<img src="' . $url . '" ' . imgResize($avatarsize[1], $avatarsize[1], $s) . ' class="' . $class . '" />';
    return $app->hook->{'apply_filter'}('user_avatar', $avatar, $email, $s, $class, $d, $r, $img);
}

function success_update()
{
    $app = \Liten\Liten::getInstance();
    $message = '<div class="alert alert-success">';
    $message .= '<strong>' . _t('Success!') . '</strong> ' . _t('The record was updated successfully.');
    $message .= '</div>';
    return $app->hook->{'apply_filter'}('success_update', $message);
}

function error_update()
{
    $app = \Liten\Liten::getInstance();
    $message = '<div class="alert alert-danger">';
    $message .= '<strong>' . _t('Error!') . '</strong> ' . _t('The system was unable to update the record in the database. Please try again. If the problem persists, contact your system administrator.');
    $message .= '</div>';
    return $app->hook->{'apply_filter'}('error_update', $message);
}

function nocache_headers()
{
    $app = \Liten\Liten::getInstance();
    $headers = [
        'Expires' => 'Sun, 01 Jan 2014 00:00:00 GMT',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache'
    ];
    foreach ($headers as $k => $v) {
        header("{$k}: {$v}");
    }
    return $app->hook->{'apply_filter'}('nocache_headers', $headers);
}
$app->hook->{'add_action'}('admin_head', 'head_release_meta', 5);
$app->hook->{'add_action'}('myet_head', 'head_release_meta', 5);
$app->hook->{'add_action'}('release', 'foot_release', 5);
$app->hook->{'add_action'}('dashboard_top_widgets', 'dashboard_student_count', 5);
$app->hook->{'add_action'}('dashboard_top_widgets', 'dashboard_course_count', 5);
$app->hook->{'add_action'}('dashboard_top_widgets', 'dashboard_acadProg_count', 5);
$app->hook->{'add_action'}('dashboard_right_widgets', 'dashboard_clock', 5);
$app->hook->{'add_action'}('dashboard_right_widgets', 'dashboard_weather', 5);
$app->hook->{'add_filter'}('the_custom_page_content', 'et_autop');
$app->hook->{'add_filter'}('the_custom_page_content', 'parsecode_unautop');
$app->hook->{'add_filter'}('the_custom_page_content', 'do_parsecode', 5);
$app->hook->{'add_filter'}('the_myet_page_content', 'et_autop');
$app->hook->{'add_filter'}('the_myet_page_content', 'parsecode_unautop');
$app->hook->{'add_filter'}('the_myet_page_content', 'do_parsecode', 5);
$app->hook->{'add_filter'}('the_myet_welcome_message', 'et_autop');
$app->hook->{'add_filter'}('the_myet_welcome_message', 'parsecode_unautop');
$app->hook->{'add_filter'}('the_myet_welcome_message', 'do_parsecode', 5);
