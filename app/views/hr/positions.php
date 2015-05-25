<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 *
 * Employee Job Positions View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       3.0.2
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$message = new \app\src\Messages;
$staffInfo = new \app\src\Staff;
$staffInfo->Load_from_key(_h($positions[0]['staffID']));
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>hr/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Employee' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Positions' );?></li>
</ul>

<div class="innerLR">

	<!-- List Widget -->
    <div class="relativeWrap">
        <div class="widget">
            <div class="widget-head">
                <h4 class="heading glyphicons user"><i></i><?=get_name(_h($staffInfo->getStaffID()));?></h4>
                <a href="<?=url('/');?>staff/<?=_h($staffInfo->getStaffID());?>/" class="heading pull-right"><?=_h($staffInfo->getStaffID());?></a>
            </div>
            <div class="widget-body">
                <!-- 3 Column Grid / One Third -->
                <div class="row">
                    
                    <!-- One Third Column -->
                    <div class="col-md-1">
                        <?=getSchoolPhoto($staffInfo->getStaffID(), $staffInfo->getEmail(), '90');?>
                    </div>
                    <!-- // One Third Column END -->
    
                    <!-- One Third Column -->
                    <div class="col-md-3">
                        <p><?=_h($staffInfo->getAddress1());?> <?=_h($staffInfo->getAddress2());?></p>
                        <p><?=_h($staffInfo->getCity());?> <?=_h($staffInfo->getState());?> <?=_h($staffInfo->getZip());?></p>
                        <p><strong><?=_t( 'Phone:' );?></strong> <?=_h($staffInfo->getPhone1());?></p>
                    </div>
                    <!-- // One Third Column END -->
                    
                    <!-- One Third Column -->
                    <div class="col-md-4">
                    	<p><strong><?=_t( 'Title:' );?></strong> <?=_h($staffInfo->getTitle());?></p>
                    	<p><strong><?=_t( 'Dept:' );?></strong> <?=_h($staffInfo->getDeptName());?></p>
                    	<p><strong><?=_t( 'Office:' );?></strong> <?=_h($staffInfo->getOfficeCode());?></p>
                    </div>
                    <!-- // One Third Column END -->
                    
                    <!-- One Third Column -->
                    <div class="col-md-3">
                        <p><strong><?=_t( 'Office Phone:' );?></strong> <?=_h($staffInfo->getOfficePhone());?></p>
                        <p><strong><?=_t( 'Email:' );?></strong> <a href="mailto:<?=_h($staffInfo->getEmail());?>"><?=_h($staffInfo->getEmail());?></a></p>
                        <p><strong><?=_t( 'Status:' );?></strong> <?=_h($staffInfo->getStaffStatus());?></p>
                    </div>
                    <!-- // One Third Column END -->
                    
                </div>
                <!-- // 3 Column Grid / One Third END -->
            </div>
        </div>
    </div>
    <!-- // List Widget END -->
    
    <div class="separator line bottom"></div>
	
	<?=$message->flashMessage();?>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
        
        <!-- Tabs Heading -->
        <div class="tabsbar">
            <ul>
                <li class="glyphicons user"><a href="<?=url('/');?>hr/<?=_h($positions[0]['staffID']);?>/"><i></i> <?=get_name(_h($positions[0]['staffID']));?></a></li>
                <li class="glyphicons nameplate"><a href="<?=url('/');?>staff/<?=_h($positions[0]['staffID']);?>/"><i></i> <?=_t( 'Staff Record' );?></a></li>
                <li class="glyphicons folder_open tab-stacked active"><a href="<?=url('/');?>hr/positions/<?=_h($positions[0]['staffID']);?>/" data-toggle="tab"><i></i> <?=_t( 'View Positions' );?></a></li>
                <li class="glyphicons circle_plus tab-stacked"><a href="<?=url('/');?>hr/add/<?=_h($positions[0]['staffID']);?>/"><i></i> <span><?=_t( 'Add Position' );?></span></a></li>
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
                    <td class="text-center">$<?=money_format("%i",_h($v['hourly_wage']));?></td>
                    <td class="text-center"><?=_h($v['weekly_hours']);?></td>
                    <td class="text-center">$<?=money_format("%i",_h($v['hourly_wage'])*_h($v['weekly_hours'])*4);?></td>
                    <td class="text-center"><?=date('D, M d, o',strtotime(_h($v['hireDate'])));?></td>
                    <td class="text-center"><?=date('D, M d, o',strtotime(_h($v['startDate'])));?></td>
                    <td class="text-center">
                    	<?php if(_h($v['endDate']) == NULL || _h($v['endDate']) == '0000-00-00') : ?>
                    	<?=_t('Not Set');?>
                    	<?php else : ?>
                		<?=date('D, M d, o',strtotime(_h($v['endDate'])));?>
                		<?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="#position<?=_h($v['sMetaID']);?>" data-toggle="modal" title="Edit Position" class="btn btn-default"><i class="fa fa-edit"></i></a>
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
    <div class="modal fade" id="position<?=_h($v['sMetaID']);?>">
	<form class="form-horizontal margin-none" action="<?=url('/');?>hr/positions/<?=_h($v['staffID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
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
				                        <?php table_dropdown('job',NULL,'ID','ID','title',_h($v['jobID'])); ?>
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
                    <input name="sMetaID" type="hidden" value="<?=$v['sMetaID'];?>" />
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