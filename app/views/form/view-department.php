<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Add Department View
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
$screen = 'dept';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=url('/');?>dashbaord/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>form/department/<?=bm();?>" class="glyphicons pin_flag"><i></i> <?=_t( 'Department' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'View Department' );?></li>
</ul>

<h3><?=_t( 'View Department' );?></h3>
<div class="innerLR">
	
	<?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>form/department/<?=_h($dept[0]['deptID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
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
                            <label class="col-md-3 control-label" for="deptCode"><font color="red">*</font> <?=_t( 'Department Code' );?></label>
							<div class="col-md-8"><input class="form-control" id="deptCode"<?=gio();?> name="deptCode" type="text" value="<?=_h($dept[0]['deptCode']);?>" required /></div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="deptTypeCode"><font color="red">*</font> <?=_t( 'Department Type' );?></label>
                            <div class="col-md-8">
                                <?=dept_type_select(_h($dept[0]['deptTypeCode']));?>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label" for="deptName"><font color="red">*</font> <?=_t( 'Department Name' );?></label>
							<div class="col-md-8"><input class="form-control" id="deptName"<?=gio();?> name="deptName" type="text" value="<?=_h($dept[0]['deptName']);?>" required /></div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Department Email' );?></label>
							<div class="col-md-8"><input class="form-control"<?=gio();?> name="deptEmail" type="text" value="<?=_h($dept[0]['deptEmail']);?>" /></div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Department Phone #' );?></label>
							<div class="col-md-8"><input class="form-control"<?=gio();?> name="deptPhone" type="text" value="<?=_h($dept[0]['deptPhone']);?>" /></div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label" for="deptDesc"><?=_t( 'Short Description' );?></label>
							<div class="col-md-8"><input class="form-control" id="deptDesc"<?=gio();?> name="deptDesc" type="text" value="<?=_h($dept[0]['deptDesc']);?>" /></div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
					<button type="submit"<?=gids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
					<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>form/department/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>