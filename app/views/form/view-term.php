<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * View Term View
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
$screen = 'term';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
    <li><a href="<?=url('/');?>form/term/<?=bm();?>" class="glyphicons pin_flag"><i></i> <?=_t( 'Term' );?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'View Term' );?></li>
</ul>

<h3><?=_t( 'View Term' );?></h3>
<div class="innerLR">
	
	<?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>form/term/<?=_h($term[0]['termID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 col-md-3 control-label" for="semCode"><font color="red">*</font> <?=_t( 'Semester' );?></label>
							<div class="col-md-8">
								<select name="semCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=gio();?> required>
									<option value="">&nbsp;</option>
                            		<?php table_dropdown("semester", 'semCode <> "NULL"', "semCode", "semCode", "semName", _h($term[0]['semCode'])); ?>
                            	</select>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="termCode"><font color="red">*</font> <?=_t( 'Term Code' );?></label>
                            <div class="col-md-8"><input class="form-control"<?=gio();?> name="termCode" type="text" value="<?=_h($term[0]['termCode']);?>" required /></div>
                        </div>
                        <!-- // Group END -->
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label" for="termName"><font color="red">*</font> <?=_t( 'Term' );?></label>
							<div class="col-md-8"><input class="form-control" id="termName"<?=gio();?> name="termName" type="text" value="<?=_h($term[0]['termName']);?>" required /></div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="reportingTerm"><font color="red">*</font> <?=_t( 'Reporting Term' );?></label>
                            <div class="col-md-8"><input class="form-control" id="reportingTerm"<?=gio();?> name="reportingTerm" type="text" value="<?=_h($term[0]['reportingTerm']);?>" required /></div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
					    
					    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="termStartDate"><font color="red">*</font> <?=_t( 'Start Date' );?></label>
                            <div class="col-md-8">
                                <div class="input-group date col-md-8" id="datepicker8">
                                    <input class="form-control"<?=gio();?> name="termStartDate" type="text" value="<?=_h($term[0]['termStartDate']);?>" required />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label" for="termEndDate"><font color="red">*</font> <?=_t( 'End Date' );?></label>
							<div class="col-md-8">
								<div class="input-group date col-md-8" id="datepicker9">
						    		<input class="form-control"<?=gio();?> name="termEndDate" type="text" value="<?=_h($term[0]['termEndDate']);?>" required />
				    				<span class="input-group-addon"><i class="fa fa-th"></i></span>
								</div>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="dropAddEndDate"><font color="red">*</font> <?=_t( 'Drop/Add End Date' );?></label>
                            <div class="col-md-8">
                                <div class="input-group date col-md-8" id="datepicker10">
                                    <input class="form-control" name="dropAddEndDate" type="text" value="<?=_h($term[0]['dropAddEndDate']);?>" required />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label" for="term"><font color="red">*</font> <?=_t( 'Active' );?></label>
							<div class="col-md-8">
								<select name="active" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=gio();?> required>
									<option value="">&nbsp;</option>
                            		<option value="1"<?php if($term[0]['active'] == '1') { echo 'selected="selected"'; } ?>><?=_t( 'Yes' );?></option>
                            		<option value="0"<?php if($term[0]['active'] == '0') { echo 'selected="selected"'; } ?>><?=_t( 'No' );?></option>
                            	</select>
							</div>
						</div>
						<!-- // Group END -->
					</div>
					
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
					<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
					<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>form/term/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
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