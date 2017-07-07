<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * NAE Record View
 * 
 * This view is used when viewing a person record via
 * the NAE screen.
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
$screen = 'vnae';
$list = '"'.implode('","', get_nae_tags()).'"';
?>

<script type="text/javascript">
$(function() {
<?php if(strlen($list) >= 3) : ?>
	$("#select2_5").select2({tags:[<?=$list;?>]});
<?php else : ?>
	$("#select2_5").select2({tags:[]});
<?php endif; ?>
});
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=get_base_url();?>nae/" class="glyphicons search"><i></i> <?=_t( 'Search Person' );?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'View Person' );?></li>
</ul>

<h3 class="hidden-print"><?=_t( 'Person:' );?> <?=_h($nae[0]['lname']);?>, <?=_h($nae[0]['fname']);?>
    <?php if($appl->personID <= 0) : ?>
    <span data-toggle="tooltip" data-original-title="Create Application" data-placement="top">
        <a<?=hl('applications','access_application_screen');?> href="<?=get_base_url();?>appl/add/<?=_h($nae[0]['personID']);?>/" class="btn btn-primary"><i class="fa fa-archive"></i></a>
    </span>
    <?php endif; ?>
</h3>
<div class="innerLR">
	
	<?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen,'','',$nae,$staff); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none grid-form" action="<?=get_base_url();?>nae/<?=_h($nae[0]['personID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-white <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
            
            <button type="button" data-toggle="print" class="btn btn-inverse pull-right print hidden-print"><i class="fa fa-fw fa-print"></i></button>
            
            <div class="breakline">&nbsp;</div>
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
			
			<div class="widget-body">
			
				<fieldset>
                    <legend><?=_t( 'Personal Details' );?></legend>
                    <div data-row-span="4">
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Unique ID' );?></label>
                            <input type="text" value="<?=(_h($nae[0]['altID']) != '' ? _h($nae[0]['altID']) : _h($nae[0]['personID']));?>" readonly />
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Username' );?></label>
                            <input type="text" value="<?=_h($nae[0]['uname']);?>" readonly />
                        </div>
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?=_t( 'Person Type' );?> <a href="#myModal" class="hidden-print" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
                            <?=person_type_select(_h($nae[0]['personType']));?>
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'Prefix' );?></label>
                            <select name="prefix" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=pio();?>>
                                <option value="">&nbsp;</option>
                                <option value="Ms"<?php if($nae[0]['prefix'] == 'Ms') { echo ' selected="selected"'; }?>><?=_t( 'Ms.' );?></option>
                                <option value="Miss"<?php if($nae[0]['prefix'] == 'Miss') { echo ' selected="selected"'; }?>><?=_t( 'Miss.' );?></option>
                                <option value="Mrs"<?php if($nae[0]['prefix'] == 'Mrs') { echo ' selected="selected"'; }?>><?=_t( 'Mrs.' );?></option>
                                <option value="Mr"<?php if($nae[0]['prefix'] == 'Mr') { echo ' selected="selected"'; }?>><?=_t( 'Mr.' );?></option>
                                <option value="Dr"<?php if($nae[0]['prefix'] == 'Dr') { echo ' selected="selected"'; }?>><?=_t( 'Dr.' );?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div data-row-span="4">
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?=_t( 'First Name' );?></label>
                            <input type="text" name="fname"<?=pio();?> value="<?=_h($nae[0]['fname']);?>" required />
                        </div>
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?=_t( 'Last Name' );?></label>
                            <input type="text" name="lname"<?=pio();?> value="<?=_h($nae[0]['lname']);?>" required />
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'Middle Initial' );?></label>
                            <input type="text" name="mname"<?=pio();?> value="<?=_h($nae[0]['mname']);?>" />
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'Social Security #' );?></label>
                            <input type="text" name="ssn"<?=pio();?> value="<?=(_h((int)$nae[0]['ssn']) > 0 ? _h((int)$nae[0]['ssn']) : '');?>" />
                        </div>
                    </div>
                    
                    <div data-row-span="4">
                        <div data-field-span="1">
                            <label><?=_t( 'Date of Birth' );?></label>
                            <div class="input-group date col-md-8" id="datepicker6">
                                <input class="form-control" name="dob"<?=pio();?> type="text" value="<?=(_h($nae[0]['dob']) > '0000-00-00' ? _h($nae[0]['dob']) : '');?>" />
                                <span class="input-group-addon"><i class="fa fa-th"></i></span>
                            </div>
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'Gender' );?></label>
                            <select name="gender" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=pio();?>>
                                <option value="">&nbsp;</option>
                                <option value="M"<?php if($nae[0]['gender'] == 'M') { echo ' selected="selected"'; }?>><?=_t( 'Male' );?></option>
                                <option value="F"<?php if($nae[0]['gender'] == 'F') { echo ' selected="selected"'; }?>><?=_t( 'Female' );?></option>
                            </select>
                        </div>
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?=_t( 'Veteran?' );?></label>
                            <select name="veteran" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=pio();?> required>
                                <option value="">&nbsp;</option>
                                <option value="1"<?php if($nae[0]['veteran'] == 1) { echo ' selected="selected"'; }?>><?=_t( 'Yes' );?></option>
                                <option value="0"<?php if($nae[0]['veteran'] == 0) { echo ' selected="selected"'; }?>><?=_t( 'No' );?></option>
                            </select>
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'Ethnicity?' );?></label>
                            <select name="ethnicity"<?=pio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                <option value="">&nbsp;</option>
                                <option value="White, Non-Hispanic"<?=selected('White, Non-Hispanic',_h($nae[0]['ethnicity']),false);?>><?=_t( 'White, Non-Hispanic' );?></option>
                                <option value="Black, Non-Hispanic"<?=selected('Black, Non-Hispanic',_h($nae[0]['ethnicity']),false);?>><?=_t( 'Black, Non-Hispanic' );?></option>
                                <option value="Hispanic"<?=selected('Hispanic',_h($nae[0]['ethnicity']),false);?>><?=_t( 'Hispanic' );?></option>
                                <option value="Native American"<?=selected('Native American',_h($nae[0]['ethnicity']),false);?>><?=_t( 'Native American' );?></option>
                                <option value="Native Alaskan"<?=selected('Native Alaskan',_h($nae[0]['ethnicity']),false);?>><?=_t( 'Native Alaskan' );?></option>
                                <option value="Pacific Islander"<?=selected('Pacific Islander',_h($nae[0]['ethnicity']),false);?>><?=_t( 'Pacific Islander' );?></option>
                                <option value="Asian"<?=selected('Asian',_h($nae[0]['ethnicity']),false);?>><?=_t( 'Asian' );?></option>
                                <option value="Indian"<?=selected('Indian',_h($nae[0]['ethnicity']),false);?>><?=_t( 'Indian' );?></option>
                                <option value="Middle Eastern"<?=selected('Middle Eastern',_h($nae[0]['ethnicity']),false);?>><?=_t( 'Middle Eastern' );?></option>
                                <option value="African"<?=selected('African',_h($nae[0]['ethnicity']),false);?>><?=_t( 'African' );?></option>
                                <option value="Mixed Race"<?=selected('Mixed Race',_h($nae[0]['ethnicity']),false);?>><?=_t( 'Mixed Race' );?></option>
                                <option value="Other"<?=selected('Other',_h($nae[0]['ethnicity']),false);?>><?=_t( 'Other' );?></option>
                            </select>
                        </div>
                    </div>
                    
                    <br />
                    
                    <fieldset>
                        <legend><?=_t( 'Emergency Contact' );?></legend>
                        <div data-row-span="2">
                            <div data-field-span="1">
                                <label><?=_t( "Emergency Contact's Name" );?></label>
                                <input type="text" name="emergency_contact"<?=pio();?> value="<?=_h($nae[0]['emergency_contact']);?>" />
                            </div>
                            <div data-field-span="1">
                                <label><?=_t( "Emergency Contact's Phone" );?></label>
                                <input type="text" name="emergency_contact_phone"<?=pio();?> value="<?=_h($nae[0]['emergency_contact_phone']);?>" />
                            </div>
                        </div>
                    </fieldset>
                </fieldset>
			
				<br /><br />
                
                <fieldset>
                    <legend><?=_t( 'Mailing Address & Contact Details' );?></legend>
                    <div data-row-span="2">
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Address1' );?> <a class="hidden-print" href="<?=get_base_url();?>nae/adsu/<?=_h($nae[0]['personID']);?>/"><img src="<?=get_base_url();?>static/common/theme/images/cascade.png" /></a></label>
                            <input type="text" readonly value="<?=_h($addr[0]['address1']);?>" required />
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Address2' );?></label>
                            <input type="text" readonly value="<?=_h($addr[0]['address2']);?>" />
                        </div>
                    </div>
                    <div data-row-span="4">
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'City' );?></label>
                            <input type="text" readonly value="<?=_h($addr[0]['city']);?>" />
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'State' );?></label>
                            <input type="text" readonly value="<?=_h($addr[0]['state']);?>" />
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Zip/Postal Code' );?></label>
                            <input type="text" readonly value="<?=_h($addr[0]['zip']);?>" />
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Country' );?></label>
                            <input type="text" readonly value="<?=_h($addr[0]['country']);?>" />
                        </div>
                    </div>
                    <div data-row-span="2">
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?=_t( 'Preferred Email' );?></label>
                            <input type="email" name="email"<?=pio();?> value="<?=_h($nae[0]['email']);?>" required />
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Phone' );?></label>
                            <input type="text" readonly value="<?=_h($addr[0]['phone1']);?>" />
                        </div>
                    </div>
                </fieldset>
                
                <br /><br />
                
                <fieldset>
                    <legend><?=_t( 'User Role, Permission & PERC' );?></legend>
                    <div data-row-span="3">
                        <div<?=ae('access_user_role_screen');?> data-field-span="1" class="readonly">
                            <label><?=_t( 'Role' );?> <a class="hidden-print" href="<?=get_base_url();?>nae/role/<?=_h($nae[0]['personID']);?>/"><img src="<?=get_base_url();?>static/common/theme/images/cascade.png" /></a></label>
                        </div>
                        <div<?=ae('access_user_permission_screen');?> data-field-span="1" class="readonly">
                            <label><?=_t( 'Permission' );?> <a class="hidden-print" href="<?=get_base_url();?>nae/perms/<?=_h($nae[0]['personID']);?>/"><img src="<?=get_base_url();?>static/common/theme/images/cascade.png" /></a></label>
                        </div>
                        <div<?=ae('access_person_screen');?> data-field-span="1" class="readonly">
                            <label><?=_t( 'PERC' );?> <a class="hidden-print" href="<?=get_base_url();?>nae/perc/<?=_h($nae[0]['personID']);?>/"><img src="<?=get_base_url();?>static/common/theme/images/cascade.png" /></a></label>
                        </div>
                    </div>
                </fieldset>
                
                <br /><br />
                
                <fieldset>
                    <legend><?=_t( 'Status & Tags' );?></legend>
                    <div data-row-span="2">
                         <div<?=ae('access_user_permission_screen');?> data-field-span="1">
                            <label><font color="red">*</font> <?=_t( 'Status' );?> <a class="hidden-print" href="#status" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
                            <select name="status" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=pio();?> required>
                                <option value="">&nbsp;</option>
                                <option value="A"<?=selected('A',_h($nae[0]['status']),false);?>><?=_t( 'Active' );?></option>
                                <option value="I"<?=selected('I',_h($nae[0]['status']),false);?>><?=_t( 'Inactive' );?></option>
                            </select>
                        </div>
                        <div data-field-span="1">
                            <label><?= _t("Tags"); ?></label>
                            <input id="select2_5" style="width:100%;" type="hidden" name="tags" value="<?=$nae[0]['tags'];?>" />
                        </div>
                    </div>
                </fieldset>
                
                <br /><br />
                
                <?php 
                    /**
                     * NAE View Form Field
                     * 
                     * Action will print a form field or any type of data
                     * at the end of the form.
                     * 
                     * @since 6.3.0
                     */
                    $app->hook->do_action('bottom_nae_view_form'); 
                ?>
                
                <fieldset>
                    <legend><?=_t( 'System Status' );?></legend>
                    <div data-row-span="4">
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Approved Date' );?></label>
                            <input type="text" readonly value="<?=\Jenssegers\Date\Date::parse(_h($nae[0]['approvedDate']))->format('D, M d, o');?>" />
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Approved By' );?></label>
                            <input type="text" readonly value="<?=get_name(_h($nae[0]['approvedBy']));?>" />
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Last Login' );?></label>
                            <input type="text" readonly value="<?=\Jenssegers\Date\Date::parse(_h($login->loginTimeStamp))->format('D, M d, o @ h:i A');?>" />
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Last Update' );?></label>
                            <input type="text" readonly value="<?=\Jenssegers\Date\Date::parse(_h($nae[0]['LastUpdate']))->format('D, M d, o @ h:i A');?>" />
                        </div>
                    </div>
                </fieldset>
                
                <br /><br />
				
                <div class="modal fade" id="altID">
					<div class="modal-dialog">
						<div class="modal-content">
							<!-- Modal heading -->
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="modal-title"><?=_t( 'Alternate ID' );?></h3>
							</div>
							<!-- // Modal heading END -->
		                    <div class="modal-body">
                                <p><?=_t( "The unique ID for each person is autogenerated by the system. However, some institutions have their own format for person/student ID's. If this is the case for your institution, you can use this alternate ID field." );?></p>
		                    </div>
		                    <div class="modal-footer">
		                        <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		                    </div>
	                   	</div>
                  	</div>
                </div>
				<div class="modal fade" id="myModal">
					<div class="modal-dialog">
						<div class="modal-content">
							<!-- Modal heading -->
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="modal-title"><?=_t( 'Person Type' );?></h3>
							</div>
							<!-- // Modal heading END -->
		                    <div class="modal-body">
		                        <?=_file_get_contents( APP_PATH . 'Info/person-type.txt' );?>
		                    </div>
		                    <div class="modal-footer">
		                        <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		                    </div>
	                   	</div>
                  	</div>
                </div>
                <div class="modal fade" id="status">
					<div class="modal-dialog">
						<div class="modal-content">
							<!-- Modal heading -->
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="modal-title"><?=_t( 'Person Status' );?></h3>
							</div>
							<!-- // Modal heading END -->
		                    <div class="modal-body">
		                        <p><?=_t( "The status on person records can be useful for when running reports, mail merge, etc in order to differentiate between 'active' and 'inactive' person records. However, when using student, staff or faculty records, it is best to join the 'person' table to those tables in order to pull their current status since the status from those tables might be more accurate than the status in the person table." );?></p>
		                    </div>
		                    <div class="modal-footer">
		                        <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		                    </div>
	                   	</div>
                  	</div>
                </div>
				
				<!-- Form actions -->
				<div class="form-actions hidden-print">
					<button type="submit"<?=pids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
					<button type="button"<?=ae('reset_person_password');?> class="btn btn-icon btn-primary glyphicons refresh" onclick="window.location='<?=get_base_url();?>nae/resetPassword/<?=_h($nae[0]['personID']);?>'"><i></i><?=_t( 'Reset Password' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>nae/'"><i></i><?=_t( 'Cancel' );?></button>
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