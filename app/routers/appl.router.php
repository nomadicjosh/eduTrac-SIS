<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;

/**
 * Application Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
$email = _etsis_email();

$app->group('/appl', function () use($app, $email) {

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/', function() {
        if (!hasPermission('access_application_screen')) {
            _etsis_flash()->{'error'}(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                $post = $_POST['appl'];
                $appl = $app->db->application()
                    ->setTableAlias('a')
                    ->select('a.applID,a.personID,b.termName,c.fname,c.lname,c.uname,c.email')
                    ->_join('term', 'a.startTerm = b.termCode', 'b')
                    ->_join('person', 'a.personID = c.personID', 'c')
                    ->whereLike('CONCAT(c.fname," ",c.lname)', "%$post%")->_or_()
                    ->whereLike('CONCAT(c.lname," ",c.fname)', "%$post%")->_or_()
                    ->whereLike('CONCAT(c.lname,", ",c.fname)', "%$post%")->_or_()
                    ->whereLike('c.fname', "%$post%")->_or_()
                    ->whereLike('c.lname', "%$post%")->_or_()
                    ->whereLike('c.uname', "%$post%")->_or_()
                    ->whereLike('a.personID', "%$post%");
                $q = $appl->find(function($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
                $spro = $app->db->student()->where('stuID = ?', $q[0]['personID'])->findOne();
            } catch (NotFoundException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
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
            _etsis_flash()->{'error'}(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->get('/(\d+)/', function ($id) use($app) {

        try {
            $appl = $app->db->application()
                ->setTableAlias('a')
                ->select('a.*,b.fname,b.mname,b.lname,b.dob,b.uname')
                ->select('b.email,b.gender')
                ->_join('person', 'a.personID = b.personID', 'b')
                ->where('a.applID = ?', $id)
                ->findOne();
            $addr = $app->db->address()
                ->setTableAlias('a')
                ->_join('application', 'a.personID = b.personID', 'b')
                ->where('b.applID = ?', $id)->_and_()
                ->where('a.addressType = "P"')
                ->findOne();
            $inst = $app->db->institution_attended()
                ->setTableAlias('a')
                ->_join('application', 'a.personID = b.personID', 'b')
                ->where('b.applID = ?', $id);
            $q3 = $inst->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
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
                'title' => get_name($appl->personID),
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
            _etsis_flash()->{'error'}(_t('Record update not allowed.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->post('/editAppl/(\d+)/', function ($id) use($app) {
        try {
            $appl = $app->db->application();
            $appl->acadProgCode = $_POST['acadProgCode'];
            $appl->startTerm = $_POST['startTerm'];
            $appl->PSAT_Verbal = $_POST['PSAT_Verbal'];
            $appl->PSAT_Math = $_POST['PSAT_Math'];
            $appl->SAT_Verbal = $_POST['SAT_Verbal'];
            $appl->SAT_Math = $_POST['SAT_Math'];
            $appl->ACT_English = $_POST['ACT_English'];
            $appl->ACT_Math = $_POST['ACT_Math'];
            $appl->applDate = $_POST['applDate'];
            $appl->appl_comments = $_POST['appl_comments'];
            $appl->staff_comments = $_POST['staff_comments'];
            $appl->applStatus = $_POST['applStatus'];
            $appl->acadProgCode = $_POST['acadProgCode'];
            $appl->acadProgCode = $_POST['acadProgCode'];
            $appl->where('applID = ?', $_POST['applID']);

            /**
             * Fires during the update of an application.
             *
             * @since 6.1.10
             * @param object $appl Application object.
             */
            $app->hook->do_action('update_application_db_table', $appl);

            if ($appl->update()) {
                etsis_logger_activity_log_write('Update Record', 'Application', get_name($_POST['personID']), get_persondata('uname'));
                _etsis_flash()->{'success'}(_etsis_flash()->notice(200));
            } else {
                _etsis_flash()->{'error'}(_etsis_flash()->notice(409));
            }

            $size = count($_POST['fice_ceeb']);
            $i = 0;
            while ($i < $size) {
                $inst = $app->db->institution_attended();
                $inst->fice_ceeb = $_POST['fice_ceeb'][$i];
                $inst->fromDate = $_POST['fromDate'][$i];
                $inst->toDate = $_POST['toDate'][$i];
                $inst->GPA = $_POST['GPA'][$i];
                $inst->major = $_POST['major'][$i];
                $inst->degree_awarded = $_POST['degree_awarded'][$i];
                $inst->degree_conferred_date = $_POST['degree_conferred_date'][$i];
                $inst->where('instAttID = ?', $_POST['instAttID'][$i])->_and_()
                    ->where('personID = ?', $_POST['personID']);
                if ($inst->update()) {
                    _etsis_flash()->{'success'}(_etsis_flash()->notice(200));
                } else {
                    _etsis_flash()->{'error'}(_etsis_flash()->notice(409));
                }
                ++$i;
            }
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        }

        redirect(get_base_url() . 'appl' . '/' . $id . '/');
    });

    /**
     * Before router check.
     */
    $app->before('GET|POST', '/add/(\d+)/', function() {
        if (!hasPermission('create_application')) {
            _etsis_flash()->{'error'}(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/add/(\d+)/', function ($id) use($app) {

        if ($app->req->isPost()) {
            try {
                $appl = $app->db->application();
                $appl->acadProgCode = _trim($_POST['acadProgCode']);
                $appl->startTerm = $_POST['startTerm'];
                $appl->PSAT_Verbal = $_POST['PSAT_Verbal'];
                $appl->PSAT_Math = $_POST['PSAT_Math'];
                $appl->SAT_Verbal = $_POST['SAT_Verbal'];
                $appl->SAT_Math = $_POST['SAT_Math'];
                $appl->ACT_English = $_POST['ACT_English'];
                $appl->ACT_MATH = $_POST['ACT_Math'];
                $appl->personID = $id;
                $appl->addDate = $app->db->NOW();
                $appl->applDate = $_POST['applDate'];
                $appl->addedBy = get_persondata('personID');
                $appl->admitStatus = $_POST['admitStatus'];

                /**
                 * Fires during the saving/creating of an application.
                 *
                 * @since 6.1.10
                 * @param object $appl Application object.
                 */
                $app->hook->do_action('save_application_db_table', $appl);

                if ($appl->save()) {
                    $ID = $appl->lastInsertId();
                    etsis_logger_activity_log_write('New Record', 'Application', get_name($id), get_persondata('uname'));
                    _etsis_flash()->{'success'}(_etsis_flash()->notice(200), get_base_url() . 'appl' . '/' . $ID . '/');
                } else {
                    _etsis_flash()->{'error'}(_etsis_flash()->notice(409));
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
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
                ->whereLte('endDate', '0000-00-00')
                ->findOne();
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
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
            _etsis_flash()->{'error'}(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/inst-attended/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                $inst = $app->db->institution_attended();
                $inst->fice_ceeb = _trim((int) $_POST['fice_ceeb']);
                $inst->fromDate = $_POST['fromDate'];
                $inst->toDate = $_POST['toDate'];
                $inst->GPA = $_POST['GPA'];
                $inst->personID = $_POST['personID'];
                $inst->major = $_POST['major'];
                $inst->degree_awarded = $_POST['degree_awarded'];
                $inst->degree_conferred_date = $_POST['degree_conferred_date'];
                $inst->addDate = $inst->NOW();
                $inst->addedBy = get_persondata('personID');
                if ($inst->save()) {
                    etsis_logger_activity_log_write('New Record', 'Institution Attended', get_name($_POST['personID']), get_persondata('uname'));
                    _etsis_flash()->{'success'}(_etsis_flash()->notice(200), get_base_url() . 'appl' . '/' . $_POST['personID'] . '/');
                } else {
                    _etsis_flash()->{'error'}(_etsis_flash()->notice(409));
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
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
            _etsis_flash()->{'error'}(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/inst/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                $post = $_POST['inst'];
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
                _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
            } catch (Exception $e) {
                _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
            } catch (ORMException $e) {
                _etsis_flash()->{'error'}($e->getMessage(), $app->req->server['HTTP_REFERER']);
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
                $inst->fice_ceeb = _trim((int) $_POST['fice_ceeb']);
                $inst->instType = $_POST['instType'];
                $inst->instName = $_POST['instName'];
                $inst->city = $_POST['city'];
                $inst->state = $_POST['state'];
                $inst->country = $_POST['country'];
                if ($inst->save()) {
                    $ID = $inst->lastInsertId();
                    etsis_logger_activity_log_write('New Record', 'Institution', $_POST['instName'], get_persondata('uname'));
                    _etsis_flash()->{'success'}(_etsis_flash()->notice(200), get_base_url() . 'appl/inst' . '/' . $ID . '/');
                } else {
                    _etsis_flash()->{'error'}(_etsis_flash()->notice(409));
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (Exception $e) {
                _etsis_flash()->{'error'}($e->getMessage());
            } catch (ORMException $e) {
                _etsis_flash()->{'error'}($e->getMessage());
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
                foreach ($_POST as $k => $v) {
                    $inst->$k = $v;
                }
                $inst->where('institutionID = ?', $id);
                if ($inst->update()) {
                    etsis_logger_activity_log_write('Update Record', 'Institution', _filter_input_string(INPUT_POST, 'instName'), get_persondata('uname'));
                    _etsis_flash()->{'success'}(_etsis_flash()->notice(200),$app->req->server['HTTP_REFERER']);
                } else {
                    _etsis_flash()->{'error'}(_etsis_flash()->notice(409),$app->req->server['HTTP_REFERER']);
                }
            } catch (NotFoundException $e) {
                _etsis_flash()->{'error'}($e->getMessage(),$app->req->server['HTTP_REFERER']);
            } catch (Exception $e) {
                _etsis_flash()->{'error'}($e->getMessage(),$app->req->server['HTTP_REFERER']);
            } catch (ORMException $e) {
                _etsis_flash()->{'error'}($e->getMessage(),$app->req->server['HTTP_REFERER']);
            }
        }

        try {
            $inst = $app->db->institution()->where('institutionID = ?', (int) $id)->findOne();
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        }

        etsis_register_style('form');
        etsis_register_script('select');
        etsis_register_script('select2');

        $app->view->display('application/view-inst', [
            'title' => $inst->instName,
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
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
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

    $app->post('/applicantLookup/', function() {
        $appl = get_person_by('personID', $_POST['personID']);

        $json = [ 'input#person' => $appl->lname . ', ' . $appl->fname];

        echo json_encode($json);
    });

    $app->get('/deleteInstAttend/(\d+)/', function($id) use($app) {
        try {
            $inst = $app->db->institution_attended()->where('instAttID = ?', $id);
            if ($inst->delete()) {
                _etsis_flash()->{'success'}(_etsis_flash()->notice(200));
            } else {
                _etsis_flash()->{'error'}(_etsis_flash()->notice(409));
            }
        } catch (NotFoundException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (Exception $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        } catch (ORMException $e) {
            _etsis_flash()->{'error'}($e->getMessage());
        }
        redirect($app->req->server['HTTP_REFERER']);
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});
