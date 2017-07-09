<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

/**
 * Application Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
/**
 * Before router check.
 */
$app->before('GET|POST', '/appl/(.*)', function() {
    if (!is_user_logged_in()) {
        _etsis_flash()->error(_t('401 - Error: Unauthorized.'), get_base_url() . 'login' . '/');
    }
});

$app->group('/appl', function () use($app) {

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/', function() {
        if (!hasPermission('access_application_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                $post = $app->req->post['appl'];
                $appl = $app->db->application()
                    ->setTableAlias('a')
                    ->select('a.id,a.personID,b.termName,c.altID,c.fname,c.lname,c.uname,c.email')
                    ->_join('term', 'a.startTerm = b.termCode', 'b')
                    ->_join('person', 'a.personID = c.personID', 'c')
                    ->whereLike('CONCAT(c.fname," ",c.lname)', "%$post%")->_or_()
                    ->whereLike('CONCAT(c.lname," ",c.fname)', "%$post%")->_or_()
                    ->whereLike('CONCAT(c.lname,", ",c.fname)', "%$post%")->_or_()
                    ->whereLike('c.fname', "%$post%")->_or_()
                    ->whereLike('c.lname', "%$post%")->_or_()
                    ->whereLike('c.uname', "%$post%")->_or_()
                    ->whereLike('a.personID', "%$post%")->_or_()
                    ->whereLike('c.altID', "%$post%");
                $q = $appl->find(function($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                $spro = $app->db->student()->where('stuID = ?', _h($q[0]['personID']))->findOne();
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

        $app->view->display('application/index', [
            'title' => 'Application Search',
            'search' => $q,
            'appl' => $spro
            ]
        );
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/(\d+)/', function() {
        if (!hasPermission('access_application_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->get('/(\d+)/', function ($id) use($app) {

        try {
            $appl = $app->db->application()
                ->setTableAlias('a')
                ->select('a.*,b.fname,b.mname,b.lname,b.dob,b.uname,b.altID')
                ->select('b.email,b.gender')
                ->_join('person', 'a.personID = b.personID', 'b')
                ->where('a.id = ?', $id)
                ->findOne();
            $addr = $app->db->address()
                ->setTableAlias('a')
                ->_join('application', 'a.personID = b.personID', 'b')
                ->where('b.id = ?', $id)->_and_()
                ->where('a.addressType = "P"')
                ->findOne();
            $inst = $app->db->institution_attended()
                ->setTableAlias('a')
                ->_join('application', 'a.personID = b.personID', 'b')
                ->where('b.id = ?', $id);
            $q3 = $inst->find(function($data) {
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
        if ($appl == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($appl) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($appl) <= 0) {

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
            etsis_register_script('datepicker');
            $app->view->display('application/view', [
                'title' => get_name(_h($appl->personID)),
                'appl' => $appl,
                'addr' => $addr,
                'inst' => $q3
                ]
            );
        }
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/editAppl/(\d+)/', function() {
        if (!hasPermission('create_application')) {
            _etsis_flash()->error(_t('Record update denied.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->post('/editAppl/(\d+)/', function ($id) use($app) {
        try {
            $appl = $app->db->application();
            $appl->acadProgCode = $app->req->post['acadProgCode'];
            $appl->startTerm = $app->req->post['startTerm'];
            $appl->PSAT_Verbal = $app->req->post['PSAT_Verbal'];
            $appl->PSAT_Math = $app->req->post['PSAT_Math'];
            $appl->SAT_Verbal = $app->req->post['SAT_Verbal'];
            $appl->SAT_Math = $app->req->post['SAT_Math'];
            $appl->ACT_English = $app->req->post['ACT_English'];
            $appl->ACT_Math = $app->req->post['ACT_Math'];
            $appl->applDate = $app->req->post['applDate'];
            $appl->appl_comments = $app->req->post['appl_comments'];
            $appl->staff_comments = $app->req->post['staff_comments'];
            $appl->applStatus = $app->req->post['applStatus'];
            $appl->acadProgCode = $app->req->post['acadProgCode'];
            $appl->acadProgCode = $app->req->post['acadProgCode'];
            $appl->where('id = ?', $app->req->post['id']);

            /**
             * Fires during the update of an application.
             *
             * @since 6.1.10
             * @param object $appl Application object.
             */
            $app->hook->do_action('update_application_db_table', $appl);
            $appl->update();

            $size = count($app->req->post['fice_ceeb']);
            $i = 0;
            while ($i < $size) {
                $inst = $app->db->institution_attended();
                $inst->fice_ceeb = $app->req->post['fice_ceeb'][$i];
                $inst->fromDate = $app->req->post['fromDate'][$i];
                $inst->toDate = $app->req->post['toDate'][$i];
                $inst->GPA = $app->req->post['GPA'][$i];
                $inst->major = $app->req->post['major'][$i];
                $inst->degree_awarded = $app->req->post['degree_awarded'][$i];
                $inst->degree_conferred_date = $app->req->post['degree_conferred_date'][$i];
                $inst->where('id = ?', $app->req->post['id'][$i])->_and_()
                    ->where('personID = ?', $app->req->post['personID']);
                $inst->update();
                ++$i;
            }
            etsis_logger_activity_log_write('Update Record', 'Application', get_name($app->req->post['personID']), get_persondata('uname'));
            _etsis_flash()->success(_etsis_flash()->notice(200));
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

        etsis_redirect(get_base_url() . 'appl' . '/' . $id . '/');
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/add/(\d+)/', function() {
        if (!hasPermission('create_application')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/add/(\d+)/', function ($id) use($app) {

        if ($app->req->isPost()) {
            try {
                $appl = $app->db->application();
                $appl->acadProgCode = _trim($app->req->post['acadProgCode']);
                $appl->startTerm = $app->req->post['startTerm'];
                $appl->PSAT_Verbal = $app->req->post['PSAT_Verbal'];
                $appl->PSAT_Math = $app->req->post['PSAT_Math'];
                $appl->SAT_Verbal = $app->req->post['SAT_Verbal'];
                $appl->SAT_Math = $app->req->post['SAT_Math'];
                $appl->ACT_English = $app->req->post['ACT_English'];
                $appl->ACT_MATH = $app->req->post['ACT_Math'];
                $appl->personID = $id;
                $appl->addDate = \Jenssegers\Date\Date::now();
                $appl->applDate = $app->req->post['applDate'];
                $appl->addedBy = get_persondata('personID');
                $appl->admitStatus = $app->req->post['admitStatus'];

                /**
                 * Fires during the saving/creating of an application.
                 *
                 * @since 6.1.10
                 * @param object $appl Application object.
                 */
                $app->hook->do_action('save_application_db_table', $appl);
                $appl->save();

                $_id = $appl->lastInsertId();
                etsis_logger_activity_log_write('New Record', 'Application', get_name($id), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'appl' . '/' . $_id . '/');
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
                ->where('personID = ?', $id)
                ->findOne();

            $address = $app->db->address()
                ->where('personID = ?', $id)->_and_()
                ->where('addressType = "P"')->_and_()
                ->where('addressStatus = "C"')->_and_()
                ->where('endDate IS NULL')->_or_()
                ->whereLte('endDate', '0000-00-00')
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
        if ($person == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($person) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($person) <= 0) {

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
            etsis_register_script('datepicker');

            $app->view->display('application/add', [
                'title' => 'Create Application',
                'person' => $person,
                'address' => $address
                ]
            );
        }
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/inst-attended/', function() {
        if (!hasPermission('access_application_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/inst-attended/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                $inst = $app->db->institution_attended();
                $inst->insert([
                    'fice_ceeb' => _trim((int) $app->req->post['fice_ceeb']),
                    'fromDate' => $app->req->post['fromDate'],
                    'toDate' => $app->req->post['toDate'],
                    'GPA' => $app->req->post['GPA'],
                    'personID' => $app->req->post['personID'],
                    'major' => $app->req->post['major'],
                    'degree_awarded' => $app->req->post['degree_awarded'],
                    'degree_conferred_date' => $app->req->post['degree_conferred_date'],
                    'addDate' => \Jenssegers\Date\Date::now(),
                    'addedBy' => get_persondata('personID')
                ]);

                etsis_logger_activity_log_write('New Record', 'Institution Attended', get_name($app->req->post['personID']), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'appl' . '/' . $app->req->post['personID'] . '/');
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
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datepicker');

        $app->view->display('application/inst-attended', [
            'title' => 'Institutions Attended'
            ]
        );
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/inst(.*)', function() {
        if (!hasPermission('access_application_screen')) {
            _etsis_flash()->error(_t('403 - Error: Forbidden.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->match('GET|POST', '/inst/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                $post = $app->req->post['inst'];
                $inst = $app->db->institution()
                    ->whereLike('instName', "%$post%")->_or_()
                    ->whereLike('fice_ceeb', "%$post%");
                $q = $inst->find(function($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
            } catch (NotFoundException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
            }
        }

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('application/inst', [
            'title' => 'Institution Search',
            'search' => $q
            ]
        );
    });

    $app->match('GET|POST', '/inst/add/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                $inst = $app->db->institution();
                $inst->insert([
                    'fice_ceeb' => _trim((int) $app->req->post['fice_ceeb']),
                    'instType' => $app->req->post['instType'],
                    'instName' => $app->req->post['instName'],
                    'city' => $app->req->post['city'],
                    'state' => $app->req->post['state'],
                    'country' => $app->req->post['country']
                ]);

                $_id = $inst->lastInsertId();
                etsis_logger_activity_log_write('New Record', 'Institution', $app->req->post['instName'], get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'appl/inst' . '/' . $_id . '/');
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
        etsis_register_script('select');
        etsis_register_script('select2');

        $app->view->display('application/add-inst', [
            'title' => 'Add Institution'
            ]
        );
    });

    $app->match('GET|POST', '/inst/(\d+)/', function ($id) use($app) {
        if ($app->req->isPost()) {
            try {
                $inst = $app->db->institution();
                foreach ($app->req->post as $k => $v) {
                    $inst->$k = $v;
                }
                $inst->where('id = ?', $id);
                $inst->update();

                etsis_logger_activity_log_write('Update Record', 'Institution', _filter_input_string(INPUT_POST, 'instName'), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
            } catch (NotFoundException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
            } catch (ORMException $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
            } catch (Exception $e) {
                Cascade::getLogger('error')->error($e->getMessage());
                _etsis_flash()->error(_etsis_flash()->notice(409), $app->req->server['HTTP_REFERER']);
            }
        }

        try {
            $inst = $app->db->institution()->where('id = ?', (int) $id)->findOne();
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

        etsis_register_style('form');
        etsis_register_script('select');
        etsis_register_script('select2');

        $app->view->display('application/view-inst', [
            'title' => _h($inst->instName),
            'inst' => $inst
            ]
        );
    });

    $app->get('/applications/', function () use($app) {

        try {
            $appl = $app->db->application()->where('personID = ?', (int) get_persondata('personID'));
            $q = $appl->find(function($data) {
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

        $css = [ 'css/admin/module.admin.page.alt.form_elements.min.css', 'css/admin/module.admin.page.alt.tables.min.css'];
        $js = [
            'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
            'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
            'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
            'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
            'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
            'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0',
            'components/modules/admin/forms/elements/bootstrap-timepicker/assets/lib/js/bootstrap-timepicker.js?v=v2.1.0',
            'components/modules/admin/forms/elements/bootstrap-timepicker/assets/custom/js/bootstrap-timepicker.init.js?v=v2.1.0'
        ];

        $app->view->display('application/appls', [
            'title' => 'My Applications',
            'cssArray' => $css,
            'jsArray' => $js,
            'appls' => $q
            ]
        );
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/applicantLookup/', function() {
        if (!hasPermission('access_application_screen')) {
            _etsis_flash()->error(_t('Permission denied.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->post('/applicantLookup/', function() use($app) {
        $appl = get_person_by('personID', $app->req->post['personID']);

        $json = [ 'input#person' => _h($appl->lname) . ', ' . _h($appl->fname)];

        echo json_encode($json);
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/deleteInstAttend/(\d+)/', function() {
        if (!hasPermission('delete_student')) {
            _etsis_flash()->error(_t('Permission denied to delete record.'), get_base_url() . 'dashboard' . '/');
            exit();
        }
    });

    $app->get('/deleteInstAttend/(\d+)/', function($id) use($app) {
        try {
            $inst = $app->db->institution_attended()->where('id = ?', $id);
            $inst->delete();
            _etsis_flash()->success(_etsis_flash()->notice(200));
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
        etsis_redirect($app->req->server['HTTP_REFERER']);
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});
