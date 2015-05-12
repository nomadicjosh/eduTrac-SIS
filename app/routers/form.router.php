<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

$json_url = url('/v1/');

$logger = new \app\src\Log();
$cache = new \app\src\Cache();
$dbcache = new \app\src\DBCache();
$flashNow = new \app\src\Messages();

$css = [ 'css/admin/module.admin.page.form_elements.min.css', 'css/admin/module.admin.page.tables.min.css'];
$js = [
    'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
    'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/jasny-fileupload/assets/js/bootstrap-fileupload.js?v=v2.1.0'
];

$app->group('/form', function() use ($app, $css, $js, $json_url, $flashNow) {

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/semester/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/semester/', function () use($app, $css, $js, $json_url) {
        $sem = $app->db->semester()->whereNot('semCode', 'NULL')->orderBy('acadYearCode', 'DESC');
        $q = $sem->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/semester', [
            'title' => 'Semester',
            'cssArray' => $css,
            'jsArray' => $js,
            'semester' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-semester/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });
    $app->get('/view-semester/(\d+)/', function ($id) use($app, $css, $js, $json_url) {

        $json = _file_get_contents($json_url . 'semester/semesterID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['semesterID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-semester', [
                'title' => 'View Semester',
                'cssArray' => $css,
                'jsArray' => $js,
                'semester' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/term/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/term/', function () use($app, $css, $js, $json_url) {
        $term = $app->db->term()
            ->select('term.*,semester.semName')
            ->_join('semester', 'term.semCode = semester.semCode')
            ->whereNot('term.termCode', 'NULL')
            ->orderBy('term.termCode', 'DESC');
        $q = $term->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/term', [
            'title' => 'Term',
            'cssArray' => $css,
            'jsArray' => $js,
            'term' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-term/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });
    $app->get('/view-term/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'term/termID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['termID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-term', [
                'title' => 'View Term',
                'cssArray' => $css,
                'jsArray' => $js,
                'term' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/acad-year/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/acad-year/', function () use($app, $css, $js, $json_url) {
        $ay = $app->db->acad_year()->whereNot('acadYearCode', 'NULL')->orderBy('acadYearCode', 'DESC');
        $q = $ay->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/acad-year', [
            'title' => 'Academic Year',
            'cssArray' => $css,
            'jsArray' => $js,
            'acadYear' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-acad-year/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-acad-year/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'acad_year/acadYearID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['acadYearID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-acad-year', [
                'title' => 'View Acad Year',
                'cssArray' => $css,
                'jsArray' => $js,
                'acadYear' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/department/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/department/', function () use($app, $css, $js, $json_url) {
        $dept = $app->db->department()->whereNot('deptCode', 'NULL')->orderBy('deptCode');
        $q = $dept->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/department', [
            'title' => 'Department',
            'cssArray' => $css,
            'jsArray' => $js,
            'dept' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-department/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-department/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'department/deptID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['deptID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-department', [
                'title' => 'View Department',
                'cssArray' => $css,
                'jsArray' => $js,
                'dept' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/subject/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/subject/', function () use($app, $css, $js, $json_url) {
        $subj = $app->db->subject()->whereNot('subjectCode', 'NULL')->orderBy('subjectCode');
        $q = $subj->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/subject', [
            'title' => 'Subject',
            'cssArray' => $css,
            'jsArray' => $js,
            'subj' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-subject/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-subject/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'subject/subjectID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['subjectID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-subject', [
                'title' => 'View Subject',
                'cssArray' => $css,
                'jsArray' => $js,
                'subj' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/student-load-rule/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/student-load-rule/', function () use($app, $css, $js, $json_url) {
        $slr = $app->db->student_load_rule()->orderBy('min_cred', 'DESC');
        $q = $slr->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/student-load-rule', [
            'title' => 'Student Load Rule',
            'cssArray' => $css,
            'jsArray' => $js,
            'slr' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-student-load-rule/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-student-load-rule/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'student_load_rule/slrID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['slrID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-student-load-rule', [
                'title' => 'View Student Load Rule',
                'cssArray' => $css,
                'jsArray' => $js,
                'slr' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/degree/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/degree/', function () use($app, $css, $js, $json_url) {
        $degree = $app->db->degree()->whereNot('degreeCode', 'NULL')->orderBy('degreeCode');
        $q = $degree->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/degree', [
            'title' => 'Degree',
            'cssArray' => $css,
            'jsArray' => $js,
            'degree' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-degree/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-degree/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'degree/degreeID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['degreeID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-degree', [
                'title' => 'View Degree',
                'cssArray' => $css,
                'jsArray' => $js,
                'degree' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/major/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/major/', function () use($app, $css, $js, $json_url) {
        $major = $app->db->major()->whereNot('majorCode', 'NULL')->orderBy('majorCode');
        $q = $major->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/major', [
            'title' => 'Major',
            'cssArray' => $css,
            'jsArray' => $js,
            'major' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-major/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-major/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'major/majorID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['majorID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-major', [
                'title' => 'View Major',
                'cssArray' => $css,
                'jsArray' => $js,
                'major' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/minor/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/minor/', function () use($app, $css, $js, $json_url) {
        $minor = $app->db->minor()->whereNot('minorCode', 'NULL')->orderBy('minorCode');
        $q = $minor->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/minor', [
            'title' => 'Minor',
            'cssArray' => $css,
            'jsArray' => $js,
            'minor' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-minor/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-minor/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'minor/minorID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['minorID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-minor', [
                'title' => 'View Minor',
                'cssArray' => $css,
                'jsArray' => $js,
                'minor' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/ccd/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/ccd/', function () use($app, $css, $js, $json_url) {
        $ccd = $app->db->ccd()->whereNot('ccdCode', 'NULL')->orderBy('ccdCode');
        $q = $ccd->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/ccd', [
            'title' => 'CCD',
            'cssArray' => $css,
            'jsArray' => $js,
            'ccd' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-ccd/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-ccd/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'ccd/ccdID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['ccdID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-ccd', [
                'title' => 'View CCD',
                'cssArray' => $css,
                'jsArray' => $js,
                'ccd' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/specialization/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/specialization/', function () use($app, $css, $js, $json_url) {
        $spec = $app->db->specialization()->whereNot('specCode', 'NULL')->orderBy('specCode');
        $q = $spec->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/specialization', [
            'title' => 'Specialization',
            'cssArray' => $css,
            'jsArray' => $js,
            'spec' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-specialization/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-specialization/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'specialization/specID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['specID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-specialization', [
                'title' => 'View Specialization',
                'cssArray' => $css,
                'jsArray' => $js,
                'spec' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/cip/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/cip/', function () use($app, $css, $js, $json_url) {
        $cip = $app->db->cip()->whereNot('cipCode', 'NULL')->orderBy('cipCode');
        $q = $cip->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/cip', [
            'title' => 'CIP',
            'cssArray' => $css,
            'jsArray' => $js,
            'cip' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-cip/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-cip/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'cip/cipID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['cipID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-cip', [
                'title' => 'View CIP',
                'cssArray' => $css,
                'jsArray' => $js,
                'cip' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/rstr-code/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/rstr-code/', function () use($app, $css, $js, $json_url) {
        $rstr = $app->db->restriction_code()
            ->select('restriction_code.*,department.deptName')
            ->_join('department', 'restriction_code.deptCode = department.deptCode')
            ->orderBy('rstrCode');
        $q = $rstr->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/rstr-code', [
            'title' => 'Restriction Code',
            'cssArray' => $css,
            'jsArray' => $js,
            'rstr' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-rstr-code/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-rstr-code/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'restriction_code/rstrCodeID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['rstrCodeID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-rstr-code', [
                'title' => 'View Restriction Code',
                'cssArray' => $css,
                'jsArray' => $js,
                'rstr' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/location/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/location/', function () use($app, $css, $js, $json_url) {
        $location = $app->db->location()->whereNot('locationCode', 'NULL')->orderBy('locationCode');
        $q = $location->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/location', [
            'title' => 'Location',
            'cssArray' => $css,
            'jsArray' => $js,
            'location' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-location/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-location/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'location/locationID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['locationID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-location', [
                'title' => 'View Location',
                'cssArray' => $css,
                'jsArray' => $js,
                'location' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/building/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/building/', function () use($app, $css, $js, $json_url) {
        $build = $app->db->building()->whereNot('buildingCode', 'NULL')->orderBy('buildingCode');
        $q = $build->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/building', [
            'title' => 'Building',
            'cssArray' => $css,
            'jsArray' => $js,
            'build' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-building/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-building/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'building/buildingID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['buildingID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-building', [
                'title' => 'View Building',
                'cssArray' => $css,
                'jsArray' => $js,
                'build' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/room/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/room/', function () use($app, $css, $js, $json_url) {
        $room = $app->db->room()
            ->select('room.*,building.buildingName')
            ->_join('building', 'room.buildingCode = building.buildingCode')
            ->where('roomCode <> "NULL"')
            ->orderBy('buildingName');
        $q = $room->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/room', [
            'title' => 'Room',
            'cssArray' => $css,
            'jsArray' => $js,
            'room' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-room/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-room/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'room/roomID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['roomID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-room', [
                'title' => 'View Room',
                'cssArray' => $css,
                'jsArray' => $js,
                'room' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/school/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/school/', function () use($app, $css, $js, $json_url) {
        $sch = $app->db->school()
            ->select('school.*,building.buildingName')
            ->_join('building', 'school.buildingCode = building.buildingCode')
            ->where('schoolCode <> "NULL"')
            ->orderBy('buildingName');
        $q = $sch->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/school', [
            'title' => 'School',
            'cssArray' => $css,
            'jsArray' => $js,
            'school' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-school/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-school/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'school/schoolID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['schoolID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-school', [
                'title' => 'View School',
                'cssArray' => $css,
                'jsArray' => $js,
                'school' => $decode
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/grade-scale/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/grade-scale/', function () use($app, $css, $js, $json_url) {
        $scale = $app->db->grade_scale()->orderBy('grade');
        $q = $scale->find(function($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        $app->view->display('form/grade-scale', [
            'title' => 'Grade Scale',
            'cssArray' => $css,
            'jsArray' => $js,
            'scale' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/view-grade-scale/(\d+)/', function() {
        if (!hasPermission('access_forms')) {
            redirect(url('/dashboard/'));
        }

        /**
         * If user is logged in and the lockscreen cookie is set, 
         * redirect user to the lock screen until he/she enters 
         * his/her password to gain access.
         */
        if (isset($_COOKIE['SCREENLOCK'])) {
            redirect(url('/lock/'));
        }
    });

    $app->get('/view-grade-scale/(\d+)/', function ($id) use($app, $css, $js, $json_url) {
        $json = _file_get_contents($json_url . 'grade_scale/ID/' . $id . '/');
        $decode = json_decode($json, true);

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['ID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('form/view-grade-scale', [
                'title' => 'View Grade Scale',
                'cssArray' => $css,
                'jsArray' => $js,
                'scale' => $decode
                ]
            );
        }
    });

    $app->setError(function() use($app) {

        $app->view->display('error/404', ['title' => '404 Error']);
    });
});
