<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Manage person permissions View
 * 
 * This view is used when editing a person's permissions.
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
$screen = 'pperm';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=get_base_url();?>nae/" class="glyphicons search"><i></i> <?=_t( 'Search Person' );?></a></li>
	<li class="divider"></li>
    <li><a href="<?=get_base_url();?>nae/<?=_h($nae[0]['personID']);?>/" class="glyphicons vcard"><i></i> <?=get_name(_h($nae[0]['personID']));?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'Manage Person Permissions' );?></li>
</ul>

<h3><?=get_name(_h($nae[0]['personID']));?>: <?=_t( 'ID#' );?> <?=(_h($nae[0]['altID']) != '' ? _h($nae[0]['altID']) : _h($nae[0]['personID']));?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen,'','',$nae,$staff); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>nae/perms/<?=_h($nae[0]['personID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
			
			<div class="widget-body">
						
				<!-- Table -->
				<table class="table table-striped table-bordered table-condensed table-white">
				
					<!-- Table heading -->
					<thead>
						<tr>
							<th><?=_t( 'Permission' );?></th>
							<th class="text-center"><?=_t( 'Allow' );?></th>
						</tr>
					</thead>
					<!-- // Table heading END -->
				
					<!-- Table body -->
					<tbody>
						<?php personPerm(_h($nae[0]['personID'])); ?>
					</tbody>
					<!-- // Table body END -->
		
			</table>
			<!-- // Table END -->
			
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
					<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
					<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>nae/<?=_h($nae[0]['personID']);?>/'"><i></i><?=_t( 'Cancel' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
	
</div>	
	
		
		</div>
<?php $app->view->stop(); ?>