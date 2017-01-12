<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Register Student View
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
$flash = new \app\src\Core\etsis_Messages();
$screen = 'rgn';
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

$(window).load(function() {
	$("#terms").jCombo({url: "<?=get_base_url();?>sect/regTermLookup/" });
	$("#section").jCombo({
		url: "<?=get_base_url();?>sect/regSecLookup/", 
		input_param: "id", 
		parent: "#terms", 
		onChange: function(newvalue) {
			$("#message").text("changed to term " + newvalue)
			.fadeIn("fast",function() {
				$(this).fadeOut(3500);
			});
		}
	});
});
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Course Registration (RGN)' );?></li>
</ul>

<h3><?=_t( 'Course Registration (RGN)' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>sect/rgn/" id="validateSubmitForm" method="post" autocomplete="off">
		
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
					<div class="col-md-6">
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Term' );?></label>
                            <div class="col-md-8">
	                        	<select id="terms" class="form-control" required></select>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Course Section' );?></label>
                            <div class="col-md-8">
	                        	<select id="section" name="courseSecID" class="form-control" required></select>
                                <span id="message" style="color:red; display:hidden;"></span>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
				</div>
				<!-- // Row END -->
				
				<hr class="separator" />
                
                <!-- Row -->
				<div class="row">
                    
                    <!-- Column -->
                    <div class="col-md-3">
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-4 control-label"><?=_t( 'RRSR' );?> <a href="<?=get_base_url();?>sect/rgn/rrsr/"><img src="<?=get_base_url();?>static/common/theme/images/cascade.png" /></a></label>
                            <div class="col-md-3">
                                <input type="text" disabled value="<?=is_node_count_zero('rrsr');?>" class="form-control col-md-1 center" />
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
					<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Register' );?></button>
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