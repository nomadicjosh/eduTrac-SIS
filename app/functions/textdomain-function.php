<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * eduTrac SIS Text Domain.
 *  
 * @license GPLv3
 * 
 * @since       6.1.13
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

include_once(APP_PATH . 'src/Gettext/autoloader.php');
include_once(APP_PATH . 'src/Gettext/Languages/autoloader.php');

$t = new \Gettext\Translator();
$t->register();

/**
 * Loads the current or default locale.
 * 
 * @since 6.1.09
 * @return string The locale.
 */
function load_core_locale()
{
    if(is_readable(BASE_PATH . 'config.php')) {
        $locale = get_option('et_core_locale');
    } else {
        $locale = 'en_US';
    }
    return apply_filter('core_locale', $locale);
}

/**
 * Load a .mo file into the text domain.
 *
 * @since 6.1.13
 *
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @param string $path Path to the .mo file.
 * @return bool True on success, false on failure.
 */
function load_textdomain($domain, $path)
{
    global $t;

    /**
     * Filter text domain and/or .mo file path for loading translations.
     *
     * @since 6.1.13
     *
     * @param bool   $override Should we override textdomain?. Default is false.
     * @param string $domain   Text domain. Unique identifier for retrieving translated strings.
     * @param string $path   Path to the .mo file.
     */
    $plugin_override = apply_filter('override_load_textdomain', false, $domain, $path);

    if (true == $plugin_override) {
        return true;
    }

    /**
     * Fires before the .mo translation file is loaded.
     *
     * @since 6.1.13
     *
     * @param string $domain Text domain. Unique identifier for retrieving translated strings.
     * @param string $path Path to the .mo file.
     */
    do_action('load_textdomain', $domain, $path);

    /**
     * Filter .mo file path for loading translations for a specific text domain.
     *
     * @since 6.1.13
     *
     * @param string $path Path to the .mo file.
     * @param string $domain Text domain. Unique identifier for retrieving translated strings.
     */
    $mofile = apply_filter('load_textdomain_mofile', $path, $domain);
    // Load only if the .mo file is present and readable.
    if (!is_readable($mofile)) {
        return false;
    }

    $translations = \Gettext\Translations::fromMoFile($mofile);
    $t->loadTranslations($translations);

    return true;
}

/**
 * Load default translated strings based on locale.
 *
 * @since 6.1.09
 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
 * @param string $path Path to the .mo file.
 * @return bool True on success, false on failure.
 */
function load_default_textdomain($domain, $path)
{
    $locale = load_core_locale();

    $mopath = $path . $domain . '-' . $locale . '.mo';

    $return = load_textdomain($domain, $mopath);

    return $return;
}

/**
 * Load a plugin's translated strings.
 *
 * If the path is not given then it will be the root of the plugin directory.
 *
 * @since 6.1.09
 * @param string $domain          Unique identifier for retrieving translated strings
 * @param string $plugin_rel_path Optional. Relative path to PLUGINS_DIR where the locale directory resides.
 *                                Default false.
 * @return bool True when textdomain is successfully loaded, false otherwise.
 */
function load_plugin_textdomain($domain, $plugin_rel_path = false)
{
    $locale = load_core_locale();
    /**
     * Filter a plugin's locale.
     * 
     * @since 6.1.09
     * 
     * @param string $locale The plugin's current locale.
     * @param string $domain Text domain. Unique identifier for retrieving translated strings.
     */
    $plugin_locale = apply_filter('plugin_locale', $locale, $domain);

    if ($plugin_rel_path !== false) {
        $path = PLUGINS_DIR . $plugin_rel_path . DS;
    } else {
        $path = PLUGINS_DIR;
    }
    
    $mofile = $path . $domain . '-' . $plugin_locale . '.mo';
    if ($loaded = load_textdomain($domain, $mofile)) {
        return $loaded;
    }

    return false;
}

function et_dropdown_languages($active = '') {
    $locales = _file_get_contents('http://edutrac.s3.amazonaws.com/translations.json');
    $json = json_decode($locales, true);
    foreach($json as $locale) {
        echo '<option value="'.$locale['language'] . '"' . selected($active, $locale['language'], false) . '>'.$locale['native_name'].'</option>';
    }
}