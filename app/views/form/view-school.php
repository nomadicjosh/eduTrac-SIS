<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * View School View
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
$screen = 'sch';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>form/school/<?=bm();?>" class="glyphicons pin_flag"><i></i> <?=_t( 'School' );?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'View School' );?></li>
</ul>

<h3><?=_t( 'View School' );?></h3>
<div class="innerLR">
	
	<?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>form/school/<?=_h($school[0]['schoolID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required.' );?></h4>
			</div>
			<!-- // Widget heading END -->
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					
					<!-- Column -->
					<div class="col-md-6">
					    
					    <!-- Group -->
                        <div class="form-group">
							<label class="col-md-3 col-md-3 control-label"><font color="red">*</font> <?=_t( 'School Code' );?></label>
                            <div class="col-md-8"><input class="form-control"<?=gio();?> name="schoolCode" type="text" value="<?=$school[0]['schoolCode'];?>" required /></div>
                        </div>
                        <!-- // Group END -->
					    
					    <!-- Group -->
                        <div class="form-group">
							<label class="col-md-3 col-md-3 control-label"><font color="red">*</font> <?=_t( 'School Name' );?></label>
                            <div class="col-md-8"><input class="form-control"<?=gio();?> name="schoolName" type="text" value="<?=$school[0]['schoolName'];?>" required /></div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 col-md-3 control-label"><?=_t( 'Building' );?></label>
							<div class="col-md-8">
								<select name="buildingCode" class="selectpicker col-md-12 form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=gio();?>>
									<option value="NULL">&nbsp;</option>
                            		<?php table_dropdown('building', 'buildingCode <> "NULL"', 'buildingCode', 'buildingCode', 'buildingName',$school[0]['buildingCode']); ?>
                            	</select>
							</div>
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
					<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>form/school/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
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