<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Add Application View
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
$screen = 'appl';
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
    <li><a href="<?=get_base_url();?>appl/" class="glyphicons search"><i></i> <?=_t( 'Search Application' );?></a></li>
    <li class="divider"></li>
    <li><?=_t( 'View Application' );?></li>
</ul>

<h3><?=_t( 'View Application' );?></h3>
<div class="innerLR">
	
	<?=_etsis_flash()->showMessage();?>
	
	<?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>appl/editAppl/<?=_h($appl->id);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
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
                            <label class="col-md-3 control-label"><?=_t( 'Person ID' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" readonly type="text" value="<?=_h($appl->personID);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'First/Mid/Last Name' );?></label>
                            <div class="col-md-3">
                            	<input class="form-control" readonly type="text" value="<?=_h($appl->fname);?>" />
                        	</div>
                        	<div class="col-md-2">
                            	<input class="form-control" readonly type="text" value="<?=_h($appl->mname);?>" />
                        	</div>
                        	<div class="col-md-3">
                            	<input class="form-control" readonly type="text" value="<?=_h($appl->lname);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Permanent Address' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" readonly type="text" value="<?=_h($addr->address1);?> <?=_h($addr->address2);?>" />
                            	<input class="form-control" readonly type="text" value="<?=_h($addr->city);?> <?=_h($addr->state);?> <?=_h($addr->zip);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'DOB' );?></label>
                            <div class="col-md-8">
                            	<?php if(_h($appl->dob) > '0000-00-00') : ?>
                            	<input class="form-control" readonly type="text" value="<?=\Jenssegers\Date\Date::parse(_h($appl->dob))->format('D, M d, o');?>" />
                            	<?php else : ?>
                            	<input class="form-control" readonly type="text" />
                        		<?php endif; ?>
                        	</div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Age' );?></label>
                            <div class="col-md-8">
                            	<?php if(_h($appl->dob) > '0000-00-00') : ?>
                            	<input class="form-control" readonly type="text" value="<?=get_age(_h($appl->dob));?>" />
                            	<?php else : ?>
                            	<input class="form-control" readonly type="text" />
                        		<?php endif; ?>
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Gender' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" readonly type="text" value="<?php if(_h($appl->gender) == 'M') : echo 'Male'; elseif(_h($appl->gender) == 'F') : echo 'Female'; endif; ?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Phone Number' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" readonly type="text" value="<?=_h($addr->phone1);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Email Address' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" readonly type="text" value="<?=_h($appl->email);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Username' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" name="uname" type="text" value="<?=_h($appl->uname);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
				</div>
				<!-- // Row END -->
			
				<div class="separator bottom"></div>
				
				<!-- Row -->
				<div class="row">
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Application Date' );?></label>
                            <div class="col-md-8">
                            	<div class="input-group date" id="datepicker6">
                            		<?php if(_h($appl->applDate) > '0000-00-00') : ?>
                                    <input class="form-control" name="applDate" type="text" value="<?=_h($appl->applDate);?>" />
                                    <?php else : ?>
                                	<input class="form-control" name="applDate" type="text" />
                                	<?php endif; ?>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                        	</div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Academic Program' );?></label>
                            <div class="col-md-8">
                                <select name="acadProgCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('acad_program','currStatus = "A"','acadProgCode','acadProgCode','acadProgTitle',_h($appl->acadProgCode)); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Start Term' );?></label>
                            <div class="col-md-8">
                                <select name="startTerm" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required/>
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('term','termCode <> "NULL"','termCode','termCode','termName',_h($appl->startTerm)); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Admit Status' );?></label>
                            <div class="col-md-8">
                                <?=admit_status_select(_h($appl->admitStatus));?>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Application Status' );?></label>
                            <div class="col-md-8">
                                <select name="applStatus" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="Pending"<?=selected('Pending',_h($appl->applStatus),false);?>><?=_t( 'Pending' );?></option>
                                    <option value="Under Review"<?=selected('Under Review',_h($appl->applStatus),false);?>><?=_t( 'Under Review' );?></option>
                                    <option value="Accepted"<?=selected('Accepted',_h($appl->applStatus),false);?>><?=_t( 'Accepted' );?></option>
                                    <option value="Not Accepted"<?=selected('Not Accepted',_h($appl->applStatus),false);?>><?=_t( 'Not Accepted' );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <?php 
                        /**
                         * Application Form Field (Left)
                         * 
                         * Action will print a form field or any type of data
                         * on the left side of the appl screen via the
                         * dashboard when triggered.
                         * 
                         * @since 6.1.10
                         * @param array $appl Application data object.
                         */
                        $app->hook->do_action('left_appl_view_dash_form', $appl); 
                        ?>
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
                        
                        <!-- Group -->
                        <div class="form-group<?=etsis_field_css_class('appl_psat_verbal');?>">
                            <label class="col-md-3 control-label"><?=_t( 'PSAT Verbal/Math' );?></label>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="PSAT_Verbal" value="<?=_h($appl->PSAT_Verbal);?>" />
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="PSAT_Math" value="<?=_h($appl->PSAT_Math);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group<?=etsis_field_css_class('appl_sat_verbal');?>">
                            <label class="col-md-3 control-label"><?=_t( 'SAT Verbal/Math' );?></label>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="SAT_Verbal" value="<?=_h($appl->SAT_Verbal);?>" />
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="SAT_Math" value="<?=_h($appl->SAT_Math);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group<?=etsis_field_css_class('appl_act_english');?>">
                            <label class="col-md-3 control-label"><?=_t( 'ACT English/Math' );?></label>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="ACT_English" value="<?=_h($appl->ACT_English);?>" />
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="ACT_Math" value="<?=_h($appl->ACT_Math);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Applicant Comments' );?> <a href="#applinfo" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-4">
                            	<a href="#applcomment-<?=_h($appl->personID);?>" data-toggle="modal" title="Edit Applicant Comments" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                        	</div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Staff Comments' );?></label>
                            <div class="col-md-4">
                            	<a href="#staffcomment-<?=_h($appl->personID);?>" data-toggle="modal" title="Edit Staff Comments" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                        	</div>
                        </div>
                        <div class="modal fade" id="applcomment-<?=_h($appl->personID);?>">
                        	<div class="modal-dialog">
								<div class="modal-content">
									<!-- Modal heading -->
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h3 class="modal-title"><?=_t( 'Applicant Comments' );?></h3>
									</div>
									<!-- // Modal heading END -->
                                    <div class="modal-body">
                                        <textarea id="appl-<?=_h($appl->personID);?>" name="appl_comments" class="form-control" rows="5"><?=_h($appl->appl_comments);?></textarea>
                                        <input type="button" class="btn btn-default" value="Insert Timestamp" onclick="addMsg('<?=\Jenssegers\Date\Date::now()->format('D, M d, o @ h:i A');?> <?=get_name(get_persondata('personID'));?>','appl-<?=_h($appl->personID);?>'); return false;" />
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></button>
                                    </div>
                            	</div>
                        	</div>
                        </div>
                        <div class="modal fade" id="staffcomment-<?=_h($appl->personID);?>">
                        	<div class="modal-dialog">
								<div class="modal-content">
									<!-- Modal heading -->
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h3 class="modal-title"><?=_t( 'Internal Comments' );?></h3>
									</div>
									<!-- // Modal heading END -->
                                    <div class="modal-body">
                                        <textarea id="staff-<?=_h($appl->personID);?>" name="staff_comments" class="form-control" rows="5"><?=_h($appl->staff_comments);?></textarea>
                                        <input type="button" class="btn btn-default" value="Insert Timestamp" onclick="addMsg('<?=\Jenssegers\Date\Date::now()->format('D, M d, o @ h:i A');?> <?=get_name(get_persondata('personID'));?>','staff-<?=_h($appl->personID);?>'); return false;" />
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></button>
                                    </div>
                            	</div>
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <?php 
                        /**
                         * Application Form Field (Right)
                         * 
                         * Action will print a form field or any type of data
                         * on the right side of the appl screen via the
                         * dashboard when triggered.
                         * 
                         * @since 6.1.10
                         * @param array $appl Application data object.
                         */
                        $app->hook->do_action('right_appl_view_dash_form', $appl); 
                        ?>
						
					</div>
					<!-- // Column END -->
					
				</div>
				<!-- // Row END -->
				
				<?php if(_h($inst[0]['fice_ceeb']) != '') : ?>
				<div class="separator bottom"></div>
        
            <!-- Table -->
            <table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-primary">
            
                <!-- Table heading -->
                <thead>
                    <tr>
                        <th class="text-center"><?=_t( 'Institution' );?></th>
                        <th class="text-center"><?=_t( 'From Date' );?></th>
                        <th class="text-center"><?=_t( 'To Date' );?></th>
                        <th class="text-center"><?=_t( 'Major' );?></th>
                        <th class="text-center"><?=_t( 'GPA' );?></th>
                        <th class="text-center"><?=_t( 'Degree' );?></th>
                        <th class="text-center"><?=_t( 'Grad Date' );?></th>
                        <th class="text-center"><?=_t( 'Action' );?></th>
                    </tr>
                </thead>
                <!-- // Table heading END -->
                
                <!-- Table body -->
                <tbody>
                <?php if($inst != '') : foreach($inst as $key => $value) { ?>
                <tr class="gradeX">
                    <td style="width:300px;">
                    	<!-- Group -->
                        <div class="form-group">
                            <div class="col-md-12">
                            	<select name="fice_ceeb[]" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('institution',null,'fice_ceeb','fice_ceeb','instName',_h($value['fice_ceeb'])); ?>
                                </select>
                        	</div>
                        </div>
                        <!-- // Group END -->
                    </td>
                    <td class="text-center" style="width:180px;">
                    	<!-- Group -->
                        <div class="form-group">
                            <div class="col-md-12">
                            	<div class="input-group date" id="datepicker6">
                                    <input class="form-control" name="fromDate[]" type="text" value="<?=_h($value['fromDate']);?>" />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                        	</div>
                        </div>
                        <!-- // Group END -->
                    </td>
                    <td class="text-center" style="width:180px;">
                    	<!-- Group -->
                        <div class="form-group">
                            <div class="col-md-12">
                            	<div class="input-group date" id="datepicker7">
                                    <input class="form-control" name="toDate[]" type="text" value="<?=_h($value['toDate']);?>" />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                        	</div>
                        </div>
                        <!-- // Group END -->
                    </td>
                    <td class="text-center">
                    	<!-- Group -->
                        <div class="form-group">
                            <div class="col-md-12">
                            	<input class="form-control" type="text" name="major[]" value="<?=_h($value['major']);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                    </td>
                    <td class="text-center">
                    	<!-- Group -->
                        <div class="form-group">
                            <div class="col-md-12">
                                <input class="form-control" type="text" name="GPA[]" value="<?=  number_format(_h($value['GPA']),3);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                    </td>
                    <td class="text-center">
                    	<!-- Group -->
                        <div class="form-group">
                            <div class="col-md-12">
                            	<input class="form-control" type="text" name="degree_awarded[]" value="<?=_h($value['degree_awarded']);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                    </td>
                    <td class="text-center">
                    	<!-- Group -->
                        <div class="form-group">
                            <div class="col-md-12" style="width:180px;">
                            	<div class="input-group date" id="datepicker8">
                            		<?php if(_h($value['degree_conferred_date']) > '0000-00-00') : ?>
                                    <input class="form-control" name="degree_conferred_date[]" type="text" value="<?=_h($value['degree_conferred_date']);?>" />
                                    <?php else : ?>
                                	<input class="form-control" name="degree_conferred_date[]" type="text" />
                                	<?php endif; ?>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                        	</div>
                        </div>
                        <!-- // Group END -->
                    </td>
                    <td class="text-center">
                    	<a href="<?=get_base_url();?>appl/deleteInstAttend/<?=_h($value['id']);?>/" title="Delete Institution Attended" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
                    </td>
                    <td style="display:none;">
                    	<input type="hidden" name="id[]" value="<?=_h($value['id']);?>" />
                    </td>
                </tr>
                <?php } endif; ?>
                    
                </tbody>
                <!-- // Table body END -->
                
            </table>
            <!-- // Table END -->
			<?php endif; ?>
			
			<div class="separator bottom"></div>
			
			<!-- Form actions -->
			<div class="form-actions">
				<input type="hidden" name="personID" value="<?=_h($appl->personID);?>" />
                <input type="hidden" name="id" value="<?=_h($appl->id);?>" />
				<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
                <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>appl/'"><i></i><?=_t( 'Cancel' );?></button>
			</div>
			<!-- // Form actions END -->
				
			</div>
			
		</div>
		<!-- // Widget END -->
	</form>
	<!-- // Form END -->
	
	<!-- Modal -->
	<div class="modal fade" id="applinfo">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Applicant Comments' );?></h3>
				</div>
				<!-- // Modal heading END -->
				<!-- Modal body -->
				<div class="modal-body">
					<p><?=_t( 'These comments will be shown to the applicant in myetSIS.' );?></p>
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