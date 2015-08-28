<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Search Academic Program View
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
$screen = 'prog';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Programs' );?></li>
</ul>

<h3><?=_t( 'Search Program' );?></h3>
<div class="innerLR">
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		<div class="widget-body">
		
			<div class="tab-pane" id="search-users">
				<div class="widget widget-heading-simple widget-body-white margin-none">
					<div class="widget-body">
						
						<div class="widget widget-heading-simple widget-body-simple text-right form-group">
							<form class="form-search text-center" action="<?=url('/');?>program/<?=bm();?>" method="post" autocomplete="off">
							  	<input type="text" name="prog" class="form-control" placeholder="Search by department or program code . . . " /> 
							</form>
						</div>
						
					</div>
				</div>
			</div>
			
			<div class="separator bottom"></div>
			
			<?php if(isset($_POST['prog'])) { ?>
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
			
				<!-- Table heading -->
				<thead>
					<tr>
						<th class="text-center"><?=_t( 'Program Code' );?></th>
						<th class="text-center"><?=_t( 'Program Title' );?></th>
						<th class="text-center"><?=_t( 'Status' );?></th>
						<th class="text-center"><?=_t( 'Effective Date' );?></th>
						<th class="text-center"><?=_t( 'End Date' );?></th>
						<th class="text-center"><?=_t( 'Actions' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
				<?php if($prog != '') : foreach($prog as $k => $v) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_h($v['acadProgCode']);?></td>
                    <td class="text-center"><?=_h($v['acadProgTitle']);?></td>
                    <td class="text-center"><?=_h($v['Status']);?></td>
                    <td class="text-center"><?=_h($v['startDate']);?></td>
                    <td class="text-center"><?=(_h($v[0]['endDate']) > '0000-00-00' ? _h($v[0]['endDate']) : '');?></td>
                    <td class="text-center">
                    	<div class="btn-group dropup">
                            <button class="btn btn-default btn-xs" type="button"><?=_t( 'Actions' ); ?></button>
                            <button data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle" type="button">
                                <span class="caret"></span>
                                <span class="sr-only"><?=_t( 'Toggle Dropdown' ); ?></span>
                            </button>
                            <ul role="menu" class="dropdown-menu dropup-text pull-right">
                                <li><a href="<?=url('/');?>program/<?=_h($v['acadProgID']);?>/<?=bm();?>"><?=_t( 'View' ); ?></a></li>
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
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>