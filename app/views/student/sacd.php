<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * View Academic Credits View
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
$stuInfo->Load_from_key(_h($sacd[0]['stuID']));
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
    <li><?=_t( 'You are here' );?></li>
    <li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=url('/');?>stu/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Student' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=url('/');?>stu/stac/<?=_h($sacd[0]['stuID']);?>/<?=bm();?>" class="glyphicons coins"><i></i> <?=_t( 'Academic Credits' );?></a></li>
    <li class="divider"></li>
    <li><?=_t( 'View Academic Credits (SACD)' );?></li>
</ul>

<div class="innerLR">
    
    <?=$message->flashMessage();?>
    
    <?php $stuInfo->getStuHeader(); ?>
    
    <div class="separator line bottom"></div>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>stu/sacd/<?=_h($sacd[0]['stuAcadCredID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray">
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'CRSE ID/Name/Sec' );?> <a href="<?=url('/');?>crse/<?=_h($sacd[0]['courseID']);?>/<?=bm();?>"><img src="<?=url('/');?>static/common/theme/images/cascade.png" /></a></label>
							<div class="col-md-3">
								<input type="text" name="courseID" value="<?=_h($sacd[0]['courseID']);?>" class="form-control" required/>
							</div>
							
							<div class="col-md-3">
								<input type="text" name="courseCode" value="<?=_h($sacd[0]['courseCode']);?>" class="form-control" required/>
							</div>
							
							<div class="col-md-2">
                                <input type="text" name="sectionNumber" value="<?=_h($sacd[0]['sectionNumber']);?>" class="form-control" required/>
                            </div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Short Title' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="shortTitle" value="<?=_h($sacd[0]['shortTitle']);?>" class="form-control" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Long Title' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="longTitle" value="<?=_h($sacd[0]['longTitle']);?>" class="form-control" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Subject' );?></label>
                            <div class="col-md-8">
                            	<select name="subjectCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="">&nbsp;</option>
	                        		<?php subject_code_dropdown(_h($sacd[0]['subjectCode'])); ?>
	                        	</select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Acad Lvl/Crse Lvl' );?></label>
                            <div class="col-md-4">
                            	<?=acad_level_select(_h($sacd[0]['acadLevelCode']),null,' required');?>
                            </div>
                            <div class="col-md-4">
                            	<?=course_level_select(_h($sacd[0]['courseLevelCode']));?>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Department' );?></label>
                            <div class="col-md-8">
                            	<select name="deptCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="">&nbsp;</option>
                            		<?php table_dropdown('department', 'deptTypeCode = "acad" AND deptCode <> "NULL"', 'deptCode', 'deptCode', 'deptName',_h($sacd[0]['deptCode'])); ?>
                            	</select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Credit Type' );?></label>
                            <div class="col-md-8">
                            	<select name="creditType" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="">&nbsp;</option>
	                        		<option value="I"<?=selected("I",_h($sacd[0]['creditType']),false);?>><?=_t( 'I Institutional' );?></option>
	                        		<option value="TR"<?=selected("TR",_h($sacd[0]['creditType']),false);?>><?=_t( 'TR Transfer' );?></option>
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
                            <label class="col-md-3 control-label"><?=_t( 'Start/End Date' );?></label>
                            <div class="col-md-4">
                            	<div class="input-group date" id="datepicker6">
                                    <input class="form-control" name="startDate"<?=sio();?> value="<?=_h($sacd[0]['startDate']);?>" type="text" required/>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="input-group date" id="datepicker7">
                                    <input class="form-control" name="endDate"<?=sio();?> value="<?=_h($sacd[0]['endDate']);?>" type="text" required/>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Term/Rpt Term' );?></label>
                            <div class="col-md-4">
                            	<select name="termCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="">&nbsp;</option>
                            		<?php table_dropdown('term', 'termCode <> "NULL"', 'termCode', 'termCode', 'termName',_h($sacd[0]['termCode'])); ?>
                            	</select>
                            </div>
                            
                            <div class="col-md-4">
                                <input type="text" readonly name="reportingTerm" value="<?=_h($sacd[0]['reportingTerm']);?>" class="form-control" required />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Att/Comp Cred' );?></label>
                            <div class="col-md-4">
                                <input type="text" name="attCred"<?=sio();?> value="<?=_h(number_format($sacd[0]['attCred'],6));?>" class="form-control" required/>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="compCred"<?=sio();?> value="<?=_h(number_format($sacd[0]['compCred'],6));?>" class="form-control" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Grade/Grd Pts' );?></label>
                            <div class="col-md-4">
                                <input type="text" name="grade"<?=sio();?> value="<?=_h($sacd[0]['grade']);?>" class="form-control" />
                            </div>
                            
                            <div class="col-md-4">
                                <input type="text" name="gradePoints"<?=sio();?> value="<?=_h(number_format($sacd[0]['gradePoints'],6));?>" class="form-control" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Status' );?></label>
                            <div class="col-md-8">
                                <?=stu_course_sec_status_select(_h($sacd[0]['status']));?>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Status Date/Time' );?></label>
                            <div class="col-md-4">
                                <div class="input-group date" id="datepicker8">
                                    <input class="form-control" name="statusDate"<?=sio();?> value="<?=_h($sacd[0]['statusDate']);?>" type="text" required/>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="input-group bootstrap-timepicker">
                                    <input id="timepicker10" type="text" <?=sio();?> class="form-control" value="<?=_h($sacd[0]['statusTime']);?>" required />
                                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                </div>
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
					<button type="submit"<?=sids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
					<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>stu/stac/<?=_h($sacd[0]['stuID']);?>/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
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