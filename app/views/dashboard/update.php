<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Update View
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
$screen = 'update';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Automatic Update' );?></li>
</ul>

<h3><?=_t( 'Automatic Update' );?></h3>
<div class="innerLR">

	<?php jstree_sidebar_menu($screen); ?>
	
	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-white <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
		<div class="widget-body">
			
				<!-- Row -->
				<div class="row">

					<!-- Alert -->
                    <div class="alert alert-info" style="color: #666666;">
                        <?php
                            $update = \app\src\CoreUpdate::inst();
                            $zip = $update->zip_extention_loaded();
                            if(is_et_exception($zip)) {
                                echo $zip->getMessage();
                                exit();
                            }
                            $update->update();
                        ?>
					</div>
					<!-- // Alert END -->
			
				</div>
		
		</div>
	
	</div>
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>