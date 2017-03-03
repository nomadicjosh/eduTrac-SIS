<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use app\src\Core\Exception\NotFoundException;
use app\src\Core\Exception\Exception;
use PDOException as ORMException;
use Cascade\Cascade;

/**
 * Academic Program Router
 *  
 * @license GPLv3
 * 
 * @since       5.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
/**
 * Before route checks to make sure the logged in user
 * us allowed to manage options/settings.
 */
$app->before('GET|POST', '/program(.*)', function() {
    if (!is_user_logged_in()) {
        redirect(get_base_url());
        exit();
    }
});

$app->group('/program', function() use ($app) {

    $app->match('GET|POST', '/', function () use($app) {
        if ($app->req->isPost()) {
            try {
                $post = $app->req->post['prog'];
                $prog = $app->db->query("SELECT 
                    CASE currStatus 
                    WHEN 'A' THEN 'Active' 
                    WHEN 'I' THEN 'Inactive' 
                    WHEN 'P' THEN 'Pending' 
                    ELSE 'Obsolete' 
                    END AS 'Status', 
                    acadProgID,
                    acadProgCode,
                    acadProgTitle,
                    startDate,
                    endDate 
                FROM acad_program 
                WHERE acadProgCode LIKE ?", [ "%$post%"]
                );

                $q = $prog->find(function($data) {
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

        $app->view->display('program/index', [
            'title' => 'Search Academic Program',
            'prog' => $q
            ]
        );
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app) {
        $program = get_acad_program($id);

        if ($app->req->isPost()) {
            try {
                $prog = $app->db->acad_program();
                $prog->acadProgCode = $app->req->post['acadProgCode'];
                $prog->acadProgTitle = $app->req->post['acadProgTitle'];
                $prog->programDesc = $app->req->post['programDesc'];
                $prog->currStatus = $app->req->post['currStatus'];
                if ($program->currStatus !== $app->req->post['currStatus']) {
                    $prog->statusDate = $app->db->NOW();
                }
                $prog->deptCode = $app->req->post['deptCode'];
                $prog->schoolCode = $app->req->post['schoolCode'];
                $prog->acadYearCode = $app->req->post['acadYearCode'];
                $prog->startDate = $app->req->post['startDate'];
                $prog->endDate = $app->req->post['endDate'];
                $prog->degreeCode = $app->req->post['degreeCode'];
                $prog->ccdCode = $app->req->post['ccdCode'];
                $prog->majorCode = $app->req->post['majorCode'];
                $prog->minorCode = $app->req->post['minorCode'];
                $prog->specCode = $app->req->post['specCode'];
                $prog->acadLevelCode = $app->req->post['acadLevelCode'];
                $prog->cipCode = $app->req->post['cipCode'];
                $prog->locationCode = $app->req->post['locationCode'];
                $prog->where('acadProdID = ?', $app->req->post['acadProgID']);

                /**
                 * Fires during the update of an academic program.
                 *
                 * @since 6.1.10
                 * @param array $prog Academic program object.
                 */
                $app->hook->{'do_action'}('update_acad_program_db_table', $prog);
                $prog->update();

                etsis_cache_delete($id, 'prog');
                etsis_logger_activity_log_write('Update', 'Acad Program', _h($program->acadProgCode), get_persondata('uname'));
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
        if ($program == false) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($program) == true) {

            $app->view->display('error/404', ['title' => '404 Error']);
        }
        /**
         * If data is zero, 404 not found.
         */ elseif (_h($program->acadProgID) <= 0) {

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

            $app->view->display('program/view', [
                'title' => _h($program->acadProgTitle) . ' :: Academic Program',
                'prog' => $program
                ]
            );
        }
    });

    /**
     * Before route checks to make sure the logged in user
     * us allowed to manage options/settings.
     */
    $app->before('GET|POST', '/add/', function() {
        if (!hasPermission('add_acad_prog')) {
            _etsis_flash()->error(_t('Permission denied to view requested screen.'), get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/add/', function () use($app) {

        if ($app->req->isPost()) {
            try {
                $prog = $app->db->acad_program();
                $prog->acadProgCode = $app->req->post['acadProgCode'];
                $prog->acadProgTitle = $app->req->post['acadProgTitle'];
                $prog->programDesc = $app->req->post['programDesc'];
                $prog->currStatus = $app->req->post['currStatus'];
                $prog->statusDate = $app->db->NOW();
                $prog->approvedDate = $app->db->NOW();
                $prog->approvedBy = get_persondata('personID');
                $prog->deptCode = $app->req->post['deptCode'];
                $prog->schoolCode = $app->req->post['schoolCode'];
                $prog->acadYearCode = $app->req->post['acadYearCode'];
                $prog->startDate = $app->req->post['startDate'];
                $prog->endDate = $app->req->post['endDate'];
                $prog->degreeCode = $app->req->post['degreeCode'];
                $prog->ccdCode = $app->req->post['ccdCode'];
                $prog->majorCode = $app->req->post['majorCode'];
                $prog->minorCode = $app->req->post['minorCode'];
                $prog->specCode = $app->req->post['specCode'];
                $prog->acadLevelCode = $app->req->post['acadLevelCode'];
                $prog->cipCode = $app->req->post['cipCode'];
                $prog->locationCode = $app->req->post['locationCode'];

                /**
                 * Fires during the saving/creating of an academic program.
                 *
                 * @since 6.1.10
                 * @param array $prog Academic program object.
                 */
                $app->hook->do_action('save_acad_program_db_table', $prog);
                $prog->save();

                $ID = $prog->lastInsertId();
                etsis_cache_flush_namespace('prog');
                etsis_logger_activity_log_write('New Record', 'Acad Program', $app->req->post['acadProgCode'], get_persondata('uname'));
                _etsis_flash()->success(_etsis_flash()->notice(200), get_base_url() . 'program' . '/' . $ID . '/');
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
        etsis_register_script('datepicker');

        $app->view->display('program/add', [
            'title' => 'Add Academic Program'
            ]
        );
    });

    $app->post('/year/', function() use($app) {
        try {
            etsis_cache_flush_namespace('ayr');
            $year = $app->db->acad_year();
            foreach ($app->req->post as $k => $v) {
                $year->$k = $v;
            }
            $year->save();
            $ID = $year->lastInsertId();

            $acad = $app->db->acad_year()
                ->where('acadYearID = ?', $ID);
            $q = $acad->find(function($data) {
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

    $app->post('/degree/', function() use($app) {
        try {
            etsis_cache_flush_namespace('deg');
            $deg = $app->db->degree();
            foreach ($app->req->post as $k => $v) {
                $deg->$k = $v;
            }
            $deg->save();
            $ID = $deg->lastInsertId();

            $degree = $app->db->degree()
                ->where('degreeID = ?', $ID);
            $q = $degree->find(function($data) {
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

    $app->post('/ccd/', function() use($app) {
        try {
            etsis_cache_flush_namespace('ccd');
            $c = $app->db->ccd();
            foreach ($app->req->post as $k => $v) {
                $c->$k = $v;
            }
            $c->save();
            $ID = $c->lastInsertId();

            $ccd = $app->db->ccd()
                ->where('ccdID = ?', $ID);
            $q = $ccd->find(function($data) {
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

    $app->post('/major/', function() use($app) {
        try {
            etsis_cache_flush_namespace('majr');
            $maj = $app->db->major();
            foreach ($app->req->post as $k => $v) {
                $maj->$k = $v;
            }
            $maj->save();
            $ID = $maj->lastInsertId();

            $major = $app->db->major()
                ->where('majorID = ?', $ID);
            $q = $major->find(function($data) {
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

    $app->post('/minor/', function() use($app) {
        try {
            etsis_cache_flush_namespace('minr');
            $min = $app->db->minor();
            foreach ($app->req->post as $k => $v) {
                $min->$k = $v;
            }
            $min->save();
            $ID = $min->lastInsertId();

            $minor = $app->db->minor()
                ->where('minorID = ?', $ID);
            $q = $minor->find(function($data) {
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

    $app->post('/spec/', function() use($app) {
        try {
            etsis_cache_flush_namespace('spec');
            $spec = $app->db->specialization();
            foreach ($app->req->post as $k => $v) {
                $spec->$k = $v;
            }
            $spec->save();
            $ID = $spec->lastInsertId();

            $specialization = $app->db->specialization()
                ->where('specID = ?', $ID);
            $q = $specialization->find(function($data) {
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

    $app->post('/cip/', function() use($app) {
        try {
            etsis_cache_flush_namespace('cip');
            $c = $app->db->cip();
            foreach ($app->req->post as $k => $v) {
                $c->$k = $v;
            }
            $c->save();
            $ID = $c->lastInsertId();

            $cip = $app->db->cip()
                ->where('cipID = ?', $ID);
            $q = $cip->find(function($data) {
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
