<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Send CSV Report to Email View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       4.5
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
	<li><?=_t( 'CSV to Email Report' );?></li>
</ul>

<h3><?=_t( 'CSV to Email Report' );?></h3>
<div class="innerLR">
    
    <?=$message->flashMessage();?>
	
	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>sql/saved-queries/csv-email/" id="validateSubmitForm" method="post">
		
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
                    <li class="glyphicons disk_save"><a href="<?=url('/');?>sql/saved-queries/add/"><i></i> <?=_t( 'Create Saved Query' );?></a></li>
                    <li class="glyphicons disk_saved tab-stacked"><a href="<?=url('/');?>sql/saved-queries/"><i></i> <?=_t( 'Saved Queries' );?></a></li>
                    <li class="glyphicons send tab-stacked active"><a href="<?=url('/');?>sql/saved-queries/csv-email/" data-toggle="tab"><i></i> <span><?=_t( 'CSV to Email' );?></span></a></li>
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
							<label class="col-md-3 control-label"><?=_t( 'Recipient' );?></label>
							<div class="col-md-8">
								<select name="recipient" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <?php get_staff_email(); ?>
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
                            <label class="col-md-3 control-label" for="term"><font color="red">*</font> <?=_t( 'Saved Query' );?></label>
                            <div class="col-md-8">
                                <select name="qID" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php userQuery(); ?>
                                </select>
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
                    <input type="hidden" name="email" value="<?=get_persondata('email');?>" />
					<button type="submit" name="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Send' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
	
	<div class="separator bottom"></div>
    
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>