<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\NodeQ\Helpers\Validate as Validate;
use app\src\Core\Exception\Exception;
use Cascade\Cascade;

try {
    if (!Validate::table('php_encryption')->exists()) {
        Node::dispense('php_encryption');
    }
    
    $email = Node::table('student_email');
    if(!in_array('cc', $email->fields())) {
        $email->addFields(['cc' => 'string', 'bcc' => 'string']);
    }
} catch (NodeQException $e) {
    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Unable to create Node: %s', $e->getCode(), $e->getMessage()));
} catch (Exception $e) {
    Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: Unable to create Node: %s', $e->getCode(), $e->getMessage()));
}
