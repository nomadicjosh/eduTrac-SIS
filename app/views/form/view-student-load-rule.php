<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Edit Student Load Rule View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       4.0.7
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$message = new \app\src\Messages;
$screen = 'slr';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>form/student-load-rule/<?=bm();?>" class="glyphicons cargo"><i></i> <?=_t( 'Student Load Rules' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Edit Student Load Rule' );?></li>
</ul>

<h3><?=_t( 'Edit Student Load Rule' );?></h3>
<div class="innerLR">
	
	<?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>form/student-load-rule/<?=_h($slr[0]['slrID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					
					<!-- Column -->
					<div class="col-md-6">
					
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Status' );?></label>
                            <div class="col-md-8">
                                <select name="status" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="F"<?=selected('F',_h($slr[0]['status']),false);?>><?=_t( 'Full Time' );?></option>
                                    <option value="Q"<?=selected('Q',_h($slr[0]['status']),false);?>><?=_t( '3/4 Time' );?></option>
                                    <option value="H"<?=selected('H',_h($slr[0]['status']),false);?>><?=_t( 'Half Time' );?></option>
                                    <option value="L"<?=selected('L',_h($slr[0]['status']),false);?>><?=_t( 'Less Than Half Time' );?></option>
                                </select>
                            </div>
                        </div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Minimum Credits' );?></label>
							<div class="col-md-8"><input class="form-control" name="min_cred" type="text" value="<?=_h($slr[0]['min_cred']);?>" required /></div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Maximum Credits' );?></label>
							<div class="col-md-8"><input class="form-control" name="max_cred" type="text" value="<?=_h($slr[0]['max_cred']);?>" required /></div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<div class="col-md-6">
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Term(s)' );?> <a href="#modal1" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
							<div class="col-md-8"><input class="form-control" name="term" type="text" value="<?=_h($slr[0]['term']);?>" required /></div>
						</div>
						<!-- // Group END -->
						
                        <!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Academic Level(s)' );?> <a href="#modal2" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
							<div class="col-md-8"><input type="text" name="acadLevelCode" class="form-control" value="<?=_h($slr[0]['acadLevelCode']);?>" required/></div>
						</div>
						<!-- // Group END -->
						
                        <!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Active' );?></label>
                            <div class="col-md-8">
                                <select name="active" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected('1',_h($slr[0]['active']),false);?>><?=_t( 'Yes' );?></option>
                                    <option value="0"<?=selected('0',_h($slr[0]['active']),false);?>><?=_t( 'No' );?></option>
                                </select>
                            </div>
                        </div>
						<!-- // Group END -->
						
                    </div>
					
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
					<button type="submit"<?=gids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
					<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>form/student-load-rule/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
    
    <div id="modal1" class="modal fade">
    	<div class="modal-dialog">
			<div class="modal-content">
		        <div class="modal-header">
		            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		            <h4 class="modal-title"><?=_t( 'Term(s)' );?></h4>
		        </div>
		        <div class="modal-body">
		            <p><?=_t( 'In this field, you will only enter your term designation without the two digit year separated by a backslash "\" (i.e. FA\FAM1\SP).' );?></p>
		        </div>
	       	</div>
       	</div>
    </div>
    
    <div id="modal2" class="modal fade">
    	<div class="modal-dialog">
			<div class="modal-content">
		        <div class="modal-header">
		            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		            <h4 class="modal-title"><?=_t( 'Academic Level(s)' );?></h4>
		        </div>
		        <div class="modal-body">
		            <p><?=_t( 'Enter the academic level or levels that should be applied to this rule separated by a backslash "\" (i.e. CE\UG\GR)' );?></p>
		        </div>
	       	</div>
        </div>
    </div>
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>