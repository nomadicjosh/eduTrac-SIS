<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * myeduTrac Login View
 *  
 * PHP 5.4+
 *
 * eduTrac ERP(tm) : College Management System (http://www.7mediaws.org/)
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

<div id="login" class="col-md-12">
	
	<div class="separator bottom"></div>
	<div class="separator bottom"></div>

	<div class="container" style="width:400px !important;">
	
		<div class="wrapper">
		
			<h1 class="glyphicons unlock"><?=_t( 'Sign in' );?> <i></i></h1>
			
			<?=$message->flashMessage();?>
		
			<!-- Box -->
			<div class="widget widget-heading-simple widget-body-gray">
				
				<div class="widget-body">
				
					<!-- Form -->
					<form class="form-horizontal margin-none" method="post" action="<?=get_base_url();?>login/" id="validateSubmitForm" autocomplete="off">
						<label><?=_t( 'Username' );?></label>
						<input type="text" name="uname" class="form-control" placeholder="Your Username" required/> 
						<label><?=_t( 'Password' );?></label>
						<input type="password" name="password" class="form-control margin-none" placeholder="Your Password" required/>
						<div class="row">
							<div class="col-md-8">
								<div class="uniformjs"><label class="checkbox"><input type="checkbox" name="rememberme" value="rememberme"><?=_t( 'Remember me' );?></label></div>
							</div>
							<div class="col-md-4 center">
								<button class="btn btn-block btn-inverse" type="submit"><?=_t( 'Sign in' );?></button>
							</div>
						</div>
					</form>
					<!-- // Form END -->
							
				</div>
			</div>
			<!-- // Box END -->
			
		</div>
		
	</div>
	
</div>
<!-- // Wrapper END -->
	</div>
</div>

	
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>