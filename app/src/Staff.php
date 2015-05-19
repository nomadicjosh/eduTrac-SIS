<?php namespace app\src;
if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Staff DBObject Class
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       5.0.7
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */

class Staff {

	private $_ID; //int(11) unsigned zerofill
	private $_staffID; //int(8) unsigned zerofill
	private $_status;
    private $_staffStatus;
	private $_officeCode; //datetime
	private $_office_phone;
	private $_LastUpdate; //timestamp
	private $_address1;
    private $_address2;
    private $_city;
    private $_state;
    private $_zip;
    private $_phone1;
    private $_email;
    private $_dob;
    private $_deptName;
    private $_title;
    private $_app;
    private $_cache;

	public function __construct() {
        $this->_app = \Liten\Liten::getInstance();
        $this->_cache = new \app\src\DBCache;
    }

    /**
     * Load one row into var_class. To use the vars use for example echo $class->getVar_name; 
     *
     * @param key_table_type $key_row
     * 
     */
	public function Load_from_key($key_row){
        $stu = $this->_app->db->query( "SELECT 
                        a.ID,a.staffID,a.officeCode,a.office_phone,a.status,
                        CASE a.status 
                        WHEN 'A' THEN 'Active' 
                        ELSE 'Inactive' 
                        END AS 'staffStatus', 
                        b.address1,b.address2,b.city,b.state,b.zip,
                        b.phone1,c.email,c.dob,d.deptName,f.title 
                    FROM staff a 
                    LEFT JOIN address b ON a.staffID = b.personID 
                    LEFT JOIN person c ON a.staffID = c.personID 
                    LEFT JOIN department d ON a.deptCode = d.deptCode 
                    LEFT JOIN staff_meta e ON a.staffID = e.staffID 
                    LEFT JOIN job f ON e.jobID = f.ID 
                    WHERE a.staffID = ? 
                    AND b.addressStatus = 'C' 
                    AND (b.endDate = '' OR b.endDate = '0000-00-00')
                    AND e.hireDate = (SELECT MAX(hireDate) FROM staff_meta WHERE staffID = ?)",
                    [$key_row, $key_row] 
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
			$this->_staffID = $row["staffID"];
            $this->_status = $row["status"];
			$this->_staffStatus = $row["staffStatus"];
			$this->_officeCode = $row["officeCode"];
			$this->_office_phone = $row["office_phone"];
            $this->_address1 = $row["address1"];
            $this->_address2 = $row["address2"];
            $this->_city = $row["city"];
            $this->_state = $row["state"];
            $this->_zip = $row["zip"];
            $this->_phone1 = $row["phone1"];
            $this->_email = $row["email"];
            $this->_dob = $row["dob"];
            $this->_deptName = $row["deptName"];
            $this->_title = $row["title"];
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
	public function getStaffID(){
		return $this->_staffID;
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
    public function getStaffStatus(){
        return $this->_staffStatus;
    }

	/**
	 * @return addDate - datetime
	 */
	public function getOfficeCode(){
		return $this->_officeCode;
	}

	/**
	 * @return addedBy - int(8)
	 */
	public function getOfficePhone(){
		return $this->_office_phone;
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
    
    public function getEmail() {
        return $this->_email;
    }
    
    public function getDob() {
        return $this->_dob;
    }
    
    public function getDeptName() {
        return $this->_deptName;
    }
    
    public function getTitle() {
        return $this->_title;
    }

}