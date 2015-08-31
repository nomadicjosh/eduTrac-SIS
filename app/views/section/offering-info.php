<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Course Section Offering View
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
$message = new \app\src\Messages;
$screen = 'vsect';
?>

<script type="text/javascript">
	$(".panel").show();
	setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/')?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>sect/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Section' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>sect/<?=_h($sect[0]['courseSecID']);?>/<?=bm();?>" class="glyphicons adjust_alt"><i></i> <?=_h($sect[0]['termCode']);?>-<?=_h($sect[0]['courseSecCode']);?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'Offering Info' );?></li>
</ul>

<h3><?=_h($sect[0]['termCode']);?>-<?=_h($sect[0]['courseSecCode']);?></h3>
<div class="innerLR">
	
	<?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen,'',$sect); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>sect/soff/<?=_h($sect[0]['courseSecID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
            
            <!-- Tabs Heading -->
            <div class="tabsbar">
                <ul>
                    <li class="glyphicons adjust_alt"><a href="<?=url('/');?>sect/<?=_h($sect[0]['courseSecID']);?>/<?=bm();?>"><i></i> <?=_h($sect[0]['courseSection']);?></a></li>
                    <li class="glyphicons circle_info"><a href="<?=url('/');?>sect/addnl/<?=_h($sect[0]['courseSecID']);?>/<?=bm();?>"><i></i> <?=_t( 'Additional Info' );?></a></li>
                    <li class="glyphicons more_items tab-stacked active"><a href="<?=url('/');?>sect/soff/<?=_h($sect[0]['courseSecID']);?>/<?=bm();?>" data-toggle="tab"><i></i> <?=_t( 'Offering Info' );?></a></li>
                    <li<?=ml('financial_module');?> class="glyphicons money tab-stacked"><a href="<?=url('/');?>sect/sbill/<?=_h($sect[0]['courseSecID']);?>/<?=bm();?>"><i></i> <?=_t( 'Billing Info' );?></a></li>
                    <?php if($sect[0]['roomCode'] != '') : ?>
                    <li<?=ml('booking_module');?> class="glyphicons calendar tab-stacked"><a href="<?=url('/');?>sect/sbook/<?=_h($sect[0]['courseSecID']);?>/<?=bm();?>"><i></i> <span><?=_t( 'Booking Info' );?></span></a></li>
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
							<label class="col-md-3 control-label"><?=_t( 'Building' );?></label>
							<div class="col-md-8">
							    <select name="buildingCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=csio();?>>
							        <option value="NULL">&nbsp;</option>
                            	   <?php table_dropdown('building','buildingCode <> "NULL"','buildingCode','buildingCode','buildingName',_h($sect[0]['buildingCode'])); ?>
                            	</select>
							</div>
						</div>
						<!-- // Group END -->
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'Class Room' );?></label>
							<div class="col-md-8">
							    <select name="roomCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=csio();?>>
							        <option value="NULL">&nbsp;</option>
                            	    <?php table_dropdown('room','roomCode <> "NULL"','roomCode','roomCode','roomNumber',_h($sect[0]['roomCode'])); ?>
                            	</select>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'Meeting Days' );?></label>
							<div class="col-md-8 widget-body uniformjs">
    							<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="Su" <?php if(preg_match("/Su/", _h($sect[0]['dotw']))) { echo 'checked="checked"'; } ?> />
									<?=_t( 'Sunday' );?>
								</label>
								<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="M" <?php if(preg_match("/M/", _h($sect[0]['dotw']))) { echo 'checked="checked"'; } ?> />
									<?=_t( 'Monday' );?>
								</label>
								<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="Tu" <?php if(preg_match("/Tu/", _h($sect[0]['dotw']))) { echo 'checked="checked"'; } ?> />
									<?=_t( 'Tuesday' );?>
								</label>
								<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="W" <?php if(preg_match("/W/", _h($sect[0]['dotw']))) { echo 'checked="checked"'; } ?> />
									<?=_t( 'Wednesday' );?>
								</label>
								<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="Th" <?php if(preg_match("/Th/", _h($sect[0]['dotw']))) { echo 'checked="checked"'; } ?> />
									<?=_t( 'Thursday' );?>
								</label>
								<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="F" <?php if(preg_match("/F/", _h($sect[0]['dotw']))) { echo 'checked="checked"'; } ?> />
									<?=_t( 'Friday' );?>
								</label>
								<label class="checkbox">
									<input type="checkbox" class="checkbox" name="dotw[]" value="Sa" <?php if(preg_match("/Sa/", _h($sect[0]['dotw']))) { echo 'checked="checked"'; } ?> />
									<?=_t( 'Saturday' );?>
								</label>
							</div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
					    
					    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Start Time' );?></label>
                            <div class="col-md-4">
                                <div class="input-group bootstrap-timepicker">
            				        <input id="timepicker10" type="text"<?=csio();?> name="startTime" class="form-control" value="<?=_h($sect[0]['startTime']);?>" />
            				        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
						        </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'End Time' );?></label>
                            <div class="col-md-4">
                                <div class="input-group bootstrap-timepicker">
        					        <input id="timepicker11" type="text"<?=csio();?> name="endTime" class="form-control" value="<?=_h($sect[0]['endTime']);?>" />
        					        <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
						        </div>
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Register Online' );?></label>
                            <div class="col-md-8">
                                <select name="webReg"<?=csio();?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected('1',_h($sect[0]['webReg']),false);?>><?=_t( 'Yes' );?></option>
                                    <option value="0"<?=selected('0',_h($sect[0]['webReg']),false);?>><?=_t( 'No' );?></option>
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
					<button type="submit"<?=csids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>sect/<?=_h($sect[0]['courseSecID']);?>/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
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