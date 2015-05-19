<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 *
 * Employee Record View
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
$staffInfo->Load_from_key(_h($staff[0]['staffID']));
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
    <li><?=_t( 'View Employee' );?></li>
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

    <!-- Form -->
    <form class="form-horizontal margin-none" action="<?=url('/');?>hr/<?=_h($staff[0]['staffID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
        
        <!-- Widget -->
        <div class="widget widget-heading-simple widget-body-gray">
        
            <!-- Widget heading -->
            <div class="widget-head">
                <h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
            </div>
            <!-- // Widget heading END -->
            
            <!-- Tabs Heading -->
            <div class="tabsbar">
                <ul>
                    <li class="glyphicons user active"><a href="<?=url('/');?>hr/<?=_h($staff[0]['staffID']);?>/" data-toggle="tab"><i></i> <?=get_name(_h($staff[0]['staffID']));?></a></li>
                    <li class="glyphicons nameplate"><a href="<?=url('/');?>staff/<?=_h($staff[0]['staffID']);?>/"><i></i> <?=_t( 'Staff Record' );?></a></li>
                    <li class="glyphicons folder_open tab-stacked"><a href="<?=url('/');?>hr/positions/<?=_h($staff[0]['staffID']);?>/"><i></i> <?=_t( 'View Positions' );?></a></li>
                    <li class="glyphicons circle_plus tab-stacked"><a href="<?=url('/');?>hr/add/<?=_h($staff[0]['staffID']);?>/"><i></i> <span><?=_t( 'Add Position' );?></span></a></li>
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
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Employment Type' );?></label>
                            <div class="col-md-8">
                                <select name="jobStatusCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('job_status',NULL,'typeCode','typeCode','type',_h($staff[0]['jobStatusCode'])); ?>
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
                                    <option value="FAC"<?=selected('FAC',_h($staff[0]['staffType']),false);?>><?=_t( 'Faculty' );?></option>
                                    <option value="STA"<?=selected('STA',_h($staff[0]['staffType']),false);?>><?=_t( 'Staff' );?></option>
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
                                    <?php supervisor(_h($staff[0]['staffID']),_h($staff[0]['supervisorID'])); ?>
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
                                    <?php table_dropdown('job',NULL,'ID','ID','title',_h($staff[0]['jobID'])); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Building' );?></label>
                            <div class="col-md-8">
                                <select name="buildingCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('building','buildingCode <> "NULL"','buildingCode','buildingCode','buildingName',_h($staff[0]['buildingCode'])); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Office' );?></label>
                            <div class="col-md-8">
                                <select name="officeCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('room','roomCode <> "NULL"','roomCode','roomCode','roomNumber',_h($staff[0]['officeCode'])); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Office Phone' );?></label>
                            <div class="col-md-6">
                                <input class="form-control" type="text" name="office_phone" value="<?=_h($staff[0]['office_phone']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'School' );?></label>
                            <div class="col-md-8">
                                <select name="schoolCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('school','schoolCode <> "NULL"','schoolCode','schoolCode','schoolName',_h($staff[0]['schoolCode'])); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Department' );?></label>
                            <div class="col-md-8">
                                <select name="deptCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('department','deptCode <> "NULL"','deptCode','deptCode','deptName',_h($staff[0]['deptCode'])); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Hourly Wage' );?></label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input class="form-control" id="appendedPrependedInput" type="text" readonly value="<?=number_format(_h($staff[0]['hourly_wage']));?>" />
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                    <!-- // Column END -->
                    
                    <!-- Column -->
                    <div class="col-md-6">
                    	
                    	<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Weekly Hours' );?></label>
                            <div class="col-md-4">
                                <input class="form-control" type="text" readonly value="<?=_h($staff[0]['weekly_hours']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Monthly Salary' );?></label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input class="form-control" id="appendedPrependedInput" type="text" readonly value="<?=number_format(_h($staff[0]['Monthly']));?>" />
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Hire Date' );?></label>
                            <div class="col-md-4">
                                <div class="input-group date" id="datepicker6">
                                    <input class="form-control" name="hireDate" type="text" value="<?=_h($staff[0]['hireDate']);?>" />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Start Date' );?></label>
                            <div class="col-md-4">
                                <div class="input-group date" id="datepicker7">
                                    <input class="form-control" name="startDate" type="text" value="<?=_h($staff[0]['startDate']);?>" />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'End Date' );?></label>
                            <div class="col-md-4">
                                <div class="input-group date" id="datepicker8">
                                	<?php if(_h($staff[0]['endDate']) == '0000-00-00') : ?>
                                    <input class="form-control" name="endDate" type="text" />
                                    <?php else : ?>
                                	<input class="form-control" name="endDate" type="text" value="<?=_h($staff[0]['endDate']);?>" />
                                	<?php endif; ?>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Status' );?></label>
                            <div class="col-md-8">
                                <select name="status" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="A"<?=selected('A',_h($staff[0]['status']),false);?>><?=_t( 'A Active' );?></option>
                                    <option value="I"<?=selected('I',_h($staff[0]['status']),false);?>><?=_t( 'I Inactive' );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Add Date' );?></label>
                            <div class="col-md-6">
                                <input class="form-control" type="text" readonly value="<?=date('D, M d, o',strtotime(_h($staff[0]['addDate'])));?>" required />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Approved By' );?></label>
                            <div class="col-md-6">
                                <input class="form-control"type="text" readonly value="<?=get_name(_h($staff[0]['approvedBy']));?>" required />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Last Update' );?></label>
                            <div class="col-md-6">
                                <input class="form-control" type="text" readonly value="<?=date('D, M d, o @ h:i A',strtotime(_h($staff[0]['LastUpdate'])));?>" required />
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
                    <input type="hidden" name="sMetaID" value="<?=_h($staff[0]['sMetaID']);?>" />
                    <button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>hr/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
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