<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * System Settings View
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
?>

<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
	selector: "textarea",
	plugins: [
		"advlist autolink lists link image charmap print preview anchor",
		"searchreplace visualblocks code fullscreen",
		"insertdatetime media table contextmenu paste"
	],
	toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
	autosave_ask_before_unload: false
});
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Email Settings' );?></li>
</ul>

<h3><?=_t( 'Email Settings' );?></h3>
<div class="innerLR">

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>email/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray">
		
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
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'System Email' );?>  <a href="#system" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
							<div class="col-md-8">
								<input type="text" name="system_email" value="<?=_h($app->hook->{'get_option'}('system_email'));?>" class="form-control" required/>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( "Registrar's Email" );?> <a href="#registrar" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <input type="text" name="registrar_email_address" value="<?=_h($app->hook->{'get_option'}('registrar_email_address'));?>" class="form-control" required/> 
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
                                <input type="text" name="contact_email" value="<?=_h($app->hook->{'get_option'}('contact_email'));?>" class="form-control" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( "Admissions Email" );?> <a href="#admissions" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <input type="text" name="admissions_email" value="<?=_h($app->hook->{'get_option'}('admissions_email'));?>" class="form-control" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<div class="separator line bottom"></div>
								
				<!-- Group -->
				<div class="form-group row">
					<label class="col-md-3 control-label"><?=_t( 'Change of Address Form Email Text' );?></label>
					<div class="col-md-8">
						<textarea id="mustHaveId" class="col-md-8 form-control" name="coa_form_text" rows="10"><?=_h($app->hook->{'get_option'}('coa_form_text'));?></textarea>
					</div>
				</div>
				<!-- // Group END -->
				
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