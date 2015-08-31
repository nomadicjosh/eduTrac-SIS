<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Semester View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @license     http://www.edutracerp.com/general/edutrac-erp-commercial-license/ Commercial License
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$message = new \app\src\Messages;
$screen = 'sem';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Semester' );?></li>
</ul>

<h3><?=_t( 'Semester' );?></h3>
<div class="innerLR">
    
    <?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>form/semester/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
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
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Academic Year' );?></label>
							<div class="col-md-8">
								<select name="acadYearCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="">&nbsp;</option>
                            		<?php table_dropdown('acad_year', 'acadYearCode <> "NULL"', 'acadYearCode', 'acadYearCode', 'acadYearDesc'); ?>
                            	</select>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="semCode"><font color="red">*</font> <?=_t( 'Semester Code' );?></label>
                            <div class="col-md-8"><input class="form-control" name="semCode" type="text" required /></div>
                        </div>
                        <!-- // Group END -->
					
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label" for="semName"><font color="red">*</font> <?=_t( 'Semester' );?></label>
							<div class="col-md-8"><input class="form-control" name="semName" type="text" required /></div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
					    
					    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="semStartDate"><font color="red">*</font> <?=_t( 'Start Date' );?></label>
                            <div class="col-md-8">
                                <div class="input-group date col-md-8" id="datepicker6">
                                    <input class="form-control" name="semStartDate" type="text" required />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label" for="semEndDate"><font color="red">*</font> <?=_t( 'End Date' );?></label>
							<div class="col-md-8">
								<div class="input-group date col-md-8" id="datepicker8">
						    		<input class="form-control" name="semEndDate" type="text" required />
				    				<span class="input-group-addon"><i class="fa fa-th"></i></span>
								</div>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label" for="active"><font color="red">*</font> <?=_t( 'Active' );?></label>
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
					<!-- // Column END -->
					
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
					<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Submit' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
			
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
	
	<div class="separator bottom"></div>
	
	<!-- Widget -->
    <div class="widget widget-heading-simple widget-body-white <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
        <div class="widget-body">
        
            <!-- Table -->
            <table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-primary">
            
                <!-- Table heading -->
                <thead>
                    <tr>
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
                <?php if($semester != '') : foreach($semester as $key => $value) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_h($value['semName']);?></td>
                    <td class="text-center"><?=date('D, M d, o',strtotime(_h($value['semStartDate'])));?></td>
                    <td class="text-center"><?=date('D, M d, o',strtotime(_h($value['semEndDate'])));?></td>
                    <td class="text-center"><?php if($value['active'] == 1) {echo 'Active';}else{'Inactive';} ?></td>
                    <td class="text-center">
                    	<div class="btn-group dropup">
                            <button class="btn btn-default btn-xs" type="button"><?=_t( 'Actions' ); ?></button>
                            <button data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle" type="button">
                                <span class="caret"></span>
                                <span class="sr-only"><?=_t( 'Toggle Dropdown' ); ?></span>
                            </button>
                            <ul role="menu" class="dropdown-menu dropup-text pull-right">
                                <li><a href="<?=url('/');?>form/semester/<?=_h($value['semesterID']);?>/<?=bm();?>"><?=_t( 'View' ); ?></a></li>
                                <?php $app->hook->{'do_action'}('search_semester_action'); ?>
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