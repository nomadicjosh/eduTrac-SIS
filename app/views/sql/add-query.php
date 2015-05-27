<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Create Save Query View
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
    <li><a href="<?=url('/');?>sql/saved-queries/<?=bm();?>" class="glyphicons database_plus"><i></i> <?=_t( 'Saved Queries' );?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'Create Save Query' );?></li>
</ul>

<h3><?=_t( 'Create Save Query' );?></h3>
<div class="innerLR">
    
    <?=$message->flashMessage();?>
	
	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>sql/saved-queries/add/" id="validateSubmitForm" method="post">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
            <?php if(function_exists('savedquery_module')) : ?>
            <!-- Tabs Heading -->
            <div class="tabsbar">
                <ul>
                    <li class="glyphicons database_lock"><a href="<?=url('/');?>sql/"><i></i> <?=_t( 'SQL Interface' );?></a></li>
                    <li class="glyphicons disk_save active"><a href="<?=url('/');?>sql/saved-queries/add/" data-toggle="tab"><i></i> <?=_t( 'Create Saved Query' );?></a></li>
                    <li class="glyphicons disk_saved tab-stacked"><a href="<?=url('/');?>sql/saved-queries/"><i></i> <?=_t( 'Saved Queries' );?></a></li>
                    <li class="glyphicons send tab-stacked"><a href="<?=url('/');?>sql/saved-queries/csv-email/"><i></i> <span><?=_t( 'CSV to Email' );?></span></a></li>
                </ul>
            </div>
            <!-- // Tabs Heading END -->
			<?php endif; ?>
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Save Query Name' );?></label>
							<div class="col-md-8">
								<input type="text" name="savedQueryName" class="form-control" required/>
							</div>
						</div>
						<!-- // Group END -->
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label" for="term"><font color="red">*</font> <?=_t( 'Query' );?></label>
							<div class="col-md-8">
								<textarea id="mustHaveId" class="form-control" rows="5" style="width:65em;" name="savedQuery" required></textarea>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="term"><?=_t( 'Auto Purge' );?></label>
                            <div class="col-md-8 uniformjs">
                                <label class="radio">
                                    <input type="radio" name="purgeQuery" class="radio" value="1" />Yes
                                </label>
                                <br />
                                <label class="radio">
                                    <input type="radio" name="purgeQuery" class="radio" value="0" checked="checked" />No &nbsp;
                                    <a href="#myModal" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a>
                                </label>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
                    <input type="hidden" name="personID" value="<?=get_persondata('personID');?>" />
                    <input type="hidden" name="createdDate" value="<?=date('Y-m-d');?>" />
					<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>sql/saved-queries/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
	
	<div class="separator bottom"></div>
	
	<div class="modal fade" id="myModal">
		<div class="modal-dialog">
			<div class="modal-content">

				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Auto Purge' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
		            <p><?=_t( 'If enabled, saved queries get purged every 30 days. If you use your saved query on a regular basis, you should set this option to no.' );?></p>
		        </div>
		        <div class="modal-footer">
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
	       	</div>
      	</div>
    </div>
    
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>