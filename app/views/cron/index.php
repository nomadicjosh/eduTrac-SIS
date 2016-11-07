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
$flash = new \app\src\Core\etsis_Messages();
$screen = 'cron';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Cronjob Handler' );?></li>
</ul>

<h3><?=_t( 'Cronjob Handler' );?></h3>
<div class="innerLR">
    
    <?=$flash->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>
    
    <!-- Form -->
    <form class="form-horizontal margin-none" action="<?=get_base_url();?>cron/" id="validateSubmitForm" method="post" autocomplete="off">
	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
        
        <!-- Tabs Heading -->
        <div class="tabsbar">
            <ul>
                <li class="glyphicons dashboard active"><a href="<?=get_base_url();?>cron/<?=bm();?>" data-toggle="tab"><i></i> <?=_t( 'Handler Dashboard' );?></a></li>
                <li class="glyphicons star"><a href="<?=get_base_url();?>cron/new/<?=bm();?>"><i></i> <?=_t( 'New Cronjob Handler' );?></a></li>
                <li class="glyphicons wrench tab-stacked"><a href="<?=get_base_url();?>cron/setting/<?=bm();?>"><i></i> <span><?=_t( 'Settings' );?></span></a></li>
                <!-- <li class="glyphicons circle_question_mark tab-stacked"><a href="<?=get_base_url();?>cron/about/<?=bm();?>"><i></i> <span><?=_t( 'About' );?></span></a></li> -->
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
						<th class="text-center"><?=_t( 'Cronjob' );?></th>
						<th class="text-center"><?=_t( 'Time/Each' );?></th>
						<th class="text-center"><?=_t( 'Last Run' );?></th>
						<th class="text-center"><?=_t( '# Runs' );?></th>
                        <th class="text-center"><?=_t( 'Logs/Run' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
                <?php foreach ($cron as $job) {?>
                <tr class="gradeX">
                    <td class="text-center"><input type="checkbox" value="<?=$job->id;?>" name="cronjobs[<?=$job->id;?>]" /></td>
                    <td class="text-center"><a href="<?=get_base_url() . 'cron/view/' . $job->id . '/';?>" title="Edit"><?=$job->name;?></a></td>
                    <td class="text-center">Each <?=($job->time != 0) ? "day on " . $job->time  . ' hours' : etsis_seconds_to_time($job->each) . (strlen($job->eachtime > 0) ? ' at ' . $job->eachtime : '');?></td>
                    <td class="text-center"><?=($job->lastrun !== '') ? date('M d, Y @ h:i A', strtotime($job->lastrun)) : '';?></td>
                    <td class="text-center"><?=$job->runned;?></td>
                    <?php foreach ($set as $s) : ?>
                    <td class="text-center"><?=isset($s) ? '<a target="_blank" href="'.get_base_url(). 'cron/cronjob' . '/' . '?password=' . $s->cronjobpassword . '&id=' . $job->id . '">'._t('Run').'</a>' : '';?></td>
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