<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Pay Grades View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       3.0.2
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
	<li><?=_t( 'Pay Grades' );?></li>
</ul>

<h3>
	<?=_t( 'Pay Grades' );?> 
	<a href="#grades" data-toggle="modal" title="Add Pay Grade" class="btn btn-default"><i class="fa fa-plus"></i></a>
</h3>
<div class="innerLR">
    
    <?=$message->flashMessage();?>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
		<div class="widget-body">
			
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
			
				<!-- Table heading -->
				<thead>
					<tr>
						<th class="text-center"><?=_t( 'Grade' );?></th>
						<th class="text-center"><?=_t( 'Starting Salary' );?></th>
						<th class="text-center"><?=_t( 'Ending Salary' );?></th>
						<th class="text-center"><?=_t( 'Actions' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
				<?php if($grades != '') : foreach($grades as $k => $v) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_h($v['grade']);?></td>
                    <td class="text-center">$<?=money_format("%i",_h($v['minimum_salary']));?></td>
                    <td class="text-center">$<?=money_format("%i",_h($v['maximum_salary']));?></td>
                    <td class="text-center">
                    	<a href="#editPayGrade<?=_h($v['ID']);?>" data-toggle="modal" title="Edit Pay Grade" class="btn btn-default"><i class="fa fa-edit"></i></a>
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
	
	<!-- Modal -->
	<div class="modal fade" id="grades">
		<form class="form-horizontal margin-none" action="<?=url('/');?>hr/grades/" id="validateSubmitForm" method="post" autocomplete="off">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Add Pay Grade' );?></h3>
				</div>
				<!-- // Modal heading END -->
				<!-- Modal body -->
				<div class="modal-body">
					<!-- Group -->
		            <div class="form-group">
		                <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Grade' );?></label>
		                <div class="col-md-8">
		                    <input class="form-control" type="text" name="grade" id="grade" required />
		                </div>
		            </div>
		            <!-- // Group END -->
		            
		            <!-- Group -->
		            <div class="form-group">
		                <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Starting Salary' );?></label>
		                <div class="col-md-8">
	                        <input class="form-control" class="form-control" type="text" name="minimum_salary" id="minimum_salary" required />
		                </div>
		            </div>
		            <!-- // Group END -->
		            
		            <!-- Group -->
		            <div class="form-group">
		                <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Ending Salary' );?></label>
		                <div class="col-md-8">
	                        <input class="form-control" type="text" name="maximum_salary" id="maximum_salary" required />
		                </div>
		            </div>
		            <!-- // Group END -->
				</div>
				<!-- // Modal body END -->
				<!-- Modal footer -->
				<div class="modal-footer">
					<input type="hidden" name="addDate" value="<?=date('Y-m-d');?>" />
                    <input type="hidden" name="addedBy" value="<?=get_persondata('personID');?>" />
		        	<button type="submit" class="btn btn-default"><?=_t( 'Submit' );?></button>
					<a href="#" class="btn btn-primary" data-dismiss="modal"><?=_t( 'Cancel' );?></a>
				</div>
				<!-- // Modal footer END -->
			</div>
		</div>
		</form>
	</div>
	<!-- // Modal END -->
    
    <?php if($grades != '') : foreach($grades as $k => $v) { ?>
	<!-- Modal -->
	<div class="modal fade" id="editPayGrade<?=_h($v['ID']);?>">
		<form class="form-horizontal margin-none" action="<?=url('/');?>hr/grades/" id="validateSubmitForm" method="post" autocomplete="off">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Add Pay Grade' );?></h3>
				</div>
				<!-- // Modal heading END -->
				<!-- Modal body -->
				<div class="modal-body">
					<!-- Group -->
		            <div class="form-group">
		                <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Grade' );?></label>
		                <div class="col-md-8">
		                    <input class="form-control" type="text" name="grade" id="grade" value="<?=_h($v['grade']);?>" required />
		                </div>
		            </div>
		            <!-- // Group END -->
		            
		            <!-- Group -->
		            <div class="form-group">
		                <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Starting Salary' );?></label>
		                <div class="col-md-8">
	                        <input class="form-control" class="form-control" type="text" name="minimum_salary" id="minimum_salary" value="<?=_h($v['minimum_salary']);?>" required />
		                </div>
		            </div>
		            <!-- // Group END -->
		            
		            <!-- Group -->
		            <div class="form-group">
		                <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Ending Salary' );?></label>
		                <div class="col-md-8">
	                        <input class="form-control" type="text" name="maximum_salary" id="maximum_salary" value="<?=_h($v['maximum_salary']);?>" required />
		                </div>
		            </div>
		            <!-- // Group END -->
				</div>
				<!-- // Modal body END -->
				<!-- Modal footer -->
				<div class="modal-footer">
                    <input type="hidden" name="ID" value="<?=_h($v['ID']);?>" />
        			<button type="submit" class="btn btn-default"><?=_t( 'Update' );?></button>
					<a href="#" class="btn btn-primary" data-dismiss="modal"><?=_t( 'Cancel' );?></a>
				</div>
				<!-- // Modal footer END -->
			</div>
		</div>
		</form>
	</div>
	<!-- // Modal END -->
    <?php } endif; ?>
	
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>