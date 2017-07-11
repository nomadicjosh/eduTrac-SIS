<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * View Staff View
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
$screen = 'staff';
$staffInfo = get_staff(_h($staff->staffID));
$list = '"'.implode('","', get_staff_tags()).'"';
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
	<li><a href="<?=get_base_url();?>staff/" class="glyphicons search"><i></i> <?=_t( 'Search Staff' );?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'View Staff' );?></li>
</ul>

<div class="innerLR">

	<!-- List Widget -->
    <div class="relativeWrap">
        <div class="widget">
            <div class="widget-head">
                <h4 class="heading glyphicons user"><i></i><?=get_name(_h($staffInfo->staffID));?></h4>
                <a href="<?=get_base_url();?>staff/<?=_h($staffInfo->staffID);?>/" class="heading pull-right"><?=(_h($staffInfo->altID) != '' ? _h($staffInfo->altID) : _h($staffInfo->staffID));?></a>
            </div>
            <div class="widget-body">
                <!-- 3 Column Grid / One Third -->
                <div class="row">
                    
                    <!-- One Third Column -->
                    <div class="col-md-1">
                        <?=get_school_photo(_h($staffInfo->staffID), _h($staffInfo->email), '90');?>
                    </div>
                    <!-- // One Third Column END -->
    
                    <!-- One Third Column -->
                    <div class="col-md-3">
                        <p><?=_h($staffInfo->address1);?> <?=_h($staffInfo->address2);?></p>
                        <p><?=_h($staffInfo->city);?> <?=_h($staffInfo->state);?> <?=_h($staffInfo->zip);?></p>
                        <p><strong><?=_t( 'Phone:' );?></strong> <?=_h($staffInfo->phone1);?></p>
                    </div>
                    <!-- // One Third Column END -->
                    
                    <!-- One Third Column -->
                    <div class="col-md-4">
                    	<p><strong><?=_t( 'Title:' );?></strong> <?=_h($staffInfo->title);?></p>
                    	<p><strong><?=_t( 'Dept:' );?></strong> <?=_h($staffInfo->deptName);?></p>
                    	<p><strong><?=_t( 'Office:' );?></strong> <?=_h($staffInfo->officeCode);?></p>
                    </div>
                    <!-- // One Third Column END -->
                    
                    <!-- One Third Column -->
                    <div class="col-md-3">
                        <p><strong><?=_t( 'Office Phone:' );?></strong> <?=_h($staffInfo->office_phone);?></p>
                        <p><strong><?=_t( 'Email:' );?></strong> <a href="mailto:<?=_h($staffInfo->email);?>"><?=_h($staffInfo->email);?></a></p>
                        <p><strong><?=_t( 'Status:' );?></strong> <?=_h($staffInfo->staffStatus);?></p>
                    </div>
                    <!-- // One Third Column END -->
                    
                </div>
                <!-- // 3 Column Grid / One Third END -->
            </div>
        </div>
    </div>
    <!-- // List Widget END -->
    
    <div class="separator line bottom"></div>
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>staff/<?=_h($staff->staffID);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
            
            <!-- Tabs Heading -->
            <div class="tabsbar">
                <ul>
                    <li<?=ae('access_human_resources');?> class="glyphicons user"><a href="<?=get_base_url();?>hr/<?=_h($staff->staffID);?>/"><i></i> <?=get_name(_h($staff->staffID));?></a></li>
                    <li class="glyphicons nameplate active"><a href="<?=get_base_url();?>staff/<?=_h($staff->staffID);?>/" data-toggle="tab"><i></i> <?=_t( 'Staff Record' );?></a></li>
                    <li<?=ae('access_human_resources');?> class="glyphicons folder_open tab-stacked"><a href="<?=get_base_url();?>hr/positions/<?=_h($staff->staffID);?>/"><i></i> <?=_t( 'View Positions' );?></a></li>
                    <li<?=ae('access_human_resources');?> class="glyphicons circle_plus tab-stacked"><a href="<?=get_base_url();?>hr/add/<?=_h($staff->staffID);?>/"><i></i> <span><?=_t( 'Add Position' );?></span></a></li>
                </ul>
            </div>
            <!-- // Tabs Heading END -->
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="address"><?=_t( 'Address' );?></label>
                            <div class="col-md-8">
                                <input class="form-control col-md-3" type="text" readonly value="<?=_h($addr[0]['address1']);?> <?=_h($addr[0]['address2']);?>" />
                                <input class="form-control col-md-3" type="text" readonly value="<?=_h($addr[0]['city']);?> <?=_h($addr[0]['state']);?> <?=_h($addr[0]['zip']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Title' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="<?=getStaffJobTitle(_h($staff->staffID));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Building' );?></label>
                            <div class="col-md-8">
                                <select name="buildingCode"<?=staio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('building','buildingCode <> "NULL"','buildingCode','buildingCode','buildingName',_h($staff->buildingCode)); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Office' );?></label>
                            <div class="col-md-8">
                                <select name="officeCode"<?=staio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('room','roomCode <> "NULL"','roomCode','roomCode','roomNumber',_h($staff->officeCode)); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Office Phone' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="office_phone"<?=staio();?> class="form-control" value="<?=_h($staff->office_phone);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'School' );?></label>
                            <div class="col-md-8">
                                <select name="schoolCode"<?=staio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('school','schoolCode <> "NULL"','schoolCode','schoolCode','schoolName',_h($staff->schoolCode)); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Department' );?></label>
                            <div class="col-md-8">
                                <select name="deptCode"<?=staio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" requied>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('department','deptCode <> "NULL"','deptCode','deptCode','deptName',_h($staff->deptCode)); ?>
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
                                <select name="status"<?=staio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="A"<?=selected('A',_h($staff->status),false);?>><?=_t( 'A Active' );?></option>
                                    <option value="I"<?=selected('I',_h($staff->status),false);?>><?=_t( 'I Inactive' );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Tags' );?></label>
                            <div class="col-md-8">
                                <input id="select2_5" style="width:100%;" type="hidden" name="tags" />
                            </div>
                        </div>
                        <!-- // Group END -->
					    
					    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Email' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="<?=_h($addr[0]['email']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Add Date' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly value="<?=\Jenssegers\Date\Date::parse(_h($staff->addDate))->format('D, M d, o');?>" class="form-control" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Approved By' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly value="<?=get_name(_h($staff->approvedBy));?>" class="form-control" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Last Update' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly value="<?=\Jenssegers\Date\Date::parse(_h($staff->LastUpdate))->format('D, M d, o @ h:i A');?>" class="form-control" />
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
					<button type="submit"<?=staids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
					<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>staff/'"><i></i><?=_t( 'Cancel' );?></button>
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