<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Support View
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
?>

<script>
  var iframe = window.getElementsByTagName( "iframe" )[ 0 ];
  alert( "Frame title: " + iframe.contentWindow.title );
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Online Documenation' );?></li>
</ul>

<h3><?=_t( 'Online Documenation' );?></h3>
<div class="innerLR">
		
	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
		
		<div class="widget-body">
			
			<iframe src="http://www.edutracsis.com/" width="100%" height="900" marginwidth="0" marginheight="0" frameborder="0">
			  <p><?=_t( 'Your browser does not support iframes.' );?></p>
			</iframe>
			
		</div>
	</div>
	<!-- // Widget END -->
	
</div>	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>