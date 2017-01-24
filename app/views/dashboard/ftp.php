<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * FTP
 * 
 * The file manager allows faculty and staff to upload their documents
 * for easy access through the tinyMCE editor.
 *  
 * @license GPLv3
 * 
 * @since       6.3.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$screen = 'ftp';
?>

<script src="<?=get_base_url();?>static/assets/js/jquery.ui.min.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/plugins/elfinder/js/elfinder.min.js"></script>
<script type="text/javascript">
	$().ready(function() {
		var elf = $('#elfinder').elfinder({
			url : '<?=get_base_url();?>dashboard/connector/',
			modal: true,
			resizable:false
		}).elfinder('instance');
	});
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'FTP' );?></li>
</ul>

<h3><?=_t( 'FTP' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
        
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