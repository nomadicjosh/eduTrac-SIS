<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Cron Job Handler View
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
$screen = 'cron';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Cronjob Handler' );?></li>
</ul>

<h3><?=_t( 'Cronjob Handler' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>
    
    <!-- Form -->
    <form class="form-horizontal margin-none" action="<?=get_base_url();?>cron/" id="validateSubmitForm" method="post" autocomplete="off">
	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
        
        <!-- Tabs Heading -->
        <div class="tabsbar">
            <ul>
                <li class="glyphicons dashboard active"><a href="<?=get_base_url();?>cron/" data-toggle="tab"><i></i> <?=_t( 'Handler Dashboard' );?></a></li>
                <li class="glyphicons star"><a href="<?=get_base_url();?>cron/new/"><i></i> <?=_t( 'New Cronjob Handler' );?></a></li>
                <li class="glyphicons wrench tab-stacked"><a href="<?=get_base_url();?>cron/setting/"><i></i> <span><?=_t( 'Settings' );?></span></a></li>
            </ul>
        </div>
        <!-- // Tabs Heading END -->
        
		<div class="widget-body">
		
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
			
				<!-- Table heading -->
				<thead>
					<tr>
                        <th class="text-center"></th>
						<th class="text-center"><?= _t('Cronjob'); ?></th>
                        <th class="text-center"><?= _t('Time/Each'); ?></th>
                        <th class="text-center"><?= _t('Last Run'); ?></th>
                        <th class="text-center"><?= _t('# Runs'); ?></th>
                        <th class="text-center"><?= _t('Status'); ?></th>
                        <th class="text-center"><?= _t('Action'); ?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
                <?php foreach ($cron as $job) {?>
                <tr class="gradeX">
                    <td class="text-center"><input type="checkbox" value="<?=_h((int)$job->id);?>" name="cronjobs[<?=_h((int)$job->id);?>]" /></td>
                    <td class="text-center"><?= _h($job->name); ?></td>
                    <td class="text-center"><?= _t('Each'); ?> <?= (_h($job->time) != 0) ? "day on " . _h($job->time) . ' hours' : etsis_seconds_to_time(_h($job->each)) . (strlen(_h($job->eachtime) > 0) ? ' at ' . _h($job->eachtime) : ''); ?></td>
                    <td class="text-center"><?= (_h($job->lastrun) !== '' ? Jenssegers\Date\Date::parse(_h($job->lastrun))->format('M. d, Y @ h:i A') : ''); ?></td>
                    <td class="text-center">
                        <span class="label label-inverse" style="font-size:1em;font-weight: bold;">
                            <?= _h((int)$job->runned); ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="label <?=etsis_cron_status_label(_h($job->status));?>" style="font-size:1em;font-weight: bold;">
                            <?= (_h((int)$job->status) == 1 ? _t('Active') : _t('Inactive')); ?>
                        </span>
                    </td>
                    <?php foreach ($set as $s) : ?>
                        <td class="text-center">
                            <?= isset($s) ? '<a href="' . get_base_url() . 'cron' . '/' . _h((int)$job->id) . '/" data-toggle="tooltip" data-placement="top" title="View/Edit"><button type="button" class="button bg-yellow"><i class="fa fa-edit"></i></button></a>' : ''; ?>
                            <?= isset($s) ? '<a target="_blank" href="' . get_base_url() . 'cron/cronjob' . '/' . '?password=' . _h($s->cronjobpassword) . '&id=' . _h((int)$job->id) . '" data-toggle="tooltip" data-placement="top" title="Run"><button type="button" class="button bg-purple"><i class="fa fa-chevron-right"></i></button></a>' : ''; ?>
                            <?= isset($s) ? '<a href="' . get_base_url() . 'cron' . '/' . _h((int)$job->id) . '/' . 'reset' . '/" data-toggle="tooltip" data-placement="top" title="Reset Runs"><button type="button" class="button bg-blue"><i class="fa fa-refresh"></i></button></a>' : ''; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php } ?>
					
				</tbody>
				<!-- // Table body END -->
				
			</table>
			<!-- // Table END -->
            
            <?php if (isset($job->id)) { ?> 
            <hr class="separator" />
				
            <div class="separator line bottom"></div>

            <!-- Form actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Delete selected handler(s)' );?></button>
            </div>
            <!-- // Form actions END -->
			<?php } ?>
		</div>
	</div>
    </form>
	<div class="separator bottom"></div>
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>