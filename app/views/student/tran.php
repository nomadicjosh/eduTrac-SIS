<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Transcript View
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
$screen = 'tran';
$flash = new \app\src\Core\etsis_Messages();
$templates_header = get_templates_header(APP_PATH . 'views/student/templates/transcript/');
?>

<script type="text/javascript">
    jQuery(document).ready(function() {
    jQuery('#stuID').live('change', function(event) {
        $.ajax({
            type    : 'POST',
            url     : '<?=get_base_url();?>sect/stuLookup/',
            dataType: 'json',
            data    : $('#validateSubmitForm').serialize(),
            cache: false,
            success: function( data ) {
                   for(var id in data) {        
                          $(id).val( data[id] );
                   }
            }
        });
    });
});
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Transcript' );?></li>
</ul>

<h3><?=_t( 'Transcript' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>stu/tran/" id="validateSubmitForm" method="post" autocomplete="off" target="_blank">
		
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
					<div class="col-md-4">
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Student ID' );?></label>
							<div class="col-md-8">
								<input type="text" name="stuID" id="stuID" class="form-control" required />
                                <input type="text" id="stuName" readonly="readonly" class="form-control text-center" />
							</div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
                    
                    <!-- Column -->
					<div class="col-md-4">
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Tran Type' );?></label>
							<div class="col-md-8">
						        <?=acad_level_select(null,null,'required');?>
							</div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
                    
                    <!-- Column -->
					<div class="col-md-4">
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Template' );?></label>
							<div class="col-md-8">
						        <select name="template" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
									<option value="">&nbsp;</option>
                            		<?php foreach($templates_header as $template) { ?>
                                    <option value="<?=$template['Slug'];?>"><?=$template['Name'];?></option>
                                    <?php } ?>
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