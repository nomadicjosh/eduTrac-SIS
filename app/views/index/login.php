<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * myetSIS Login View
 *  
 * @license GPLv3
 * 
 * @since       4.3
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/myetsis/' . _h(get_option('myetsis_layout')) . '.layout');
$app->view->block('myetsis');
?>

<div id="login" class="col-md-12">

	<div class="container" style="width:400px !important;">
	
		<div class="wrapper">
		
			<h1 class="glyphicons unlock"><?=_t( 'Sign in' );?> <i></i></h1>
            
            <?php 
            /**
             * Prints scripts or data at the top
             * of the login form
             * 
             * @since 6.1.06
             */
            $app->hook->do_action('login_form_top'); 
            ?>
		
			<!-- Box -->
			<div class="widget widget-heading-simple widget-body-gray">
				
				<div class="widget-body">
				
					<!-- Form -->
					<form class="form-horizontal margin-none" method="post" action="<?=get_base_url();?>login/" id="validateSubmitForm" autocomplete="off">
						<label><?=_t( 'Username / Email' );?></label>
						<input type="text" name="uname" class="form-control" placeholder="Your Username" required/> 
						<label><?=_t( 'Password' );?></label>
						<input type="password" name="password" class="form-control margin-none" placeholder="Your Password" required/>
						<div class="row">
							<div class="col-md-8">
								<div class="uniformjs"><label class="checkbox"><input type="checkbox" name="rememberme" value="yes"><?=_t( 'Remember me' );?></label></div>
							</div>
							<div class="col-md-4 center">
                                <input type="hidden" name="redirect_to" value="<?=($app->req->get['redirect_to'] != null ? '?redirect_to=' . $app->req->get['redirect_to'] : '');?>" />
								<button class="btn btn-block btn-inverse" type="submit"><?=_t( 'Sign in' );?></button>
							</div>
						</div>
					</form>
					<!-- // Form END -->
							
				</div>
			</div>
			<!-- // Box END -->
            
            <div class="innerT center">
				<p><a href="#resetpass" data-toggle="modal"><?=_t( 'Request Password Reset' );?></a></p>
			</div>

            
            <?php 
            /**
             * Prints scripts or data at the bottom
             * of the login form.
             * 
             * @since 6.1.06
             */
            $app->hook->do_action('login_form_bottom'); ?>
			
		</div>
		
	</div>
    
    <!-- Modal -->
	<div class="modal fade" id="resetpass">
		<form class="form-horizontal margin-none" action="<?=get_base_url();?>reset-password/" id="validateSubmitForm" method="post" autocomplete="off">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Reset Password Request' );?></h3>
				</div>
				<!-- // Modal heading END -->
				<!-- Modal body -->
				<div class="modal-body">
					<div class="row">
					<div class="col-md-12">
                        <p class="alerts alerts-info"><?=_t( "If you've forgotten your password, send a request to the administrator to have it reset." );?></p>
                        <p>&nbsp;</p>
					<!-- Group -->
		            <div class="form-group">
		                <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Email' );?></label>
		                <div class="col-md-8">
		                    <input class="form-control" type="email" name="email" required/>
		                </div>
		            </div>
		            <!-- // Group END -->
		            
		            <!-- Group -->
		            <div class="form-group">
		                <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Full Name' );?></label>
		                <div class="col-md-8">
                            <input class="form-control" type="text" name="name" required/>
                        </div>
		            </div>
		            <!-- // Group END -->
                    
                    <!-- Group -->
		            <div class="form-group">
		                <label class="col-md-3 control-label"><?=_t( 'Username' );?></label>
		                <div class="col-md-8">
                            <input class="form-control" type="text" name="uname" />
                        </div>
		            </div>
		            <!-- // Group END -->
                    
                    <!-- Group -->
		            <div class="form-group">
		                <label class="col-md-3 control-label"><?=_t( 'Student / Staff ID' );?></label>
		                <div class="col-md-8">
                            <input class="form-control" type="text" name="sid" />
                        </div>
		            </div>
		            <!-- // Group END -->
		            
		            <!-- Group -->
		            <div class="form-group">
		                <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Message' );?></label>
		                <div class="col-md-8">
		                    <textarea class="form-control" name="message" rows="3" data-height="auto" required></textarea>
		                </div>
		            </div>
		            <!-- // Group END -->
		           	</div>
		           	</div>
				</div>
				<!-- // Modal body END -->
				<!-- Modal footer -->
				<div class="modal-footer">
		        	<button type="submit" class="btn btn-default"><?=_t( 'Send' );?></button>
					<a href="#" class="btn btn-primary" data-dismiss="modal"><?=_t( 'Cancel' );?></a>
				</div>
				<!-- // Modal footer END -->
			</div>
		</div>
		</form>
	</div>
	<!-- // Modal END -->
	
</div>
<!-- // Wrapper END -->
	</div>
</div>

	
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>