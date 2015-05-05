<?php namespace app\src;
if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Student DBObject Class
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

class Student {

	private $_ID; //int(11) unsigned zerofill
	private $_stuID; //int(8) unsigned zerofill
	private $_status;
    private $_stuStatus;
	private $_addDate; //datetime
	private $_approvedBy;
	private $_LastUpdate; //timestamp
	private $_address1;
    private $_address2;
    private $_city;
    private $_state;
    private $_zip;
    private $_phone1;
    private $_email1;
    private $_dob;
    private $_app;
    private $_cache;

	public function __construct() {
        $this->_app = \Liten\Liten::getInstance();
        $this->_cache = new \app\src\DBCache;
    }

    /**
     * Load one row into var_class. To use the vars use for exemple echo $class->getVar_name; 
     *
     * @param key_table_type $key_row
     * 
     */
	public function Load_from_key($key_row){
        $stu = $this->_app->db->query( "SELECT 
                        a.ID,a.stuID,a.addDate,a.approvedBy,a.LastUpdate,a.status,
                        CASE a.status 
                        WHEN 'A' THEN 'Active' 
                        ELSE 'Inactive' 
                        END AS 'stuStatus', 
                        b.address1,b.address2,b.city,b.state,b.zip,
                        b.phone1,b.email1,c.dob 
                    FROM student a 
                    LEFT JOIN address b ON a.stuID = b.personID 
                    LEFT JOIN person c ON a.stuID = c.personID 
                    WHERE a.stuID = ? 
                    AND b.addressStatus = 'C' 
                    AND (b.endDate = '' OR b.endDate = '0000-00-00')",
                    [$key_row] 
        );
        $q = $stu->find(function($data) {
            $array = [];
            foreach($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
		foreach($q as $row){
			$this->_ID = $row["ID"];
			$this->_stuID = $row["stuID"];
            $this->_status = $row["status"];
			$this->_stuStatus = $row["stuStatus"];
			$this->_addDate = $row["addDate"];
			$this->_approvedBy = $row["approvedBy"];
			$this->_LastUpdate = $row["LastUpdate"];
            $this->_address1 = $row["address1"];
            $this->_address2 = $row["address2"];
            $this->_city = $row["city"];
            $this->_state = $row["state"];
            $this->_zip = $row["zip"];
            $this->_phone1 = $row["phone1"];
            $this->_email1 = $row["email1"];
            $this->_dob = $row["dob"];
		}
	}

	/**
	 * @return ID - int(11) unsigned zerofill
	 */
	public function getID(){
		return $this->_ID;
	}

	/**
	 * @return stuID - int(8) unsigned zerofill
	 */
	public function getStuID(){
		return $this->_stuID;
	}

	/**
	 * @return current status
	 */
	public function getStatus(){
		return $this->_status;
	}
    
    /**
     * @return current status
     */
    public function getStuStatus(){
        return $this->_stuStatus;
    }

	/**
	 * @return addDate - datetime
	 */
	public function getAddDate(){
		return $this->_addDate;
	}

	/**
	 * @return addedBy - int(8)
	 */
	public function getAddedBy(){
		return $this->_addedBy;
	}

	/**
	 * @return LastUpdate - timestamp
	 */
	public function getLastUpdate(){
		return $this->_LastUpdate;
	}
    
    public function getAddress1() {
        return $this->_address1;
    }
    
    public function getAddress2() {
        return $this->_address2;
    }
    
    public function getCity() {
        return $this->_city;
    }
    
    public function getState() {
        return $this->_state;
    }
    
    public function getZip() {
        return $this->_zip;
    }
    
    public function getPhone1() {
        return $this->_phone1;
    }
    
    public function getEmail1() {
        return $this->_email1;
    }
    
    public function getDob() {
        return $this->_dob;
    }

}