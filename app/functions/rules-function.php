<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use \app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;

/**
 * eduTrac SIS Rules Functions
 *
 * @license GPLv3
 *
 * @since 6.3.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
$app = \Liten\Liten::getInstance();

try {
    /**
     * Creates rlde node if it does not exist.
     * 
     * @since 6.3.0
     */
    Node::dispense('rlde');

    /**
     * Creates stld node if it does not exist.
     * 
     * @since 6.3.0
     */
    Node::dispense('stld');

    /**
     * Creates clvr node if it does not exist.
     * 
     * @since 6.3.0
     */
    Node::dispense('clvr');

    /**
     * Creates rrsr node if it does not exist.
     * 
     * @since 6.3.0
     */
    Node::dispense('rrsr');
} catch (NodeQException $e) {
    _etsis_flash()->error($e->getMessage());
} catch (Exception $e) {
    _etsis_flash()->error($e->getMessage());
}

/**
 * Retrieve a list of rules.
 * 
 * @since 6.3.0
 * @param string $active
 */
function get_rules($active = null)
{
    try {
        $rlde = Node::table('rlde')->findAll();
        foreach ($rlde as $rule) {
            echo '<option value="' . _h($rule->code) . '"' . selected(_h($rule->code), $active, false) . '>' . '(' . _h($rule->code) . ') - ' . _h($rule->description) . '</option>';
        }
    } catch (NodeQException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage());
    }
}

/**
 * Retrieve department data as object.
 * 
 * @since 6.3.0
 * @param string $code Deparment code.
 * @return mixed
 */
function get_department($code)
{
    $app = \Liten\Liten::getInstance();
    try {
        $dept = $app->db->department()->where('deptCode = ?', $code)->find();
        foreach ($dept as $d) {
            return $d;
        }
    } catch (NotFoundException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (ORMException $e) {
        _etsis_flash()->error($e->getMessage());
    }
}

/**
 * Counts records in a particular node.
 * 
 * @since 6.3.0
 * @param string $node Name of node to query.
 * @param string $field Field name.
 * @param string $op Operator
 * @param string $value Field value.
 * @return string
 */
function is_node_count_zero($node, $field = null, $op = null, $value = null)
{
    try {
        if ($field != null) {
            $count = Node::table($node)->where($field, $op, $value)->findAll()->count();
            if ($count > 0) {
                return 'X';
            }
        } else {
            $count = Node::table($node)->findAll()->count();
            if ($count > 0) {
                return 'X';
            }
        }
    } catch (NodeQException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage());
    }
}

/**
 * Retrieve rule data as object by the rule's unique code.
 * 
 * @since 6.3.0
 * @param string $code Rule's unique code.
 * @return object
 */
function get_rule_by_code($code)
{
    try {
        $rlde = Node::table('rlde')->where('code', '=', $code)->find();
        return $rlde;
    } catch (NodeQException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage());
    }
}

/**
 * Updates rlde rule codes on update that are connected to other rules.
 * 
 * @since 6.3.0
 * @param string $node Name of node to update.
 * @param int $id Primary ID of the rule being updated.
 * @param string $code Level that was updated.
 */
function update_rlde_code_on_update($node, $id, $code)
{
    try {
        $find = Node::table("$node")->where('rid', '=', $id)->findAll();
        if ($find->count() > 0) {
            foreach ($find as $rule) {
                $upd = Node::table("$node")->find($rule->id);
                $upd->rule = $code;
                $upd->save();
            }
        }
    } catch (NodeQException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage());
    }
}

/**
 * Updates aclv level codes on update that are connected to other rules.
 * 
 * @since 6.3.0
 * @param string $node Name of node to update.
 * @param int $id Primary ID of the rule being updated.
 * @param string $code Level that was updated.
 */
function update_aclv_code_on_update($node, $id, $code)
{
    try {
        $find = Node::table("$node")->where('aid', '=', $id)->findAll();
        if ($find->count() > 0) {
            foreach ($find as $rule) {
                $upd = Node::table("$node")->find($rule->id);
                $upd->level = $code;
                $upd->save();
            }
        }
    } catch (NodeQException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage());
    }
}

function clas_dropdown($table, $where = null, $id, $code, $name, $bind = null)
{
    $app = \Liten\Liten::getInstance();
    if ($where !== null && $bind == null) {
        $table = $app->db->query("SELECT $id, $code, $name FROM $table WHERE $where");
    } elseif ($bind !== null) {
        $table = $app->db->query("SELECT $id, $code, $name FROM $table WHERE $where", $bind);
    } else {
        $table = $app->db->query("SELECT $id, $code, $name FROM $table");
    }
    $q = $table->find(function ($data) {
        $array = [];
        foreach ($data as $d) {
            $array[] = $d;
        }
        return $array;
    });

    foreach ($q as $r) {
        echo '<option value="' . _h($r[$code]) . '">' . _h($r[$code]) . ' ' . _h($r[$name]) . '</option>';
    }
}

/**
 * Registration restriction rule check.
 * 
 * @since 6.3.0
 * @param int $stuID Student ID.
 */
function etsis_reg_rstr_rule($stuID)
{
    $app = \Liten\Liten::getInstance();
    try {
        $node = Node::table('rrsr')->findAll();
        foreach ($node as $rule) {
            $rlde = get_rule_by_code($rule->rule);
            $db = $app->db->restriction()
                ->setTableAlias('strs')
                ->where('strs.stuID = ?', $stuID)->_and_()
                ->where("$rlde->rule")
                ->findOne();
            if (false != $db) {
                $app->flash('error_message', $rule->value);
                redirect($app->req->server['HTTP_REFERER']);
                exit();
            }
        }
    } catch (NodeQException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (NotFoundException $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (Exception $e) {
        _etsis_flash()->error($e->getMessage());
    } catch (ORMException $e) {
        _etsis_flash()->error($e->getMessage());
    }
}
