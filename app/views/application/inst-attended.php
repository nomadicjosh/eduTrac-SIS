<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Add Institution Attended View
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
$screen = 'insta';
?>

<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#applicantID').live('change', function(event) {
        $.ajax({
            type    : 'POST',
            url     : '<?=get_base_url();?>appl/applicantLookup/',
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
</script>

<ul class="breadcrumb">
    <li><?=_t( 'You are here' );?></li>
    <li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=get_base_url();?>appl/" class="glyphicons search"><i></i> <?=_t( 'Search Application' );?></a></li>
    <li class="divider"></li>
    <li><?=_t( 'Institution Attended' );?></li>
</ul>

<h3><?=_t( 'Institution Attended' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>appl/inst-attended/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required.' );?></h4>
			</div>
			<!-- // Widget heading END -->
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Applicant ID' );?></label>
                            <div class="col-md-8">
                            	<input type="text" name="personID" id="applicantID" class="form-control" required/>
                                <input type="text" id="person" readonly="readonly" class="form-control text-center" />
                        	</div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Institution' );?></label>
                            <div class="col-md-8">
                            	<select name="fice_ceeb" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('institution',null,'fice_ceeb','fice_ceeb','instName',($app->req->post['fice_ceeb'] != '' ? $app->req->post['fice_ceeb'] : '')); ?>
                                </select>
                        	</div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Attend From Date' );?></label>
                            <div class="col-md-8">
                            	<div class="input-group date" id="datepicker6">
                                    <input class="form-control" name="fromDate" type="text" value="<?=($app->req->post['fromDate'] != '' ? $app->req->post['fromDate'] : '');?>" />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Attend To Date' );?></label>
                            <div class="col-md-8">
                            	<div class="input-group date" id="datepicker7">
                                    <input class="form-control" name="toDate" type="text" value="<?=($app->req->post['toDate'] != '' ? $app->req->post['toDate'] : '');?>" />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                        	</div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Major' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" type="text" name="major" value="<?=($app->req->post['major'] != '' ? $app->req->post['major'] : '');?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'GPA' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" type="text" name="GPA" value="<?=($app->req->post['GPA'] != '' ? $app->req->post['GPA'] : '');?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Degree' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" type="text" name="degree_awarded" value="<?=($app->req->post['degree_awarded'] != '' ? $app->req->post['degree_awarded'] : '');?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Conferral Date' );?></label>
                            <div class="col-md-8">
                            	<div class="input-group date" id="datepicker8">
                                    <input class="form-control" name="degree_conferred_date" type="text" value="<?=($app->req->post['degree_conferred_date'] != '' ? $app->req->post['degree_conferred_date'] : '');?>" />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                        	</div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
				</div>
				<!-- // Row END -->
				
				<!-- Form actions -->
				<div class="form-actions">
					<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Submit' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>appl/'"><i></i><?=_t( 'Cancel' );?></button>
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