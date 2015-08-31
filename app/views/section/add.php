<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Add section view.
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
$screen = 'csect';
?>

<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#termCode').live('change', function(event) {
        $.ajax({
            type    : 'POST',
            url     : '<?=url('/');?>sect/secTermLookup/',
            dataType: 'json',
            data    : $('#validateSubmitForm').serialize(),
            cache: false,
            success: function( data ) {
                   for(var id in data) {        
                          $(id).val( data[id] );
                   }
            }
        });
    });
});
$(function(){
    $('#sectionNumber').keyup(function() {
        $('#section').text($(this).val());
    });
});
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>sect/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Section' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Create Section' );?></li>
</ul>

<h3><?=_h($sect[0]['courseCode']);?>-<sec id="section"></sec></h3>
<div class="innerLR">
    
    <?php jstree_sidebar_menu($screen,'',$sect); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>sect/add/<?=_h($sect[0]['courseID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
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
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Section' );?></label>
                            <div class="col-md-2">
                                <input type="text" name="sectionNumber" id="sectionNumber" class="form-control" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Term' );?></label>
							<div class="col-md-8" id="divTerm">
								<select name="termCode" id="termCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="">&nbsp;</option>
                            		<?php table_dropdown('term', 'termCode <> "NULL"', 'termCode', 'termCode', 'termName'); ?>
                            	</select>
							</div>
						</div>
						<!-- // Group END -->
						
						<?php do_action( 'create_course_section_field_left' ); ?>
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Start / End' );?></label>
                            <div class="col-md-4">
                                <div class="input-group date col-md-12" id="datepicker6">
                                    <input class="form-control"<?=csio();?> id="startDate" name="startDate" type="text" required />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="input-group date col-md-12" id="datepicker7">
                                    <input class="form-control"<?=csio();?> id="endDate" name="endDate" type="text" required />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Department' );?></label>
                            <div class="col-md-8" id="divDept">
                                <select name="deptCode" id="deptCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('department', 'deptTypeCode = "acad" AND deptCode <> "NULL"', 'deptCode', 'deptCode', 'deptName', _h($sect[0]['deptCode'])); ?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#dept" data-toggle="modal" title="Department" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( "Credits / CEU's" );?></label>
                            <div class="col-md-4">
                                <input type="text" name="minCredit" readonly="readonly" class="form-control" value="<?=_h($sect[0]['minCredit']);?>" required/>
                            </div>
                            
                            <div class="col-md-4">
                                <input type="text" name="ceu" readonly="readonly" value="0.0" class="form-control" />
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Course Level' );?></label>
                            <div class="col-md-8">
                                <?=course_level_select(_h($sect[0]['courseLevelCode']));?>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Academic Level' );?></label>
                            <div class="col-md-8">
                                <?=acad_level_select(_h($sect[0]['acadLevelCode']),null,'required');?>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Short Title' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="secShortTitle" class="form-control" value="<?=_h($sect[0]['courseShortTitle']);?>" maxlength="25" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Location' );?></label>
                            <div class="col-md-8" id="divLoc">
                                <select name="locationCode" id="locationCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('location', 'locationCode <> "NULL"', 'locationCode', 'locationCode', 'locationName'); ?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#loc" data-toggle="modal" title="Location" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Section Type' );?></label>
							<div class="col-md-8">
								<select name="secType" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="">&nbsp;</option>
	                        		<option value="ONL"><?=_t( 'ONL Online' );?></option>
	                        		<option value="HB"><?=_t( 'HB Hybrid' );?></option>
	                        		<option value="ONC"><?=_t( 'ONC On-Campus' );?></option>
	                        	</select>
	                       </div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Instructor Method' );?></label>
                            <div class="col-md-8">
                                <?=instructor_method();?>
                           </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
                        
                        <?php do_action( 'create_course_section_field_right' ); ?>
                        
                        <!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'Meeting Days' );?></label>
							<div class="col-md-8 widget-body uniformjs">
    							<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="Su" />
									<?=_t( 'Sunday' );?>
								</label>
								<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="M" />
									<?=_t( 'Monday' );?>
								</label>
								<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="Tu" />
									<?=_t( 'Tuesday' );?>
								</label>
								<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="W" />
									<?=_t( 'Wednesday' );?>
								</label>
								<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="Th" />
									<?=_t( 'Thursday' );?>
								</label>
								<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="F" />
									<?=_t( 'Friday' );?>
								</label>
								<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="Sa" />
									<?=_t( 'Saturday' );?>
								</label>
							</div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Start Time' );?></label>
                            <div class="col-md-4">
                                <div class="input-group bootstrap-timepicker">
            				        <input id="timepicker10" type="text" name="startTime" class="form-control" />
            				        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
						        </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'End Time' );?></label>
                            <div class="col-md-4">
                                <div class="input-group bootstrap-timepicker">
        					        <input id="timepicker11" type="text" name="endTime" class="form-control" />
        					        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
						        </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Register Online' );?></label>
                            <div class="col-md-8">
                                <select name="webReg" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="1"><?=_t( 'Yes' );?></option>
                                    <option value="0"><?=_t( 'No' );?></option>
                                </select>
                           </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Status / Date' );?></label>
                            <div class="col-md-4">
                                <?=course_sec_status_select();?>
                            </div>
                            
                            <div class="col-md-4">
                                <input class="form-control" type="text" readonly="readonly" value="<?=date('D, M d, o',strtotime(date('Y-m-d')));?>" />
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
								<input type="text" readonly="readonly" value="<?=date('D, M d, o',strtotime(date('Y-m-d')));?>" class="form-control" />
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Comments' );?></label>
                            <div class="col-md-8">
                                <textarea class="form-control" name="comment" rows="3" data-height="auto"></textarea>
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
	
</div>	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>