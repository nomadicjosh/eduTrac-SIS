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
	selector: "textarea.password",
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
	<li><?=_t( 'System Settings' );?></li>
</ul>

<h3><?=_t( 'System Settings' );?></h3>
<div class="innerLR">

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>setting/" id="validateSubmitForm" method="post" autocomplete="off">
		
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
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Institution Name' );?></label>
							<div class="col-md-8">
								<input type="text" name="institution_name" value="<?=_h($app->hook->{'get_option'}('institution_name'));?>" class="form-control" required />
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'Enable SSL' );?></label>
							<div class="col-md-8">
								<select name="enable_ssl" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" disabled="disabled">
                            		<option value="">&nbsp;</option>
                            		<option value="1"<?=selected( _h($app->hook->{'get_option'}( 'enable_ssl' )), '1', false ); ?>><?=_t( "Yes" );?></option>
                            		<option value="0"<?=selected( _h($app->hook->{'get_option'}( 'enable_ssl' )), '0', false ); ?>><?=_t( "No" );?></option>
                            	</select>
							</div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Maintenance Mode' );?></label>
                            <div class="col-md-8">
                                <select name="maintenance_mode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" disabled="disabled">
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected( _h($app->hook->{'get_option'}( 'maintenance_mode' )), '1', false ); ?>><?=_t( "Yes" );?></option>
                                    <option value="0"<?=selected( _h($app->hook->{'get_option'}( 'maintenance_mode' )), '0', false ); ?>><?=_t( "No" );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Enable Benchmark' );?></label>
                            <div class="col-md-8">
                                <select name="enable_benchmark" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected( _h($app->hook->{'get_option'}( 'enable_benchmark' )), '1', false ); ?>><?=_t( "Yes" );?></option>
                                    <option value="0"<?=selected( _h($app->hook->{'get_option'}( 'enable_benchmark' )), '0', false ); ?>><?=_t( "No" );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Enable Cron Jobs' );?> <a href="#myModalECJ" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <select name="enable_cron_jobs" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected( _h($app->hook->{'get_option'}( 'enable_cron_jobs' )), '1', false ); ?>><?=_t( "Yes" );?></option>
                                    <option value="0"<?=selected( _h($app->hook->{'get_option'}( 'enable_cron_jobs' )), '0', false ); ?>><?=_t( "No" );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Enable Cron Log' );?></label>
                            <div class="col-md-8">
                                <select name="enable_cron_log" class="selectpicker form-control" disabled data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected( _h($app->hook->{'get_option'}( 'enable_cron_log' )), '1', false ); ?>><?=_t( "Yes" );?></option>
                                    <option value="0"<?=selected( _h($app->hook->{'get_option'}( 'enable_cron_log' )), '0', false ); ?>><?=_t( "No" );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'myeT Mode' );?> <a href="#portal" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <select name="enable_myet_portal" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected( _h($app->hook->{'get_option'}( 'enable_myet_portal' )), '1', false ); ?>><?=_t( "Online" );?></option>
                                    <option value="0"<?=selected( _h($app->hook->{'get_option'}( 'enable_myet_portal' )), '0', false ); ?>><?=_t( "Offline" );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
    					<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Offline Message' );?> <a href="#portaloff" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
							<div class="col-md-8">
								<textarea name="myet_offline_message" class="form-control" rows="5" required><?=_h($app->hook->{'get_option'}('myet_offline_message'));?></textarea>
							</div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Application' );?> <a href="#appl" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <select name="enable_myet_appl_form" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected( _h($app->hook->{'get_option'}( 'enable_myet_appl_form' )), '1', false ); ?>><?=_t( "Enabled" );?></option>
                                    <option value="0"<?=selected( _h($app->hook->{'get_option'}( 'enable_myet_appl_form' )), '0', false ); ?>><?=_t( "Disabled" );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Screen Caching' );?> <a href="#scache" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <select name="screen_caching" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected( _h($app->hook->{'get_option'}( 'screen_caching' )), '1', false ); ?>><?=_t( "Enabled" );?></option>
                                    <option value="0"<?=selected( _h($app->hook->{'get_option'}( 'screen_caching' )), '0', false ); ?>><?=_t( "Disabled" );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'DB Caching' );?> <a href="#dbcache" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <select name="db_caching" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                	<option value="">&nbsp;</option>
                                    <option value="1"<?=selected( _h($app->hook->{'get_option'}( 'db_caching' )), '1', false ); ?>><?=_t( "Enabled" );?></option>
                                    <option value="0"<?=selected( _h($app->hook->{'get_option'}( 'db_caching' )), '0', false ); ?>><?=_t( "Disabled" );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Language' );?></label>
                            <div class="col-md-8">
                                <select name="et_core_locale" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                	<option value="">&nbsp;</option>
                                    <option value="en_US"<?=selected( _h($app->hook->{'get_option'}( 'et_core_locale' )), 'en_US', false ); ?>><?=_t( "English" );?></option>
                                    <option value="es_ES"<?=selected( _h($app->hook->{'get_option'}( 'et_core_locale' )), 'es_ES', false ); ?>><?=_t( "Spanish" );?></option>
                                    <option value="pt_BR"<?=selected( _h($app->hook->{'get_option'}( 'et_core_locale' )), 'pt_BR', false ); ?>><?=_t( "Portuguese" );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Timezone' );?></label>
                            <div class="col-md-8">
                                <select name="system_timezone" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                	<option value="">&nbsp;</option>
                                    <?php foreach(generate_timezone_list() as $k => $v) : ?>
                                    <option value="<?=$k;?>"<?=selected( _h($app->hook->{'get_option'}( 'system_timezone' )), $k, false ); ?>><?=$v;?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Cookie TTL' );?></label>
							<div class="col-md-8">
								<input type="text" name="cookieexpire" value="<?=_h((int)$app->hook->{'get_option'}('cookieexpire'));?>" class="form-control" required/>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Cookie Path' );?></label>
							<div class="col-md-8">
								<input type="text" name="cookiepath" value="<?=_h($app->hook->{'get_option'}('cookiepath'));?>" class="form-control" required/>
							</div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Curl' );?></label>
                            <div class="col-md-8">
                                <select name="curl" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected( _h($app->hook->{'get_option'}( 'curl' )), '1', false ); ?>><?=_t( "On" );?></option>
                                    <option value="0"<?=selected( _h($app->hook->{'get_option'}( 'curl' )), '0', false ); ?>><?=_t( "Off" );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
    					<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'Auth Token' );?> <a href="#token" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
							<div class="col-md-8">
								<input type="text" name="auth_token" value="<?=_h($app->hook->{'get_option'}('auth_token'));?>" class="form-control" />
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
    					<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'Contact Phone' );?></label>
							<div class="col-md-8">
								<input type="text" name="contact_phone" value="<?=_h($app->hook->{'get_option'}('contact_phone'));?>" class="form-control" />
							</div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
    					<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'Help Desk' );?></label>
							<div class="col-md-8">
								<input type="text" name="help_desk" value="<?=_h($app->hook->{'get_option'}('help_desk'));?>" class="form-control" />
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'eduTrac Analytics' );?> <a href="#ea" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <input type="text" name="edutrac_analytics_url" value="<?=_h($app->hook->{'get_option'}('edutrac_analytics_url'));?>" class="form-control" />
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
    					<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'Mailing Address' );?></label>
							<div class="col-md-8">
								<textarea name="mailing_address" class="form-control" rows="5"><?=_h($app->hook->{'get_option'}('mailing_address'));?></textarea>
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
					<label class="col-md-3 control-label"><?=_t( 'Reset Password Text' );?></label>
					<div class="col-md-8">
						<textarea id="mustHaveId" class="col-md-8 form-control password" name="reset_password_text" rows="20"><?=_h($app->hook->{'get_option'}('reset_password_text'));?></textarea>
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
	
    <div class="modal fade" id="myModalECJ">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Enable Cron Jobs' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t("This option should be set to 'No' until you have configured each");?> <a href="<?=url('/');?>cron/"><?=_t('cron job');?></a>. <?=_t("If this is set to 'Yes' before that, your error logs will be huge.");?></p>
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
					<h3 class="modal-title"><?=_t( 'Auth Token' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t("If you plan to do development work using the RESTful API feature, then you will need an api key.");?> <a href="http://www.edutracerp.com/auth-token/1.1/"><?=_t('Click here');?></a> <?=_t("to generate an api key for your account.");?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	        </div>
      	</div>
    </div>
    <div class="modal fade" id="portal">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'myeduTrac Self Service' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t("Use this option to place myeduTrac self service into maintenance mode.");?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	        </div>
      	</div>
    </div>
    <div class="modal fade" id="portaloff">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'myeduTrac Offline Message' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t("Type the message that should be shown to vistors when myeduTrac self service is in offline mode. Those who have an account can still log into the site. If the logged in user does not have dashboard access, they will be redirected back to the offline message.");?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	        </div>
      	</div>
    </div>
    <div class="modal fade" id="appl">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'myeduTrac Application Form' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t("Enable this option to allow access to the application for admissions form.");?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	        </div>
      	</div>
    </div>
    <div class="modal fade" id="scache">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Screen Caching' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t("Static screens that don't really change often can be cached to reduce page loading time. However, if you decide to disable this option, make sure to clear the screen cache.");?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	        </div>
      	</div>
    </div>
    <div class="modal fade" id="dbcache">
    	<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Database Caching' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t("Along with screen caching, database caching helps with scalability, performance and greatly reduces overhead. However, if you decide to disable this option, make sure to clear the database cache.");?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	        </div>
      	</div>
    </div>
    <div class="modal fade" id="ea">
        <div class="modal-dialog">
            <div class="modal-content">
    
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?=_t( 'eduTrac Analytics' );?></h3>
                </div>
                <!-- // Modal heading END -->
                <div class="modal-body">
                    <p><?=_t("If you are using eduTrac Analytics, enter the base url where it is installed (i.e. http://example.com/ea/).");?></p>
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