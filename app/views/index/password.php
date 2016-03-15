<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Change Password View
 *  
 * @license GPLv3
 * 
 * @since       4.3
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/myet/' . _h(get_option('myet_layout')) . '.layout');
$app->view->block('myet');
$flash = new \app\src\Core\etsis_Messages();
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 10000);
</script>
<script src="<?=get_base_url();?>static/assets/js/pwdwidget.js" type="text/javascript"></script>

<div class="col-md-12">
	<div class="separator bottom"></div>
	<div class="separator bottom"></div>

	<h3 class="glyphicons keys"><i></i><?=_t( 'Change Password' );?></h3>
	<div class="separator bottom"></div>
    
    <?=$flash->showMessage();?>

<!-- Form -->
<form class="form-horizontal margin-none" action="<?=get_base_url();?>password/" id="validateSubmitForm" method="post" autocomplete="off">	
<div class="widget widget-heading-simple widget-body-white">
	<div class="widget-body">
		<div class="row">
			<div class="col-md-12">
				<form class="margin-none">
					<div class="row innerB">
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Current Password' );?></label>
							<input type="password" class="form-control" name="currPass" required/>
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'New Password' );?></label>
                            <?php if($app->hook->has_action('post_save_person') && _h(get_option('moodle_secure_passwords') == 'yes')) : ?>
                            <div class='pwdwidgetdiv' id='thepwddiv'></div>
                            <script type="text/javascript">
                            var pwdwidget = new PasswordWidget('thepwddiv','newPass');
                            pwdwidget.enableGenerate=true;
                            pwdwidget.enableShowStrength=false;
                            pwdwidget.MakePWDWidget();
                            </script>
                            <noscript>
                            <input type="password" class="form-control" id="newPass" name="newPass" required/>
                            </noscript>
                            <?php else : ?>
							<input type="password" class="form-control" id="newPass" name="newPass" required/>
                            <?php endif; ?>
						</div>
					</div>
					<div class="innerT">
						<button class="btn btn-primary btn-icon glyphicons circle_ok"><i></i> <?=_t( 'Update' );?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
</form>
<!-- // Form END -->

</div>
	</div>
</div>

	
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>