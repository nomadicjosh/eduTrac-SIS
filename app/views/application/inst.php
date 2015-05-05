<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Institution Search View
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
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Institution' );?></li>
</ul>

<h3><?=_t( 'Institution' );?></h3>
<div class="innerLR">
    
    <?=$message->flashMessage();?>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
		<div class="widget-body">
		
			<div class="tab-pane" id="search-users">
				<div class="widget widget-heading-simple widget-body-white margin-none">
					<div class="widget-body">
						
						<div class="widget widget-heading-simple widget-body-simple text-right form-group">
							<form class="form-search text-center" action="<?=url('/');?>appl/inst/<?=bm();?>" method="post" autocomplete="off">
							  	<input type="text" name="inst" class="form-control" placeholder="Search institution . . . " /> 
							</form>
						</div>
						
					</div>
				</div>
			</div>
			
			<div class="separator bottom"></div>
			
			<?php if(isset($_POST['inst'])) { ?>
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
			
				<!-- Table heading -->
				<thead>
					<tr>
						<th class="text-center"><?=_t( 'FICE/CEEB Code' );?></th>
						<th class="text-center"><?=_t( 'Institution Type' );?></th>
						<th class="text-center"><?=_t( 'Institution Name' );?></th>
						<th class="text-center"><?=_t( 'Actions' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
				<?php if($search != '') : foreach($search as $k => $v) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_h($v['fice_ceeb']);?></td>
                    <td class="text-center"><?=_h($v['instType']);?></td>
                    <td class="text-center"><?=_h($v['instName']);?></td>
                    <td class="text-center">
                    	<div class="btn-group dropup">
                            <button class="btn btn-default btn-xs" type="button"><?=_t( 'Actions' ); ?></button>
                            <button data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle" type="button">
                                <span class="caret"></span>
                                <span class="sr-only"><?=_t( 'Toggle Dropdown' ); ?></span>
                            </button>
                            <ul role="menu" class="dropdown-menu dropup-text pull-right">
                                <li><a href="<?=url('/');?>appl/inst/<?=_h($v['institutionID']);?>/<?=bm();?>"><?=_t( 'View' ); ?></a></li>
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