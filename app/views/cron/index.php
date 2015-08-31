<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Cron Job Handler View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$message = new \app\src\Messages;
$screen = 'cron';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Cronjob Handler' );?></li>
</ul>

<h3><?=_t( 'Cronjob Handler' );?></h3>
<div class="innerLR">
    
    <?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>
    
    <!-- Form -->
    <form class="form-horizontal margin-none" action="<?=url('/');?>cron/" id="validateSubmitForm" method="post" autocomplete="off">
	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
        
        <!-- Tabs Heading -->
        <div class="tabsbar">
            <ul>
                <li class="glyphicons dashboard active"><a href="<?=url('/');?>cron/<?=bm();?>" data-toggle="tab"><i></i> <?=_t( 'Handler Dashboard' );?></a></li>
                <li class="glyphicons star"><a href="<?=url('/');?>cron/new/<?=bm();?>"><i></i> <?=_t( 'New Cronjob Handler' );?></a></li>
                <li class="glyphicons list tab-stacked"><a href="<?=url('/');?>cron/log/<?=bm();?>"><i></i> <?=_t( 'Log' );?></a></li>
                <li class="glyphicons wrench tab-stacked"><a href="<?=url('/');?>cron/setting/<?=bm();?>"><i></i> <span><?=_t( 'Settings' );?></span></a></li>
                <!-- <li class="glyphicons circle_question_mark tab-stacked"><a href="<?=url('/');?>cron/about/<?=bm();?>"><i></i> <span><?=_t( 'About' );?></span></a></li> -->
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
				<?php if (isset($_SESSION['cronjobs'], $_SESSION['cronjobs']['jobs']) && count($_SESSION['cronjobs']['jobs']) > 0) { ?>
                <?php foreach ($_SESSION['cronjobs']['jobs'] as $k => $cronjob) {?>
                <tr class="gradeX">
                    <td class="text-center"><input type="checkbox" value="xx" name="cronjobs[<?=$k;?>]" /></td>
                    <td class="text-center"><a href="<?=url('/cron/view/') . $k . '/';?>" title="Edit"><?=(strlen($cronjob['url']) > 52) ? substr($cronjob['url'], 0, 50) . '..' : $cronjob['url'];?></a></td>
                    <td class="text-center">Each <?=($cronjob['time'] != '') ? "day on " . $cronjob['time']  . ' hours' : $options[$cronjob['each']] . ((isset($cronjob['eachtime']) && strlen($cronjob['eachtime']) > 0) ? ' at ' . $cronjob['eachtime'] : '');?></td>
                    <td class="text-center"><?=($cronjob['lastrun'] !== '') ? date('M d, Y @ h:i A', strtotime($cronjob['lastrun'])) : '';?></td>
                    <td class="text-center"><?=$cronjob['runned'];?></td>
                    <td class="text-center"><?=($cronjob['savelog'] == true) ? 'Yes' : 'No'; ?><?php echo isset($_SESSION['cronjobs']['settings']) ? ' / <a target="_blank" href="'.url('/cron/cronjob/').'?password=' . $_SESSION['cronjobs']['settings']['cronjobpassword'] . '&id=' . $k . '">'._t('Run').'</a>' : '';?></td>
                </tr>
                <?php } } ?>
					
				</tbody>
				<!-- // Table body END -->
				
			</table>
			<!-- // Table END -->
            
            <?php if (isset($k, $cronjob)) { ?> 
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