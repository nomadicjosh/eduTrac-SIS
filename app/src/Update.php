<?php namespace app\src;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Automatic Update Class
 *  
 * @deprecated 6.1.14
 * @since       4.0.1
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

/**
 * Updates the fiels of an aplication
 *
 * @name Application Update
 * @author Alexandru Savin alexandrusavin@gmail.com
 * @version 1.00
 * @http://www.ozz.ro/appupdate
 */
class Update
{

    public $server_address;
    public $current_version;
    public $server_version;
    public $updated_files_list;
    public $writable_files;
    public $charlen_file;

    /**
     * The constructor for the class
     *
     * @param string $server_address
     * @param integer $current_version
     * @return update
     */
    function __construct($server_address, $current_version)
    {
        _deprecated_class(__CLASS__, '6.1.14', 'CoreUpdate');
        $this->server_address = $server_address;
        $this->current_version = $current_version;
        $this->get_server_variables();
    }

    /**
     * Checks whatever there are updates for the software
     *
     */
    function check_for_updates()
    {
        if ($this->current_version < $this->server_version)
            return true;
        else
            return false;
    }

    /**
     * Connects to the server and retrives the variables needed for the update.
     *
     */
    private function get_server_variables()
    {
        $variables_file_address = $this->server_address . "/update_vars.php";
        $file = file($variables_file_address);
        foreach ($file as $line) {
            if (strstr($line, "[server_version]") !== false)
                $this->server_version = trim(substr($line, strpos($line, "=") + 1));
            elseif (strstr($line, "[server_file_list]") !== false)
                $this->updated_files_list[] = trim(substr($line, strpos($line, "=") + 1));
            elseif (strstr($line, "[charlen_file]") !== false)
                $this->charlen_file[] = trim(substr($line, strpos($line, "=") + 1));
        }
    }

    /**
     * Returns the files to be updated in a html format.
     *
     * @return string
     */
    function print_updated_files_list()
    {
        $filelist = "";
        foreach ($this->updated_files_list as $filename) {
            $filelist.=$filename . "<br>";
        }
        return $filelist;
    }

    /**
     * Single file writable atribute check.
     * Thanks to legolas558.users.sf.net
     *
     * @param unknown_type $path
     * @return true
     */
    private function is__writable($path)
    {
        //will work in despite of Windows ACLs bug
        //NOTE: use a trailing slash for folders!!!
        //see http://bugs.php.net/bug.php?id=27609
        //see http://bugs.php.net/bug.php?id=30931

        if ($path{strlen($path) - 1} == '/') // recursively return a temporary file path
            return is__writable($path . uniqid(mt_rand()) . '.tmp');
        else if (is_dir($path))
            return is__writable($path . '/' . uniqid(mt_rand()) . '.tmp');
        // check tmp file for read/write capabilities
        $rm = file_exists($path);
        $f = fopen($path, 'a');
        if ($f === false)
            return false;
        fclose($f);
        if (!$rm)
            unlink($path);
        return true;
    }

    /**
     * Checks watever the files are writable or not. Return boolean and the list of filles in a string;
     *
     * @return true
     */
    function check_if_are_writable()
    {
        $err = "";
        foreach ($this->updated_files_list as $filename) {
            if ($this->is__writable($filename) === true) {
                $this->writable_files[$filename] = '<font color="green">yes</font>';
            } else {
                $this->writable_files[$filename] = '<font color="red">no</font>';
                $err = 1;
            }
        }
        return $err == 1 ? false : true;
    }

    function get_total_charlen()
    {
        foreach ($this->charlen_file as $len) {
            $total_len+=$len;
        }
        return $total_len;
    }

    /**
     * Updates the files with the ones from the server
     *
     * @return error string for error or true
     */
    function update_files()
    {
        $err = "";
        if ($this->check_if_are_writable() === true) {
            $i = 0;
            foreach ($this->updated_files_list as $filename) {

                $updated_file_url = $this->server_address . "/update_" . $this->server_version . "/" . $filename;
                $updated_file_contents = _file_get_contents($updated_file_url);

                if ($updated_file_contents != "") {

                    $file = fopen($filename, "w");
                    fwrite($file, $updated_file_contents);
                    fclose($file);
                }

                $len_till_now+=$this->charlen_file[$i];
                $perc = $len_till_now * 100 / $this->get_total_charlen();
                echo str_repeat(".", $perc);
                $i++;
            }
        } else {
            $err = "Some file or all are not writable.";

            foreach ($this->writable_files as $id => $value) {
                if ($value == "no") {
                    echo $id . " file is not writable!<br>";
                }
            }

            $err .= "No file was updated.<br>";
        }
        return $err == "" ? true : $err;
    }
}
