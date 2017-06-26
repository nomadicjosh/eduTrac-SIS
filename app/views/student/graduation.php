<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Graduation View
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
$screen = 'grad';
?>

<script type="text/javascript">
$(document).ready(function(){
  $("#stuID").autocomplete({
        source: '<?=get_base_url();?>stu/stuLookup/', // The source of the AJAX results
        minLength: 2, // The minimum amount of characters that must be typed before the autocomplete is triggered
        focus: function( event, ui ) { // What happens when an autocomplete result is focused on
            $("#stuID").val( ui.item.value );
            return false;
      },
      select: function ( event, ui ) { // What happens when an autocomplete result is selected
          $("#stuID").val( ui.item.value );
          $('#StudentID').val( ui.item.id );
      }
  });
});
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Graduate Student(s)' );?></li>
</ul>

<h3><?=_t( 'Graduate Student(s)' );?></h3>
<div class="innerLR">
	
	<?=_etsis_flash()->showMessage();?>
	
	<?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>stu/graduation/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
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
							<label class="col-md-3 control-label"><?=_t( 'Student ID/Name' );?></label>
							<div class="col-md-8">
								<input type="text" id="stuID" class="form-control" required />
                                <input type="hidden" id="StudentID" name="stuID" />
							</div>
						</div>
						
					</div>
					<!-- // Column END -->
                    
                    <div class="col-md-6">
                        
						<!-- // Group END -->
						<?php if(function_exists('savedquery_module')) : ?>
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Saved Query' );?></label>
                            <div class="col-md-8">
                                <select name="id" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
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