<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Add Institution View
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
$screen = 'ainst';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
    <li><a href="<?=get_base_url();?>appl/inst/" class="glyphicons search"><i></i> <?=_t( 'Search Institution' );?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'Add Institution' );?></li>
</ul>

<h3><?=_t( 'Add Institution' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>appl/inst/add/" id="validateSubmitForm" method="post" autocomplete="off">
		
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
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'FICE/CEEB Code' );?></label>
                            <div class="col-md-8"><input class="form-control" name="fice_ceeb" type="text" value="<?=($app->req->post['fice_ceeb'] != '' ? $app->req->post['fice_ceeb'] : '');?>" required/></div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="instType"><font color="red">*</font> <?=_t( 'Type' );?></label>
                            <div class="col-md-8">
                                <select name="instType" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" >
                                    <option value="">&nbsp;</option>
                                    <option value="HS"<?=selected('HS',($app->req->post['instType'] != '' ? $app->req->post['instType'] : ''),false);?>><?=_t( 'High School' );?></option>
                                    <option value="COL"<?=selected('COL',($app->req->post['instType'] != '' ? $app->req->post['instType'] : ''),false);?>><?=_t( 'College' );?></option>
                                    <option value="UNIV"<?=selected('UNIV',($app->req->post['instType'] != '' ? $app->req->post['instType'] : ''),false);?>><?=_t( 'University' );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label" for="instName"><font color="red">*</font> <?=_t( 'Institution Name' );?></label>
							<div class="col-md-8"><input class="form-control" id="instName" name="instName" type="text" value="<?=($app->req->post['instName'] != '' ? $app->req->post['instName'] : '');?>" required /></div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
					    
					    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="city"><?=_t( 'City' );?></label>
                            <div class="col-md-8"><input class="form-control" id="city" name="city" type="text" value="<?=($app->req->post['city'] != '' ? $app->req->post['city'] : '');?>" /></div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="state"><?=_t( 'State' );?></label>
                            <div class="col-md-8">
                                <select name="state" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" >
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('state',null,'code','code','name',($app->req->post['state'] != '' ? $app->req->post['state'] : '')); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Country' );?></label>
                            <div class="col-md-8">
                            	<select name="country" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" >
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('country',null,'iso2','iso2','short_name',($app->req->post['short_name'] != '' ? $app->req->post['short_name'] : '')); ?>
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
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>institution/'"><i></i><?=_t( 'Cancel' );?></button>
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