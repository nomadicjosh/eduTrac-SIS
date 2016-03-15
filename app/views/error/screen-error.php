<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 *
 * Search Screen Error View
 *  
 *  
 * @since       3.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$message = new \app\src\Core\etsis_Messages();
?>

<div class="innerLR errorView">
	
	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-white">
		
		<div class="widget-body">
			
				<!-- Row -->
				<div class="row">

					<!-- Alert -->
					<div class="alerts alerts-error">
						<strong><?=_t( 'Error!' );?></strong> 
                            <?=(is_etsis_exception($error)) ? $error->getMessage() : '';?>
					</div>
					<!-- // Alert END -->
			
				</div>
		
		</div>
	
	</div>
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>