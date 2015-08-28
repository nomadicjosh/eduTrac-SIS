<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Edit address View
 * 
 * This view is used when editing a person's address record via
 * the ADDR screen.
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
$screen = 'addr';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>nae/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Person' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=url('/');?>nae/<?=_h($addr[0]['personID']);?>/<?=bm();?>" class="glyphicons user"><i></i> <?=get_name(_h((int)$addr[0]['personID']));?></a></li>
    <li class="divider"></li>
    <li><a href="<?=url('/');?>nae/adsu/<?=_h($addr[0]['personID']);?>/<?=bm();?>" class="glyphicons vcard"><i></i> <?=_t( 'Address Summary' );?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'Edit Address' );?></li>
</ul>

<h3><?=get_name(_h((int)$addr[0]['personID']));?></h3>
<div class="innerLR">
	
	<?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen,'','',$nae,$staff); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>nae/addr/<?=_h($addr[0]['addressID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
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
							<label class="col-md-3 control-label"><?=_t( 'Person ID' );?></label>
							<div class="col-md-8">
								<input type="text" readonly class="form-control" value="<?=_h($nae[0]['personID']);?>" />
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'First Name' );?></label>
							<div class="col-md-8">
								<input type="text" readonly class="form-control" value="<?=_h($nae[0]['fname']);?>" />
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Last Name' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="<?=_h($nae[0]['lname']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Middle Initial' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="<?=_h($nae[0]['mname']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Address1' );?></label>
							<div class="col-md-8">
								<input type="text" name="address1"<?=aio();?> class="form-control" value="<?=_h($addr[0]['address1']);?>" />
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Address2' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="address2"<?=aio();?> class="form-control" value="<?=_h($addr[0]['address2']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'City' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="city"<?=aio();?> class="form-control" value="<?=_h($addr[0]['city']);?>" required />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'State' );?></label>
                            <div class="col-md-8">
                                <select name="state" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('state',null,'code','code','name',_h($addr[0]['state'])); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Zip Code' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="zip"<?=aio();?> class="form-control" value="<?=_h($addr[0]['zip']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Country' );?></label>
                            <div class="col-md-8">
                                <select name="country" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('country',null,'iso2','iso2','short_name',_h($addr[0]['country'])); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Address Type' );?></label>
                            <div class="col-md-8">
                                <?=address_type_select(_h($addr[0]['addressType']));?>
                            </div>
                        </div>
                        <!-- // Group END -->
					
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Start Date' );?></label>
                            <div class="col-md-8">
                                <div class="input-group date col-md-8" id="datepicker6">
                                    <input class="form-control" name="startDate"<?=aio();?> type="text" value="<?=_h($addr[0]['startDate']);?>" required/>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'End Date' );?></label>
                            <div class="col-md-8">
                                <div class="input-group date col-md-8" id="datepicker7">
                                    <?php if($addr[0]['endDate'] == NULL || $addr[0]['endDate'] == '0000-00-00') { ?>
                                    <input class="form-control" name="endDate"<?=aio();?> type="text" />
                                    <?php } else { ?>
                                    <input class="form-control" name="endDate"<?=aio();?> type="text" value="<?=_h($addr[0]['endDate']);?>" />
                                    <?php } ?>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Status' );?></label>
                            <div class="col-md-8">
                               <?=address_status_select(_h($addr[0]['addressStatus']));?>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Add Date' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="<?=date('D, M d, o',strtotime(_h($addr[0]['addDate'])));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Added By' );?></label>
                            <div class="col-md-8">
                                <input type="text" readonly class="form-control" value="<?=get_name(_h((int)$addr[0]['addedBy']));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
            
            <div class="widget-body">		
				<!-- Row -->
                <div class="row">
                    <!-- Column -->
                    <div class="col-md-4">
                        
        				<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Phone' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="phone1"<?=aio();?> class="form-control" value="<?=_h($addr[0]['phone1']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                    
                    <!-- Column -->
                    <div class="col-md-4">
                        
                        <!-- Group -->
                        <div class="form-group">
                            
                            <label class="col-md-3 control-label"><?=_t( 'Extension' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="ext1"<?=aio();?> class="form-control" value="<?=_h($addr[0]['ext1']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                    
                    <!-- Column -->
                    <div class="col-md-4">
                        
                        <!-- Group -->
                        <div class="form-group">
                            
                            <label class="col-md-3 control-label"><?=_t( 'Type' );?></label>
                            <div class="col-md-8">
                                <select name="phoneType1" class="selectpicker col-md-8" data-style="btn-info" data-size="10" data-live-search="true"<?=aio();?>>
                                    <option value="">&nbsp;</option>
                                    <option value="BUS"<?=selected(_h($addr[0]['phoneType1']),'BUS',false);?>><?=_t( 'Business' );?></option>
                                    <option value="CEL"<?=selected(_h($addr[0]['phoneType1']),'CEL',false);?>><?=_t( 'Cellular' );?></option>
                                    <option value="H"<?=selected(_h($addr[0]['phoneType1']),'H',false);?>><?=_t( 'Home' );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                </div>
                <!-- Row End -->
                
                <!-- Row -->
                <div class="row">
                    <!-- Column -->
                    <div class="col-md-4">
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Phone' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="phone2"<?=aio();?> class="form-control" value="<?=_h($addr[0]['phone2']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                    
                    <!-- Column -->
                    <div class="col-md-4">
                        
                        <!-- Group -->
                        <div class="form-group">
                            
                            <label class="col-md-3 control-label"><?=_t( 'Extension' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="ext2"<?=aio();?> class="form-control" value="<?=_h($addr[0]['ext2']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                    
                    <!-- Column -->
                    <div class="col-md-4">
                        
                        <!-- Group -->
                        <div class="form-group">
                            
                            <label class="col-md-3 control-label"><?=_t( 'Type' );?></label>
                            <div class="col-md-8">
                                <select name="phoneType2" class="selectpicker col-md-8" data-style="btn-info" data-size="10" data-live-search="true"<?=aio();?>>
                                    <option value="">&nbsp;</option>
                                    <option value="BUS"<?=selected(_h($addr[0]['phoneType2']),'BUS',false);?>><?=_t( 'Business' );?></option>
                                    <option value="CEL"<?=selected(_h($addr[0]['phoneType2']),'CEL',false);?>><?=_t( 'Cellular' );?></option>
                                    <option value="H"<?=selected(_h($addr[0]['phoneType2']),'H',false);?>><?=_t( 'Home' );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                </div>
                <!-- Row End -->
            </div>
            
                <hr class="separator" />
                
            <div class="widget-body">       
                <!-- Row -->
                <div class="row">
                    <!-- Column -->
                    <div class="col-md-12">
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Primary Email' );?></label>
                            <div class="col-md-8">
                                <input type="email" readonly class="form-control" value="<?=_h($addr[0]['email1']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                </div>
                <!-- Row End -->
                
                <!-- Row -->
                <div class="row-fluid">
                    <!-- Column -->
                    <div class="col-md-12">
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Secondary Email' );?></label>
                            <div class="col-md-8">
                                <input type="email" name="email2"<?=aio();?> class="form-control" value="<?=_h($addr[0]['email2']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                </div>
                <!-- Row End -->
            </div>
				
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
					<button type="submit"<?=aids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>nae/adsu/<?=_h($addr[0]['personID']);?>/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
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