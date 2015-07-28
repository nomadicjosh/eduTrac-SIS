<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Error View
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
if($app->hook->{'get_option'}('myet_layout') === null) {
    $app->view->extend('_layouts/myet/default.layout');
} else {
    $app->view->extend('_layouts/myet/' . $app->hook->{'get_option'}('myet_layout') . '.layout');
}
$app->view->block('myet');
?>


		<div class="col-md-12">
			
			<div class="separator bottom"></div>
			<div class="separator bottom"></div>
		
			<div class="widget widget-heading-simple widget-body-white">
				<div class="widget-body">
					<div class="row">	
						<div class="col-md-12">
							<h5 class="strong"><?=_t('Ouch!');?> <span><?=_t('404 error');?></span></h5>
							<div class="separator bottom"></div>
							<!-- Column -->
                            <div class="span6">
                                <div class="center">
                                    <p><?=_t('It seems the page you are looking for is not here anymore. The page might have moved to another address or just removed by our staff.');?></p>
                                </div>
                            </div>
                            <!-- // Column END -->

                            <!-- Column -->
                            <div class="span6">
                                <div class="center">
                                    <p><?=_t('Is this a serious error?');?> <a href="<?=$app->hook->{'get_option'}('help_desk');?>"><?=_t('Let us know');?></a></p>
                                </div>
                            </div>
                            <!-- // Column END -->
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