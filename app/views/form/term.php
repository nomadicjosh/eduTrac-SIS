<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Term View
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
$flash = new \app\src\Core\etsis_Messages();
$screen = 'term';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=get_base_url();?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Term' );?></li>
</ul>

<h3><?=_t( 'Term' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

    <!-- Form -->
    <form class="form-horizontal margin-none" action="<?=get_base_url();?>form/term/" id="validateSubmitForm" method="post" autocomplete="off">
        
        <!-- Widget -->
        <div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
        
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
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Semester' );?></label>
                            <div class="col-md-8">
                                <select name="semCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown("semester", 'semCode <> "NULL"', "semCode", "semCode", "semName"); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="termCode"><font color="red">*</font> <?=_t( 'Term Code' );?></label>
                            <div class="col-md-8"><input class="form-control" name="termCode" type="text" required /></div>
                        </div>
                        <!-- // Group END -->
                    
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="termName"><font color="red">*</font> <?=_t( 'Term' );?></label>
                            <div class="col-md-8"><input class="form-control" name="termName" type="text" required /></div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="reportingTerm"><font color="red">*</font> <?=_t( 'Reporting Term' );?></label>
                            <div class="col-md-8"><input class="form-control" name="reportingTerm" type="text" required /></div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                    <!-- // Column END -->
                    
                    <!-- Column -->
                    <div class="col-md-6">
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="termStartDate"><font color="red">*</font> <?=_t( 'Start Date' );?></label>
                            <div class="col-md-8">
	                            <div class="input-group date col-md-8" id="datepicker8">
	                                <input class="form-control" name="termStartDate" type="text" required />
	                                <span class="input-group-addon"><i class="fa fa-th"></i></span>
	                            </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="termEndDate"><font color="red">*</font> <?=_t( 'End Date' );?></label>
                            <div class="col-md-8">
	                            <div class="input-group date col-md-8" id="datepicker9">
	                                <input class="form-control" name="termEndDate" type="text" required />
	                                <span class="input-group-addon"><i class="fa fa-th"></i></span>
	                            </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="dropAddEndDate"><font color="red">*</font> <?=_t( 'Drop/Add End Date' );?></label>
                            <div class="col-md-8">
	                            <div class="input-group date col-md-8" id="datepicker10">
	                                <input class="form-control" name="dropAddEndDate" type="text" required />
	                                <span class="input-group-addon"><i class="fa fa-th"></i></span>
	                            </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="term"><font color="red">*</font> <?=_t( 'Active' );?></label>
                            <div class="col-md-8">
	                            <select name="active" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
	                                <option value="">&nbsp;</option>
	                                <option value="1"><?=_t( 'Yes' );?></option>
	                                <option value="0"><?=_t( 'No' );?></option>
	                            </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                    </div>
                    
                </div>
                <!-- // Row END -->
            
                <hr class="separator" />
                
                <!-- Form actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
                </div>
                <!-- // Form actions END -->
                
            </div>
        </div>
        <!-- // Widget END -->
        
    </form>
    <!-- // Form END -->
    
    <div class="separator bottom"></div>
    
    <!-- Widget -->
    <div class="widget widget-heading-simple widget-body-white <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
        <div class="widget-body">
        
            <!-- Table -->
            <table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-primary">
            
                <!-- Table heading -->
                <thead>
                    <tr>
                        <th class="text-center"><?=_t( 'Term' );?></th>
                        <th class="text-center"><?=_t( 'Semester' );?></th>
                        <th class="text-center"><?=_t( 'Start Date' );?></th>
                        <th class="text-center"><?=_t( 'End Date' );?></th>
                        <th class="text-center"><?=_t( 'Status' );?></th>
                        <th class="text-center"><?=_t( 'Actions' );?></th>
                    </tr>
                </thead>
                <!-- // Table heading END -->
                
                <!-- Table body -->
                <tbody>
                <?php if($term != '') : foreach($term as $key => $value) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_h($value['termName']);?></td>
                    <td class="text-center"><?=_h($value['semName']);?></td>
                    <td class="text-center"><?=date('D, M d, o',strtotime(_h($value['termStartDate'])));?></td>
                    <td class="text-center"><?=date("D, M d, o",strtotime(_h($value['termEndDate'])));?></td>
                    <td class="text-center"><?php if($value['active'] == 1) {echo 'Active';}else{'Inactive';} ?></td>
                    <td class="text-center">
                        <div class="btn-group dropup">
                            <button class="btn btn-default btn-xs" type="button"><?=_t( 'Actions' ); ?></button>
                            <button data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle" type="button">
                                <span class="caret"></span>
                                <span class="sr-only"><?=_t( 'Toggle Dropdown' ); ?></span>
                            </button>
                            <ul role="menu" class="dropdown-menu dropup-text pull-right">
                                <li><a href="<?=get_base_url();?>form/term/<?=_h($value['termID']);?>/<?=bm();?>"><?=_t( 'View' ); ?></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php } endif; ?>
                    
                </tbody>
                <!-- // Table body END -->
                
            </table>
            <!-- // Table END -->
            
        </div>
    </div>
    <!-- // Widget END -->
    
</div>  
    
        
        </div>
        <!-- // Content END -->
<?php $app->view->stop(); ?>