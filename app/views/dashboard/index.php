<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * Dashboard View
 *  
 * @license GPLv3
 * 
 * @since       3.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$screen = 'dash';

?>

<ul class="breadcrumb">
    <li><?= _t('You are here'); ?></li>
    <li><a href="#" class="glyphicons dashboard"><i></i> <?= _t('Dashboard'); ?></a></li>
    <li class="pull-right hidden-phone"><a href="<?= _h(get_option('help_desk')); ?>" class="glyphicons shield"><?= _t('Get Help'); ?><i></i></a></li>
    <li class="pull-right hidden-phone divider"></li>

</ul>

<h2><?= _t('Dashboard'); ?></h2>
<div class="innerLR">

    <div class="row">

        <?= _etsis_flash()->showMessage(); ?>

        <?php jstree_sidebar_menu($screen); ?>

        <div class="<?= ($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10'; ?> tablet-column-reset">

            <div class="row">
                <?php dashboard_top_widgets(); ?>
            </div>

        </div>

        <div class="<?= ($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10'; ?> tablet-column-reset">

            <div class="row">
                <div class="col-md-12">

                    <!-- Students by Academic Program Chart -->
                    <div class="widget widget-body-white">
                        <div class="widget-head">
                            <h4 class="heading glyphicons cardio"><i></i><?= _t('Students by Academic Program'); ?></h4>
                        </div>
                        <div class="widget-body">

                            <!-- Simple Chart -->
                            <div class="widget-chart">
                                <div class="chart" id="getSACP" style="position: relative; height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                    <!-- // Students by Academic Program Chart END -->

                </div>

                <div class="col-md-12">

                    <!-- Gender by Academic Departments Chart -->
                    <div class="widget widget-body-white">
                        <div class="widget-head">
                            <h4 class="heading glyphicons parents"><i></i><?= _t('Gender by Academic Departments'); ?></h4>
                        </div>
                        <div class="widget-body">

                            <!-- Simple Chart -->
                            <div class="widget-chart">
                                <div class="chart" id="getDEPT" style="position: relative; height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                    <!-- // Gender by Academic Departments Chart END -->
                </div>

            </div>
        </div>
    </div>

</div>


</div>
<!-- // Content END -->
<?php
$app->view->stop();
