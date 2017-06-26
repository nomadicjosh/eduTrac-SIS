<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Add Application View
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
$screen = 'appl';
?>

<ul class="breadcrumb">
    <li><?=_t( 'You are here' );?></li>
    <li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=get_base_url();?>appl/" class="glyphicons search"><i></i> <?=_t( 'Search Application' );?></a></li>
    <li class="divider"></li>
    <li><?=_t( 'Create Application' );?></li>
</ul>

<h3><?=_t( 'Create Application' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>

	<?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>appl/add/<?=_h($person->personID);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
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
                            <label class="col-md-3 control-label"><?=_t( 'Person ID' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" readonly type="text" value="<?=_h($person->personID);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'First/Mid/Last Name' );?></label>
                            <div class="col-md-3">
                            	<input class="form-control" readonly type="text" value="<?=_h($person->fname);?>" />
                        	</div>
                        	<div class="col-md-2">
                            	<input class="form-control" readonly type="text" value="<?=_h($person->mname);?>" />
                        	</div>
                        	<div class="col-md-3">
                            	<input class="form-control" readonly type="text" value="<?=_h($person->lname);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Permanent Address' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" readonly type="text" value="<?=_h($address->address1);?> <?=_h($address->address2);?>" />
                            	<input class="form-control" readonly type="text" value="<?=_h($address->city);?> <?=_h($address->state);?> <?=_h($address->zip);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'DOB' );?></label>
                            <div class="col-md-8">
                            	<?php if(_h($person->dob) > '0000-00-00') : ?>
                                <input class="form-control" readonly type="text" value="<?=\Jenssegers\Date\Date::parse(_h($person->dob))->format('D, M d, o');?>" />
                            	<?php else : ?>
                            	<input class="form-control" readonly type="text" />
                        		<?php endif; ?>
                        	</div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Age' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" readonly type="text" value="<?=get_age(_h($person->dob));?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Gender' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" readonly type="text" value="<?php if(_h($person->gender) == 'M') : echo 'Male'; elseif(_h($person->gender) == 'F') : echo 'Female'; endif; ?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Phone Number' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" readonly type="text" value="<?=_h($address->phone1);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Email Address' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" readonly type="text" value="<?=_h($person->email);?>" />
                        	</div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
				</div>
				<!-- // Row END -->
			
				<div class="separator bottom"></div>
				
				<!-- Row -->
				<div class="row">
					
					<!-- Column -->
					<div class="col-md-6">
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Application Date' );?></label>
                            <div class="col-md-8">
                            	<div class="input-group date" id="datepicker6">
                                    <input class="form-control" name="applDate" type="text" value="<?=($app->req->post['applDate'] != '' ? $app->req->post['applDate'] : '');?>" />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                        	</div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Academic Program' );?></label>
                            <div class="col-md-8">
                                <select name="acadProgCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('acad_program','currStatus = "A"','acadProgCode','acadProgCode','acadProgTitle',($app->req->post['acadProgCode'] != '' ? $app->req->post['acadProgCode'] : '')); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Start Term' );?></label>
                            <div class="col-md-8">
                                <select name="startTerm" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required/>
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('term','termCode <> "NULL"','termCode','termCode','termName',($app->req->post['startTerm'] != '' ? $app->req->post['startTerm'] : '')); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Admit Status' );?></label>
                            <div class="col-md-8">
                                <?=admit_status_select();?>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <?php 
                            /**
                             * Application Form Field (Left)
                             * 
                             * Action will print a form field or any type of data
                             * on the left side of the appl screen via the
                             * dashboard when triggered.
                             * 
                             * @since 6.1.10
                             */
                            $app->hook->do_action('left_appl_new_dash_form'); 
                        ?>
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
                        
                        <!-- Group -->
                        <div class="form-group<?=etsis_field_css_class('appl_psat_verbal');?>">
                            <label class="col-md-3 control-label"><?=_t( 'PSAT Verbal/Math' );?></label>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="PSAT_Verbal" value="<?=($app->req->post['PSAT_Verbal'] != '' ? $app->req->post['PSAT_Verbal'] : '');?>" />
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="PSAT_Math" value="<?=($app->req->post['PSAT_Math'] != '' ? $app->req->post['PSAT_Math'] : '');?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group<?=etsis_field_css_class('appl_sat_verbal');?>">
                            <label class="col-md-3 control-label"><?=_t( 'SAT Verbal/Math' );?></label>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="SAT_Verbal" value="<?=($app->req->post['SAT_Verbal'] != '' ? $app->req->post['SAT_Verbal'] : '');?>" />
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="SAT_Math" value="<?=($app->req->post['SAT_Math'] != '' ? $app->req->post['SAT_Math'] : '');?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group<?=etsis_field_css_class('appl_act_english');?>">
                            <label class="col-md-3 control-label"><?=_t( 'ACT English/Math' );?></label>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="ACT_English" value="<?=($app->req->post['ACT_English'] != '' ? $app->req->post['ACT_English'] : '');?>" />
                            </div>
                            <div class="col-md-4">
                                <input class="form-control" type="text" name="ACT_Math" value="<?=($app->req->post['ACT_Math'] != '' ? $app->req->post['ACT_Math'] : '');?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <?php 
                            /**
                             * Application Form Field (Right)
                             * 
                             * Action will print a form field or any type of data
                             * on the right side of the appl screen via the
                             * dashboard when triggered.
                             * 
                             * @since 6.1.10
                             */
                            $app->hook->do_action('right_appl_new_dash_form'); 
                        ?>
						
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