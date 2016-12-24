<?php namespace app\src\Core;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * eduTrac SIS Assets Management
 *  
 * @since       2.0.0
 * @package     eduTrac SIS
 * @subpackage  Assets
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
class etsis_Assets extends \Stolz\Assets\Manager
{
    /**
	 * Available style collections.
	 * Each collection is an array of assets.
	 * Collections may also contain other collections.
	 *
	 * @var array
	 */
	protected $style_collections = [];
    
    /**
	 * Available script collections.
	 * Each collection is an array of assets.
	 * Collections may also contain other collections.
	 *
	 * @var array
	 */
	protected $script_collections = [];
    
    /**
	 * Class constructor.
	 *
	 * @param  array $options See config() method for details.
	 * @return void
	 */
    public function __construct(array $options)
    {
        parent::__construct($options);
    }
    
    /**
     * Add css asset or a collection of css assets.
     * 
     * You may add more than one css asset passing an array as argument.
     * 
     * @since   2.0.0
     * @param   mixed $asset
     * @return  `\app\src\tc_Assets`
     */
    public function register_style($asset)
    {
        // More than one asset
        if (is_array($asset)) {
            foreach ($asset as $a) {
                $this->add($a);
            }
        }

        // Collection
        elseif (isset($this->style_collections[$asset])) {
            $this->add($this->style_collections[$asset]);
        }

        // CSS asset
        elseif (preg_match($this->css_regex, $asset)) {
            $this->addCss($asset);
        }

        return $this;
    }

    /**
     * Add js asset or a collection of js assets.
     * 
     * You may add more than one js asset passing an array as argument.
     * 
     * @since   2.0.0
     * @param   mixed $asset
     * @return  `\app\src\tc_Assets`
     */
    public function register_script($asset)
    {
        // More than one asset
        if (is_array($asset)) {
            foreach ($asset as $a) {
                $this->add($a);
            }
        }

        // Collection
        elseif (isset($this->script_collections[$asset])) {
            $this->add($this->script_collections[$asset]);
        }

        // JavaScript asset
        elseif (preg_match($this->js_regex, $asset)) {
            $this->addJs($asset);
        }
        return $this;
    }
    
    /**
	 * Build the CSS `<link>` tags.
	 *
	 * Accepts an array of $attributes for the HTML tag.
	 * You can take control of the tag rendering by
	 * providing a closure that will receive an array of assets.
	 *
	 * @param  array|Closure $attributes
	 * @return string
	 */
    public function enqueue_style($attributes = null)
    {
        return $this->css($attributes);
    }

    /**
	 * Build the JavaScript `<script>` tags.
	 *
	 * Accepts an array of $attributes for the HTML tag.
	 * You can take control of the tag rendering by
	 * providing a closure that will receive an array of assets.
	 *
	 * @param  array|Closure $attributes
	 * @return string
	 */
    public function enqueue_script($attributes = null)
    {
        return $this->js($attributes);
    }
    
    /**
	 * Add/replace style collection.
	 *
     * @since 2.0.0
	 * @param   string  $collectionName
	 * @param   array   $assets
	 * @return  `\app\src\tc_Assets`
	 */
	public function registerStyleCollection($collectionName, array $assets)
	{
		$this->style_collections[$collectionName] = $assets;

		return $this;
	}
    
    /**
     * Add/replace script collection.
	 *
     * @since 2.0.0
	 * @param   string  $collectionName
	 * @param   array   $assets
     * @return  `\app\src\tc_Assets`
     */
    public function registerScriptCollection($collectionName, array $assets)
	{
		$this->script_collections[$collectionName] = $assets;

		return $this;
	}
}
