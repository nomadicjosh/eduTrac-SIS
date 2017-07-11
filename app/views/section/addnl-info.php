<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Additional Course Section View
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
$screen = 'vsect';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url()?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=get_base_url();?>sect/" class="glyphicons search"><i></i> <?=_t( 'Search Section' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=get_base_url();?>sect/<?=_h($sect->courseSecID);?>/" class="glyphicons adjust_alt"><i></i> <?=_h($sect->termCode);?>-<?=_h($sect->courseSecCode);?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'View Section' );?></li>
</ul>

<h3><?=_h($sect->termCode);?>-<?=_h($sect->courseSecCode);?></h3>
<div class="innerLR">
	
	<?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen, '', $sect); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>sect/addnl/<?=_h($sect->courseSecID);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
            
            <!-- Tabs Heading -->
            <div class="tabsbar">
                <ul>
                    <li class="glyphicons adjust_alt"><a href="<?=get_base_url();?>sect/<?=_h($sect->courseSecID);?>/"><i></i> <?=_h($sect->courseSection);?></a></li>
                    <li class="glyphicons circle_info active"><a href="<?=get_base_url();?>sect/addnl/<?=_h($sect->courseSecID);?>/" data-toggle="tab"><i></i> <?=_t( 'Additional Info' );?></a></li>
                    <li class="glyphicons more_items tab-stacked"><a href="<?=get_base_url();?>sect/soff/<?=_h($sect->courseSecID);?>/"><i></i> <?=_t( 'Offering Info' );?></a></li>
                    <li<?=ml('financial_module');?> class="glyphicons money tab-stacked"><a href="<?=get_base_url();?>sect/sbill/<?=_h($sect->courseSecID);?>/"><i></i> <?=_t( 'Billing Info' );?></a></li>
                    <?php if($sect->roomCode != '') : ?>
                    <li<?=ml('booking_module');?> class="glyphicons calendar tab-stacked"><a href="<?=get_base_url();?>sect/sbook/<?=_h($sect->courseSecID);?>/"><i></i> <span><?=_t( 'Booking Info' );?></span></a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- // Tabs Heading END -->
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					<!-- Column -->
					<div class="col-md-6">
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Instructor' );?></label>
							<div class="col-md-8">
							    <select name="facID" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=csio();?> required>
							        <option value="">&nbsp;</option>
                            	   <?php facID_dropdown(_h($sect->facID)); ?>
                            	</select>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Section Type' );?></label>
							<div class="col-md-8">
								<select name="secType" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=csio();?> required>
									<option value="">&nbsp;</option>
	                        		<option value="ONL"<?=selected(_h($sect->secType),'ONL',false);?>><?=_t( 'ONL Online' );?></option>
	                        		<option value="HB"<?=selected(_h($sect->secType),'HB',false);?>><?=_t( 'HB Hybrid' );?></option>
	                        		<option value="ONC"<?=selected(_h($sect->secType),'ONC',false);?>><?=_t( 'ONC On-Campus' );?></option>
	                        	</select>
	                       </div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Instructor Method' );?></label>
                            <div class="col-md-8">
                                <?=instructor_method(_h($sect->instructorMethod));?>
                           </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
					    
					    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Contact Hours' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="contactHours"<?=csio();?> value="<?=_h($sect->contactHours);?>" class="form-control" required />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Instructor Load' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="instructorLoad"<?=csio();?> value="<?=_h($sect->instructorLoad);?>" class="form-control" required />
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
					<button type="submit"<?=csids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>sect/<?=_h($sect->courseSecID);?>/'"><i></i><?=_t( 'Cancel' );?></button>
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