<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Plugins View
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
$plugins_header = $app->hook->get_plugins_header(APP_PATH . 'plugins/');
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Plugins' );?></li>
</ul>

<h3><?=_t( 'Plugins' );?></h3>
<div class="innerLR">

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
		<div class="widget-body">
        
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
			
				<!-- Table heading -->
				<thead>
					<tr>
						<th class="text-center"><?=_t( 'Plugin' );?></th>
						<th class="text-center"><?=_t( 'Description' );?></th>
						<th class="text-center"><?=_t( 'Action' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
                <?php         		
        			// Let's read the content of the array
        			foreach($plugins_header as $plugin) {
                    if($app->hook->{'is_plugin_activated'}($plugin['filename']) == true)
        				echo '<tr class="separated gradeX">';
        			else
        				echo '<tr class="separated gradeX">';
        							
        			// Display the plugin information
        			echo '<td class="text-center">'.$plugin['Name'].'</td>';
        			echo '<td>'.$plugin['Description'];
        			echo '<br /><br />';
        			echo 'Version '.$plugin['Version'];
        			echo ' By <a href="'.$plugin['AuthorURI'].'">'.$plugin['Author']. '</a> ';
        			echo ' | <a href="' .$plugin['PluginURI'].'">Visit plugin site</a></td>';
        			
                        if($app->hook->{'is_plugin_activated'}($plugin['filename']) == true) {
        					echo '<td class="text-center"><a href="'.url('/').'plugins/deactivate/?id='.urlencode($plugin['filename']).'" title="Deactivate" class="btn btn-default"><i class="fa fa-minus"></i></a></td>';
        				} else {
        					echo '<td class="text-center"><a href="'.url('/').'plugins/activate/?id='.urlencode($plugin['filename']).'" title="Activate" class="btn btn-default"><i class="fa fa-plus"></i></a></td>';
        				}
        				
        				echo '</tr>';
        		} ?>
				</tbody>
				<!-- // Table body END -->
				
			</table>
			<!-- // Table END -->
			
		</div>
	</div>
	
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>