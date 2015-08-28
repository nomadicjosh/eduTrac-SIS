<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Edit Course View
 * 
 * This view renders the course record for the CRSE screen.
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
$message = new \app\src\Messages;
include('ajax.php');
$screen = 'acrse';
?>

<script src="//tinymce.cachefly.net/4.2/tinymce.min.js"></script>
<script src="<?=url('/');?>static/assets/plugins/tinymce/plugin.js"></script>
<script type="text/javascript">
	tinymce.init({selector: "textarea",plugins: [ "placeholder" ]});
	$(".panel").show();
	setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>crse/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Course' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Create Course' );?></li>
</ul>

<h3><?=_t( 'Add Course' );?></h3>
<div class="innerLR">
    
    <?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>crse/add/" id="validateSubmitForm" method="post" autocomplete="off">
        
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
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Department' );?></label>
							<div class="col-md-8" id="divDept">
								<select name="deptCode" id="deptCode" class="selectpicker divSelect form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="">&nbsp;</option>
                            		<?php table_dropdown('department', 'deptTypeCode = "acad" AND deptCode <> "NULL"', 'deptCode', 'deptCode', 'deptName'); ?>
                            	</select>
							</div>
							<a<?=ae('access_forms');?> href="#dept" data-toggle="modal" title="Department" class="btn btn-primary"><i class="fa fa-plus"></i></a>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Subject' );?></label>
							<div class="col-md-8" id="divSubj">
								<select name="subjectCode" id="subjectCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="">&nbsp;</option>
	                        		<?php subject_code_dropdown(); ?>
	                        	</select>
	                       </div>
	                       <a<?=ae('access_forms');?> href="#subj" data-toggle="modal" title="Subject" class="btn btn-primary"><i class="fa fa-plus"></i></a>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Course Level' );?></label>
                            <div class="col-md-8">
                                <?=course_level_select();?>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Short Title' );?></label>
                            <div class="col-md-8"><input class="form-control" type="text" name="courseShortTitle" maxlength="25" required/></div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Long Title' );?></label>
                            <div class="col-md-8"><input class="form-control" type="text" name="courseLongTitle" maxlength="60" required/></div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Course Number' );?></label>
							<div class="col-md-8"><input class="form-control" type="text" name="courseNumber" required /></div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Effective / End Date' );?></label>
							<div class="col-md-4">
								<div class="input-group date col-md-12" id="datepicker6">
						    		<input class="form-control" name="startDate" type="text" required/>
				    				<span class="input-group-addon"><i class="fa fa-th"></i></span>
								</div>
							</div>
							
							<div class="col-md-4">
								<div class="input-group date col-md-12" id="datepicker7">
						    		<input class="form-control" name="endDate" type="text" />
				    				<span class="input-group-addon"><i class="fa fa-th"></i></span>
								</div>
							</div>
						</div>
						<!-- // Group END -->
					    
					    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Credits' );?></label>
                            <div class="col-md-8"><input class="form-control" type="text" name="minCredit" required/></div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Academic Level' );?></label>
                            <div class="col-md-8">
                                <?=acad_level_select(null,null,'required');?>
                            </div>
                        </div>
                        <!-- // Group END -->
					
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Status / Date' );?></label>
							<div class="col-md-4">
								<?=status_select();?>
							</div>
							<div class="col-md-4">
								<input class="form-control" type="text" readonly value="<?=date('D, M d, o',strtotime(date('Y-m-d')));?>" />
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Approval Person' );?></label>
                            <div class="col-md-6"><input class="form-control" type="text" readonly value="<?=get_name(get_persondata('personID'));?>" /></div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Approval Date' );?></label>
							<div class="col-md-6"><input class="form-control" type="text" readonly value="<?=date('D, M d, o',strtotime(date('Y-m-d')));?>" /></div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<div class="separator line bottom"></div>
								
				<!-- Group -->
				<div class="form-group col-md-12">
					<div class="widget-body">
						<div class="col-md-12"><textarea id="mustHaveId"<?=cio();?> class="wysihtml5 col-md-12 form-control" name="courseDesc" rows="8" placeholder="Enter the course description . . ."></textarea></div>
					</div>
				</div>
				<!-- // Group END -->
				
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
	
</div>	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>