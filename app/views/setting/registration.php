<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Registration Settings View
 * 
 * This view is used to render the registration settings screen.
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
$screen = 'setting';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Web Registration' );?></li>
</ul>

<h3><?=_t( 'Web Registration' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>registration/" id="validateSubmitForm" method="post" autocomplete="off">
		
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
                    <li<?= hl('general_settings'); ?> class="glyphicons user chevron-left"><a href="<?=get_base_url();?>setting/"><i></i> <?=_t( 'General' );?></a></li>
                    <li<?= hl('registration_settings'); ?> class="glyphicons lock active"><a href="<?=get_base_url();?>registration/" data-toggle="tab"><i></i> <?=_t( 'Web Reg' );?></a></li>
                    <li<?= hl('email_settings'); ?> class="glyphicons inbox"><a href="<?=get_base_url();?>email/"><i></i> <?=_t( 'Email' );?></a></li>
                    <li<?= hl('email_settings'); ?> class="glyphicons show_lines"><a href="<?=get_base_url();?>templates/"><i></i> <span><?=_t( 'Email Templates' );?></span></a></li>
                    <li<?= hl('general_settings'); ?> class="glyphicons iphone"><a href="<?=get_base_url();?>sms/"><i></i> <span><?=_t( 'SMS' );?></span></a></li>
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
							<label class="col-md-3 control-label"><?=_t( 'Open Registration' );?></label>
							<div class="col-md-8">
								<select name="open_registration" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                            		<option value="">&nbsp;</option>
                            		<option value="1"<?=selected( _h(get_option( 'open_registration' )), '1', false ); ?>><?=_t( "Yes" );?></option>
                            		<option value="0"<?=selected( _h(get_option( 'open_registration' )), '0', false ); ?>><?=_t( "No" );?></option>
                            	</select>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Current Term' );?></label>
                            <div class="col-md-8">
                                <select name="current_term_code" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('term','termCode <> "NULL"','termCode','termCode','termName',_h(get_option('current_term_code'))); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( '# of Courses' );?> <a href="#courses" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <input type="text" name="number_of_courses" value="<?=_h(get_option('number_of_courses'));?>" class="form-control" required/> 
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Open Reg Date' );?> <a href="#webreg" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <div class="input-group date col-md-12" id="datepicker6">
                                    <input class="form-control" name="open_webreg_date" type="text" value="<?=_h(get_option('open_webreg_date'));?>" required />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
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
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Open Terms' );?> <a href="#openterm" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <input type="text" name="open_terms" value="<?=_h(get_option('open_terms'));?>" class="form-control" required/> 
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Registration Term' );?> <a href="#register" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <select name="registration_term" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('term','termCode <> "NULL"','termCode','termCode','termName',_h(get_option('registration_term'))); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Instructions' );?> <a href="#reginfo" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <textarea class="form-control" name="reg_instructions" rows="3" data-height="auto"><?=_h(get_option('reg_instructions'));?></textarea>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
				</div>
				<!-- // Row END -->
				
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
	
	<!-- Modal -->
	<div class="modal fade" id="courses">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( '# of Courses' );?></h3>
				</div>
				<!-- // Modal heading END -->
				<!-- Modal body -->
				<div class="modal-body">
					<p><?=_t( 'Set the number of courses a student is able to register into for any given term. There should only be one open term and it must match the registration term. Depending how long your course offering list is, this may not work 100%.' );?></p>
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
	<!-- Modal -->
	<div class="modal fade" id="webreg">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Open Web Reg Date' );?></h3>
				</div>
				<!-- // Modal heading END -->
				<!-- Modal body -->
				<div class="modal-body">
					<p><?=_t( "Enter the date for when web registratration should be opened for the registration term." );?></p>
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
	<!-- Modal -->
	<div class="modal fade" id="openterm">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Open Terms' );?></h3>
				</div>
				<!-- // Modal heading END -->
				<!-- Modal body -->
				<div class="modal-body">
					<p><?=_t( 'Enter a comma delimited list of course terms that can be viewed on the course registration screen (i.e. "13/FA","14/SP").' );?></p>
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
	<!-- Modal -->
	<div class="modal fade" id="register">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Registration Term' );?></h3>
				</div>
				<!-- // Modal heading END -->
				<!-- Modal body -->
				<div class="modal-body">
					<p><?=_t( "If `Open Registration` is set to yes, then choose the term for which a student can register into courses." );?></p>
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
	<!-- Modal -->
	<div class="modal fade" id="reginfo">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Registration Instructions' );?></h3>
				</div>
				<!-- // Modal heading END -->
				<!-- Modal body -->
				<div class="modal-body">
					<p><?=_t( "Enter any comments or instructions you would like to appear on the course registration page." );?></p>
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