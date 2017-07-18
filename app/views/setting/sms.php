<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Twilio Settings View
 * 
 * This view is used to render the email settings screen.
 *  
 * @license GPLv3
 * 
 * @since       6.3.0
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
	<li><?=_t( 'Twilio Settings' );?></li>
</ul>

<h3><?=_t( 'Twilio Settings' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>sms/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
            
            <?php if(_h(get_option('twilio_phone_number')) != '') : ?>
                <a href="#test" data-toggle="modal" class="btn btn-inverse pull-right"><i class="fa fa-caret-square-o-right"></i></a>
                <div class="breakline">&nbsp;</div>
            <?php endif; ?>
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
            
            <!-- Tabs Heading -->
            <div class="tabsbar">
                <ul>
                    <li<?= hl('general_settings'); ?> class="glyphicons user chevron-left"><a href="<?=get_base_url();?>setting/"><i></i> <?=_t( 'General' );?></a></li>
                    <li<?= hl('registration_settings'); ?> class="glyphicons lock"><a href="<?=get_base_url();?>registration/"><i></i> <?=_t( 'Web Reg' );?></a></li>
                    <li<?= hl('email_settings'); ?> class="glyphicons inbox"><a href="<?=get_base_url();?>email/"><i></i> <?=_t( 'Email' );?></a></li>
                    <li<?= hl('email_settings'); ?> class="glyphicons show_lines"><a href="<?=get_base_url();?>templates/"><i></i> <span><?=_t( 'Email Templates' );?></span></a></li>
                    <li<?= hl('general_settings'); ?> class="glyphicons iphone tab-stacked active"><a href="<?=get_base_url();?>sms/" data-toggle="tab"><i></i> <span><?=_t( 'SMS' );?></span></a></li>
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
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Account SID' );?>  <a href="#sid" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
							<div class="col-md-8">
								<input type="text" name="twilio_account_sid" value="<?=_h(get_option('twilio_account_sid'));?>" class="form-control" required/>
							</div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( "Auth Token" );?> <a href="#token" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <input type="text" name="twilio_auth_token" value="<?=_h(get_option('twilio_auth_token'));?>" class="form-control" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( "Phone Number" );?> <a href="#number" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <input type="text" name="twilio_phone_number" value="<?=_h(get_option('twilio_phone_number'));?>" class="form-control" required/>
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
	
	<div class="modal fade" id="sid">
		<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Twilio Account SID' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
                    <p><?=sprintf(_t( 'Your Account SID from %s.' ), make_clickable('www.twilio.com/console'));?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	       	</div>
      	</div>
    </div>
    
    <div class="modal fade" id="token">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Twilio Auth Token' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
                    <p><?=sprintf(_t( "Your Auth Token from %s." ), make_clickable('www.twilio.com/console'));?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	        </div>
      	</div>
    </div>
    
    <div class="modal fade" id="number">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Twilio Phone Number' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t( "Your Twilio phone number where texts are sent from." );?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	        </div>
      	</div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="test">
        <form class="form-horizontal margin-none" action="<?=get_base_url();?>sms/test/" id="validateSubmitForm" method="post" autocomplete="off">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?=_t( 'Send Test SMS' );?></h3>
                </div>
                <!-- // Modal heading END -->
                <!-- Modal body -->
                <div class="modal-body">
                    <!-- Column -->
					<div class="col-md-12">
					
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( "Recipient's Number" );?></label>
							<div class="col-md-8">
								<input type="text" name="to" class="form-control" required/>
							</div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Message' );?></label>
							<div class="col-md-8">
                                <textarea name="message" class="form-control" placeholder="Write your text message . . ." required></textarea>
							</div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
                </div>
                <!-- // Modal body END -->
                <div class="breakline">&nbsp;</div>
                <div class="breakline">&nbsp;</div>
                <div class="breakline">&nbsp;</div>
                <div class="breakline">&nbsp;</div>
                <div class="breakline">&nbsp;</div>
                <div class="breakline">&nbsp;</div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i></i><?=_t( 'Submit' );?></button>
                    <a href="#" class="btn btn-default" data-dismiss="modal"><?=_t( 'Close' );?></a>
                </div>
                <!-- // Modal footer END -->
            </div>
        </div>
        </form>
    </div>
    <!-- // Modal END -->
	
</div>	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>