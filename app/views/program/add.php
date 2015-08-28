<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Add Academic Program View
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
$screen = 'aprog';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>program/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Program' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Create Program' );?></li>
</ul>

<h3><?=_t( 'Add Academic Program' );?></h3>
<div class="innerLR">
    
    <?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>program/add/" id="validateSubmitForm" method="post" autocomplete="off">
		
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
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Program Code' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" name="acadProgCode" required />
                            </div>
                        </div>
                        <!-- // Group END -->
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Title' );?></label>
							<div class="col-md-8">
                                <input class="form-control" type="text" name="acadProgTitle" required />
                            </div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Short Description' );?></label>
							<div class="col-md-8">
                                <input class="form-control" type="text" name="programDesc" required />
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
                                <input class="form-control" type="text" readonly value="<?=date('D, M d, o',strtotime(date("Y-m-d")));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Approval Person' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" readonly value="<?=get_name(get_persondata('personID'));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Approval Date' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" readonly value="<?=date('D, M d, o',strtotime(date('Y-m-d')));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Department' );?></label>
                            <div class="col-md-8" id="divDept">
                                <select name="deptCode" id="deptCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('department','deptCode <> "NULL"','deptCode','deptCode','deptName');?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#dept" data-toggle="modal" title="Department" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'School' );?></label>
                            <div class="col-md-8" id="divSchool">
                                <select name="schoolCode" id="schoolCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('school','schoolCode <> "NULL"','schoolCode','schoolCode','schoolName');?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Effective Catalog Year' );?></label>
                            <div class="col-md-8" id="divYear">
                                <select name="acadYearCode" id="acadYearCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('acad_year','acadYearCode <> "NULL"','acadYearCode','acadYearCode','acadYearDesc');?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#year" data-toggle="modal" title="Academic Year" class="btn btn-primary"><i class="fa fa-plus"></i></a>
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
                                    <input class="form-control" name="startDate" type="text" required />
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
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Degree' );?></label>
                            <div class="col-md-8" id="divDegree">
                                <select name="degreeCode" id="degreeCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('degree','degreeCode <> "NULL"','degreeCode','degreeCode','degreeName');?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#degree" data-toggle="modal" title="Degree" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'CCD' );?></label>
                            <div class="col-md-8" id="divCCD">
                                <select name="ccdCode" id="ccdCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('ccd','ccdCode <> "NULL"','ccdCode','ccdCode','ccdName');?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#ccd" data-toggle="modal" title="CCD" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Major' );?></label>
                            <div class="col-md-8" id="divMajor">
                                <select name="majorCode" id="majorCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('major','majorCode <> "NULL"','majorCode','majorCode','majorName');?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#major" data-toggle="modal" title="Major" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Minor' );?></label>
                            <div class="col-md-8" id="divMinor">
                                <select name="minorCode" id="minorCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('minor','minorCode <> "NULL"','minorCode','minorCode','minorName');?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#minor" data-toggle="modal" title="Minor" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Specialization' );?></label>
                            <div class="col-md-8" id="divSpec">
                                <select name="specCode" id="specCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('specialization', 'specCode <> "NULL"', 'specCode', 'specCode', 'specName'); ?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#spec" data-toggle="modal" title="Specialization" class="btn btn-primary"><i class="fa fa-plus"></i></a>
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
                            <label class="col-md-3 control-label"><?=_t( 'CIP' );?></label>
                            <div class="col-md-8" id="divCIP">
                                <select name="cipCode" id="cipCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('cip','cipCode <> "NULL"','cipCode','cipCode','cipName');?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#cip" data-toggle="modal" title="CIP" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Location' );?></label>
                            <div class="col-md-8" id="divLoc">
                                <select name="locationCode" id="locationCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('location','locationCode <> "NULL"','locationCode','locationCode','locationName');?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#loc" data-toggle="modal" title="Location" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
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