<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;

/**
 * Person Router
 *
 * @license GPLv3
 *         
 * @since 5.0.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
$json_url = get_base_url() . 'api' . '/';

$app->group('/nae', function () use($app, $json_url) {

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/', function () {
        if (!hasPermission('access_person_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/', function () use($app, $json_url) {

        if ($app->req->isPost()) {
            try {
                $post = $app->req->post['nae'];
                $search = $app->db->person()
                    ->select('person.personID,person.altID,person.fname,person.lname,person.uname,person.email')
                    ->select('staff.staffID, appl.personID AS ApplicantID')
                    ->_join('staff', 'person.personID = staff.staffID')
                    ->_join('application', 'person.personID = appl.personID', 'appl')
                    ->whereLike('CONCAT(person.fname," ",person.lname)', "%$post%")->_or_()
                    ->whereLike('CONCAT(person.lname," ",person.fname)', "%$post%")->_or_()
                    ->whereLike('CONCAT(person.lname,", ",person.fname)', "%$post%")->_or_()
                    ->whereLike('person.fname', "%$post%")->_or_()
                    ->whereLike('person.lname', "%$post%")->_or_()
                    ->whereLike('person.uname', "%$post%")->_or_()
                    ->whereLike('person.personID', "%$post%")->_or_()
                    ->whereLike('person.altID', "%$post%");
                $q = $search->find(function ($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
            } catch (NotFoundException $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->error($e->getMessage());
            }
        }

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('person/index', [
            'title' => 'Name and Address',
            'search' => $q
        ]);
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/(\d+)/', function () {
        if (!hasPermission('access_person_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $json_url) {

        if ($app->req->isPost()) {
            try {
                $nae = $app->db->person();
                foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                    $nae->$k = $v;
                }
                $nae->where('personID = ?', $id);

                /**
                 * Fires before person record is updated.
                 *
                 * @since 6.1.07
                 * @param object $nae Name and address object.
                 */
                $app->hook->do_action('pre_update_person', $nae);

                if ($nae->update()) {
                    $email = $app->db->address();
                    $email->email1 = $app->req->post['email'];
                    $email->where('personID = ?', $id)
                        ->update();

                    etsis_logger_activity_log_write('Update Record', 'Person (NAE)', get_name($id), get_persondata('uname'));

                    /**
                     *
                     * @since 6.1.07
                     */
                    $person = get_person_by('personID', $id);
                    /**
                     * Fires after person record has been updated.
                     *
                     * @since 6.1.07
                     * @param array $person
                     *            Person data object.
                     */
                    $app->hook->do_action('post_update_person', $person);
                    etsis_cache_delete($id, 'stu');
                    etsis_cache_delete($id, 'person');

                    _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                } else {
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->error($e->getMessage());
            }
        }

        $json = _file_get_contents($json_url . 'person/personID/' . $id . '/?key=' . _h(get_option('api_key')));
        $decode = json_decode($json, true);

        try {
            $staff = $app->db->staff()
                ->where('staffID = ?', $id)
                ->findOne();

            $appl = $app->db->application()
                ->where('personID = ?', $id)
                ->findOne();

            $addr = $app->db->address()
                ->where('addressType = "P"')->_and_()
                ->where('endDate = "0000-00-00"')->_and_()
                ->where('addressStatus = "C"')->_and_()
                ->where('personID = ?', $id);

            $q = $addr->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['personID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('gridforms');
            etsis_register_script('select2');
            etsis_register_script('select');
            etsis_register_script('datepicker');
            etsis_register_script('gridforms');

            $app->view->display('person/view', [
                'title' => get_name($decode[0]['personID']),
                'nae' => $decode,
                'addr' => $q,
                'staff' => $staff,
                'appl' => $appl
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/add/', function () {
        if (!hasPermission('add_person')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/add/', function () use($app, $json_url) {

        $passSuffix = 'etSIS*';

        if ($app->req->isPost()) {
            try {
                $dob = str_replace('-', '', $app->req->post['dob']);
                $ssn = str_replace('-', '', $app->req->post['ssn']);

                if ($app->req->post['ssn'] > 0) {
                    $password = etsis_hash_password((int) $ssn . $passSuffix);
                } elseif (!empty($app->req->post['dob'])) {
                    $password = etsis_hash_password((int) $dob . $passSuffix);
                } else {
                    $password = etsis_hash_password('myaccount' . $passSuffix);
                }

                $nae = $app->db->person();
                $nae->uname = $app->req->post['uname'];
                $nae->altID = $app->req->post['altID'];
                $nae->personType = $app->req->post['personType'];
                $nae->prefix = $app->req->post['prefix'];
                $nae->fname = $app->req->post['fname'];
                $nae->lname = $app->req->post['lname'];
                $nae->mname = $app->req->post['mname'];
                $nae->email = $app->req->post['email'];
                $nae->ssn = $app->req->post['ssn'];
                $nae->veteran = $app->req->post['veteran'];
                $nae->ethnicity = $app->req->post['ethnicity'];
                $nae->dob = $app->req->post['dob'];
                $nae->gender = $app->req->post['gender'];
                $nae->emergency_contact = $app->req->post['emergency_contact'];
                $nae->emergency_contact_phone = $app->req->post['emergency_contact_phone'];
                $nae->status = "A";
                $nae->approvedBy = get_persondata('personID');
                $nae->approvedDate = $app->db->NOW();
                $nae->password = $password;

                /**
                 * Fires before person record is created.
                 *
                 * @since 6.1.07
                 */
                $app->hook->do_action('pre_save_person');

                /**
                 * Fires during the saving/creating of an person record.
                 *
                 * @since 6.1.10
                 * @param array $nae
                 *            Person data object.
                 */
                $app->hook->do_action('save_person_db_table', $nae);

                if ($nae->save()) {
                    $ID = $nae->lastInsertId();

                    $role = $app->db->person_roles();
                    $role->personID = $ID;
                    $role->roleID = $app->req->post['roleID'];
                    $role->addDate = $app->db->NOW();
                    $role->save();

                    $addr = $app->db->address();
                    $addr->personID = $ID;
                    $addr->address1 = $app->req->post['address1'];
                    $addr->address2 = $app->req->post['address2'];
                    $addr->city = $app->req->post['city'];
                    $addr->state = $app->req->post['state'];
                    $addr->zip = $app->req->post['zip'];
                    $addr->country = $app->req->post['country'];
                    $addr->addressType = "P";
                    $addr->addressStatus = "C";
                    $addr->startDate = $addr->NOW();
                    $addr->addDate = $addr->NOW();
                    $addr->addedBy = get_persondata('personID');
                    $addr->phone1 = $app->req->post['phone'];
                    $addr->email1 = $app->req->post['email'];

                    if (isset($app->req->post['sendemail']) && $app->req->post['sendemail'] == 'send') {
                        if ($app->req->post['ssn'] > 0) {
                            $pass = (int) $ssn . $passSuffix;
                        } elseif (!empty($app->req->post['dob'])) {
                            $pass = (int) $dob . $passSuffix;
                        } else {
                            $pass = 'myaccount' . $passSuffix;
                        }

                        try {
                            Node::dispense('login_details');
                            $node = Node::table('login_details');
                            $node->uname = (string) $app->req->post['uname'];
                            $node->email = (string) $app->req->post['email'];
                            $node->personid = (int) $ID;
                            $node->fname = (string) $app->req->post['fname'];
                            $node->lname = (string) $app->req->post['lname'];
                            $node->password = (string) $pass;
                            $node->altid = (string) $app->req->post['altID'];
                            $node->sent = (int) 0;
                            $node->save();
                        } catch (NodeQException $e) {
                            Cascade\Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                        }
                    }
                    if ($addr->save()) {

                        /**
                         * Fires after person record has been created.
                         *
                         * @since 6.1.07
                         * @param string $pass
                         *            Plaintext password.
                         * @param array $nae
                         *            Person data object.
                         */
                        $app->hook->do_action_array('post_save_person', [
                            $pass,
                            $nae
                        ]);

                        etsis_logger_activity_log_write('New Record', 'Name and Address', get_name($ID), get_persondata('uname'));
                        _etsis_flash()->success(_t('200 - Success: Ok. If checked `Send username & password to the user`, email has been sent to the queue.'), get_base_url() . 'nae' . '/' . $ID . '/');
                    } else {
                        _etsis_flash()->error(_etsis_flash()->notice(409));
                    }
                } else {
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->error($e->getMessage());
            }
        }

        etsis_register_style('form');
        etsis_register_style('gridforms');
        etsis_register_script('select2');
        etsis_register_script('select');
        etsis_register_script('datepicker');
        etsis_register_script('gridforms');

        $app->view->display('person/add', [
            'title' => 'Name and Address'
        ]);
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/adsu/(\d+)/', function () {
        if (!hasPermission('access_person_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->get('/adsu/(\d+)/', function ($id) use($app, $json_url) {

        try {
            $staff = $app->db->staff()
                ->where('staffID = ?', $id)
                ->findOne();

            $adsu = $app->db->person()
                ->setTableAlias('a')
                ->select('a.personID,a.fname,a.lname,a.mname')
                ->select('b.addressID,b.address1,b.address2,b.city')
                ->select('b.state,b.zip,b.addressType,b.addressStatus')
                ->_join('address', 'a.personID = b.personID', 'b')
                ->where('a.personID = ?', $id)->_and_()
                ->where('b.personID <> "NULL"');

            $q = $adsu->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($q == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($q[0]['personID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('person/adsu', [
                'title' => get_name($id),
                'nae' => $q,
                'staff' => $staff
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/addr-form/(\d+)/', function () {
        if (!hasPermission('add_address')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/addr-form/(\d+)/', function ($id) use($app, $json_url) {

        $json = _file_get_contents($json_url . 'person/personID/' . $id . '/?key=' . _h(get_option('api_key')));
        $decode = json_decode($json, true);

        try {
            $staff = $app->db->staff()
                ->where('staffID = ?', $id)
                ->findOne();
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }

        if ($app->req->isPost()) {
            try {
                $addr = $app->db->address();
                $addr->personID = $decode[0]['personID'];
                $addr->address1 = $app->req->post['address1'];
                $addr->address2 = $app->req->post['address2'];
                $addr->city = $app->req->post['city'];
                $addr->state = $app->req->post['state'];
                $addr->zip = $app->req->post['zip'];
                $addr->country = $app->req->post['country'];
                $addr->addressType = $app->req->post['addressType'];
                $addr->startDate = $app->req->post['startDate'];
                $addr->endDate = $app->req->post['endDate'];
                $addr->addressStatus = $app->req->post['addressStatus'];
                $addr->phone1 = $app->req->post['phone1'];
                $addr->phone2 = $app->req->post['phone2'];
                $addr->ext1 = $app->req->post['ext1'];
                $addr->ext2 = $app->req->post['ext2'];
                $addr->phoneType1 = $app->req->post['phoneType1'];
                $addr->phoneType2 = $app->req->post['phoneType2'];
                $addr->email1 = $app->req->post['email1'];
                $addr->email2 = $app->req->post['email2'];
                $addr->addDate = $addr->NOW();
                $addr->addedBy = get_persondata('personID');

                if ($addr->save()) {
                    $ID = $addr->lastInsertId();
                    etsis_logger_activity_log_write('New Record', 'Address', get_name($decode[0]['personID']), get_persondata('uname'));
                    etsis_cache_delete($id, 'stu');
                    etsis_cache_delete($id, 'person');
                    _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'nae/addr' . '/' . $ID . '/');
                } else {
                    _etsis_flash()->error(_etsis_flash()->notice(409));
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
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['personID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('gridforms');
            etsis_register_script('select2');
            etsis_register_script('select');
            etsis_register_script('datepicker');
            etsis_register_script('gridforms');

            $app->view->display('person/addr-form', [
                'title' => get_name($id),
                'nae' => $decode,
                'staff' => $staff
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/addr/(\d+)/', function () {
        if (!hasPermission('access_person_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/addr/(\d+)/', function ($id) use($app, $json_url) {

        $json_a = _file_get_contents($json_url . 'address/addressID/' . $id . '/?key=' . _h(get_option('api_key')));
        $a_decode = json_decode($json_a, true);

        $json_p = _file_get_contents($json_url . 'person/personID/' . $a_decode[0]['personID'] . '/?key=' . _h(get_option('api_key')));
        $p_decode = json_decode($json_p, true);

        try {
            $staff = $app->db->staff()
                ->where('staffID = ?', $id)
                ->findOne();
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }

        if ($app->req->isPost()) {
            try {
                $addr = $app->db->address();
                foreach ($_POST as $k => $v) {
                    $addr->$k = $v;
                }
                $addr->where('addressID = ?', $id);
                if ($addr->update()) {
                    etsis_logger_activity_log_write('Update Record', 'Address', get_name($a_decode[0]['personID']), get_persondata('uname'));
                    _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
                } else {
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                }
                etsis_cache_delete($a_decode[0]['personID'], 'stu');
                etsis_cache_delete($a_decode[0]['personID'], 'person');
            } catch (NotFoundException $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->error($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->error($e->getMessage());
            }
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($a_decode == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($a_decode) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($a_decode[0]['addressID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('gridforms');
            etsis_register_script('select2');
            etsis_register_script('select');
            etsis_register_script('datepicker');
            etsis_register_script('gridforms');

            $app->view->display('person/addr', [
                'title' => get_name($a_decode[0]['personID']),
                'addr' => $a_decode,
                'nae' => $p_decode,
                'staff' => $staff
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/role/(\d+)/', function () {
        if (!hasPermission('access_user_role_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/role/(\d+)/', function ($id) use($app, $json_url) {

        $json = _file_get_contents($json_url . 'person/personID/' . $id . '/?key=' . _h(get_option('api_key')));
        $decode = json_decode($json, true);

        try {
            $staff = $app->db->staff()
                ->where('staffID = ?', $id)
                ->findOne();
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }

        if ($app->req->isPost()) {
            try {
                foreach ($_POST as $k => $v) {
                    if (substr($k, 0, 5) == "role_") {
                        $roleID = str_replace("role_", "", $k);
                        if ($v == '0' || $v == 'x') {
                            $strSQL = sprintf("DELETE FROM `person_roles` WHERE `personID` = %u AND `roleID` = %u", $id, $roleID);
                        } else {
                            $strSQL = sprintf("REPLACE INTO `person_roles` SET `personID` = %u, `roleID` = %u, `addDate` = '%s'", $id, $roleID, $app->db->NOW());
                        }
                        $q = $app->db->query($strSQL);
                    }
                }
                if ($q) {
                    _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'nae/role' . '/' . $id . '/');
                } else {
                    _etsis_flash()->error(_etsis_flash()->notice(409));
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
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['personID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('person/role', [
                'title' => get_name($id),
                'nae' => $decode,
                'staff' => $staff
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/perms/(\d+)/', function () {
        if (!hasPermission('access_user_permission_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/perms/(\d+)/', function ($id) use($app, $json_url) {

        $json = _file_get_contents($json_url . 'person/personID/' . $id . '/?key=' . _h(get_option('api_key')));
        $decode = json_decode($json, true);

        try {
            $staff = $app->db->staff()
                ->where('staffID = ?', $id)
                ->findOne();
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage());
        }


        if ($app->req->isPost()) {
            try {
                if (count($app->req->post['permission']) > 0) {
                    $q = $app->db->query(sprintf("REPLACE INTO person_perms SET personID = %u, permission = '%s'", $id, maybe_serialize($app->req->post['permission'])));
                } else {
                    $q = $app->db->query(sprintf("DELETE FROM person_perms WHERE personID = %u", $id));
                }
                if ($q) {
                    _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'nae/perms' . '/' . $id . '/');
                } else {
                    _etsis_flash()->error(_etsis_flash()->notice(409));
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
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['personID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

             etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');
            
            $app->view->display('person/perms', [
                'title' => get_name($id),
                'nae' => $decode,
                'staff' => $staff
            ]);
        }
    });

    $app->match('GET|POST', '/usernameCheck/', function () {
        $uname = get_person_by('uname', $app->req->post['uname']);

        if ($uname->uname == $app->req->post['uname']) {
            echo '1';
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/resetPassword/(\d+)/', function () {
        if (!hasPermission('reset_person_password')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->get('/resetPassword/(\d+)/', function ($id) use($app) {

        $passSuffix = 'etSIS*';

        $person = get_person_by('personID', $id);

        $dob = str_replace('-', '', $person->dob);
        $ssn = str_replace('-', '', $person->ssn);
        if ($ssn > 0) {
            $pass = $ssn . $passSuffix;
        } elseif ($person->dob > '0000-00-00') {
            $pass = $dob . $passSuffix;
        } else {
            $pass = 'myaccount' . $passSuffix;
        }

        try {
            Node::dispense('reset_password');
            $node = Node::table('reset_password');
            $node->uname = (string) _h($person->uname);
            $node->email = (string) _h($person->email);
            $node->name = (string) get_name(_h($person->personID));
            $node->personid = (int) _h($person->personID);
            $node->fname = (string) _h($person->fname);
            $node->lname = (string) _h($person->lname);
            $node->password = (string) $pass;
            $node->sent = (int) 0;
            $node->save();
        } catch (NodeQException $e) {
            Cascade\Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }


        $password = etsis_hash_password($pass);

        try {
            $q2 = $app->db->person();
            $q2->password = $password;
            $q2->where('personID = ?', $id);
            if ($q2->update()) {
                /**
                 *
                 * @since 6.1.07
                 */
                $pass = [];
                $pass['pass'] = $pass;
                $pass['personID'] = $id;
                $pass['uname'] = $person->uname;
                $pass['fname'] = $person->fname;
                $pass['lname'] = $person->lname;
                $pass['email'] = $person->email;
                /**
                 * Fires after successful reset of person's password.
                 *
                 * @since 6.1.07
                 * @param array $pass
                 *            Plaintext password.
                 * @param string $uname
                 *            Person's username
                 */
                $app->hook->do_action('post_reset_password', $pass);

                etsis_desktop_notify(_t('Reset Password'), _t('Password reset; new email sent to queue.'), 'false');
                etsis_logger_activity_log_write(_t('Update Record'), _t('Reset Password'), get_name($id), get_persondata('uname'));
                redirect($app->req->server['HTTP_REFERER']);
            } else {
                _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
            }
        } catch (NotFoundException $e) {
            _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (Exception $e) {
            _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
        } catch (ORMException $e) {
            _etsis_flash()->error($e->getMessage(), $app->req->server['HTTP_REFERER']);
        }
    });
});

$app->setError(function () use($app) {

    $app->view->display('error/404', [
        'title' => '404 Error'
    ]);
});
