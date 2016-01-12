<?php namespace app\src;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Gettext
 *  
 * @license GPLv3
 * 
 * @since       6.1.09
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
class Gettext
{

    /**
     * Current encoding
     * @type String
     */
    protected $encoding;

    /**
     * Current locale
     * @type String
     */
    protected $locale;

    /**
     * @var String
     */
    protected $domain;

    /**
     * Sets the default encoding and domain.
     * 
     */
    public function __construct($domain = null)
    {
        /**
         * Deprecated. Use the new Gettext alternative.
         */
        _deprecated_class(__CLASS__, '6.1.13', '\\Gettext\\');
        $this->encoding = 'UTF-8';
        $this->domain = $domain;
    }

    /**
     * Sets the current locale code
     * 
     * @since 6.1.09
     * @param string $locale Translation that should be loaded.
     * @param string $path Language directory.
     */
    public function setLocale($locale, $path)
    {

        try {
            $gettextLocale = $locale . "." . $this->encoding;

            // All locale functions are updated: LC_COLLATE, LC_CTYPE,
            // LC_MONETARY, LC_NUMERIC, LC_TIME and LC_MESSAGES
            putenv("LC_ALL=$gettextLocale");
            putenv("LANGUAGE=$gettextLocale");
            setlocale(LC_ALL, $gettextLocale);

            // Domain
            $this->setDomain($this->domain, $path);

            $this->locale = $locale;

            return $this->getLocale();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Returns the current locale string identifier
     *
     * @since 6.1.09
     * @return String
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Return the current locale
     *
     * @since 6.1.09
     * @return mixed
     */
    public function __toString()
    {
        return $this->getLocale();
    }

    /**
     * Gets the Current encoding.
     *
     * @since 6.1.09
     * @return mixed
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Sets the Current encoding.
     *
     * @since 6.1.09
     * @param mixed $encoding the encoding
     * @return self
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Sets the current domain and updates gettext domain application
     *
     * @since 6.1.09
     * @param   String $domain
     * @return  self
     */
    public function setDomain($domain, $path)
    {

        bindtextdomain($domain, $path);
        bind_textdomain_codeset($domain, $this->encoding);

        $this->domain = textdomain($domain);

        return $this;
    }

    /**
     * Returns the current domain
     *
     * @since 6.1.09
     * @return String
     */
    public function getDomain()
    {
        return $this->domain;
    }
}
