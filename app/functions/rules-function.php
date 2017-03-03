<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use \app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

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
    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
} catch (Exception $e) {
    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
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
        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
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
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
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
        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
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
        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
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
                $upd = Node::table("$node")->find(_h($rule->id));
                $upd->rule = $code;
                $upd->save();
            }
        }
    } catch (NodeQException $e) {
        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
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
                $upd = Node::table("$node")->find(_h($rule->id));
                $upd->level = $code;
                $upd->save();
            }
        }
    } catch (NodeQException $e) {
        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    }
}

function clas_dropdown($table, $where = null, $id, $code, $name, $bind = null)
{
    $app = \Liten\Liten::getInstance();
    try {
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
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    }
}

/**
 * Registration restriction rule check.
 * 
 * @since 6.3.0
 * @param int $stuID Student ID.
 */
function etsis_reg_rest_rule($stuID)
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
                _etsis_flash()->error(_h($rule->value), $app->req->server['HTTP_REFERER']);
                exit();
            }
        }
    } catch (NodeQException $e) {
        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    }
}

/**
 * Retrieve a list of academic levels.
 * 
 * @since 6.3.0
 */
function get_acad_levels()
{
    $app = \Liten\Liten::getInstance();
    try {
        $q = $app->db->aclv()
            ->find();
        foreach ($q as $r) {
            echo "'" . _h($r->code) . "'" . ': ' . "'" . _h($r->code) . " " . _h($r->name) . "'" . ',' . "\n";
        }
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    }
}

/**
 * Retrieve a list of academic terms.
 * 
 * @since 6.3.0
 */
function get_acad_terms()
{
    $app = \Liten\Liten::getInstance();
    try {
        $q = $app->db->term()
            ->where('termCode <> "NULL"')
            ->orderBy('termStartDate', 'DESC')
            ->find();
        foreach ($q as $r) {
            echo "'" . _h($r->termCode) . "'" . ': ' . "'" . _h($r->termCode) . " " . _h($r->termName) . "'" . ',' . "\n";
        }
    } catch (NotFoundException $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (Exception $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    } catch (ORMException $e) {
        Cascade::getLogger('error')->error(sprintf('SQLSTATE[%s]: Error: %s', $e->getCode(), $e->getMessage()));
        _etsis_flash()->error(_etsis_flash()->notice(409));
    }
}
