<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * myeduTrac Offline View
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
if($app->hook->{'get_option'}('enable_myet_portal') == 1) {
    redirect(url('/'));
}
?>

		<div class="col-md-12">
			
			<div class="separator bottom"></div>
			<div class="separator bottom"></div>
		
			<div class="widget widget-heading-simple widget-body-white">
				<div class="widget-body">
					<div class="row">	
						<div class="col-md-12">
							<h5 class="strong"><?=_t( 'Offline' );?></h5>
							<div class="separator bottom"></div>
                            <section class="panel error-panel"><div class="alerts alerts-info"><?=nl2br(_h($app->hook->{'get_option'}('myet_offline_message')));?></div></section>
						</div>
					</div>
				</div>
			</div>
		
		</div>
	</div>
</div>
	
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>