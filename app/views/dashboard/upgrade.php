<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Upgrade View
 *  
 * @license GPLv3
 * 
 * @since       3.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
?>

<div class="innerLR errorView">
	
	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
		
		<div class="widget-body">
			
				<!-- Row -->
				<div class="row">

                    <?php if(_h(get_option('dbversion')) < \app\src\ReleaseAPI::inst()->init('DB_VERSION')) { ?>
					<!-- Alert -->
					<div class="alert alert-primary center">
						<strong><?=_t( 'Warning!' );?></strong> <?=_t( 'Hey admin, your database is out of date and currently at version ') . _h(get_option('dbversion')) . _t('. Click the button below to upgrade your database. When the upgrade is complete,'). ' <a href="'.get_base_url(). 'dashboard' . DS . '"><font color="orange">'._t( 'click here').'</font></a> '. _t( 'to return to the dashboard. If you are behind on a few versions, you may be redirected to this page again until the system is fully up to date.' );?>
					</div>
					<!-- // Alert END -->
                    <!-- Form -->
                        <form class="form-horizontal margin-none" action="<?=get_base_url() . 'dashboard/upgrade' . DS;?>" id="validateSubmitForm" method="post">
                            <input type="hidden" name="upgradeDB" value="1" />
                            <button type="submit" name="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Submit' );?></button>
                        </form>
                    <!-- // Form END -->
                    
                    <?php } else { ?>
                    
                    <!-- Alert -->
    				<div class="alert alert-success center">
						<strong><?=_t( 'Hey admin, you database is currently up to date. There is nothing else here to see. ').'<a href="'.get_base_url().'dashboard/"><font color="orange">'._t('Click here').'</font></a> '._t('to return to the dashboard.' );?>
					</div>
					<!-- // Alert END -->
                    
                    <?php } ?>
                    
                    <?php
                        if(isset($_POST['upgradeDB']) && $_POST['upgradeDB'] == 1) {
                            upgradeSQL(\app\src\ReleaseAPI::inst()->getSchema());
                        }
                    ?>
			
				</div>
		
		</div>
	
	</div>
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>