<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Rule Definition View
 *  
 * @license GPLv3
 * 
 * @since       6.2.12
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$screen = 'rlde';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Rule Definition (RLDE)' );?></li>
</ul>

<h3><?=_t( 'Rule Definition (RLDE)' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
			
			<div class="widget-body">
			
				<!-- Table -->
                <table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">

                    <!-- Table heading -->
                    <thead>
                        <tr>
                            <th class="text-center"><?=_t( 'Department' );?></th>
                            <th class="text-center"><?=_t( 'Code' );?></th>
                            <th class="text-center"><?=_t( 'Description' );?></th>
                            <th class="text-center"><?=_t( 'Actions' );?></th>
                        </tr>
                    </thead>
                    <!-- // Table heading END -->

                    <!-- Table body -->
                    <tbody>
                    <?php foreach($rules as $rule) { ?>
                    <?php $dept = get_department(_h($rule->dept)); ?>
                    <tr class="gradeX">
                        <td class="text-center"><?=_h($dept->deptName);?></td>
                        <td class="text-center"><?=_h($rule->code);?></td>
                        <td class="text-center"><?=_h($rule->description);?></td>
                        <td class="text-center">
                            <div class="btn-group dropup">
                                <button class="btn btn-default btn-xs" type="button"><?=_t( 'Actions' );?></button>
                                <button data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle" type="button">
                                    <span class="caret"></span>
                                    <span class="sr-only"><?=_t( 'Toggle Dropdown' );?></span>
                                </button>
                                <ul role="menu" class="dropdown-menu dropup-text pull-right">
                                    <li><a href="<?=get_base_url();?>rlde/<?=_h($rule->id);?>/"><?=_t( 'View' );?></a></li>
                                    <li><a href="#rlde_<?=_h($rule->id);?>" data-toggle="modal" title="Delete Rule"><?=_t( 'Delete' );?></a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <div class="modal fade" id="rlde_<?=_h($rule->id);?>">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <!-- Modal heading -->
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h3 class="modal-title"><?=_h($rule->code);?> - <?=_h($rule->description);?></h3>
                                </div>
                                <!-- // Modal heading END -->
                                <div class="modal-body">
                                    <?=_t( 'If this rule is connected to any other record, that record will need to be updated. Are you sure you want to delete this rule?' );?>
                                </div>
                                <div class="modal-footer">
                                    <a<?=gids();?> href="<?=get_base_url();?>rlde/<?=_h($rule->id);?>/d/" class="btn btn-default"><?=_t( 'Delete' );?></a>
                                    <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
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