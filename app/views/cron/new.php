<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * New Cronjob Handler View
 *  
 * @license GPLv3
 * 
 * @since       6.0.00
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$screen = 'cron';
$options = [
                 30        => '30 seconds',
                 60        => 'Minute',
                 120       => '2 minutes',
                 300       => '5 minutes',
                 600       => '10 minutes',
                 900       => '15 minutes',
                 1800      => 'Half hour',
                 2700      => '45 minutes',
                 3600      => 'Hour', 
                 7200      => '2 hours', 
                 14400     => '4 hours', 
                 43200     => '12 hours',
                 86400     => 'Day', 
                 172800    => '2 days', 
                 259200    => '3 days', 
                 604800    => 'Week', 
                 209600    => '2 weeks', 
                 2629743   => 'Month'
];
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'New Cronjob Handler' );?></li>
</ul>

<h3><?=_t( 'New Cronjob Handler' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>cron/new/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
            
            <!-- Tabs Heading -->
            <div class="tabsbar">
                <ul>
                    <li class="glyphicons dashboard"><a href="<?=get_base_url();?>cron/"><i></i> <?=_t( 'Handler Dashboard' );?></a></li>
                    <li class="glyphicons star active"><a href="<?=get_base_url();?>cron/new/" data-toggle="tab"><i></i> <?=_t( 'New Cronjob Handler' );?></a></li>
                    <li class="glyphicons wrench tab-stacked"><a href="<?=get_base_url();?>cron/setting/"><i></i> <span><?=_t( 'Settings' );?></span></a></li>
                </ul>
            </div>
            <!-- // Tabs Heading END -->
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Handler Name' );?></label>
							<div class="col-md-8">
								<input type="text" name="name" class="form-control" required/>
							</div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( "Cronjob URL" );?></label>
                            <div class="col-md-8">
                                <input type="text" name="url" class="form-control" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( "Each / Time" );?> <a href="#each" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-4">
                                <select name="each" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <?php 
                                    foreach ($options as $each => $key) {
                                        $s = (isset($app->req->post['each']) && $app->req->post['each'] == $each) ? ' selected="selected"' : '';
                                    ?>
                                    <option value="<?=$each;?>"<?=$s;?>><?=$key; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <select name="eachtime" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <?php 
                                    for ($x = 0; $x < 24;$x++) {
                                        for ($y = 0; $y < 4; $y++) {
                                            $time = ((strlen($x) == 1) ? '0' . $x : $x) . ':' . (($y == 0) ? '00' : ($y * 15));

                                            $s = (isset($app->req->post['eachtime']) && $app->req->post['eachtime'] == $time) ? ' selected="selected"' : '';
                                    ?>
                                    <option value="<?=$time;?>"<?=$s;?>><?=$time;?></option>
                                    <?php } } ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( "Status" );?></label>
                            <div class="col-md-8">
                                <select name="status" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option>&nbsp;</option>
                                    <option value="1"><?=_t( "Active" );?></option>
                                    <option value="0"><?=_t( "Inactive" );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
                    
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<div class="separator line bottom"></div>
				
				<!-- Form actions -->
				<div class="form-actions">
					<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
    
    <div class="modal fade" id="each">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Each / Time' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t("Set the time in seconds (Each) of how often the cronjob should run (i.e. 2 minute, Hour or every Day / 07:00.)");?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	        </div>
      	</div>
    </div>
	
</div>	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>