<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Manage Roles View
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
$roles = new \app\src\ACL();
$screen = 'role';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Manage Roles' );?></li>
</ul>

<h3><?=_t( 'Manage Roles' );?></h3>
<div class="innerLR">
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		<div class="widget-body">
		
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
			
				<!-- Table heading -->
				<thead>
					<tr>
						<th class="text-center"><?=_t( 'ID' );?></th>
						<th class="text-center"><?=_t( 'Name' );?></th>
						<th class="text-center"><?=_t( 'Edit' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
				<?php 
					$listRoles = $roles->getAllRoles('full');
					if($listRoles != '') {
						foreach ($listRoles as $k => $v) {
							echo '<tr class="gradeX">'."\n";
							echo '<td>'._h($v['ID']).'</td>'."\n";
							echo '<td>'._h($v['Name']).'</td>'."\n";
							echo '<td class="text-center"><a href="'.url('/').'role/'._h($v['ID']).'/" title="View Role" class="btn btn-default"><i class="fa fa-edit"></i></a></td>';
							echo '</tr>';
						}
					}
					
					/*if (count($listRoles) < 1) {
					_e( "No roles yet.<br />" );
					}*/
				?>
					
				</tbody>
				<!-- // Table body END -->
				
			</table>
			<!-- // Table END -->
            <hr class="separator" />
		<!-- Form actions -->
        <div class="form-actions">
            <button type="submit" name="NewRole" class="btn btn-icon btn-primary glyphicons circle_ok" onclick="window.location='<?=url('/');?>role/add/<?=bm();?>'"><i></i><?=_t( 'New Role' );?></button>
        </div>
        <!-- // Form actions END -->
		</div>
	</div>
	<div class="separator bottom"></div>
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>