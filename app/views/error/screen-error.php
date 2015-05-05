<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 *
 * Search Screen Error View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$message = new \app\src\Messages;
?>

<div class="innerLR errorView">
	
	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-white">
		
		<div class="widget-body">
			
				<!-- Row -->
				<div class="row">

					<!-- Alert -->
					<div class="alerts alerts-error">
						<strong><?=_t( 'Error!' );?></strong> <?=_t( 'The screen ' )._h(strtoupper($_GET['code']))._t(' does not exist. Please try your search again.' );?>
					</div>
					<!-- // Alert END -->
			
				</div>
		
		</div>
	
	</div>
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>