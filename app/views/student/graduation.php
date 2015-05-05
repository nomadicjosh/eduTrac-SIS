<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Graduation View
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
	<li><?=_t( 'Graduate Student(s)' );?></li>
</ul>

<h3><?=_t( 'Graduate Student(s)' );?></h3>
<div class="innerLR">
	
	<?=$message->flashMessage();?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>stu/graduation/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray">
		
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
							<label class="col-md-3 control-label"><?=_t( 'Student ID' );?></label>
							<div class="col-md-8">
								<input type="text" name="studentID" id="studentID" class="form-control" />
							</div>
						</div>
						<!-- // Group END -->
						<?php if(function_exists('savedquery_module')) : ?>
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Saved Query' );?></label>
                            <div class="col-md-8">
                                <select name="queryID" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
							        <option value="">&nbsp;</option>
							        <?php userQuery(); ?>
						        </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        <?php endif; ?>
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Graduation Date' );?></label>
                            <div class="col-md-8">
                                <div class="input-group date" id="datepicker6">
                                    <input class="form-control" name="gradDate" type="text" required />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
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
					<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Submit' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
	
</div>	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>