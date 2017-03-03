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
 * Course Section Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
/**
 * Before route check.
 */
$app->before('GET|POST', '/sect(.*)', function() {
    if (!is_user_logged_in()) {
        _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url());
    }
});

$app->group('/sect', function() use ($app) {

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/', function() {
        if (!hasPermission('access_course_sec_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $post = $app->req->post['sect'];
                $sect = $app->db->query("SELECT 
                    CASE a.currStatus 
                    WHEN 'A' THEN 'Active' 
                    WHEN 'I' THEN 'Inactive' 
                    WHEN 'P' THEN 'Pending' 
                    WHEN 'C' THEN 'Cancelled' 
                    ELSE 'Obsolete'
                    END AS 'Status', 
                        a.courseSecCode,a.secShortTitle,a.courseSecID,a.termCode 
                    FROM course_sec a 
                    WHERE courseSection LIKE ? 
                    ORDER BY a.termCode DESC", [ "%$post%"]
                );

                $q = $sect->find(function($data) {
                    $array = [];
                    foreach ($data as $d) {
                        $array[] = $d;
                    }
                    return $array;
                });
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('section/index', [
            'title' => 'Search Course Section',
            'sect' => $q
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/(\d+)/', function() {
        if (!hasPermission('access_course_sec_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app) {
        $section = get_course_sec($id);

        $date = date("Y-m-d");
        $time = date("h:i A");

        if ($app->req->isPost()) {
            try {
                /**
                 * Fires before the course section has been updated.
                 *
                 * @since 6.1.07
                 * @param int $id Primary key of the course section.
                 */
                $app->hook->do_action('pre_update_course_sec', $id);

                $term = str_replace("/", "", $app->req->post['termCode']);

                $sect = $app->db->course_sec();
                $sect->locationCode = $app->req->post['locationCode'];
                $sect->termCode = $app->req->post['termCode'];
                $sect->secShortTitle = $app->req->post['secShortTitle'];
                $sect->startDate = $app->req->post['startDate'];
                $sect->endDate = $app->req->post['endDate'];
                $sect->deptCode = $app->req->post['deptCode'];
                $sect->minCredit = $app->req->post['minCredit'];
                $sect->comment = $app->req->post['comment'];
                $sect->courseSection = $app->req->post['termCode'] . '-' . $app->req->post['courseSecCode'];
                $sect->ceu = $app->req->post['ceu'];
                $sect->courseLevelCode = $app->req->post['courseLevelCode'];
                $sect->where('courseSecID = ?', (int) $id);
                $sect->update();

                /**
                 * Fires during the update of a course section.
                 *
                 * @since 6.1.10
                 * @param array $sect Course section object.
                 */
                $app->hook->do_action('update_course_sec_db_table', $sect);

                $da = $app->db->term()->where('termCode = ?', _h($section->termCode))->findOne();

                if ($section->currStatus != $app->req->post['currStatus']) {
                    /**
                     * If the posted status is 'C' and today's date is less than the 
                     * primary term start date, then delete all student course sec as well as 
                     * student acad cred records.
                     */
                    if ($app->req->post['currStatus'] == 'C' && $date < _h($da->termStartDate)) {
                        $q = $app->db->course_sec();
                        $q->currStatus = $app->req->post['currStatus'];
                        $q->statusDate = $date;
                        $q->where('courseSecID = ?', $id);
                        $q->update();

                        $app->db->stu_course_sec()->where('courseSecID = ?', $id)->delete();
                        $app->db->stu_acad_cred()->where('courseSecID = ?', $id)->delete();
                    }
                    /**
                     * If posted status is 'C' and today's date is greater than equal to the 
                     * primary term start date, then update student course sec records as 
                     * well as the student academic credit records with a 'C' status and 
                     * update the status date and time.
                     */ elseif ($app->req->post['currStatus'] == 'C' && $date >= _h($da->termStartDate)) {
                        $q = $app->db->course_sec();
                        $q->currStatus = $app->req->post['currStatus'];
                        $q->statusDate = $date;
                        $q->where('courseSecID = ?', $id);
                        $q->update();

                        $sql1 = $app->db->stu_course_sec();
                        $sql1->status = $app->req->post['currStatus'];
                        $sql1->statusDate = $date;
                        $sql1->statusTime = $time;
                        $sql1->where('courseSecID = ?', $id)->update();

                        $sql2 = $app->db->stu_acad_cred();
                        $sql2->status = $app->req->post['currStatus'];
                        $sql2->statusDate = $date;
                        $sql2->statusTime = $time;
                        $sql2->where('courseSecID = ?', $id)->update();
                    }
                    /**
                     * If the status is different from 'C', update the status and status date
                     * as long as there are
                     */ else {
                        $q = $app->db->course_sec();
                        $q->currStatus = $app->req->post['currStatus'];
                        $q->statusDate = $date;
                        $q->where('courseSecID = ?', $id);
                        $q->update();
                    }
                }

                etsis_cache_delete($id, 'sect');
                etsis_logger_activity_log_write('Update Record', 'Course Section', $app->req->post['secShortTitle'] . ' (' . $app->req->post['termCode'] . '-' . _h($section->courseSecCode) . ')', get_persondata('uname'));

                /**
                 * Query course section after it has been updated.
                 * 
                 * @since 6.1.07
                 */
                $section = $app->db->course_sec()
                    ->setTableAlias('sect')
                    ->select('sect.*,crse.subjectCode,crse.deptCode,crse.creditType')
                    ->select('crse.courseShortTitle,crse.courseLongTitle')
                    ->_join('course', 'sect.courseID = crse.courseID', 'crse')
                    ->where('sect.courseSecID = ?', $id)
                    ->findOne();

                /**
                 * Fires after the course section has been updated.
                 * 
                 * @since 6.1.07
                 * @param array $sect Course section data object.
                 */
                $app->hook->do_action('post_update_course_sec', $section);

                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'sect' . '/' . $id . '/');
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

        try {
            $preReq = $app->db->course()->select('preReq')->where('courseID = ?', _h($section->courseID));
            $req = $preReq->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
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

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($section == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($section) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_h($section->courseSecID) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datepicker');
            etsis_register_script('maxlength');

            $app->view->display('section/view', [
                'title' => _h($section->secShortTitle) . ' :: Course Section',
                'sect' => $section,
                'req' => $req
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/add/(\d+)/', function() {
        if (!hasPermission('add_course_sec')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/add/(\d+)/', function ($id) use($app) {
        $crse = get_course($id);

        if ($app->req->isPost()) {
            try {
                /**
                 * Fires before a course section has been created.
                 *
                 * @since 6.1.07
                 * @param int $id Primary key of the course from which the course section is created.
                 */
                $app->hook->do_action('pre_save_course_sec', $id);

                $sc = _h($crse->courseCode) . '-' . $app->req->post['sectionNumber'];
                $courseSection = $app->req->post['termCode'] . '-' . _h($crse->courseCode) . '-' . $app->req->post['sectionNumber'];

                $dotw = '';
                /** Combine the days of the week to be entered into the database */
                $days = $app->req->post['dotw'];
                for ($i = 0; $i < sizeof($days); $i++) {
                    $dotw .= $days[$i];
                }

                $sect = $app->db->course_sec();
                $sect->insert([
                    'sectionNumber' => $app->req->post['sectionNumber'],
                    'courseSecCode' => _trim($sc),
                    'courseSection' => _trim($courseSection),
                    'buildingCode' => 'NULL',
                    'roomCode' => 'NULL',
                    'locationCode' => $app->req->post['locationCode'],
                    'courseLevelCode' => $app->req->post['courseLevelCode'],
                    'acadLevelCode' => $app->req->post['acadLevelCode'],
                    'deptCode' => $app->req->post['deptCode'],
                    'termCode' => $app->req->post['termCode'],
                    'courseID' => $id,
                    'courseCode' => _h($crse->courseCode),
                    'secShortTitle' => $app->req->post['secShortTitle'],
                    'startDate' => $app->req->post['startDate'],
                    'endDate' => $app->req->post['endDate'],
                    'minCredit' => $app->req->post['minCredit'],
                    'ceu' => $app->req->post['ceu'],
                    'secType' => $app->req->post['secType'],
                    'instructorMethod' => $app->req->post['instructorMethod'],
                    'dotw' => $dotw,
                    'startTime' => $app->req->post['startTime'],
                    'endTime' => $app->req->post['endTime'],
                    'webReg' => $app->req->post['webReg'],
                    'currStatus' => $app->req->post['currStatus'],
                    'statusDate' => $app->db->NOW(),
                    'comment' => $app->req->post['comment'],
                    'approvedDate' => $app->db->NOW(),
                    'approvedBy' => get_persondata('personID')
                ]);

                /**
                 * Fires during the saving/creating of a course section.
                 *
                 * @since 6.1.10
                 * @param array $sect Course section object.
                 */
                $app->hook->do_action('save_course_sec_db_table', $sect);

                $ID = $sect->lastInsertId();
                $section = [
                    "sectionNumber" => _trim($app->req->post['sectionNumber']), "courseSecCode" => _trim($sc),
                    "courseID" => $app->req->post['courseID'], "locationCode" => _trim($app->req->post['locationCode']),
                    "termCode" => _trim($app->req->post['termCode']), "courseCode" => _trim($app->req->post['courseCode']), "secShortTitle" => $app->req->post['secShortTitle'],
                    "startDate" => $app->req->post['startDate'], "endDate" => $app->req->post['endDate'], "deptCode" => _trim($app->req->post['deptCode']),
                    "minCredit" => $app->req->post['minCredit'], "ceu" => $app->req->post['ceu'], "courseSection" => _trim($courseSection),
                    "courseLevelCode" => _trim($app->req->post['courseLevelCode']), "acadLevelCode" => _trim($app->req->post['acadLevelCode']),
                    "currStatus" => $app->req->post['currStatus'], "statusDate" => $app->req->post['statusDate'], "comment" => $app->req->post['comment'],
                    "approvedDate" => $app->req->post['approvedDate'], "approvedBy" => $app->req->post['approvedBy'], "secLongTitle" => _h($crse->courseLongTitle),
                    "section" => _trim($courseSection), "description" => _h($crse->courseDesc)
                ];
                /**
                 * Fires after a course section has been created.
                 * 
                 * @since 6.1.07
                 * @param array $section Course section data array.
                 */
                $app->hook->do_action('post_save_course_sec', $section);

                etsis_cache_flush_namespace('sect');
                etsis_logger_activity_log_write('New Record', 'Course Section', _trim($courseSection), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'sect' . '/' . $ID . '/');
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
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($crse == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($crse) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (count($crse->courseID) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('datepicker');
            etsis_register_script('timepicker');
            etsis_register_script('maxlength');

            $app->view->display('section/add', [
                'title' => 'Create Section',
                'sect' => $crse
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/addnl/(\d+)/', function() {
        if (!hasPermission('access_course_sec_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/addnl/(\d+)/', function ($id) use($app) {
        $section = get_course_sec($id);

        if ($app->req->isPost()) {
            try {
                $sect = $app->db->course_sec();
                foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                    $sect->$k = $v;
                }
                /**
                 * Fires before course section additional
                 * information has been updated.
                 *
                 * @since 6.1.07
                 * @param object $sect Course section additional info object.
                 */
                $app->hook->do_action('pre_course_sec_addnl', $sect);
                $sect->where('courseSecID = ?', $id);
                $sect->update();

                etsis_cache_delete($id, 'sect');
                etsis_logger_activity_log_write('Update Record', 'Course Section', _h($section->courseSection), get_persondata('uname'));
                /**
                 * Query course section after it has been updated.
                 * 
                 * @since 6.1.07
                 */
                $section = $app->db->course_sec()
                    ->setTableAlias('sect')
                    ->select('sect.*,crse.subjectCode,crse.deptCode,crse.creditType')
                    ->select('crse.courseShortTitle,crse.courseLongTitle')
                    ->_join('course', 'sect.courseID = crse.courseID', 'crse')
                    ->where('sect.courseSecID = ?', $id)
                    ->findOne();

                /**
                 * Fires after course section additional
                 * information has been updated.
                 * 
                 * @since 6.1.07
                 * @param array $section Course section data object.
                 */
                $app->hook->do_action('post_course_sec_addnl', $section);

                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
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
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($section == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($section) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_h($section->courseSecID) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_script('select');
            etsis_register_script('select2');

            $app->view->display('section/addnl-info', [
                'title' => _h($section->secShortTitle) . ' :: Course Section',
                'sect' => $section
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/soff/(\d+)/', function() {
        if (!hasPermission('access_course_sec_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/soff/(\d+)/', function ($id) use($app) {
        $sect = get_course_sec($id);

        if ($app->req->isPost()) {
            try {
                $dotw = '';
                /** Combine the days of the week to be entered into the database */
                $days = $app->req->post['dotw'];
                for ($i = 0; $i < sizeof($days); $i++) {
                    $dotw .= $days[$i];
                }

                $soff = $app->db->course_sec();
                $soff->set([
                        'buildingCode' => $app->req->post['buildingCode'],
                        'roomCode' => $app->req->post['roomCode'],
                        'dotw' => $dotw,
                        'startTime' => $app->req->post['startTime'],
                        'endTime' => $app->req->post['endTime'],
                        'webReg' => $app->req->post['webReg']
                    ])
                    ->where('courseSecID = ?', $id)
                    ->update();

                etsis_cache_delete($id, 'sect');
                etsis_logger_activity_log_write('Update Record', 'Course Section Offering', _h($sect->courseSection), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'sect/soff' . '/' . (int) $id . '/');
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
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($sect == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($sect) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_h($sect->courseSecID) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            etsis_register_style('form');
            etsis_register_script('select');
            etsis_register_script('select2');
            etsis_register_script('timepicker');

            $app->view->display('section/offering-info', [
                'title' => _h($sect->secShortTitle) . ' :: Course Section',
                'sect' => $sect
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/fgrade/(\d+)/', function() {
        if (!hasPermission('submit_final_grades')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/fgrade/(\d+)/', function ($id) use($app) {

        if ($app->req->isPost()) {
            try {
                $size = count($app->req->post['stuID']);
                $i = 0;
                while ($i < $size) {
                    if (acadCredGradePoints($app->req->post['grade'][$i], $app->req->post['attCredit']) > 0) {
                        $compCred = $app->req->post['attCredit'];
                    } else {
                        $compCred = '0';
                    }

                    $grade = $app->db->stu_acad_cred();
                    $grade->grade = $app->req->post['grade'][$i];
                    $grade->gradePoints = acadCredGradePoints($app->req->post['grade'][$i], $app->req->post['attCredit']);
                    $grade->compCred = $compCred;
                    $grade->where('stuID = ?', $app->req->post['stuID'][$i])->_and_()
                        ->where('courseSecID = ?', $id)
                        ->update();

                    etsis_logger_activity_log_write('Update Record', 'Final Grade', get_name($app->req->post['stuID'][$i]) . ' (' . $app->req->post['termCode'] . '-' . $app->req->post['courseSecCode'] . ')', get_persondata('uname'));
                    ++$i;
                }
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
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

        $sect = get_course_sec($id);

        try {
            $fgrade = $app->db->course_sec()
                ->select('course_sec.courseSecID,course_sec.secShortTitle,course_sec.minCredit,course_sec.courseSection,course_sec.facID')
                ->select('b.stuID,b.courseSecCode,b.courseSection,b.termCode,b.grade')
                ->_join('stu_acad_cred', 'course_sec.courseSecID = b.courseSecID', 'b')
                ->where('course_sec.courseSecID = ?', $id);
            $q = $fgrade->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
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

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($q == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['courseSecID']) <= 0) {

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

            $app->view->display('section/section-fgrade', [
                'title' => _h($q[0]['courseSection']) . ' :: Section Final Grades',
                'grade' => $q,
                'sect' => $sect
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/rgn/', function() use($app) {
        if (!hasPermission('register_students')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }

        if ($app->req->isPost()) {
            if (is_null($app->req->post['courseSecID'])) {
                _etsis_flash()->error(_t("The course section cannot be a 'null' value"), $app->req->server['HTTP_REFERER']);
                exit();
            }
        }
    });

    $app->match('GET|POST', '/rgn/', function () use($app) {
        $time = date("h:i A");

        if ($app->req->isPost()) {
            try {
                $app->hook->do_action('execute_reg_rest_rule', $app->req->post['stuID']);

                $sect = get_course_sec($app->req->post['courseSecID']);
                $crse = $app->db->course()->where('courseID = ?', (int) _h($sect->courseID))->findOne();
                $term = $app->db->term()->where('termCode = ?', $sect->termCode)->findOne();

                $stcs = $app->db->stu_course_sec();
                $stac = $app->db->stu_acad_cred();

                /**
                 * Fires before a student is registered into
                 * a course by a staff member.
                 *
                 * @since 6.1.07
                 * @since 6.2.0 Changed to use $app->hook->do_action_array and added $stcs and $stac objects.
                 * @param object $stcs Student course section object.
                 * @param object $stac Student academic credit object.
                 */
                $app->hook->do_action_array('pre_rgn_stu_crse_reg', [ $stcs, $stac]);

                $stcs->insert([
                    'stuID' => (int) $app->req->post['stuID'],
                    'courseSecID' => _h((int) $sect->courseSecID),
                    'courseSecCode' => _h($sect->courseSecCode),
                    'courseSection' => _h($sect->courseSection),
                    'termCode' => _h($sect->termCode),
                    'courseCredits' => _h($sect->minCredit),
                    'ceu' => _h($sect->ceu),
                    'status' => 'A',
                    'regDate' => $app->db->NOW(),
                    'regTime' => date("h:i A"),
                    'statusDate' => $app->db->NOW(),
                    'statusTime' => $time,
                    'addedBy' => get_persondata('personID')
                ]);

                $stac->insert([
                    'stuID' => (int) $app->req->post['stuID'],
                    'courseID' => _h((int) $sect->courseID),
                    'courseSecID' => _h((int) $sect->courseSecID),
                    'courseCode' => _h($sect->courseCode),
                    'courseSecCode' => _h($sect->courseSecCode),
                    'sectionNumber' => _h($sect->sectionNumber),
                    'courseSection' => _h($sect->courseSection),
                    'termCode' => _h($sect->termCode),
                    'reportingTerm' => _h($term->reportingTerm),
                    'subjectCode' => _h($crse->subjectCode),
                    'deptCode' => _h($sect->deptCode),
                    'shortTitle' => _h($crse->courseShortTitle),
                    'longTitle' => _h($crse->courseLongTitle),
                    'attCred' => _h($sect->minCredit),
                    'status' => 'A',
                    'statusDate' => $app->db->NOW(),
                    'statusTime' => $time,
                    'acadLevelCode' => _h($sect->acadLevelCode),
                    'courseLevelCode' => _h($sect->courseLevelCode),
                    'startDate' => _h($sect->startDate),
                    'endDate' => _h($sect->endDate),
                    'addedBy' => get_persondata('personID'),
                    'addDate' => $app->db->NOW()
                ]);

                /**
                 * @since 6.1.07
                 */
                $ID = $stac->lastInsertId();
                $sacd = $app->db->stu_acad_cred()
                    ->setTableAlias('stac')
                    ->select('stac.*,nae.uname,nae.fname,nae.lname,nae.email')
                    ->_join('person', 'stac.stuID = nae.personID', 'nae')
                    ->where('stac.stuAcadCredID = ?', $ID)
                    ->findOne();

                /**
                 * Fires after a student has been registered into
                 * a course by a staff member.
                 * 
                 * @since 6.1.07
                 * @param array $sacd Student Academic Credit detail data object.
                 */
                $app->hook->do_action('post_rgn_stu_crse_reg', $sacd);

                if (function_exists('financial_module')) {
                    /**
                     * Generate bill and/or add fees.
                     */
                    generate_stu_bill(_h($sect->termCode), $app->req->post['stuID'], _h($sect->courseSecID));
                }
                etsis_logger_activity_log_write('New Record', 'Course Registration Via Staff', get_name($app->req->post['stuID']) . ' - ' . _h($sect->secShortTitle), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'sect/rgn' . '/');
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

        etsis_register_style('form');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_style('jquery-ui');
        etsis_register_script('jCombo');
        etsis_register_script('jquery-ui');

        $app->view->display('section/register', [
            'title' => 'Course Registration'
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/rgn/rrsr/', function() {
        if (!hasPermission('register_students')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/rgn/rrsr/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $size = count($app->req->post['rule']);
                $i = 0;
                while ($i < $size) {
                    if ($app->req->post['id'][$i] == null) {
                        $rlde = get_rule_by_code((string) $app->req->_post('rule')[$i]);
                        $rrsr = Node::table('rrsr');
                        $rrsr->rid = _h((int) $rlde->id);
                        $rrsr->rule = (string) $app->req->_post('rule')[$i];
                        $rrsr->value = (string) $app->req->_post('value')[$i];
                        $rrsr->save();
                    }
                    ++$i;
                }

                $id_size = count($app->req->post['rule']);
                $t = 0;
                while ($t < $id_size) {
                    if ($app->req->post['id'][$t] > 0) {
                        $rlde = get_rule_by_code((string) $app->req->_post('rule')[$t]);
                        $rrsr = Node::table('rrsr')->find($app->req->post['id'][$t]);
                        $rrsr->rid = _h((int) $rlde->id);
                        $rrsr->rule = (string) $app->req->_post('rule')[$t];
                        $rrsr->value = (string) $app->req->_post('value')[$t];
                        $rrsr->save();
                    }
                    ++$t;
                }

                etsis_logger_activity_log_write('New Record', 'Registration Restriction Rule (RRSR)', _h($rlde->code) . ' - ' . _h($rlde->description), get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), $app->req->server['HTTP_REFERER']);
            } catch (NodeQException $e) {
                Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            } catch (Exception $e) {
                Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
            }
        }

        try {
            $rrsr = Node::table('rrsr')->findAll();
        } catch (NodeQException $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        } catch (Exception $e) {
            Cascade::getLogger('error')->error(sprintf('NODEQSTATE[%s]: %s', $e->getCode(), $e->getMessage()));
        }

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');

        $app->view->display('section/rrsr', [
            'title' => 'Registration Restriction Rule (RRSR)',
            'rrsr' => $rrsr
            ]
        );
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/sros.*', function() {
        if (!hasPermission('access_stu_roster_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/sros/', function () use($app) {

        if ($app->req->isPost()) {
            redirect(get_base_url() . 'sect/sros' . '/' . $app->req->post['sectionID'] . '/' . $app->req->post['template'] . '/');
        }

        etsis_register_style('form');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('jCombo');

        $app->view->display('section/sros', [
            'title' => 'Course Section Roster'
            ]
        );
    });

    $app->get('/sros/(\d+)/(\w+)/', function ($id, $template) use($app) {

        try {
            $sros = $app->db->query("SELECT 
						a.stuID,a.courseSecCode,a.termCode,a.courseCredits,
					CASE a.status 
					WHEN 'A' THEN 'Add' 
					WHEN 'N' THEN 'New'
					ELSE 'Drop' 
					END AS 'Status',
						b.acadProgCode,b.acadLevelCode,c.courseSection,
						c.facID,c.roomCode,c.secShortTitle,c.startDate,
						c.endDate,c.startTime,c.endTime,c.dotw,
						c.instructorMethod,d.altID 
					FROM stu_course_sec a 
					LEFT JOIN stu_acad_level b ON a.stuID = b.stuID 
					LEFT JOIN course_sec c ON a.courseSecID = c.courseSecID 
                    LEFT JOIN person d ON a.stuID = d.personID 
					WHERE c.courseSecID = ? 
					AND c.termCode = a.termCode 
					AND a.status IN('A','N','D') 
					AND b.addDate = (SELECT MAX(addDate) FROM stu_acad_level WHERE stuID = a.stuID) 
					GROUP BY a.stuID,a.courseSecCode,a.termCode", [ $id]);
            $q = $sros->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            $stu = $app->db->stu_course_sec()->select('COUNT(stu_course_sec.stuID) AS count')
                ->where('courseSecID = ?', $id)->_and_()
                ->whereIn('status', ['A', 'N', 'D']);

            $count = $stu->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
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


        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($q == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['stuID']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('section/templates/roster/' . $template . '.template', [
                'sros' => $q,
                'count' => $count
                ]
            );
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/catalog.*', function() {
        if (!hasPermission('access_course_sec_screen')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->get('/catalog/', function () use($app) {

        try {
            $cat = $app->db->course_sec()
                ->select('course_sec.termCode,COUNT(course_sec.courseSecCode) as Courses,b.termName')
                ->_join('term', 'course_sec.termCode = b.termCode', 'b')
                ->where('course_sec.currStatus = "A"')
                ->groupBy('course_sec.termCode')
                ->orderBy('course_sec.termCode', 'DESC');

            $q = $cat->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
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

        etsis_register_style('form');
        etsis_register_style('table');
        etsis_register_script('select');
        etsis_register_script('select2');
        etsis_register_script('datatables');

        $app->view->display('section/catalog', [
            'title' => 'Course Catalogs',
            'catalog' => $q
            ]
        );
    });

    $app->get('/catalog/(.*)/', function ($term) use($app) {

        try {
            $cat = $app->db->course_sec()
                ->select('courseSecCode,termCode,secShortTitle,facID')
                ->select('dotw,startTime,endTime,buildingCode,roomCode')
                ->select('locationCode,minCredit')
                ->where('termCode = ?', $term)
                ->orderBy('courseSecCode');

            $q = $cat->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
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

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($q == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_h($q[0]['courseSecCode']) <= 0) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('section/catalog-pdf', [
                'catalog' => $q
                ]
            );
        }
    });

    $app->post('/secTermLookup/', function() use($app) {
        try {
            $term = $app->db->term()
                ->select('termCode,termStartDate,termEndDate')
                ->where('termCode = ?', $app->req->post['termCode'])->_and_()
                ->where('active = "1"');
            $q = $term->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });

            foreach ($q as $v) {
                $json = array('input#startDate' => _h($v['termStartDate']), 'input#endDate' => _h($v['termEndDate']));
            }
            echo json_encode($json);
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
    });

    $app->match('GET|POST', '/stuLookup/', function() use($app) {
        try {
            $term = $app->req->get['term'];
            $stu = $app->db->student()
                ->select('student.stuID,person.altID,person.fname,person.lname')
                ->_join('person', 'student.stuID = person.personID')
                ->whereLike('student.stuID', "%" . $term . "%")->_or_()
                ->whereLike('person.altID', "%" . $term . "%")->_or_()
                ->whereLike('person.fname', "%" . $term . "%")->_or_()
                ->whereLike('person.lname', "%" . $term . "%")->_or_()
                ->whereLike('CONCAT(person.lname,", ",person.fname)', "%" . $term . "%")->_or_()
                ->whereLike('CONCAT(person.fname," ",person.lname)', "%" . $term . "%")
                ->find();
            $items = [];
            foreach ($stu as $x) {
                $option = array(
                    'id' => (_h($x->altID) == '' ? _h($x->stuID) : _h($x->altID)),
                    'label' => get_name(_h($x->stuID)),
                    'value' => get_name(_h($x->stuID))
                );
                $items[] = $option;
            }
            echo json_encode($items);
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
    });

    $app->get('/regTermLookup/', function() use($app) {
        try {
            $q = $app->db->term()
                ->select('term.termCode,term.termName')
                ->where('term.termCode <> "NULL"')->_and_()
                ->where('term.active = "1"')
                ->find();

            $items = [];
            foreach ($q as $r) {
                $option = [ 'id' => _h($r->termCode), 'value' => _h($r->termName)];
                $items[] = $option;
            }

            $data = json_encode($items);
            $response = isset($app->req->get['callback']) ? $app->req->get['callback'] . "(" . $data . ")" : $data;
            echo($response);
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
    });

    $app->get('/regSecLookup/', function() use($app) {
        try {
            // Get parameters from Array
            $id = !empty($app->req->get['id']) ? $app->req->get['id'] : '';

            $q = $app->db->course_sec()
                ->setTableAlias('sect')
                ->select('sect.courseSecID,sect.courseSection')
                ->where('sect.termCode = ?', $id)->_and_()
                ->where('sect.currStatus = "A"')
                ->find();

            $items = [];
            foreach ($q as $r) {
                $option = [ 'id' => _h($r->courseSecID), 'value' => _h($r->courseSection)];
                $items[] = $option;
            }

            $data = json_encode($items);
            $response = isset($app->req->get['callback']) ? $app->req->get['callback'] . "(" . $data . ")" : $data;
            echo($response);
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
    });

    $app->get('/defSecLookup/', function() use($app) {
        try {
            // Get parameters from Array
            $term = !empty($app->req->get['term']) ? $app->req->get['term'] : '';
            $sect = $app->db->course_sec()
                ->select('DISTINCT course_sec.courseSecID,course_sec.courseSecCode,course_sec.termCode,course_sec.courseSection')
                ->_join('stu_course_sec', 'course_sec.courseSecID = b.courseSecID', 'b')
                ->where('course_sec.termCode = ?', $term)->_and_()
                ->where('course_sec.currStatus = "A"')->_and_()
                ->whereNotNull('b.stuID');

            $q = $sect->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            $items = [];
            foreach ($q as $r) {
                $option = [ 'id' => _h($r['courseSecID']), 'value' => _h($r['courseSection'])];
                $items[] = $option;
            }

            $data = json_encode($items);
            $response = isset($app->req->get['callback']) ? $app->req->get['callback'] . "(" . $data . ")" : $data;
            echo($response);
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
    });

    $app->post('/loc/', function() use($app) {
        try {
            etsis_cache_flush_namespace('loc');
            $loc = $app->db->location();
            foreach ($app->req->post as $k => $v) {
                $loc->$k = $v;
            }
            $loc->save();
            $ID = $loc->lastInsertId();

            $location = $app->db->location()
                ->where('locationID = ?', $ID);
            $q = $location->find(function($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
            echo json_encode($q);
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
    });
});

$app->setError(function() use($app) {

    $app->view->display('error/404', ['title' => '404 Error']);
});
