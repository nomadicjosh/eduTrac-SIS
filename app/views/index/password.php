<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Change Password View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       4.3
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
if($app->hook->{'get_option'}('myet_layout') === null) {
    $app->view->extend('_layouts/myet/default.layout');
} else {
    $app->view->extend('_layouts/myet/' . $app->hook->{'get_option'}('myet_layout') . '.layout');
}
$app->view->block('myet');
$message = new \app\src\Messages;
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<div class="col-md-12">
	<div class="separator bottom"></div>
	<div class="separator bottom"></div>

	<h3 class="glyphicons keys"><i></i><?=_t( 'Change Password' );?></h3>
	<div class="separator bottom"></div>
    
    <?=$message->flashMessage();?>

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
							<input type="password" class="form-control" name="newPass" required/>
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