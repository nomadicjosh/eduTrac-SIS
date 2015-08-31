<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * File Manager
 * 
 * The file manager allows faculty and staff to upload their documents
 * for easy access through the tinyMCE editor.
 *  
 * @license GPLv3
 * 
 * @since       6.1.00
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$message = new \app\src\Messages;
$screen = 'fm';
?>

<script src="<?=url('/');?>static/assets/js/jquery.ui.min.js"></script>
<script type="text/javascript" src="<?=url('/');?>static/assets/plugins/elfinder/js/elfinder.min.js"></script>
<script type="text/javascript">
	$().ready(function() {
		var elf = $('#elfinder').elfinder({
			url : '<?=url('/');?>staff/connector/',
			modal: true,
			resizable:false
		}).elfinder('instance');
	});
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'File Manager' );?></li>
</ul>

<h3><?=_t( 'File Manager' );?></h3>
<div class="innerLR">
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
        
		<div class="widget-body">
			<div class="row">
                        
                <div class="col-md-12">
					<div class="panel-body">
		                <div id="elfinder"></div>
		            </div>
	            </div>
           	</div>
		</div>
	</div>
	
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>