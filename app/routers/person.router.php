<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\NodeQ\etsis_NodeQ as Node;
use app\src\Core\NodeQ\NodeQException;
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

/**
 * Person Router
 *
 * @license GPLv3
 *         
 * @since 5.0.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
$app->before('GET|POST', '/nae(.*)', function () {
    if (!is_user_logged_in()) {
        _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
        exit();
    }

    if (!hasPermission('access_person_screen')) {
        _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        exit();
    }
});

$app->group('/nae', function () use($app) {

    $app->match('GET|POST', '/', function () use($app) {

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
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
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
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app) {

        if ($app->req->isPost()) {
            try {
                $nae = $app->db->person();
                $nae->altID = if_null($app->req->post['altID']);
                $nae->personType = $app->req->post['personType'];
                $nae->prefix = $app->req->post['prefix'];
                $nae->fname = $app->req->post['fname'];
                $nae->lname = $app->req->post['lname'];
                $nae->mname = $app->req->post['mname'];
                $nae->email = $app->req->post['email'];
                $nae->ssn = $app->req->post['ssn'];
                $nae->veteran = $app->req->post['veteran'];
                $nae->ethnicity = $app->req->post['ethnicity'];
                $nae->dob = if_null($app->req->post['dob']);
                $nae->gender = $app->req->post['gender'];
                $nae->emergency_contact = $app->req->post['emergency_contact'];
                $nae->emergency_contact_phone = $app->req->post['emergency_contact_phone'];
                $nae->status = $app->req->post['status'];
                $nae->tags = if_null($app->req->post['tags']);
                $nae->where('personID = ?', (int) $id);

                /**
                 * Fires before person record is updated.
                 *
                 * @since 6.1.07
                 * @param object $nae Name and address object.
                 */
                $app->hook->do_action('pre_update_person', $nae);
                $nae->update();

                $email = $app->db->address();
                $email->set([
                        'email1' => $app->req->post['email']
                    ])
                    ->where('personID = ?', (int) $id)
                    ->update();

                etsis_logger_activity_log_write('Update Record', 'Person (NAE)', get_name($id), get_persondata('uname'));

                /**
                 *
                 * @since 6.1.07
                 */
                $person = get_person_by('personID', (int) $id);
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
            } catch (NotFoundException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            }
        }

        try {
            $person = $app->db->person()
                ->where('personID = ?', (int) $id);
            $sql = $person->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            $staff = $app->db->staff()
                ->where('staffID = ?', (int) $id)
                ->findOne();

            $appl = $app->db->application()
                ->where('personID = ?', (int) $id)
                ->findOne();

            $addr = $app->db->address()
                ->where('addressType = "P"')->_and_()
                ->where('addressStatus = "C"')->_and_()
                ->where('personID = ?', (int) $id)->_and_()
                ->where('endDate IS NULL');

            $q = $addr->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            $login = $app->db->last_login()
                ->where('personID = ?', $id)
                ->orderBy('loginTimeStamp', 'DESC')
                ->findOne();
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($sql == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($sql) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_escape($sql[0]['personID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('selectize');
            etsis_register_style('gridforms');
            etsis_register_script('datepicker');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('gridforms');

            $app->view->display('person/view', [
                'title' => get_name(_escape($sql[0]['personID'])),
                'nae' => $sql,
                'addr' => $q,
                'staff' => $staff,
                'appl' => $appl,
                'login' => $login
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/perc/(\d+)/', function () {
        if (!hasPermission('access_person_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/perc/(\d+)/', function ($id) use($app) {

        if ($app->req->isPost()) {
            try {
                if (isset($app->req->post['id'])) {
                    $size = count($app->req->post['id']);
                    $i = 0;
                    while ($i < $size) {
                        $perc = $app->db->perc();
                        $perc->set([
                                'code' => $app->req->post['code'][$i],
                                'severity' => $app->req->post['severity'][$i],
                                'startDate' => $app->req->post['startDate'][$i],
                                'endDate' => if_null($app->req->post['endDate'][$i]),
                                'comment' => $app->req->post['comment'][$i]
                            ])
                            ->where('personID = ?', $id)->_and_()
                            ->where('id = ?', $app->req->post['id'][$i])
                            ->update();
                        ++$i;
                    }
                    etsis_logger_activity_log_write('Update Record', 'Person Restriction (PERC)', get_name($id), get_persondata('uname'));
                } else {
                    $perc = $app->db->perc();
                    foreach ($app->req->post as $k => $v) {
                        $perc->$k = $v;
                    }
                    $perc->save();
                    etsis_logger_activity_log_write('New Record', 'Person Restriction (PERC)', get_name($id), get_persondata('uname'));
                }
                etsis_cache_delete($id, 'person');
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
            } catch (NotFoundException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            }
        }

        try {
            $nae = $app->db->person()
                ->select('personID,altID')
                ->where('personID = ?', $id);
            $q1 = $nae->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            $perc = $app->db->perc()
                ->select('perc.*,rest.deptCode')
                ->_join('rest', 'perc.code = rest.code')
                ->where('perc.personID = ?', $id)
                ->orderBy('perc.id');
            $q2 = $perc->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            $staff = $app->db->staff()
                ->where('staffID = ?', (int) $id)
                ->findOne();
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($nae == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($nae) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_escape($q1[0]['personID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_style('table');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datatables');

            $app->view->display('person/perc', [
                'title' => get_name($id),
                'perc' => $q2,
                'nae' => $q1,
                'staff' => $staff
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/add/', function () {
        if (!hasPermission('add_person')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/add/', function () use($app) {

        $passSuffix = 'etSIS*';

        if ($app->req->isPost()) {
            try {
                $dob = str_replace(['-', '_', '/', '.'], '', $app->req->post['dob']);
                $ssn = str_replace(['-', '_', '.'], '', $app->req->post['ssn']);

                if ($app->req->post['ssn'] > 0) {
                    $password = $ssn . $passSuffix;
                } elseif (!empty($app->req->post['dob'])) {
                    $password = $dob . $passSuffix;
                } else {
                    $password = 'myaccount' . $passSuffix;
                }

                $nae = $app->db->person();
                $nae->uname = $app->req->post['uname'];
                $nae->altID = if_null($app->req->post['altID']);
                $nae->personType = $app->req->post['personType'];
                $nae->prefix = $app->req->post['prefix'];
                $nae->fname = $app->req->post['fname'];
                $nae->lname = $app->req->post['lname'];
                $nae->mname = $app->req->post['mname'];
                $nae->email = $app->req->post['email'];
                $nae->ssn = $app->req->post['ssn'];
                $nae->veteran = $app->req->post['veteran'];
                $nae->ethnicity = $app->req->post['ethnicity'];
                $nae->dob = if_null($app->req->post['dob']);
                $nae->gender = $app->req->post['gender'];
                $nae->emergency_contact = $app->req->post['emergency_contact'];
                $nae->emergency_contact_phone = $app->req->post['emergency_contact_phone'];
                $nae->status = "A";
                $nae->tags = if_null($app->req->post['tags']);
                $nae->approvedBy = get_persondata('personID');
                $nae->approvedDate = \Jenssegers\Date\Date::now();
                $nae->password = etsis_hash_password($password);

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

                $nae->save();

                $_id = $nae->lastInsertId();

                $role = $app->db->person_roles();
                $role->insert([
                    'personID' => (int) $_id,
                    'roleID' => $app->req->post['roleID'],
                    'addDate' => \Jenssegers\Date\Date::now()
                ]);

                $addr = $app->db->address();
                $addr->personID = (int) $_id;
                $addr->address1 = $app->req->post['address1'];
                $addr->address2 = $app->req->post['address2'];
                $addr->city = $app->req->post['city'];
                $addr->state = $app->req->post['state'];
                $addr->zip = $app->req->post['zip'];
                $addr->country = $app->req->post['country'];
                $addr->addressType = "P";
                $addr->addressStatus = "C";
                $addr->startDate = \Jenssegers\Date\Date::now();
                $addr->addDate = \Jenssegers\Date\Date::now();
                $addr->endDate = NULL;
                $addr->addedBy = get_persondata('personID');
                $addr->phone1 = $app->req->post['phone'];
                $addr->email1 = $app->req->post['email'];

                if (isset($app->req->post['sendemail']) && $app->req->post['sendemail'] == 'send') {

                    try {
                        Node::dispense('login_details');
                        $node = Node::table('login_details');
                        $node->uname = (string) $app->req->post['uname'];
                        $node->email = (string) $app->req->post['email'];
                        $node->personid = (int) $_id;
                        $node->fname = (string) $app->req->post['fname'];
                        $node->lname = (string) $app->req->post['lname'];
                        $node->password = (string) $password;
                        $node->altid = (string) $app->req->post['altID'];
                        $node->sent = (int) 0;
                        $node->save();
                    } catch (NodeQException $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    } catch (Exception $e) {
                        Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
                    }
                }

                $addr->save();

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
                    $password,
                    $nae
                ]);

                etsis_logger_activity_log_write('New Record', 'Name and Address', get_name($_id), get_persondata('uname'));
                _etsis_flash()->success(_t('200 - Success: Ok. If checked `Send username & password to the user`, email has been sent to the queue.'), get_base_url() . 'nae' . '/' . (int) $_id . '/');
            } catch (NotFoundException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            }
        }

        etsis_register_style('form');
        etsis_register_style('selectize');
        etsis_register_style('gridforms');
        etsis_register_script('datepicker');
        etsis_register_script('select2');
        etsis_register_script('select');
        etsis_register_script('gridforms');

        $app->view->display('person/add', [
            'title' => 'Name and Address'
        ]);
    });

    $app->get('/adsu/(\d+)/', function ($id) use($app) {

        try {
            $staff = $app->db->staff()
                ->where('staffID = ?', (int) $id)
                ->findOne();

            $adsu = $app->db->person()
                ->setTableAlias('a')
                ->select('a.personID,a.fname,a.lname,a.mname,a.altID')
                ->select('b.id,b.address1,b.address2,b.city')
                ->select('b.state,b.zip,b.addressType,b.addressStatus')
                ->_join('address', 'a.personID = b.personID', 'b')
                ->where('a.personID = ?', (int) $id)->_and_()
                ->where('b.personID <> "NULL"');

            $q = $adsu->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
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
         */ elseif (_escape($q[0]['personID']) <= 0) {

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
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/addr-form/(\d+)/', function ($id) use($app) {

        try {
            $person = $app->db->person()
                ->where('personID = ?', (int) $id);
            $sql = $person->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            $staff = $app->db->staff()
                ->where('staffID = ?', (int) $id)
                ->findOne();
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        if ($app->req->isPost()) {
            try {
                $addr = $app->db->address();
                $addr->personID = (int) _escape($sql[0]['personID']);
                $addr->address1 = $app->req->post['address1'];
                $addr->address2 = $app->req->post['address2'];
                $addr->city = $app->req->post['city'];
                $addr->state = $app->req->post['state'];
                $addr->zip = $app->req->post['zip'];
                $addr->country = $app->req->post['country'];
                $addr->addressType = $app->req->post['addressType'];
                $addr->startDate = $app->req->post['startDate'];
                $addr->endDate = if_null($app->req->post['endDate']);
                $addr->addressStatus = $app->req->post['addressStatus'];
                $addr->phone1 = $app->req->post['phone1'];
                $addr->phone2 = $app->req->post['phone2'];
                $addr->ext1 = $app->req->post['ext1'];
                $addr->ext2 = $app->req->post['ext2'];
                $addr->phoneType1 = $app->req->post['phoneType1'];
                $addr->phoneType2 = $app->req->post['phoneType2'];
                $addr->email1 = $app->req->post['email1'];
                $addr->email2 = $app->req->post['email2'];
                $addr->addDate = \Jenssegers\Date\Date::now();
                $addr->addedBy = get_persondata('personID');
                $addr->save();

                $_id = $addr->lastInsertId();
                etsis_logger_activity_log_write('New Record', 'Address', get_name(_escape($sql[0]['personID'])), get_persondata('uname'));
                etsis_cache_delete($id, 'stu');
                etsis_cache_delete($id, 'person');
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'nae/addr' . '/' . (int) $_id . '/');
            } catch (NotFoundException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            }
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($sql == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($sql) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_escape($sql[0]['personID']) <= 0) {

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
                'nae' => $sql,
                'staff' => $staff
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/addr/(\d+)/', function () {
        if (!hasPermission('access_person_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/addr/(\d+)/', function ($id) use($app) {

        try {
            $addr = $app->db->address()
                ->where('id = ?', (int) $id);
            $q = $addr->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            $person = $app->db->person()
                ->where('personID = ?', (int) _escape($q[0]['personID']));
            $sql = $person->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            $staff = $app->db->staff()
                ->where('staffID = ?', (int) $id)
                ->findOne();
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        if ($app->req->isPost()) {
            try {
                $addr = $app->db->address();
                $addr->address1 = $app->req->post['address1'];
                $addr->address2 = $app->req->post['address2'];
                $addr->city = $app->req->post['city'];
                $addr->state = $app->req->post['state'];
                $addr->zip = $app->req->post['zip'];
                $addr->country = $app->req->post['country'];
                $addr->addressType = $app->req->post['addressType'];
                $addr->startDate = $app->req->post['startDate'];
                $addr->endDate = if_null($app->req->post['endDate']);
                $addr->addressStatus = $app->req->post['addressStatus'];
                $addr->phone1 = $app->req->post['phone1'];
                $addr->phone2 = $app->req->post['phone2'];
                $addr->ext1 = $app->req->post['ext1'];
                $addr->ext2 = $app->req->post['ext2'];
                $addr->phoneType1 = $app->req->post['phoneType1'];
                $addr->phoneType2 = $app->req->post['phoneType2'];
                $addr->email1 = $app->req->post['email1'];
                $addr->email2 = $app->req->post['email2'];
                $addr->where('id = ?', (int) $id);
                $addr->update();

                etsis_logger_activity_log_write('Update Record', 'Address', get_name($q[0]['personID']), get_persondata('uname'));
                etsis_cache_delete($q[0]['personID'], 'stu');
                etsis_cache_delete($q[0]['personID'], 'person');
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
            } catch (NotFoundException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            }
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
         */ elseif (_escape($q[0]['id']) <= 0) {

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
                'title' => get_name(_escape($q[0]['personID'])),
                'addr' => $q,
                'nae' => $sql,
                'staff' => $staff
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/role/(\d+)/', function () {
        if (!hasPermission('access_user_role_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/role/(\d+)/', function ($id) use($app) {

        try {
            $person = $app->db->person()
                ->where('personID = ?', (int) $id);
            $sql = $person->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            $staff = $app->db->staff()
                ->where('staffID = ?', (int) $id)
                ->findOne();
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        if ($app->req->isPost()) {
            try {
                foreach ($app->req->post as $k => $v) {
                    if (substr($k, 0, 5) == "role_") {
                        $roleID = str_replace("role_", "", $k);
                        if ($v == '0' || $v == 'x') {
                            $strSQL = sprintf("DELETE FROM `person_roles` WHERE `personID` = %u AND `roleID` = %u", $id, $roleID);
                        } else {
                            $strSQL = sprintf("REPLACE INTO `person_roles` SET `personID` = %u, `roleID` = %u, `addDate` = '%s'", $id, $roleID, \Jenssegers\Date\Date::now());
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
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            }
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($sql == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($sql) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_escape($sql[0]['personID']) <= 0) {

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
                'nae' => $sql,
                'staff' => $staff
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/perms/(\d+)/', function () {
        if (!hasPermission('access_user_permission_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/perms/(\d+)/', function ($id) use($app) {

        try {
            $person = $app->db->person()
                ->where('personID = ?', (int) $id);
            $sql = $person->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            $staff = $app->db->staff()
                ->where('staffID = ?', (int) $id)
                ->findOne();
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }

        if ($app->req->isPost()) {
            try {
                if (count($app->req->post['permission']) > 0) {
                    $q = $app->db->query(sprintf("REPLACE INTO person_perms SET personID = %u, permission = '%s'", $id, maybe_serialize($app->req->post['permission'])));
                } else {
                    $q = $app->db->query(sprintf("DELETE FROM person_perms WHERE personID = %u", $id));
                }
                if ($q) {
                    _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'nae/perms' . '/' . (int) $id . '/');
                } else {
                    _etsis_flash()->error(_etsis_flash()->notice(409));
                }
            } catch (NotFoundException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409));
            }
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($sql == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($sql) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (_escape($sql[0]['personID']) <= 0) {

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
                'nae' => $sql,
                'staff' => $staff
            ]);
        }
    });

    $app->match('GET|POST', '/usernameCheck/', function () use($app) {
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
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
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
            $node->uname = (string) _escape($person->uname);
            $node->email = (string) _escape($person->email);
            $node->name = (string) get_name(_escape($person->personID));
            $node->personid = (int) _escape($person->personID);
            $node->fname = (string) _escape($person->fname);
            $node->lname = (string) _escape($person->lname);
            $node->password = (string) $pass;
            $node->sent = (int) 0;
            $node->save();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }

        $password = etsis_hash_password($pass);

        try {
            $q2 = $app->db->person();
            $q2->set([
                    'password' => $password
                ])
                ->where('personID = ?', (int) $id)
                ->update();

            /**
             *
             * @since 6.1.07
             */
            $pass = [];
            $pass['pass'] = $pass;
            $pass['personID'] = (int) $id;
            $pass['uname'] = _escape($person->uname);
            $pass['fname'] = _escape($person->fname);
            $pass['lname'] = _escape($person->lname);
            $pass['email'] = _escape($person->email);
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
            _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
        } catch (NotFoundException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (ORMException $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error($e->getMessage());
            _etsis_flash()->error(_etsis_flash()->notice(409));
        }
    });
});

$app->setError(function () use($app) {

    $app->view->display('error/404', [
        'title' => '404 Error'
    ]);
});
