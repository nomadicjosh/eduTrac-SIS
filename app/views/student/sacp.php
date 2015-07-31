<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Edit Student Program
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
$stuInfo = new \app\src\Student;
$stuInfo->Load_from_key(_h($sacp[0]['stuID']));
?>

<script type="text/javascript">
function addMsg(text,element_id) {
	document.getElementById(element_id).value += text;
}
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>stu/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Student' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=url('/');?>stu/<?=$stuInfo->getStuID();?>/<?=bm();?>" class="glyphicons user"><i></i> <?=get_name($stuInfo->getStuID());?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'Edit Student Program (SACP)' );?></li>
</ul>

<div class="innerLR">
    
    <?php $stuInfo->getStuHeader(); ?>
    
    <div class="separator line bottom"></div>
	
	<?=$message->flashMessage();?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>stu/sacp/<?=_h($sacp[0]['stuProgID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
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
								<input type="text" readonly class="form-control" value="<?=_h($sacp[0]['acadProgCode']);?>" />
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'School' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="<?=_h($sacp[0]['schoolCode'].' '.$sacp[0]['schoolName']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Ant Grad Date' );?></label>
                            <div class="col-md-2">
                                <input type="text"<?=sio();?> name="antGradDate" class="form-control center" value="<?=_h($sacp[0]['antGradDate']);?>" required />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Advisor' );?></label>
                            <div class="col-md-8">
                                <select name="advisorID"<?=sio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php facID_dropdown(_h($sacp[0]['advisorID'])); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Academic Level' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="<?=_h($sacp[0]['acadLevelCode']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Catalog Year' );?></label>
                            <div class="col-md-8">
                                <select name="catYearCode"<?=sio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('acad_year', 'acadYearCode <> "NULL"', 'acadYearCode', 'acadYearCode', 'acadYearDesc',_h($sacp[0]['catYearCode'])); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
						<div class="form-group">
						    <label class="col-md-3 control-label"><?=_t( 'Eligible to Grad?' );?> <a href="#myModal" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
							<div class="col-md-8">
    							<label class="checkbox">
									<input type="checkbox"<?=sio();?> class="checkbox" name="eligible_to_graduate" value="1"<?=checked('1',_h($sacp[0]['eligible_to_graduate']),false);?> />
								</label>
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
                                <?=stu_prog_status_select(_h($sacp[0]['currStatus']));?>
                            </div>
                        </div>
                        <!-- // Group END -->
					    
					    <?php if(_h($sacp[0]['currStatus']) == 'G' || _h($sacp[0]['graduationDate']) > '0000-00-00') { ?>
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Graduation Date' );?></label>
                            <div class="col-md-8">
                                <div class="input-group date" id="datepicker6">
                                    <input class="form-control"<?=sio();?> name="graduationDate"<?=sio();?> type="text" value="<?=_h($sacp[0]['graduationDate']);?>" />
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
                                    <input class="form-control"<?=sio();?> name="startDate" type="text" value="<?=_h($sacp[0]['startDate']);?>" required/>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="input-group date" id="datepicker8">
                                    <?php if($sacp[0]['endDate'] == NULL || $sacp[0]['endDate'] == '0000-00-00') { ?>
                                    <input class="form-control"<?=sio();?> name="endDate"<?=sio();?> type="text" />
                                    <?php } else { ?>
                                    <input class="form-control"<?=sio();?> name="endDate"<?=sio();?> type="text" value="<?=_h($sacp[0]['endDate']);?>" />
                                    <?php } ?>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Comments' );?></label>
                            &nbsp;&nbsp;&nbsp;<a href="#comment-<?=_h($sacp[0]['stuID']);?>" data-toggle="modal" title="Edit Comment" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                        </div>
                        <div class="modal fade" id="comment-<?=_h($sacp[0]['stuID']);?>">
                        	<div class="modal-dialog">
								<div class="modal-content">
									<!-- Modal heading -->
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h3 class="modal-title"><?=_t( 'Comments' );?></h3>
									</div>
									<!-- // Modal heading END -->
                                    <div class="modal-body">
                                        <textarea id="<?=_h($sacp[0]['stuID']);?>" name="comments" class="form-control" rows="5"><?=_h($sacp[0]['comments']);?></textarea>
                                        <input type="button" class="btn btn-default" value="Insert Timestamp" onclick="addMsg('<?=date('D, M d, o @ h:i A',strtotime(date('Y-m-d h:i A')));?> <?=get_name(get_persondata('personID'));?>','<?=_h($sacp[0]['stuID']);?>'); return false;" />
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
                            <label class="col-md-3 control-label"><?=_t( 'Approved By' );?></label>
                            <div class="col-md-6">
                                <input type="text" readonly class="form-control" value="<?=_h(get_name($sacp[0]['approvedBy']));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Last Update' );?></label>
                            <div class="col-md-6">
                                <input type="text" readonly class="form-control" value="<?=date('D, M d, o @ h:i A',strtotime(_h($sacp[0]['LastUpdate'])));?>" />
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
				    <input type="hidden" name="stuID" value="<?=_h($sacp[0]['stuID']);?>" />
					<button type="submit"<?=sids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
					<button type="button"<?=sids();?> class="btn btn-icon btn-primary glyphicons circle_plus" onclick="window.location='<?=url('/');?>stu/add-prog/<?=_h($sacp[0]['stuID']);?>/<?=bm();?>'"><i></i><?=_t( 'Add' );?></button>
					<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>stu/<?=_h($sacp[0]['stuID']);?>/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
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