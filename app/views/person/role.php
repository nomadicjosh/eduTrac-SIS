<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Manage Person Role View
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
$message = new \app\src\Messages;
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>nae/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Person' );?></a></li>
	<li class="divider"></li>
    <li><a href="<?=url('/');?>nae/<?=_h($role[0]['personID']);?>/<?=bm();?>" class="glyphicons vcard"><i></i> <?=get_name(_h($role[0]['personID']));?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'Manage Person Role' );?></li>
</ul>

<h3><?=get_name(_h($role[0]['personID']));?>: <?=_t( 'ID#' );?> <?=_h($role[0]['personID']);?></h3>
<div class="innerLR">
    
    <?=$message->flashMessage();?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>nae/role/<?=_h($role[0]['personID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray">
			
			<div class="widget-body">
						
				<!-- Table -->
				<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
				
					<!-- Table heading -->
					<thead>
						<tr>
							<th><?=_t( 'Role' );?></th>
							<th><?=_t( 'Member' );?></th>
							<th><?=_t( 'Not Member' );?></th>
						</tr>
					</thead>
					<!-- // Table heading END -->
				
					<!-- Table body -->
					<tbody>
						<?php 
						$roleACL = new \app\src\ACL(_h((int)$role[0]['personID']));
							$role = $roleACL->getAllRoles('full');
							foreach ($role as $k => $v) {
								echo '<tr><td>'._h($v['Name']).'</td>';
								
								echo "<td class=\"center\"><input type=\"radio\" name=\"role_" . _h($v['ID']) . "\" id=\"role_" . _h($v['ID']) . "_1\" value=\"1\"";
    							if ($roleACL->userHasRole(_h($v['ID']))) { echo " checked=\"checked\""; }
    							echo " /></td>";
								 
								echo "<td class=\"center\"><input type=\"radio\" name=\"role_" . _h($v['ID']) . "\" id=\"role_" . _h($v['ID']) . "_0\" value=\"0\"";
    							if (!$roleACL->userHasRole(_h($v['ID']))) { echo " checked=\"checked\""; }
    							echo " /></td></tr>";
							}
						?>
					</tbody>
					<!-- // Table body END -->
		
			</table>
			<!-- // Table END -->
			
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
					<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>