<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Grade Scale View
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
$screen = 'grsc';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=get_base_url();?>dashbaord/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=get_base_url();?>form/grade-scale/" class="glyphicons pin_flag"><i></i> <?=_t( 'Grading Scale' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Edit Grade' );?></li>
</ul>

<h3><?=_t( 'Edit Grade' );?></h3>
<div class="innerLR">
	
	<?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>form/grade-scale/<?=_h($scale[0]['id']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
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
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Grade' );?></label>
							<div class="col-md-8"><input class="form-control"<?php if(_h($scale[0]['grade']) == 'W') : echo ' readonly'; endif; ?> name="grade" type="text" value="<?=_h($scale[0]['grade']);?>" required /></div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Percent' );?></label>
							<div class="col-md-8"><input class="form-control" name="percent" type="text" value="<?=_h($scale[0]['percent']);?>" required /></div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Points' );?></label>
							<div class="col-md-8"><input class="form-control" name="points" type="text" value="<?=_h($scale[0]['points']);?>" required /></div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Count in GPA' );?> <a href="#modal" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <select name="count_in_gpa" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected('1',_h($scale[0]['count_in_gpa']),false);?>><?=_t( 'Yes' );?></option>
                                    <option value="0"<?=selected('0',_h($scale[0]['count_in_gpa']),false);?>><?=_t( 'No' );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Active' );?></label>
                            <div class="col-md-8">
                                <select name="status" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected('1',_h($scale[0]['status']),false);?>><?=_t( 'Yes' );?></option>
                                    <option value="0"<?=selected('0',_h($scale[0]['status']),false);?>><?=_t( 'No' );?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Short Description' );?></label>
                            <div class="col-md-8">
                                <textarea name="description" class="form-control" rows="3" data-height="auto"><?=_h($scale[0]['description']);?></textarea>
                            </div>
                        </div>
                    </div>
					
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
					<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
					<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>form/grade-scale/'"><i></i><?=_t( 'Cancel' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
	
	<!-- Modal -->
	<div class="modal fade" id="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Count in GPA' );?></h3>
				</div>
				<!-- // Modal heading END -->
				<!-- Modal body -->
				<div class="modal-body">
					<p><?=_t( 'Should this be applied and calculated in the GPA?' );?></p>
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