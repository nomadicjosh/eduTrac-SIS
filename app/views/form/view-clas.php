<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * View CLAS View
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
$screen = 'clas';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=get_base_url();?>dashbaord/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=get_base_url();?>form/clas/" class="glyphicons road"><i></i> <?=_t( 'Class Level' );?></a></li>
	<li class="divider"></li>
	<li><?=_h($clas[0]['code']);?> - <?=_h($clas[0]['name']);?> <?=_t( 'Class Level' );?></li>
</ul>

<h3><?=_t( 'Viewing' );?> <?=_h($clas[0]['name']);?></h3>
<div class="innerLR">
	
	<?=$flash->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>form/clas/<?=_h($clas[0]['id']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
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
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'CLAS Code' );?></label>
							<div class="col-md-8"><input class="form-control"<?=gio();?> name="code" type="text" value="<?=_h($clas[0]['code']);?>" required /></div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'CLAS Name' );?></label>
							<div class="col-md-8"><input class="form-control"<?=gio();?> name="name" type="text" value="<?=_h($clas[0]['name']);?>" required /></div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
                    
                    <!-- Column -->
					<div class="col-md-6">
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Acad. Level' );?></label>
							<div class="col-md-8">
                                <select name="acadLevelCode" id="term" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=gio();?> required>
									<option value="">&nbsp;</option>
                                    <?php table_dropdown("aclv", 'code <> "NULL"', "code", "code", "name", _h($class[0]['acadLevelCode'])); ?>
                            	</select>
                            </div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'CLVR' );?> <a href="<?=get_base_url();?>form/clas/<?=_h($clas[0]['id']);?>/clvr/"><img src="<?=get_base_url();?>static/common/theme/images/cascade.png" /></a></label>
                            <div class="col-md-2">
                                <input type="text" disabled value="<?=is_node_count_zero(_h($clas[0]['id']).'_clvr');?>" class="form-control col-md-1 center" />
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
					<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>form/clas/'"><i></i><?=_t( 'Cancel' );?></button>
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