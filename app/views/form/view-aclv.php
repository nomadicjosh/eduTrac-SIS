<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * View ACLV View
 *  
 * @license GPLv3
 * 
 * @since       6.2.12
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$flash = new \app\src\Core\etsis_Messages();
$screen = 'aclv';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=get_base_url();?>dashbaord/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=get_base_url();?>form/aclv/" class="glyphicons road"><i></i> <?=_t( 'Academic Level' );?></a></li>
	<li class="divider"></li>
	<li><?=_h($aclv[0]['code']);?> - <?=_h($aclv[0]['name']);?> <?=_t( 'Academic Level' );?></li>
</ul>

<h3><?=_h($aclv[0]['name']);?> <?=_t( 'Academic Level' );?></h3>
<div class="innerLR">
	
	<?=$flash->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>form/aclv/<?=_h($aclv[0]['id']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
            
            <a href="#help" data-toggle="modal" class="btn btn-inverse pull-right"><i class="fa fa-question-circle"></i></a>
            
            <div class="breakline">&nbsp;</div>
		
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
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'ACLV Code' );?></label>
							<div class="col-md-8"><input class="form-control"<?=gio();?> name="code" type="text" value="<?=_h($aclv[0]['code']);?>" required /></div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'ACLV Name' );?></label>
							<div class="col-md-8"><input class="form-control"<?=gio();?> name="name" type="text" value="<?=_h($aclv[0]['name']);?>" required /></div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'HT / FT / OVR' );?></label>							
							<div class="col-md-3">
								<input type="text" name="ht_creds" id="inputmask-decimal_1" value="<?=_h($aclv[0]['ht_creds']);?>" class="form-control"<?=gio();?> required/>
							</div>
							
							<div class="col-md-2">
                                <input type="text" name="ft_creds" id="inputmask-decimal_2" value="<?=_h($aclv[0]['ft_creds']);?>" class="form-control"<?=gio();?> required/>
                            </div>
                            
                            <div class="col-md-3">
								<input type="text" name="ovr_creds" id="inputmask-decimal_3" value="<?=_h($aclv[0]['ovr_creds']);?>" class="form-control"<?=gio();?> required/>
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
                                    <option value="Yes"<?=selected('Yes', _h($aclv[0]['grad_level']), false);?>><?=_t( 'Yes' );?></option>
                                    <option value="No"<?=selected('No', _h($aclv[0]['grad_level']), false);?>><?=_t( 'No' );?></option>
                            	</select>
                            </div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Comp. Months' );?></label>
							<div class="col-md-2"><input type="text" name="comp_months" id="inputmask-int" class="form-control"<?=gio();?> value="<?=_h($aclv[0]['comp_months']);?>" required/></div>
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
                            <label class="col-md-4 control-label"><?=_t( 'STLD' );?> <a href="<?=get_base_url();?>form/aclv/<?=_h($aclv[0]['id']);?>/stld/"><img src="<?=get_base_url();?>static/common/theme/images/cascade.png" /></a></label>
                            <div class="col-md-3">
                                <input type="text" disabled value="<?=is_node_count_zero('stld','level','=',_h($aclv[0]['code']));?>" class="form-control col-md-1 center" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
					<!-- // Column END -->
                        
                    <!-- Column -->
                    <div class="col-md-3">
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-4 control-label"><?=_t( 'CLAS' );?> <a href="<?=get_base_url();?>form/aclv/<?=_h($aclv[0]['id']);?>/clas/"><img src="<?=get_base_url();?>static/common/theme/images/cascade.png" /></a></label>
                            <div class="col-md-3">
                                <input type="text" disabled value="<?=is_count_zero('clas','acadLevelCode', _h($aclv[0]['code']));?>" class="form-control col-md-1 center" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
					<!-- // Column END -->
                    
                    <!-- Column -->
                    <div class="col-md-3">
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-4 control-label"><?=_t( 'CLVR' );?> <a href="<?=get_base_url();?>form/aclv/<?=_h($aclv[0]['id']);?>/clvr/"><img src="<?=get_base_url();?>static/common/theme/images/cascade.png" /></a></label>
                            <div class="col-md-3">
                                <input type="text" disabled value="<?=is_node_count_zero('clvr','level','=',_h($aclv[0]['code']));?>" class="form-control col-md-1 center" />
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
					<button type="submit"<?=gids();?> class="btn btn-icon btn-success glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
					<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>form/aclv/'"><i></i><?=_t( 'Cancel' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
    
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
                <div class="modal-body"><?=_file_get_contents( APP_PATH.$app->hook->{'apply_filter'}('modal_info_folder', 'Info').'/aclv-view.txt' );?></div>
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