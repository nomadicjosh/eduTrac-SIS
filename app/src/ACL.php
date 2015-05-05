<?php namespace app\src;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

/**
 * Access Level Control Class
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @license     http://www.edutracerp.com/general/edutrac-erp-commercial-license/ Commercial License
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
class ACL
{

    /**
     * Stores the permissions for the user
     *
     * @access public
     * @var array
     */
    protected $_perms = [];

    /**
     * Stores the ID of the current user
     *
     * @access public
     * @var integer
     */
    protected $_personID = 0;

    /**
     * Stores the roles of the current user
     *
     * @access public
     * @var array
     */
    protected $_userRoles = [];
    
    protected $_app;

    public function __construct($personID = '')
    {
        $this->_app = \Liten\Liten::getInstance();

        if ($personID != '') {
            $this->_personID = floatval($personID);
        } else {
            $this->_personID = floatval(get_persondata('personID'));
        }
        $this->_userRoles = $this->getUserRoles('ids');
        $this->buildACL();
    }

    public function ACL($personID = '')
    {
        $this->__construct($personID);
    }

    public function buildACL()
    {
        //first, get the rules for the user's role
		if (count($this->_userRoles) > 0) {
			$this->_perms = array_merge($this->_perms,$this->getRolePerms($this->_userRoles));
		}
		//then, get the individual user permissions
		$this->_perms = array_merge($this->_perms,$this->getUserPerms($this->_personID));
        
    }

    public function getPermKeyFromID($permID)
    {
        $strSQL = $this->_app->db->permission()
            ->select('permission.permKey')
            ->where('ID = ?', floatval($permID))
            ->limit(1);
        $q = $strSQL->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        
        foreach($q as $r) {
            return $r['permKey'];
        }
    }

    public function getPermNameFromID($permID)
    {
        $strSQL = $this->_app->db->permission()
            ->select('permission.permName')
            ->where('ID = ?', floatval($permID))
            ->limit(1);
        $q = $strSQL->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        
        foreach($q as $r) {
            return $r['permName'];
        }
    }

    public function getRoleNameFromID($roleID)
    {
        $strSQL = $this->_app->db->role()
            ->select('role.roleName')
            ->where('ID = ?', floatval($roleID))
            ->limit(1);
        $q = $strSQL->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        
        foreach($q as $r) {
            return $r['roleName'];
        }
    }

    public function getUserRoles()
    {
        $strSQL = $this->_app->db->person_roles()
            ->where('personID = ?', floatval($this->_personID))
            ->orderBy('addDate', 'ASC');
        $q = $strSQL->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        
        $resp = [];
        foreach($q as $r) {
            $resp[] = $r['roleID'];
        }
        
        return $resp;
    }

    public function getAllRoles($format = 'ids')
    {
        $format = strtolower($format);

        $strSQL = $this->_app->db->role()
            ->orderBy('roleName', 'ASC');
        $q = $strSQL->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        
        $resp = [];
        foreach($q as $r) {
            if ($format == 'full')
			{
				$resp[] = [ "ID" => $r['ID'],"Name" => $r['roleName'] ];
			} else {
				$resp[] = $r['ID'];
			}
        }
        return $resp;
    }

    public function getAllPerms($format = 'ids')
    {
        $format = strtolower($format);

        $strSQL = $this->_app->db->permission()
            ->orderBy('permName', 'ASC');
        $q = $strSQL->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        
        $resp = [];
        foreach($q as $r) {
            if ($format == 'full') {
				$resp[$r['permKey']] = [ 'ID' => $r['ID'], 'Name' => $r['permName'], 'Key' => $r['permKey'] ];
			} else {
				$resp[] = $r['ID'];
			}
        }
        return $resp;
    }

    public function getRolePerms($role)
    {
        if (is_array($role)) {
            $roleSQL = $this->_app->db->query("SELECT * FROM role_perms WHERE roleID IN (" . implode(",",$role) . ") ORDER BY ID ASC");
        } else {
            $roleSQL = $this->_app->db->role_perms()
                ->where('roleID = ?', floatval($role))
                ->orderBy('ID', 'ASC');
        }

       $q = $roleSQL->find(function($data) {
           $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        
        $perms = [];
        foreach($q as $r) {
            $pK = strtolower($this->getPermKeyFromID($r['permID']));
			if ($pK == '') { continue; }
			if ($r['value'] === '1') {
				$hP = true;
			} else {
				$hP = false;
			}
			$perms[$pK] = [ 'perm' => $pK,'inheritted' => true,'value' => $hP,'Name' => $this->getPermNameFromID($r['permID']),'ID' => $r['permID'] ];
        }
        return $perms;
    }

    public function getUserPerms($personID)
    {
        $strSQL = $this->_app->db->person_perms()
            ->where('personID = ?', floatval($personID))
            ->orderBy('LastUpdate', 'ASC');

        $q = $strSQL->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        
        $perms = [];
        foreach ($q as $r) {
                $pK = strtolower($this->getPermKeyFromID($r['permID']));
                if ($pK == '') {
                    continue;
                }
                if ($r['value'] === '1') {
                    $hP = true;
                } else {
                    $hP = false;
                }
                $perms[$pK] = [ 'perm' => $pK, 'inheritted' => false, 'value' => $hP, 'Name' => $this->getPermNameFromID($r['permID']), 'ID' => $r['permID'] ];
            }
            return $perms;
    }

    public function userHasRole($roleID)
    {
        foreach ($this->_userRoles as $k => $v) {
            if (floatval($v) === floatval($roleID)) {
                return true;
            }
        }
        return false;
    }

    public function hasPermission($permKey)
    {
        $roles = $this->_app->db->query("SELECT 
						a.ID 
					FROM 
						role a 
					LEFT JOIN 
						person_roles b 
					ON 
						a.ID = b.roleID 
					WHERE 
						a.permission LIKE ? 
					AND 
						b.personID = ?", ["%$permKey%", get_persondata('personID')]
        );
        $q1 = $roles->find(function($data) {
            $array = [];
            foreach($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        
        $perms = $this->_app->db->query('SELECT ID FROM person_perms WHERE permission LIKE ? AND personID = ?', ["%$permKey%", get_persondata('personID')]);
        
        $q2 = $perms->find(function($data) {
            $array = [];
            foreach($data as $d) {
                $array[] = $d;
            }
            return $array;
        });
        
        if (count($q1) > 0) {
            return true;
        } elseif (count($q2) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getUsername($personID)
    {
        $strSQL = $this->_app->db->person()
            ->select('person.uname')
            ->where('personID = ?', floatval($personID))
            ->limit(1);
        $q = $strSQL->find(function($data) {
            foreach ($data as $d) {
                return $d['uname'];
            }
        });
        
        $array = [];
        foreach($q as $r) {
            $array[] = $r;
        }
        return $array;
    }
}
