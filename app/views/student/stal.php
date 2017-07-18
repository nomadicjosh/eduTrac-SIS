<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * View Student Academic Level View
 *  
 * @license GPLv3
 * 
 * @since       6.3.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$stu = get_student(_h($stal->stuID));
?>

<ul class="breadcrumb">
    <li><?=_t( 'You are here' );?></li>
    <li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=get_base_url();?>stu/" class="glyphicons search"><i></i> <?=_t( 'Search Student' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=get_base_url();?>stu/sacp/<?=_h($stal->sacpID);?>/" class="glyphicons coins"><i></i> <?=_h($stal->acadProgCode);?> <?=_t( 'Academic Program' );?></a></li>
    <li class="divider"></li>
    <li><?=_t( 'Student Academic Level (STAL)' );?></li>
</ul>

<div class="innerLR">

	<?php get_stu_header(_h($stal->stuID)); ?>
    
    <div class="separator line bottom"></div>
    
    <?=_etsis_flash()->showMessage();?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>stu/sacp/<?=_h($stal->sacpID);?>/stal/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray">
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'Program' );?></label>
							<div class="col-md-8">
								<input type="text" readonly value="<?=_h($stal->acadProgCode);?> - <?=_h($stal->acadProgTitle);?>" class="form-control" />
							</div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Academic Level' );?></label>
                            <div class="col-md-8">
                                <select name="acadLevelCode"<?=sio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('aclv', null, 'code', 'code', 'name', _h($stal->acadLevelCode)); ?>
	                        	</select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Classification' );?></label>
                            <div class="col-md-8">
                                <select name="currentClassLevel"<?=sio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('clas', null, 'code', 'code', 'name', _h($stal->currentClassLevel)); ?>
	                        	</select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Enrollment Status' );?></label>
                            <div class="col-md-8">
                            	<select name="enrollmentStatus"<?=sio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="NULL">&nbsp;</option>
                                    <option value="L"<?=selected('L',_h($stal->enrollmentStatus,false));?>><?=_t('(L) Less Than Half Time');?></option>
                                    <option value="H"<?=selected('H',_h($stal->enrollmentStatus,false));?>><?=_t('(H) Half Time');?></option>
                                    <option value="Q"<?=selected('Q',_h($stal->enrollmentStatus,false));?>><?=_t('(Q) Quarter Time');?></option>
                                    <option value="F"<?=selected('F',_h($stal->enrollmentStatus,false));?>><?=_t('(F) Full Time');?></option>
                                    <option value="O"<?=selected('O',_h($stal->enrollmentStatus,false));?>><?=_t('(O) Overload');?></option>
                                    <option value="G"<?=selected('G',_h($stal->enrollmentStatus,false));?>><?=_t('(G) Graduated');?></option>
                                    <option value="W"<?=selected('W',_h($stal->enrollmentStatus,false));?>><?=_t('(W) Withdrawn');?></option>
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
                            <label class="col-md-3 control-label"><?=_t( 'GPA' );?></label>
                            <div class="col-md-8">
                                <input class="form-control"<?=sio();?> name="gpa" value="<?=_h(number_format($stal->gpa,6));?>" type="text" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Start Term' );?></label>
                            <div class="col-md-8">
                            	<select name="startTerm"<?=sio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="NULL">&nbsp;</option>
                            		<?php table_dropdown('term', 'termCode <> "NULL"', 'termCode', 'termCode', 'termName',_h($stal->startTerm)); ?>
                            	</select>
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Start/End Date' );?></label>
                            <div class="col-md-4">
                            	<div class="input-group date" id="datepicker6">
                                    <input class="form-control"<?=sio();?> name="startDate" value="<?=_h($stal->startDate);?>" type="text" required/>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="input-group date" id="datepicker7">
                                    <input class="form-control"<?=sio();?> name="endDate" value="<?=_h($stal->endDate);?>" type="text" />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
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
                    <input name="stalID" value="<?=_h($stal->stalID);?>" type="hidden" />
                    <input name="stuID" value="<?=_h($stal->stuID);?>" type="hidden" />
					<button type="submit"<?=sids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
					<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>stu/sacp/<?=_h($stal->sacpID);?>/'"><i></i><?=_t( 'Cancel' );?></button>
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