<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * View Institution View
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
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
    <li><a href="<?=url('/');?>appl/inst/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Institution' );?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'View Institution' );?></li>
</ul>

<h3><?=_h($inst[0]['instName']);?></h3>
<div class="innerLR">
	
	<?=$message->flashMessage();?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>appl/inst/<?=_h($inst[0]['institutionID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray">
		
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
                            <div class="col-md-8"><input class="form-control" name="fice_ceeb"<?=gio();?> type="text" value="<?=_h($inst[0]['fice_ceeb']);?>" required/></div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Type' );?></label>
                            <div class="col-md-8">
                                <select name="instType"<?=gio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <option value="HS"<?=selected('HS',_h($inst[0]['instType']),false);?>><?=_t( 'HS High School' );?></option>
                                    <option value="COL"<?=selected('COL',_h($inst[0]['instType']),false);?>><?=_t( 'COL College' );?></option>
                                    <option value="UNIV"<?=selected('UNIV',_h($inst[0]['instType']),false);?>><?=_t( 'UNIV University' );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label" for="instName"><font color="red">*</font> <?=_t( 'Institution Name' );?></label>
							<div class="col-md-8"><input class="form-control" id="instName" name="instName"<?=gio();?> type="text" value="<?=_h($inst[0]['instName']);?>" required /></div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
					    
					    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="city"><?=_t( 'City' );?></label>
                            <div class="col-md-8"><input class="form-control" id="city" name="city"<?=gio();?> type="text" value="<?=_h($inst[0]['city']);?>" /></div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="state"><?=_t( 'State' );?></label>
                            <div class="col-md-8"><input class="form-control" id="state" name="state"<?=gio();?> type="text" value="<?=_h($inst[0]['state']);?>" /></div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Country' );?></label>
                            <div class="col-md-8">
                            	<select name="country"<?=gio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" >
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('country',null,'iso2','iso2','short_name',_h($inst[0]['country'])); ?>
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
                    <input name="institutionID" type="hidden" value="<?=_h($inst[0]['institutionID']);?>" />
					<button type="submit"<?=gids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Submit' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>appl/inst/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
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