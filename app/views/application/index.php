<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Application Search View
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
$screen = 'appl';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Application' );?></li>
</ul>

<h3><?=_t( 'Search Application' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>

    <?php jstree_sidebar_menu($screen); ?>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		<div class="widget-body">
		
			<div class="tab-pane" id="search-users">
				<div class="widget widget-heading-simple widget-body-white margin-none">
					<div class="widget-body">
						
						<div class="widget widget-heading-simple widget-body-simple text-right form-group">
							<form class="form-search text-center" action="<?=get_base_url();?>appl/" method="post" autocomplete="off">
							  	<input type="text" name="appl" class="form-control" placeholder="Search applications . . . " /> 
							  	<a href="#myModal" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a>
							</form>
						</div>
						
					</div>
				</div>
			</div>
			
			<div class="separator bottom"></div>
			
			<?php if(isset($_POST['appl'])) { ?>
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
			
				<!-- Table heading -->
				<thead>
					<tr>
						<th class="text-center"><?=_t( 'Image' );?></th>
						<th class="text-center"><?=_t( 'Person ID' );?></th>
						<th class="text-center"><?=_t( 'Name' );?></th>
                        <th class="text-center"><?=_t( 'Start Term' );?></th>
						<th class="text-center"><?=_t( 'Actions' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
				<?php if($search != '') : foreach($search as $k => $v) { ?>
                <tr class="gradeX">
                	<td class="text-center"><?=get_school_photo(_h($v['personID']), _h($v['email']), 48, 'avatar-frame');?></td>
                    <td class="text-center"><?=_h($v['personID']);?></td>
                    <td class="text-center"><?=get_name(_h($v['personID']));?></td>
                    <td class="text-center"><?=_h($v['termName']);?></td>
                    <td class="text-center">
                    	<div class="btn-group dropup">
                            <button class="btn btn-default btn-xs" type="button"><?=_t( 'Actions' ); ?></button>
                            <button data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle" type="button">
                                <span class="caret"></span>
                                <span class="sr-only"><?=_t( 'Toggle Dropdown' ); ?></span>
                            </button>
                            <ul role="menu" class="dropdown-menu dropup-text pull-right">
                                <li><a href="<?=get_base_url();?>appl/<?=_h($v['id']);?>/"><?=_t( 'View' ); ?></a></li>
                                <?php if($appl->stuID == NULL) { ?>
                                <li><a href="<?=get_base_url();?>stu/add/<?=_h($v['personID']);?>/"><?=_t( 'Create Student' ); ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </td>
                </tr>
				<?php } endif; ?>
				</tbody>
				<!-- // Table body END -->
				
			</table>
			<!-- // Table END -->
			
			<?php } ?>
			
		</div>
	</div>
	<div class="separator bottom"></div>
	
	<!-- Modal -->
	<div class="modal fade" id="myModal">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Application Search' );?></h3>
				</div>
				<!-- // Modal heading END -->
				<!-- Modal body -->
				<div class="modal-body">
					<?=file_get_contents( APP_PATH . 'Info/person-search.txt' );?>
				</div>
				<!-- // Modal body END -->
				<!-- Modal footer -->
				<div class="modal-footer">
					<a href="#" class="btn btn-default" data-dismiss="modal"><?=_t( 'Close' );?></a> 
				</div>
				<!-- // Modal footer END -->
			</div>
		</div>
	</div>
	<!-- // Modal END -->
	
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>