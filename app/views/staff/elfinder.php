<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * File Manager Window
 * 
 * This file will be called when the image button is invoked on
 * tinyMCE
 *  
 * @license GPLv3
 * 
 * @since       6.1.00
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/blank');
$app->view->block('blank');
?>

<!DOCTYPE html>
<!--[if lt IE 7]> <html class="ie lt-ie9 lt-ie8 lt-ie7 fluid top-full"> <![endif]-->
<!--[if IE 7]>    <html class="ie lt-ie9 lt-ie8 fluid top-full sticky-top"> <![endif]-->
<!--[if IE 8]>    <html class="ie lt-ie9 fluid top-full sticky-top"> <![endif]-->
<!--[if gt IE 8]> <html class="ie gt-ie8 fluid top-full sticky-top"> <![endif]-->
<!--[if !IE]><!--><html class="fluid top-full sticky-top"><!-- <![endif]-->
<head>
<!--[if lt IE 9]><link rel="stylesheet" href="<?= url('/'); ?>static/assets/components/library/bootstrap/css/bootstrap.min.css" /><![endif]-->
<link rel="stylesheet" href="<?= get_css_directory_uri(); ?>admin/module.admin.page.layout.section.layout-fluid-menu-top-full.min.css" />
<link rel="stylesheet" href="<?= get_css_directory_uri(); ?>admin/custom.css" />
<script src="<?= get_javascript_directory_uri(); ?>library/jquery/jquery.min.js?v=v2.1.0"></script>
<script src="<?= get_javascript_directory_uri(); ?>library/jquery/jquery-migrate.min.js?v=v2.1.0"></script>
<script src="<?= url('/'); ?>static/assets/js/jquery.ui.min.js"></script>
<?php
if (isset($cssArray)) {
    foreach ($cssArray as $css) {
        echo '<link href="' . url('/') . 'static/assets/' . $css . '" rel="stylesheet">' . "\n";
    }
}

?>
<script>
    var basePath = '',
        commonPath = '<?= url('/'); ?>static/assets/',
        rootPath = '<?= url('/'); ?>',
        DEV = false,
        componentsPath = '<?= url('/'); ?>static/assets/components/';
</script>
<script src="<?= get_javascript_directory_uri(); ?>library/bootstrap/js/bootstrap.min.js?v=v2.1.0"></script>

<?php
if (isset($jsArray)) {
    foreach ($jsArray as $js) {
        echo '<script type="text/javascript" src="' . url('/') . 'static/assets/' . $js . '"></script>' . "\n";
    }
}

?>
</head>
    <body class="">
        
		<div class="widget-body">
			<div class="row">
                        
                <div class="col-md-12">
					<div class="panel-body">
		                <div id="elfinder"></div>
		            </div>
	            </div>
           	</div>
		</div>
        
    </body>
</html>
<?php $app->view->stop(); ?>