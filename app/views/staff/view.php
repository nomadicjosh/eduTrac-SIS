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
$staffInfo = get_staff(_escape($staff->staffID));
$tags = "{tag: '".implode("'},{tag: '", get_staff_tags())."'}";
?>

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
                <h4 class="heading glyphicons user"><i></i><?=get_name(_escape($staffInfo->staffID));?></h4>
                <a href="<?=get_base_url();?>staff/<?=_escape($staffInfo->staffID);?>/" class="heading pull-right"><?=(_escape($staffInfo->altID) != '' ? _escape($staffInfo->altID) : _escape($staffInfo->staffID));?></a>
            </div>
            <div class="widget-body">
                <!-- 3 Column Grid / One Third -->
                <div class="row">
                    
                    <!-- One Third Column -->
                    <div class="col-md-1">
                        <?=get_school_photo(_escape($staffInfo->staffID), _escape($staffInfo->email), '90');?>
                    </div>
                    <!-- // One Third Column END -->
    
                    <!-- One Third Column -->
                    <div class="col-md-3">
                        <p><?=_escape($staffInfo->address1);?> <?=_escape($staffInfo->address2);?></p>
                        <p><?=_escape($staffInfo->city);?> <?=_escape($staffInfo->state);?> <?=_escape($staffInfo->zip);?></p>
                        <p><strong><?=_t( 'Phone:' );?></strong> <?=_escape($staffInfo->phone1);?></p>
                    </div>
                    <!-- // One Third Column END -->
                    
                    <!-- One Third Column -->
                    <div class="col-md-4">
                    	<p><strong><?=_t( 'Title:' );?></strong> <?=_escape($staffInfo->title);?></p>
                    	<p><strong><?=_t( 'Dept:' );?></strong> <?=_escape($staffInfo->deptName);?></p>
                    	<p><strong><?=_t( 'Office:' );?></strong> <?=_escape($staffInfo->officeCode);?></p>
                    </div>
                    <!-- // One Third Column END -->
                    
                    <!-- One Third Column -->
                    <div class="col-md-3">
                        <p><strong><?=_t( 'Office Phone:' );?></strong> <?=_escape($staffInfo->office_phone);?></p>
                        <p><strong><?=_t( 'Email:' );?></strong> <a href="mailto:<?=_escape($staffInfo->email);?>"><?=_escape($staffInfo->email);?></a></p>
                        <p><strong><?=_t( 'Status:' );?></strong> <?=_escape($staffInfo->staffStatus);?></p>
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
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>staff/<?=_escape($staff->staffID);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
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
                    <li<?=ae('access_human_resources');?> class="glyphicons user"><a href="<?=get_base_url();?>hr/<?=_escape($staff->staffID);?>/"><i></i> <?=get_name(_escape($staff->staffID));?></a></li>
                    <li class="glyphicons nameplate active"><a href="<?=get_base_url();?>staff/<?=_escape($staff->staffID);?>/" data-toggle="tab"><i></i> <?=_t( 'Staff Record' );?></a></li>
                    <li<?=ae('access_human_resources');?> class="glyphicons folder_open tab-stacked"><a href="<?=get_base_url();?>hr/positions/<?=_escape($staff->staffID);?>/"><i></i> <?=_t( 'View Positions' );?></a></li>
                    <li<?=ae('access_human_resources');?> class="glyphicons circle_plus tab-stacked"><a href="<?=get_base_url();?>hr/add/<?=_escape($staff->staffID);?>/"><i></i> <span><?=_t( 'Add Position' );?></span></a></li>
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
                                <input class="form-control col-md-3" type="text" readonly value="<?=_escape($addr[0]['address1']);?> <?=_escape($addr[0]['address2']);?>" />
                                <input class="form-control col-md-3" type="text" readonly value="<?=_escape($addr[0]['city']);?> <?=_escape($addr[0]['state']);?> <?=_escape($addr[0]['zip']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Title' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="<?=getStaffJobTitle(_escape($staff->staffID));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Building' );?></label>
                            <div class="col-md-8">
                                <select name="buildingCode"<?=staio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('building','buildingCode <> "NULL"','buildingCode','buildingCode','buildingName',_escape($staff->buildingCode)); ?>
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
                                    <?php table_dropdown('room','roomCode <> "NULL"','roomCode','roomCode','roomNumber',_escape($staff->officeCode)); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Office Phone' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="office_phone"<?=staio();?> class="form-control" value="<?=_escape($staff->office_phone);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'School' );?></label>
                            <div class="col-md-8">
                                <select name="schoolCode"<?=staio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('school','schoolCode <> "NULL"','schoolCode','schoolCode','schoolName',_escape($staff->schoolCode)); ?>
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
                                    <?php table_dropdown('department','deptCode <> "NULL"','deptCode','deptCode','deptName',_escape($staff->deptCode)); ?>
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
                                    <option value="A"<?=selected('A',_escape($staff->status),false);?>><?=_t( 'A Active' );?></option>
                                    <option value="I"<?=selected('I',_escape($staff->status),false);?>><?=_t( 'I Inactive' );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Tags' );?></label>
                            <div class="col-md-8">
                                <input type="hidden" id="input-tags" name="tags" value="<?=_escape($staff->tags);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
					    
					    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Email' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="<?=_escape($addr[0]['email']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Add Date' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly value="<?=\Jenssegers\Date\Date::parse(_escape($staff->addDate))->format('D, M d, o');?>" class="form-control" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Approved By' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly value="<?=get_name(_escape($staff->approvedBy));?>" class="form-control" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Last Update' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly value="<?=\Jenssegers\Date\Date::parse(_escape($staff->LastUpdate))->format('D, M d, o @ h:i A');?>" class="form-control" />
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
        
<script src="<?=get_base_url();?>static/assets/components/modules/querybuilder/selectize/js/standalone/selectize.min.js" type="text/javascript"></script>
<script type="text/javascript">
$('#input-tags').selectize({
    plugins: ['remove_button'],
    delimiter: ',',
    persist: false,
    maxItems: null,
    valueField: 'tag',
    labelField: 'tag',
    searchField: ['tag'],
    options: [
        <?=$tags;?>
    ],
    render: {
        item: function(item, escape) {
            return '<div>' +
                (item.tag ? '<span class="tag">' + escape(item.tag) + '</span>' : '') +
            '</div>';
        },
        option: function(item, escape) {
            var caption = item.tag ? item.tag : null;
            return '<div>' +
                (caption ? '<span class="caption">' + escape(caption) + '</span>' : '') +
            '</div>';
        }
    },
    create: function(input) {
        return {
            tag: input
        };
    }
});
</script>

		<!-- // Content END -->
<?php $app->view->stop(); ?>