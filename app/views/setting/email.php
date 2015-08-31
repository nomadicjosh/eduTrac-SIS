<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Email Settings View
 * 
 * This view is used to render the email settings screen.
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
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Email Settings' );?></li>
</ul>

<h3><?=_t( 'Email Settings' );?></h3>
<div class="innerLR">
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>email/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
            
            <!-- Tabs Heading -->
            <div class="tabsbar">
                <ul>
                    <li<?= hl('general_settings'); ?> class="glyphicons user chevron-left"><a href="<?=url('/');?>setting/"><i></i> <?=_t( 'General' );?></a></li>
                    <li<?= hl('registration_settings'); ?> class="glyphicons lock"><a href="<?=url('/');?>registration/"><i></i> <?=_t( 'Registration' );?></a></li>
                    <li<?= hl('email_settings'); ?> class="glyphicons inbox tab-stacked active"><a href="<?=url('/');?>email/" data-toggle="tab"><i></i> <?=_t( 'Email' );?></a></li>
                    <li<?= hl('email_settings'); ?> class="glyphicons show_lines tab-stacked"><a href="<?=url('/');?>templates/"><i></i> <span><?=_t( 'Email Templates' );?></span></a></li>
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
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'System Email' );?>  <a href="#system" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
							<div class="col-md-8">
								<input type="text" name="system_email" value="<?=_h(get_option('system_email'));?>" class="form-control" required/>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( "Registrar's Email" );?> <a href="#registrar" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <input type="text" name="registrar_email_address" value="<?=_h(get_option('registrar_email_address'));?>" class="form-control" required/> 
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( "Room Request Email" );?> <a href="#request" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <input type="text" name="room_request_email" value="<?=_h(get_option('room_request_email'));?>" class="form-control" required/> 
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( "Contact Email" );?> <a href="#contact" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <input type="text" name="contact_email" value="<?=_h(get_option('contact_email'));?>" class="form-control" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( "Admissions Email" );?> <a href="#admissions" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <input type="text" name="admissions_email" value="<?=_h(get_option('admissions_email'));?>" class="form-control" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<div class="separator line bottom"></div>
				
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
	
	<div class="modal fade" id="system">
		<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'System Email' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t( 'All system and error related emails will be sent to this email address.' );?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	       	</div>
      	</div>
    </div>
    
    <div class="modal fade" id="registrar">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Registrar\'s Email' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t( "All course and course registration emails will be sent to this address." );?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	        </div>
      	</div>
    </div>
    
    <div class="modal fade" id="request">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Room Request Email' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t("This is the email where all room requests are sent.");?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	        </div>
      	</div>
    </div>
    
    <div class="modal fade" id="contact">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Contact Email' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t("Emails via the myeduTrac portal (i.e. address changes) will be sent to this email address. This email address will show up in the contact details in the portal's footer as well.");?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	        </div>
      	</div>
    </div>
    
    <div class="modal fade" id="admissions">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Admissions Email' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t("New applications submitted via the myeduTrac portal will be sent to this email adddress.");?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	        </div>
      	</div>
    </div>
	
</div>	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>