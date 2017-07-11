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
$screen = 'addr';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=get_base_url();?>nae/" class="glyphicons search"><i></i> <?=_t( 'Search Person' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=get_base_url();?>nae/<?=_h($addr[0]['personID']);?>/" class="glyphicons user"><i></i> <?=get_name(_h((int)$addr[0]['personID']));?></a></li>
    <li class="divider"></li>
    <li><a href="<?=get_base_url();?>nae/adsu/<?=_h($addr[0]['personID']);?>/" class="glyphicons vcard"><i></i> <?=_t( 'Address Summary' );?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'Edit Address' );?></li>
</ul>

<h3 class="hidden-print"><?=get_name(_h((int)$addr[0]['personID']));?></h3>
<div class="innerLR">
	
	<?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen,'','',$nae,$staff); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none grid-form" action="<?=get_base_url();?>nae/addr/<?=_h($addr[0]['id']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-white <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
            
            <button type="button" data-toggle="print" class="btn btn-inverse pull-right print hidden-print"><i class="fa fa-fw fa-print"></i></button>
            
            <div class="breakline">&nbsp;</div>
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
			
			<div class="widget-body">
                
                <fieldset>
                    <legend><?=_t( 'Personal Details' );?></legend>
                    <div data-row-span="4">
                        <div data-field-span="1" class="readonly">
                            <label><?= _t("Unique ID"); ?></label>
                            <input type="text" readonly value="<?=(_h($nae[0]['altID']) != '' ? _h($nae[0]['altID']) : _h($nae[0]['personID']));?>" />
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'First Name' );?></label>
                            <input type="text" readonly value="<?=_h($nae[0]['fname']);?>" />
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Last Name' );?></label>
                            <input type="text" readonly value="<?=_h($nae[0]['lname']);?>" />
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Middle Initial' );?></label>
                            <input type="text" readonly value="<?=_h($nae[0]['mname']);?>" />
                        </div>
                    </div>
                </fieldset>
                
                <br /><br />
                
                <fieldset>
                    <legend><?=_t( 'Mailing Address & Contact Details' );?></legend>
                    <div data-row-span="2">
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?=_t( 'Address1' );?></label>
                            <input type="text" name="address1"<?=aio();?> value="<?=_h($addr[0]['address1']);?>" required/>
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'Address2' );?></label>
                            <input type="text" name="address2"<?=aio();?> value="<?=_h($addr[0]['address2']);?>" />
                        </div>
                    </div>
                    <div data-row-span="4">
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?=_t( 'City' );?></label>
                            <input type="text" name="city"<?=aio();?> value="<?=_h($addr[0]['city']);?>" required />
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'State' );?></label>
                            <select name="state" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                <option value="">&nbsp;</option>
                                <?php table_dropdown('state',null,'code','code','name',_h($addr[0]['state'])); ?>
                            </select>
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'Zip/Postal Code' );?></label>
                            <input type="text" name="zip"<?=aio();?> value="<?=_h($addr[0]['zip']);?>" />
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'Country' );?></label>
                            <select name="country" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                <option value="">&nbsp;</option>
                                <?php table_dropdown('country',null,'iso2','iso2','short_name',_h($addr[0]['country'])); ?>
                            </select>
                        </div>
                    </div>
                </fieldset>
                
                <br /><br />
                
                <fieldset>
                    <legend><?=_t( 'Address Status' );?></legend>
                    <div data-row-span="4">
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?=_t( 'Address Type' );?></label>
                            <?=address_type_select(_h($addr[0]['addressType']));?>
                        </div>
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?=_t( 'Start Date' );?></label>
                            <div class="input-group date col-md-8" id="datepicker6">
                                <input name="startDate"<?=aio();?> type="text" value="<?=_h($addr[0]['startDate']);?>" required/>
                                <span class="input-group-addon"><i class="fa fa-th"></i></span>
                            </div>
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'End Date' );?></label>
                            <div class="input-group date col-md-8" id="datepicker7">
                                <?php if($addr[0]['endDate'] == NULL || $addr[0]['endDate'] == '0000-00-00') { ?>
                                <input name="endDate"<?=aio();?> type="text" />
                                <?php } else { ?>
                                <input name="endDate"<?=aio();?> type="text" value="<?=_h($addr[0]['endDate']);?>" />
                                <?php } ?>
                                <span class="input-group-addon"><i class="fa fa-th"></i></span>
                            </div>
                        </div>
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?=_t( 'Status' );?></label>
                            <?=address_status_select(_h($addr[0]['addressStatus']));?>
                        </div>
                    </div>
                </fieldset>
                
                <br /><br />
                
                <fieldset>
                    <legend><?=_t( 'Contact Details' );?></legend>
                    <div data-row-span="3">
                        <div data-field-span="1">
                            <label><?= _t("Phone1"); ?></label>
                            <input type="text" name="phone1"<?=aio();?> value="<?=_h($addr[0]['phone1']);?>" />
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'Extension1' );?></label>
                            <input type="text" name="ext1"<?=aio();?> value="<?=_h($addr[0]['ext1']);?>" />
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'Type1' );?></label>
                            <select name="phoneType1" class="selectpicker col-md-8" data-style="btn-info" data-size="10" data-live-search="true"<?=aio();?>>
                                <option value="">&nbsp;</option>
                                <option value="BUS"<?=selected(_h($addr[0]['phoneType1']),'BUS',false);?>><?=_t( 'Business' );?></option>
                                <option value="CEL"<?=selected(_h($addr[0]['phoneType1']),'CEL',false);?>><?=_t( 'Cellular' );?></option>
                                <option value="H"<?=selected(_h($addr[0]['phoneType1']),'H',false);?>><?=_t( 'Home' );?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div data-row-span="3">
                        <div data-field-span="1">
                            <label><?= _t("Phone2"); ?></label>
                            <input type="text" name="phone2"<?=aio();?> value="<?=_h($addr[0]['phone2']);?>" />
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'Extension2' );?></label>
                            <input type="text" name="ext2"<?=aio();?> value="<?=_h($addr[0]['ext2']);?>" />
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'Type2' );?></label>
                            <select name="phoneType2" class="selectpicker col-md-8" data-style="btn-info" data-size="10" data-live-search="true"<?=aio();?>>
                                <option value="">&nbsp;</option>
                                <option value="BUS"<?=selected(_h($addr[0]['phoneType2']),'BUS',false);?>><?=_t( 'Business' );?></option>
                                <option value="CEL"<?=selected(_h($addr[0]['phoneType2']),'CEL',false);?>><?=_t( 'Cellular' );?></option>
                                <option value="H"<?=selected(_h($addr[0]['phoneType2']),'H',false);?>><?=_t( 'Home' );?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div data-row-span="2">
                        <div data-field-span="1" class="readonly">
                            <label><?= _t("Primary Email"); ?></label>
                            <input type="email" readonly value="<?=_h($addr[0]['email1']);?>" />
                        </div>
                        <div data-field-span="1">
                            <label><?=_t( 'Secondary Email' );?></label>
                            <input type="email" name="email2"<?=aio();?> value="<?=_h($addr[0]['email2']);?>" />
                        </div>
                    </div>
                </fieldset>
                
                <br /><br />
                
                <fieldset>
                    <legend><?=_t( 'System Status' );?></legend>
                    <div data-row-span="2">
                        <div data-field-span="1" class="readonly">
                            <label><?= _t("Add Date"); ?></label>
                            <input type="text" readonly value="<?=\Jenssegers\Date\Date::parse(_h($addr[0]['addDate']))->format('D, M d, o');?>" />
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?=_t( 'Added By' );?></label>
                            <input type="text" readonly value="<?=get_name(_h((int)$addr[0]['addedBy']));?>" />
                        </div>
                    </div>
                </fieldset>
				
				<hr class="separator hidden-print" />
				
				<!-- Form actions -->
				<div class="form-actions hidden-print">
					<button type="submit"<?=aids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>nae/adsu/<?=_h($addr[0]['personID']);?>/'"><i></i><?=_t( 'Cancel' );?></button>
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