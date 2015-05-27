<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Saved Query List View
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
setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Saved Queries' );?></li>
</ul>

<h3><?=_t( 'Saved Query' );?></h3>
<div class="innerLR">
    
    <?=$message->flashMessage();?>
	
	<!-- Widget -->
    <div class="widget widget-heading-simple widget-body-gray">
        <?php if(function_exists('savedquery_module')) : ?>
        <!-- Tabs Heading -->
        <div class="tabsbar">
            <ul>
                <li class="glyphicons database_lock"><a href="<?=url('/');?>sql/"><i></i> <?=_t( 'SQL Interface' );?></a></li>
                <li class="glyphicons disk_save"><a href="<?=url('/');?>sql/saved-queries/add/"><i></i> <?=_t( 'Create Saved Query' );?></a></li>
                <li class="glyphicons disk_saved tab-stacked active"><a href="<?=url('/');?>sql/saved-queries/" data-toggle="tab"><i></i> <?=_t( 'Saved Queries' );?></a></li>
                <li class="glyphicons send tab-stacked"><a href="<?=url('/');?>sql/saved-queries/csv-email/"><i></i> <span><?=_t( 'CSV to Email' );?></span></a></li>
            </ul>
        </div>
        <!-- // Tabs Heading END -->
        <?php endif; ?>
        <div class="widget-body">
        
            <!-- Table -->
            <table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-primary">
            
                <!-- Table heading -->
                <thead>
                    <tr>
                        <th class="text-center"><?=_t( 'Saved Query Name' );?></th>
                        <th class="text-center"><?=_t( 'Creation Date' );?></th>
                        <th class="text-center"><?=_t( 'Actions' );?></th>
                    </tr>
                </thead>
                <!-- // Table heading END -->
                
                <!-- Table body -->
                <tbody>
                <?php if($query != '') : foreach($query as $key => $value) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_h($value['savedQueryName']);?></td>
                    <td class="text-center"><?=date('D, M d, o',strtotime(_h($value['createdDate'])));?></td>
                    <td class="text-center">
                        <a href="<?=url('/');?>sql/saved-queries/<?=_h($value['savedQueryID']);?>/<?=bm();?>" title="View Saved Query" class="btn btn-default"><i class="fa fa-eye"></i></a>
                        <a href="#myModal<?=_h($value['savedQueryID']);?>" data-toggle="modal" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
                        <!-- Modal -->
						<div class="modal fade" id="myModal<?=_h($value['savedQueryID']);?>">
							<div class="modal-dialog">
								<div class="modal-content">
									<!-- Modal heading -->
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h3 class="modal-title"><?=_h($value['savedQueryName']);?></h3>
									</div>
									<!-- // Modal heading END -->
									<!-- Modal body -->
									<div class="modal-body">
										<p><?= _t( "Are you sure you want to delete this saved query?" );?></p>
									</div>
									<!-- // Modal body END -->
									<!-- Modal footer -->
									<div class="modal-footer">
										<a href="<?=url('/');?>sql/saved-queries/delete/<?=_h($value['savedQueryID']);?>/" class="btn btn-default"><?=_t( 'Delete' );?></a>
										<a href="#" class="btn btn-primary" data-dismiss="modal"><?=_t( 'Cancel' );?></a> 
									</div>
									<!-- // Modal footer END -->
								</div>
							</div>
						</div>
						<!-- // Modal END -->
                    </td>
                </tr>
                <?php } endif; ?>
                    
                </tbody>
                <!-- // Table body END -->
                
            </table>
            <!-- // Table END -->
            
        </div>
    </div>
    <div class="separator bottom"></div>
    <!-- // Widget END -->
    
</div>  
    
        
        </div>
        <!-- // Content END -->
<?php $app->view->stop(); ?>