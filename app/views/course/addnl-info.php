<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Additional course information view.
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
$message = new \app\src\Messages;
$list = '"'.implode('","', courseList(_h($crse[0]['preReq']))).'"';
$screen = 'vcrse';
?>

<script type="text/javascript">
$(function() {
    $("#select2_5").select2({tags:[<?=$list;?>]});
});
$(".success-panel").show();
setTimeout(function() { $(".success-panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/')?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>crse/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Course' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>crse/<?=_h($crse[0]['courseID']);?>/<?=bm();?>" class="glyphicons adjust_alt"><i></i> <?=_h($crse[0]['courseCode']);?></a></li>
    <li class="divider"></li>
	<li><?=_h($crse[0]['courseCode']);?></li>
</ul>

<h3><?=_t( 'Additional Course Info:' );?> <?=_h($crse[0]['courseCode']);?></h3>
<div class="innerLR">
	
	<?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen,$crse); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>crse/addnl/<?=_h($crse[0]['courseID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					<!-- Column -->
					<div class="col-md-6">
					
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Prerequisites' );?></label>
							<div class="col-md-8"><input id="select2_5" style="width:100%;" type="hidden"<?=cio();?> name="preReq" value="<?=_h($crse[0]['preReq']);?>" /></div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Allow Audit' );?></label>
							<div class="col-md-8">
								<select name="allowAudit" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=cio();?> required>
									<option value="">&nbsp;</option>
	                        		<option value="1"<?=selected(_h((int)$crse[0]['allowAudit']),'1',false);?>><?=_t( 'Yes' );?></option>
	                        		<option value="0"<?=selected(_h((int)$crse[0]['allowAudit']),'0',false);?>><?=_t( 'No' );?></option>
	                        	</select>
	                       </div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Allow Waitlist' );?></label>
                            <div class="col-md-8">
                                <select name="allowWaitlist" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=cio();?> required>
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected(_h((int)$crse[0]['allowWaitlist']),'1',false);?>><?=_t( 'Yes' );?></option>
                                    <option value="0"<?=selected(_h((int)$crse[0]['allowWaitlist']),'0',false);?>><?=_t( 'No' );?></option>
                                </select>
                           </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
					    
					    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Minimum Enrollment' );?></label>
                            <div class="col-md-8"><input class="form-control" type="text"<?=cio();?> name="minEnroll" value="<?=_h((int)$crse[0]['minEnroll']);?>" required /></div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Seating Capacity' );?></label>
                            <div class="col-md-8"><input class="form-control" type="text"<?=cio();?> name="seatCap" value="<?=_h((int)$crse[0]['seatCap']);?>" required /></div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
					<button type="submit"<?=cids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>crse/<?=_h($crse[0]['courseID']);?>/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
    
    <!-- Modal -->
    <div class="modal fade" id="crse<?=_h($crse[0]['courseID']);?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?=_h($crse[0]['courseShortTitle']);?> <?=_h($crse[0]['courseCode']);?></h3>
                </div>
                <!-- // Modal heading END -->
                <!-- Modal body -->
                <div class="modal-body">
                    <p><?=_t( "Are you sure you want to create a copy of this course?" );?></p>
                </div>
                <!-- // Modal body END -->
                <!-- Modal footer -->
                <div class="modal-footer">
                    <a href="<?=url('/');?>crse/clone/<?=_h($crse[0]['courseID']);?>/<?=bm();?>" class="btn btn-default"><?=_t( 'Yes' );?></a>
                    <a href="#" class="btn btn-primary" data-dismiss="modal"><?=_t( 'No' );?></a> 
                </div>
                <!-- // Modal footer END -->
            </div>
        </div>
    </div>
    <!-- // Modal END -->
	
</div>	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>