<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Edit Student Program
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
$stu = get_student(_escape($sacp[0]['stuID']));
?>

<script type="text/javascript">
function addMsg(text,element_id) {
	document.getElementById(element_id).value += text;
}
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=get_base_url();?>stu/" class="glyphicons search"><i></i> <?=_t( 'Search Student' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=get_base_url();?>stu/<?=_escape($stu->stuID);?>/" class="glyphicons user"><i></i> <?=get_name(_escape($stu->stuID));?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'Edit Student Program (SACP)' );?></li>
</ul>

<div class="innerLR">
    
    <?php get_stu_header($stu->stuID); ?>
    
    <div class="separator line bottom"></div>
	
	<?=_etsis_flash()->showMessage();?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>stu/sacp/<?=_escape($sacp[0]['id']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray">
		
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
							<label class="col-md-3 control-label"><?=_t( 'Program' );?></label>
							<div class="col-md-8">
								<input type="text" readonly class="form-control" value="<?=_escape($sacp[0]['acadProgCode']);?>" />
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'School' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="<?=_escape($sacp[0]['schoolCode'].' '.$sacp[0]['schoolName']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Ant Grad Date' );?></label>
                            <div class="col-md-2">
                                <input type="text"<?=sio();?> name="antGradDate" class="form-control center" value="<?=_escape($sacp[0]['antGradDate']);?>" required />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Advisor' );?></label>
                            <div class="col-md-8">
                                <select name="advisorID"<?=sio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php facID_dropdown(_escape($sacp[0]['advisorID'])); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Academic Level' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="<?=_escape($sacp[0]['acadLevelCode']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Catalog Year' );?></label>
                            <div class="col-md-8">
                                <select name="catYearCode"<?=sio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('acad_year', 'acadYearCode <> "NULL"', 'acadYearCode', 'acadYearCode', 'acadYearDesc',_escape($sacp[0]['catYearCode'])); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
						<div class="form-group">
						    <label class="col-md-3 control-label"><?=_t( 'Eligible to Grad?' );?> <a href="#myModal" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
							<div class="col-md-4">
								<select name="eligible_to_graduate"<?=sio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected('1',_escape($sacp[0]['eligible_to_graduate']),false);?>><?=_t( 'Yes' );?></option>
                                    <option value="0"<?=selected('0',_escape($sacp[0]['eligible_to_graduate']),false);?>><?=_t( 'No' );?></option>
                                </select>
							</div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Status' );?></label>
                            <div class="col-md-8">
                                <?=sacp_status_select(_escape($sacp[0]['currStatus']));?>
                            </div>
                        </div>
                        <!-- // Group END -->
					    
					    <?php if(_escape($sacp[0]['currStatus']) == 'G' || _escape($sacp[0]['graduationDate']) > '0000-00-00') { ?>
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Graduation Date' );?></label>
                            <div class="col-md-8">
                                <div class="input-group date" id="datepicker6">
                                    <input class="form-control"<?=sio();?> name="graduationDate"<?=sio();?> type="text" value="<?=_escape($sacp[0]['graduationDate']);?>" />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
						<?php } ?>
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Start / End Date' );?></label>
                            <div class="col-md-4">
                                <div class="input-group date" id="datepicker7">
                                    <input class="form-control"<?=sio();?> name="startDate" type="text" value="<?=_escape($sacp[0]['startDate']);?>" required/>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="input-group date" id="datepicker8">
                                    <?php if($sacp[0]['endDate'] == NULL || $sacp[0]['endDate'] == '0000-00-00') { ?>
                                    <input class="form-control"<?=sio();?> name="endDate"<?=sio();?> type="text" />
                                    <?php } else { ?>
                                    <input class="form-control"<?=sio();?> name="endDate"<?=sio();?> type="text" value="<?=_escape($sacp[0]['endDate']);?>" />
                                    <?php } ?>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Comments' );?></label>
                            &nbsp;&nbsp;&nbsp;<a href="#comment-<?=_escape($stu->stuID);?>" data-toggle="modal" title="Edit Comment" class="btn <?=(_escape($sacp[0]['Comment']) == 'empty' ? 'btn-primary' : 'btn-danger');?>"><i class="fa fa-edit"></i></a>
                        </div>
                        <div class="modal fade" id="comment-<?=_escape($stu->stuID);?>">
                        	<div class="modal-dialog">
								<div class="modal-content">
									<!-- Modal heading -->
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h3 class="modal-title"><?=_t( 'Comments' );?></h3>
									</div>
									<!-- // Modal heading END -->
                                    <div class="modal-body">
                                        <textarea id="<?=_escape($stu->stuID);?>" name="comments" class="form-control" rows="5"><?=_escape($sacp[0]['comments']);?></textarea>
                                        <input type="button" class="btn btn-default" value="Insert Timestamp" onclick="addMsg('<?=\Jenssegers\Date\Date::now()->format('D, M d, o @ h:i A');?> <?=get_name(get_persondata('personID'));?>','<?=_escape($stu->stuID);?>'); return false;" />
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></button>
                                    </div>
                            	</div>
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'STAL' );?> <a href="<?=get_base_url();?>stu/sacp/<?=_escape($sacp[0]['id']);?>/stal/"><img src="<?=get_base_url();?>static/common/theme/images/cascade.png" /></a></label>
                            <div class="col-md-3">
                                <input type="text" disabled value="X" class="form-control col-md-1 center" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Approved By' );?></label>
                            <div class="col-md-6">
                                <input type="text" readonly class="form-control" value="<?=_escape(get_name($sacp[0]['approvedBy']));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Last Update' );?></label>
                            <div class="col-md-6">
                                <input type="text" readonly class="form-control" value="<?=\Jenssegers\Date\Date::parse(_escape($sacp[0]['LastUpdate']))->format('D, M d, o @ h:i A');?>" />
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
				    <input type="hidden" name="stuID" value="<?=_escape($stu->stuID);?>" />
					<button type="submit"<?=sids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
					<button type="button"<?=sids();?> class="btn btn-icon btn-primary glyphicons circle_plus" onclick="window.location='<?=get_base_url();?>stu/add-prog/<?=_escape($stu->stuID);?>/'"><i></i><?=_t( 'Add' );?></button>
					<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>stu/<?=_escape($stu->stuID);?>/'"><i></i><?=_t( 'Cancel' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
        
        <!-- Modal -->
		<div class="modal fade" id="myModal">
			<div class="modal-dialog">
				<div class="modal-content">
					<!-- Modal heading -->
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3 class="modal-title"><?=_t( 'Eligible to Graduate' );?></h3>
					</div>
					<!-- // Modal heading END -->
					<!-- Modal body -->
					<div class="modal-body">
						<p><?=_t('Select the checkbox if the student is eligible to graduate from this particular program.');?></p>
					</div>
					<!-- // Modal body END -->
					<!-- Modal footer -->
					<div class="modal-footer">
						<a href="#" class="btn btn-default" data-dismiss="modal"><?=_t( 'Close' );?></a> 
					</div>
					<!-- // Modal footer END -->
				</div>
			</div>
		</div>
		<!-- // Modal END -->
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
	
	<!-- Modal -->
    <div class="modal fade" id="FERPA">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?=_t( 'Family Educational Rights and Privacy Act (FERPA)' );?></h3>
                </div>
                <!-- // Modal heading END -->
                <!-- Modal body -->
                <div class="modal-body">
                    <p><?=_t('"FERPA gives parents certain rights with respect to their children\'s education records. 
                    These rights transfer to the student when he or she reaches the age of 18 or attends a school beyond 
                    the high school level. Students to whom the rights have transferred are \'eligible students.\'"');?></p>
                    <p><?=_t('If the FERPA restriction states "Yes", then the student has requested that none of their 
                    information be given out without their permission. To get a better understanding of FERPA, visit 
                    the U.S. DOE\'s website @ ') . 
                    '<a href="http://www2.ed.gov/policy/gen/guid/fpco/ferpa/index.html">http://www2.ed.gov/policy/gen/guid/fpco/ferpa/index.html</a>.';?></p>
                </div>
                <!-- // Modal body END -->
                <!-- Modal footer -->
                <div class="modal-footer">
                    <a href="#" class="btn btn-default" data-dismiss="modal"><?=_t( 'Close' );?></a> 
                </div>
                <!-- // Modal footer END -->
            </div>
        </div>
    </div>
    <!-- // Modal END -->
	
</div>	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>