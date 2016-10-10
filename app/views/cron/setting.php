<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Cronjob Handler Settings View
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
	<li><?=_t( 'Cronjob Handler Settings' );?></li>
</ul>

<h3><?=_t( 'Cronjob Handler Settings' );?></h3>
<div class="innerLR">
    
    <?=$flash->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>cron/setting/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
            
            <!-- Tabs Heading -->
            <div class="tabsbar">
                <ul>
                    <li class="glyphicons dashboard"><a href="<?=get_base_url();?>cron/<?=bm();?>"><i></i> <?=_t( 'Handler Dashboard' );?></a></li>
                    <li class="glyphicons star"><a href="<?=get_base_url();?>cron/new/<?=bm();?>"><i></i> <?=_t( 'New Cronjob Handler' );?></a></li>
                    <li class="glyphicons list tab-stacked"><a href="<?=get_base_url();?>cron/log/<?=bm();?>"><i></i> <?=_t( 'Log' );?></a></li>
                    <li class="glyphicons wrench tab-stacked active"><a href="<?=get_base_url();?>cron/setting/<?=bm();?>" data-toggle="tab"><i></i> <span><?=_t( 'Settings' );?></span></a></li>
                    <!-- <li class="glyphicons circle_question_mark tab-stacked"><a href="<?=get_base_url();?>cron/about/<?=bm();?>"><i></i> <span><?=_t( 'About' );?></span></a></li> -->
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
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Cronjob Password' );?>  <a href="#cronpass" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
							<div class="col-md-8">
								<input type="text" id="cronjobpassword" name="cronjobpassword" value="<?=_h($data['settings']['cronjobpassword']);?>" class="form-control" required/>
							</div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( "Cronjob Timeout" );?> <a href="#crontimeout" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <input type="text" id="timeout" name="timeout" value="<?=($data['settings']['timeout'] !== null) ? $data['settings']['timeout'] : 30;?>" class="form-control" required/>
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
	
	<div class="modal fade" id="cronpass">
		<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Cronjob Password' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t( "This password is required in order to run your master cronjob (i.e. http://localhost/etsis/cron/cronjob?password=CRONPASSWORD)." );?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	       	</div>
      	</div>
    </div>
    
    <div class="modal fade" id="crontimeout">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Cronjob Timeout' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t( "The number in seconds that a cronjob can run before it should time out." );?></p>
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