<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * ACLV View
 *  
 * @license GPLv3
 * 
 * @since       6.3.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$screen = 'aclv';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Academic Level (ACLV)' );?></li>
</ul>

<h3><?=_t( 'Academic Level (ACLV)' );?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>form/aclv/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
            
            <a href="#help" data-toggle="modal" class="btn btn-inverse pull-right"><i class="fa fa-question-circle"></i></a>
            
            <div class="breakline">&nbsp;</div>
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					
					<!-- Column -->
					<div class="col-md-6">
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'ACLV Code' );?></label>
							<div class="col-md-8"><input class="form-control"<?=gio();?> name="code" type="text" required /></div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'ACLV Name' );?></label>
							<div class="col-md-8"><input class="form-control"<?=gio();?> name="name" type="text" required /></div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'HT / FT / OVR' );?></label>							
							<div class="col-md-3">
								<input type="text" name="ht_creds" id="inputmask-decimal_1" class="form-control"<?=gio();?> required/>
							</div>
							
							<div class="col-md-2">
                                <input type="text" name="ft_creds" id="inputmask-decimal_2" class="form-control"<?=gio();?> required/>
                            </div>
                            
                            <div class="col-md-3">
								<input type="text" name="ovr_creds" id="inputmask-decimal_3" class="form-control"<?=gio();?> required/>
							</div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
                    
                    <!-- Column -->
					<div class="col-md-6">
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Grad. Level' );?></label>
							<div class="col-md-8">
                                <select name="grad_level" id="term" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=gio();?> required>
									<option value="">&nbsp;</option>
                                    <option value="Yes"><?=_t( 'Yes' );?></option>
                                    <option value="No"><?=_t( 'No' );?></option>
                            	</select>
                            </div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Comp. Months' );?></label>
							<div class="col-md-2"><input type="text" name="comp_months" id="inputmask-int" class="form-control"<?=gio();?> required /></div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
					<button type="submit"<?=gids();?> class="btn btn-icon btn-success glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
	
    <div class="separator bottom"></div>
	
	<!-- Widget -->
    <div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
        <div class="widget-body">
        
            <!-- Table -->
            <table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-primary">
            
                <!-- Table heading -->
                <thead>
                    <tr>
                        <th class="text-center"><?=_t( 'Code' );?></th>
                        <th class="text-center"><?=_t( 'Name' );?></th>
                        <th class="text-center"><?=_t( 'Actions' );?></th>
                    </tr>
                </thead>
                <!-- // Table heading END -->
                
                <!-- Table body -->
                <tbody>
                <?php if($aclv != '') : foreach($aclv as $key => $value) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_h($value['code']);?></td>
                    <td class="text-center"><?=_h($value['name']);?></td>
                    <td class="text-center">
                    	<div class="btn-group dropup">
                            <button class="btn btn-default btn-xs" type="button"><?=_t( 'Actions' ); ?></button>
                            <button data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle" type="button">
                                <span class="caret"></span>
                                <span class="sr-only"><?=_t( 'Toggle Dropdown' ); ?></span>
                            </button>
                            <ul role="menu" class="dropdown-menu dropup-text pull-right">
                                <li><a href="<?=get_base_url();?>form/aclv/<?=_h($value['id']);?>/"><?=_t( 'View' ); ?></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php } endif; ?>
                    
                </tbody>
                <!-- // Table body END -->
                
            </table>
            <!-- // Table END -->
            
        </div>
    </div>
    <!-- // Widget END -->
    
    <!-- Modal -->
    <div class="modal fade" id="help">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?=_t( 'Academic Level (ACLV) Screen' );?></h3>
                </div>
                <!-- // Modal heading END -->
                <!-- Modal body -->
                <div class="modal-body"><?=_file_get_contents( APP_PATH.$app->hook->{'apply_filter'}('modal_info_folder', 'Info').'/aclv.txt' );?></div>
                <!-- // Modal body END -->
                <!-- Modal footer -->
                <div class="modal-footer">
                    <a href="#" class="btn btn-default" data-dismiss="modal"><?=_t( 'Close' );?></a> 
                </div>
                <!-- // Modal footer END -->
            </div>
        </div>
    </div>
    <!-- // Modal END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>