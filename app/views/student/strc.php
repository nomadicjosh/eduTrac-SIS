<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Studen Restriction View
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
$stuInfo->Load_from_key(_h($stu[0]['stuID']));
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
    <li><?=_t( 'Student Restriction (STRC)' );?></li>
</ul>

<div class="innerLR">
    
    <?php $stuInfo->getStuHeader(); ?>
    
    <div class="separator line bottom"></div>
    
    <?=$message->flashMessage();?>
    
    <!-- Tabs Heading -->
    <div class="tabsbar">
        <ul>
            <li class="glyphicons user"><a href="<?=url('/');?>stu/<?=_h($stu[0]['stuID']);?>/<?=bm();?>"><i></i> <?=_t( 'Student Profile (SPRO)' );?></a></li>
            <li class="glyphicons package"><a href="<?=url('/');?>stu/stac/<?=_h($stu[0]['stuID']);?>/<?=bm();?>"><i></i> <?=_t( 'Student Academic Credits (STAC)' );?></a></li>
            <li class="glyphicons tags tab-stacked"><a href="<?=url('/');?>stu/sttr/<?=_h($stu[0]['stuID']);?>/<?=bm();?>"><i></i> <?=_t( 'Student Terms (STTR)' );?></a></li>
            <li class="glyphicons disk_remove tab-stacked active"><a href="<?=url('/');?>stu/strc/<?=_h($stu[0]['stuID']);?>/<?=bm();?>" data-toggle="tab"><i></i> <span><?=_t( 'Student Restriction (STRC)' );?></span></a></li>
            <li class="glyphicons history tab-stacked"><a href="<?=url('/');?>stu/shis/<?=_h($stu[0]['stuID']);?>/<?=bm();?>"><i></i> <span><?=_t( 'Student Hiatus (SHIS)' );?></span></a></li>
        </ul>
    </div>
    <!-- // Tabs Heading END -->

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>stu/strc/<?=_h($stu[0]['stuID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Table -->
		<table class="table table-striped table-responsive swipe-horizontal table-primary">
		
			<!-- Table heading -->
			<thead>
				<tr>
					<th class="text-center"><?=_t( 'Restriction' );?></th>
                    <th class="text-center"><?=_t( 'Severity' );?></th>
                    <th class="text-center"><?=_t( 'Start Date' );?></th>
                    <th class="text-center"><?=_t( 'End Date' );?></th>
                    <th class="text-center"><?=_t( 'Department' );?></th>
                    <th class="text-center"><?=_t( 'Comments' );?></th>
				</tr>
			</thead>
			<!-- // Table heading END -->
			
			<!-- Table body -->
			<tbody>
				<?php if($strc != '') : foreach($strc as $k => $v) { ?>
				<!-- Table row -->
				<tr class="gradeA">
					<td style="width:300px;">
						<select name="rstrCode[]" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                            <option value="">&nbsp;</option>
                            <?=table_dropdown('restriction_code', null, 'rstrCode', 'rstrCode', 'description',_h($v['rstrCode']));?>
                        </select>
					</td>
					<td style="width:80px;"><input type="text" name="severity[]" class="form-control text-center" value="<?=_h($v['severity']);?>" parsley-type="digits" parsley-maxlength="2" /></td>
					<td style="width:160px;">
						<div class="input-group date" id="datepicker6<?=_h($v['rstrID']);?>">
                            <input type="text" name="startDate[]" class="form-control" value="<?=_h($v['startDate']);?>" required/>
                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
                        </div>
					</td>
					<td style="width:160px;">
						<div class="input-group date" id="datepicker7<?=_h($v['rstrID']);?>">
                            <?php if(_h($v['endDate']) != '0000-00-00') : ?>
                            <input type="text" name="endDate[]" class="form-control" value="<?=_h($v['endDate']);?>" />
                            <?php else : ?>
                            <input type="text" name="endDate[]" class="form-control" />
                            <?php endif; ?>
                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
                        </div>
					</td>
					<td><input type="text" readonly class="form-control text-center" value="<?=_h($v['deptCode']);?>" /></td>
					<td class="text-center">
						<button type="button" title="Comment" class="btn bt-sm" data-toggle="modal" data-target="#comments-<?=_h($v['rstrID']);?>"><i class="fa fa-comment"></i></button>
						<!-- Modal -->
						<div class="modal fade" id="comments-<?=_h($v['rstrID']);?>">
							
							<div class="modal-dialog">
								<div class="modal-content">
						
									<!-- Modal heading -->
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h3 class="modal-title"><?=_t( 'Comments' );?></h3>
									</div>
									<!-- // Modal heading END -->
									
									<!-- Modal body -->
									<div class="modal-body">
										<textarea id="<?=_h($v['rstrID']);?>" class="form-control" name="comment[]" rows="5" data-height="auto" parsley-required="true"><?=_h($v['comment']);?></textarea>
                                        <input type="button" class="btn btn-default" value="Insert Timestamp" onclick="addMsg('<?=date('D, M d, o @ h:i A',strtotime(date('Y-m-d h:i A')));?> <?=get_name(get_persondata('personID'));?>','<?=_h($v['rstrID']);?>'); return false;" />
									</div>
									<!-- // Modal body END -->
									
									<!-- Modal footer -->
									<div class="modal-footer">
										<a href="#" class="btn btn-default" data-dismiss="modal"><?=_t( 'Close' );?></a> 
									</div>
									<!-- // Modal footer END -->
						
								</div>
							</div>
							<input type="hidden" name="rstrID[]" value="<?=_h($v['rstrID']);?>" />
						</div>
						<!-- // Modal END -->
					</td>
				</tr>
				<!-- // Table row END -->
				<?php } endif; ?>
				
			</tbody>
			<!-- // Table body END -->
			
		</table>
		<!-- // Table END -->
		
		<!-- Form actions -->
		<div class="form-actions">
		    <?php if(_h($strc[0]['stuID']) != '') : ?>
			<button type="submit"<?=sids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
			<?php endif; ?>
			<button type="button"<?=sids();?> class="btn btn-icon btn-primary glyphicons circle_plus" data-toggle="modal" data-target="#md-ajax"><i></i><?=_t( 'Add' );?></button>
			<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>stu/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
		</div>
		<!-- // Form actions END -->
		
	</form>
	<!-- // Form END -->
	
	<!-- Modal -->
	<div class="modal fade" id="md-ajax">
		<form class="form-horizontal" data-collabel="3" data-alignlabel="left" action="<?=url('/');?>stu/strc/<?=_h($stu[0]['stuID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Comments' );?></h3>
				</div>
				<!-- // Modal heading END -->
				
				<!-- Modal body -->
				<div class="modal-body">
					<div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Restriction' );?></label>
                        <div class="col-md-8">
	                        <select name="rstrCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
	                            <option value="">&nbsp;</option>
	                            <?=table_dropdown('restriction_code', 'deptCode = ?', 'rstrCode', 'rstrCode', 'description','',get_persondata('deptCode'));?>
	                        </select>
                       </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?=_t( 'Severity' );?></label>
                        <div class="col-md-8">
                        	<input type="text" name="severity" class="form-control" parsley-type="digits" parsley-maxlength="2" />
                    	</div>
                    </div>
                    
                    <div class="form-group">
                    	<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Start Date' );?></label>
                    	<div class="col-md-8">
	                        <div class="input-group date" id="datepicker9">
	                            <input type="text" name="startDate" class="form-control" required/>
	                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
	                        </div>
                       </div>
                    </div>
                    
                    <div class="form-group">
                    	<label class="col-md-3 control-label"><?=_t( 'End Date' );?></label>
                    	<div class="col-md-8">
	                        <div class="input-group date" id="datepicker9">
	                            <input type="text" name="endDate" class="form-control" />
	                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
	                        </div>
                       </div>
                    </div>
                    
                    <div class="form-group">
                    	<label class="col-md-3 control-label"><?=_t( 'Comment' );?></label>
                    	<div class="col-md-8">
	                        <textarea id="comment" class="form-control" name="comment" rows="5" data-height="auto" parsley-required="true"></textarea>
	                        <input type="button" class="btn btn-default" value="Insert Timestamp" onclick="addMsg('<?=date('D, M d, o @ h:i A',strtotime(date('Y-m-d h:i A')));?> <?=get_name(get_persondata('personID'));?>','comment'); return false;" />
                       </div>
                    </div>
				</div>
				<!-- // Modal body END -->
				
				<!-- Modal footer -->
				<div class="modal-footer">
					<input type="hidden" name="stuID" value="<?=_h($stu[0]['stuID']);?>" />
                    <input type="hidden" name="addDate" value="<?=date('Y-m-d');?>" />
                    <input type="hidden" name="addedBy" value="<?=get_persondata('personID');?>" />
					<button type="submit" class="btn btn-default"><?=_t( 'Submit' );?></button>
					<a href="#" class="btn btn-primary" data-dismiss="modal"><?=_t( 'Cancel' );?></a>
				</div>
				<!-- // Modal footer END -->
	
			</div>
		</div>
		</form>
	</div>
	<!-- // Modal END -->
	
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