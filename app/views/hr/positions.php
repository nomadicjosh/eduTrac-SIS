<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 *
 * Employee Job Positions View
 *  
 * @license GPLv3
 * 
 * @since       3.0.2
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$screen = 'hr';
$staffInfo = get_staff(_h($positions[0]['staffID']));
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=get_base_url();?>hr/" class="glyphicons search"><i></i> <?=_t( 'Search Employee' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Positions' );?></li>
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

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
        
        <!-- Tabs Heading -->
        <div class="tabsbar">
            <ul>
                <li class="glyphicons user"><a href="<?=get_base_url();?>hr/<?=_h($positions[0]['staffID']);?>/"><i></i> <?=get_name(_h($positions[0]['staffID']));?></a></li>
                <li class="glyphicons nameplate"><a href="<?=get_base_url();?>staff/<?=_h($positions[0]['staffID']);?>/"><i></i> <?=_t( 'Staff Record' );?></a></li>
                <li class="glyphicons folder_open tab-stacked active"><a href="<?=get_base_url();?>hr/positions/<?=_h($positions[0]['staffID']);?>/" data-toggle="tab"><i></i> <?=_t( 'View Positions' );?></a></li>
                <li class="glyphicons circle_plus tab-stacked"><a href="<?=get_base_url();?>hr/add/<?=_h($positions[0]['staffID']);?>/"><i></i> <span><?=_t( 'Add Position' );?></span></a></li>
            </ul>
        </div>
        <!-- // Tabs Heading END -->
            
		<div class="widget-body">
		
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
			
				<!-- Table heading -->
				<thead>
					<tr>
						<th class="text-center"><?=_t( 'Pay Grade' );?></th>
						<th class="text-center"><?=_t( 'Job Title' );?></th>
						<th class="text-center"><?=_t( 'Hourly Wage' );?></th>
						<th class="text-center"><?=_t( 'Weekly Hours' );?></th>
						<th class="text-center"><?=_t( 'Monthly Salary' );?></th>
						<th class="text-center"><?=_t( 'Hire Date' );?></th>
						<th class="text-center"><?=_t( 'Start Date' );?></th>
						<th class="text-center"><?=_t( 'End Date' );?></th>
						<th class="text-center"><?=_t( 'Actions' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
				<?php if($positions != '') : foreach($positions as $k => $v) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_h($v['grade']);?></td>
                    <td class="text-center"><?=_h($v['title']);?></td>
                    <td class="text-center">$<?=money_format("%i",(double)_h($v['hourly_wage']));?></td>
                    <td class="text-center"><?=_h($v['weekly_hours']);?></td>
                    <td class="text-center">$<?=money_format("%i",(double)_h($v['hourly_wage'])*_h($v['weekly_hours'])*4);?></td>
                    <td class="text-center"><?=\Jenssegers\Date\Date::parse(_h($v['hireDate']))->format('D, M d, o');?></td>
                    <td class="text-center"><?=\Jenssegers\Date\Date::parse(_h($v['startDate']))->format('D, M d, o');?></td>
                    <td class="text-center">
                    	<?php if(_h($v['endDate']) == NULL || _h($v['endDate']) == '0000-00-00') : ?>
                    	<?=_t('Not Set');?>
                    	<?php else : ?>
                		<?=\Jenssegers\Date\Date::parse(_h($v['endDate']))->format('D, M d, o');?>
                		<?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="#position<?=_h($v['id']);?>" data-toggle="modal" title="Edit Position" class="btn btn-default"><i class="fa fa-edit"></i></a>
                    </td>
                </tr>
				<?php } endif; ?>
				</tbody>
				<!-- // Table body END -->
				
			</table>
			<!-- // Table END -->
			
		</div>
	</div>
	<div class="separator bottom"></div>
	
	<?php if($positions != '') : foreach($positions as $k => $v) { ?>
    <div class="modal fade" id="position<?=_h($v['id']);?>">
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>hr/positions/<?=_h($v['staffID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		<div class="modal-dialog">
			<div class="modal-content">
				
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Edit Position' );?></h3>
				</div>
				<!-- // Modal heading END -->
            	
            	<!-- Modal body -->
				<div class="modal-body">
					<div class="widget-body">
            
		                <!-- Row -->
		                <div class="row">
				            <!-- Group -->
				            <div class="form-group">
				                <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Employment Type' );?></label>
				                <div class="col-md-8">
				                    <select name="jobStatusCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
				                        <option value="">&nbsp;</option>
				                        <?php table_dropdown('job_status',NULL,'typeCode','typeCode','type',_h($v['jobStatusCode'])); ?>
				                    </select>
				                </div>
				            </div>
				            <!-- // Group END -->
				            
				            <!-- Group -->
				            <div class="form-group">
				                <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Staff Type' );?></label>
				                <div class="col-md-8">
				                    <select name="staffType" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
				                        <option value="">&nbsp;</option>
				                        <option value="FAC"<?=selected('FAC',_h($v['staffType']),false);?>><?=_t( 'Faculty' );?></option>
				                        <option value="STA"<?=selected('STA',_h($v['staffType']),false);?>><?=_t( 'Staff' );?></option>
				                    </select>
				                </div>
				            </div>
				            <!-- // Group END -->
				            
				            <!-- Group -->
				            <div class="form-group">
				                <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Supervisor' );?></label>
				                <div class="col-md-8">
				                    <select name="supervisorID" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
				                        <option value="">&nbsp;</option>
				                        <?php supervisor(_h($v['staffID']),_h($v['supervisorID'])); ?>
				                    </select>
				                </div>
				            </div>
				            <!-- // Group END -->
				            
				            <!-- Group -->
				            <div class="form-group">
				                <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Job Title' );?></label>
				                <div class="col-md-8">
				                    <select name="jobID" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
				                        <option value="">&nbsp;</option>
				                        <?php table_dropdown('job',NULL,'id','id','title',_h($v['jobID'])); ?>
				                    </select>
				                </div>
				            </div>
				            <!-- // Group END -->
				            
				            <!-- Group -->
				            <div class="form-group">
				                <label class="col-md-3 control-label"><?=_t( 'Hire Date' );?></label>
				                <div class="col-md-8">
				                    <div class="input-group date" id="datepicker6">
				                        <input class="form-control" name="hireDate" type="text" value="<?=$v['hireDate'];?>" />
				                        <span class="input-group-addon"><i class="fa fa-th"></i></span>
				                    </div>
				                </div>
				            </div>
				            <!-- // Group END -->
				            
				            <!-- Group -->
				            <div class="form-group">
				                <label class="col-md-3 control-label"><?=_t( 'Start Date' );?></label>
				                <div class="col-md-8">
				                    <div class="input-group date" id="datepicker7">
				                        <input class="form-control" name="startDate" type="text" value="<?=$v['startDate'];?>" />
				                        <span class="input-group-addon"><i class="fa fa-th"></i></span>
				                    </div>
				                </div>
				            </div>
				            <!-- // Group END -->
				            
				            <!-- Group -->
				            <div class="form-group">
				                <label class="col-md-3 control-label"><?=_t( 'End Date' );?></label>
				                <div class="col-md-8">
				                    <div class="input-group date" id="datepicker8">
				                        <?php if(_h($v['endDate']) == NULL || _h($v['endDate']) == '0000-00-00') : ?>
				                        <input class="form-control" name="endDate" type="text" />
				                        <?php else : ?>
				                        <input class="form-control" name="endDate" type="text" value="<?=$v['endDate'];?>" />
				                        <?php endif; ?>
				                        <span class="input-group-addon"><i class="fa fa-th"></i></span>
				                    </div>
				                </div>
				            </div>
				            <!-- // Group END -->
			            </div>
		            </div>
	            </div>
		        <div class="modal-footer">
                    <input name="id" type="hidden" value="<?=$v['id'];?>" />
		            <button type="submit" class="btn btn-default"><?=_t( 'Update' );?></button>
		            <button data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></button>
		        </div>
	        </div>
       	</div>
   	</form>
    </div>
    <!-- Form -->
    <?php } endif; ?>
	
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>