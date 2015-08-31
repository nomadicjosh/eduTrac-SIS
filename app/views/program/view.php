<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * View Academic Program View
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
$message = new \app\src\Messages;
include('ajax.php');
$screen = 'vprog';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
    <li><?=_t( 'You are here' );?></li>
    <li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=url('/');?>program/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Program' );?></a></li>
    <li class="divider"></li>
    <li><?=_t( 'View Program' );?></li>
</ul>

<h3><?=_h($prog[0]['acadProgCode']);?>
    <span data-toggle="tooltip" data-original-title="Create Program" data-placement="top">
        <a<?=ae('add_acad_prog');?> href="<?=url('/');?>program/add/" class="btn btn-primary"><i class="fa fa-plus"></i></a>
    </span>
</h3>
<div class="innerLR">
	
	<?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen,'','','','','',$prog); ?>

    <!-- Form -->
    <form class="form-horizontal margin-none" action="<?=url('/');?>program/<?=_h($prog[0]['acadProgID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
        
        <!-- Widget -->
        <div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
        
            <!-- Widget heading -->
            <div class="widget-head">
                <h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
            </div>
            <!-- // Widget heading END -->
            
            <div class="widget-body">
            
                <!-- Row -->
                <div class="row">
                    <!-- Column -->
                    <div class="col-md-6">
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Program Code' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" name="acadProgCode"<?=apio();?> value="<?=_h($prog[0]['acadProgCode']);?>" required />
                            </div>
                        </div>
                        <!-- // Group END -->
                    
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Title' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" name="acadProgTitle"<?=apio();?> value="<?=_h($prog[0]['acadProgTitle']);?>" required />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Short Description' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" name="programDesc"<?=apio();?> value="<?=_h($prog[0]['programDesc']);?>" required />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Status / Date' );?></label>
                            <div class="col-md-4">
                                <?=status_select(_h($prog[0]['currStatus']), csid());?>
                            </div>
                            
                            <div class="col-md-4">
                                <input class="form-control" name="statusDate" type="text" readonly value="<?=date("D, M d, o",strtotime(_h($prog[0]['statusDate'])));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Approval Person' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" readonly value="<?=get_name(_h($prog[0]['approvedBy']));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Approval Date' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" readonly value="<?=date("D, M d, o",strtotime(_h($prog[0]['approvedDate'])));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Department' );?></label>
                            <div class="col-md-8" id="divDept">
                                <select name="deptCode" id="deptCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=apid();?>>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('department','deptCode <> "NULL"','deptCode','deptCode','deptName',_h($prog[0]['deptCode']));?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#dept" data-toggle="modal" title="Department" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'School' );?></label>
                            <div class="col-md-8">
                                <select name="schoolCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=apid();?>>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('school','schoolCode <> "NULL"','schoolCode','schoolCode','schoolName',_h($prog[0]['schoolCode']));?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Effective Catalog Year' );?></label>
                            <div class="col-md-8" id="divYear">
                                <select name="acadYearCode" id="acadYearCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=apid();?> required>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('acad_year','acadYearCode <> "NULL"','acadYearCode','acadYearCode','acadYearDesc',_h($prog[0]['acadYearCode']));?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#year" data-toggle="modal" title="Academic Year" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                    <!-- // Column END -->
                    
                    <!-- Column -->
                    <div class="col-md-6">
                    
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Effective / End Date' );?></label>
                            <div class="col-md-4">
                                <div class="input-group date col-md-12" id="datepicker6">
                                    <input class="form-control"<?=apio();?> name="startDate" type="text" value="<?=_h($prog[0]['startDate']);?>" required />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="input-group date col-md-12" id="datepicker7">
                                    <input class="form-control"<?=apio();?> name="endDate" type="text" value="<?=(_h($prog[0]['endDate']) > '0000-00-00' ? _h($prog[0]['endDate']) : '');?>" />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Degree' );?></label>
                            <div class="col-md-8" id="divDegree">
                                <select name="degreeCode" id="degreeCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=apid();?> required>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('degree','degreeCode <> "NULL"','degreeCode','degreeCode','degreeName',_h($prog[0]['degreeCode']));?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#degree" data-toggle="modal" title="Degree" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'CCD' );?></label>
                            <div class="col-md-8" id="divCCD">
                                <select name="ccdCode" id="ccdCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=apid();?>>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('ccd','ccdCode <> "NULL"','ccdCode','ccdCode','ccdName',_h($prog[0]['ccdCode']));?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#ccd" data-toggle="modal" title="CCD" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Major' );?></label>
                            <div class="col-md-8" id="divMajor">
                                <select name="majorCode" id="majorCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=apid();?>>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('major','majorCode <> "NULL"','majorCode','majorCode','majorName',_h($prog[0]['majorCode']));?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#major" data-toggle="modal" title="Major" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Minor' );?></label>
                            <div class="col-md-8" id="divMinor">
                                <select name="minorCode" id="minorCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=apid();?>>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('minor','minorCode <> "NULL"','minorCode','minorCode','minorName',_h($prog[0]['minorCode']));?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#minor" data-toggle="modal" title="Minor" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Specialization' );?></label>
                            <div class="col-md-8" id="divSpec">
                                <select name="specCode" id="specCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=apid();?>>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('specialization', 'specCode <> "NULL"', 'specCode', 'specCode', 'specName',_h($prog[0]['specCode'])); ?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#spec" data-toggle="modal" title="Specialization" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Academic Level' );?></label>
                            <div class="col-md-8">
                                <?=acad_level_select(_h($prog[0]['acadLevelCode']),csid().' ','required');?>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'CIP' );?></label>
                            <div class="col-md-8" id="divCIP">
                                <select name="cipCode" id="cipCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=apid();?>>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('cip','cipCode <> "NULL"','cipCode','cipCode','cipName',_h($prog[0]['cipCode']));?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#cip" data-toggle="modal" title="CIP" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Location' );?></label>
                            <div class="col-md-8" id="divLoc">
                                <select name="locationCode" id="locationCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=apid();?>>
                                    <option value="NULL">&nbsp;</option>
                                    <?php table_dropdown('location','locationCode <> "NULL"','locationCode','locationCode','locationName',_h($prog[0]['locationCode']));?>
                                </select>
                            </div>
                            <a<?=ae('access_forms');?> href="#loc" data-toggle="modal" title="Location" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                    <!-- // Column END -->
                </div>
                <!-- // Row END -->
            
                <hr class="separator" />
                
                <!-- Form actions -->
                <div class="form-actions">
                    <input class="form-control" type="hidden" name="acadProgID" value="<?=_h($prog[0]['acadProgID']);?>" />
                    <button type="submit"<?=apids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>program/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
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