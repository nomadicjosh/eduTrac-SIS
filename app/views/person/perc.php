<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Studen Restriction View
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
$screen = 'perc';
?>

<script type="text/javascript">

function addMsg(text,element_id) {

document.getElementById(element_id).value += text;

}
</script>

<ul class="breadcrumb">
    <li><?=_t( 'You are here' );?></li>
    <li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=get_base_url();?>nae/" class="glyphicons search"><i></i> <?=_t( 'Person Lookup' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=get_base_url();?>stu/<?=_escape($nae[0]['personID']);?>/" class="glyphicons user"><i></i> <?=get_name(_escape($nae[0]['personID']));?></a></li>
    <li class="divider"></li>
    <li><?=_t( 'Person Restriction (PERC)' );?></li>
</ul>

<h3><?=get_name(_escape($nae[0]['personID']));?>: <?=_t( 'ID#' );?> <?=(_escape($nae[0]['altID']) != '' ? _escape($nae[0]['altID']) : _escape($nae[0]['personID']));?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen,'','',$nae,$staff); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>nae/perc/<?=_escape($nae[0]['personID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
        
        <!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
			
			<div class="widget-body">
		
                <!-- Table -->
                <table class="table table-striped table-responsive swipe-horizontal table-primary">

                    <!-- Table heading -->
                    <thead>
                        <tr>
                            <th class="text-center"><?=_t( 'Restriction' );?></th>
                            <th class="text-center"><?=_t( 'Severity' );?></th>
                            <th class="text-center"><?=_t( 'Start Date' );?></th>
                            <th class="text-center"><?=_t( 'End Date' );?></th>
                            <th class="text-center"><?=_t( 'Department' );?></th>
                            <th class="text-center"><?=_t( 'Comments' );?></th>
                        </tr>
                    </thead>
                    <!-- // Table heading END -->

                    <!-- Table body -->
                    <tbody>
                        <?php if($perc != '') : foreach($perc as $k => $v) { ?>
                        <!-- Table row -->
                        <tr class="gradeA">
                            <td style="width:300px;">
                                <select name="code[]" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?=table_dropdown('rest', null, 'code', 'code', 'description',_escape($v['code']));?>
                                </select>
                            </td>
                            <td style="width:80px;"><input type="text" name="severity[]" class="form-control text-center" value="<?=_escape($v['severity']);?>" parsley-type="digits" parsley-maxlength="2" /></td>
                            <td style="width:160px;">
                                <div class="input-group date" id="datepicker6<?=_escape($v['id']);?>">
                                    <input type="text" name="startDate[]" class="form-control" value="<?=_escape($v['startDate']);?>" required/>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </td>
                            <td style="width:160px;">
                                <div class="input-group date" id="datepicker7<?=_escape($v['id']);?>">
                                    <input type="text" name="endDate[]" class="form-control" value="<?=(_escape($v['endDate']) != '' ? _escape($v['endDate']) : NULL);?>" />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </td>
                            <td><input type="text" readonly class="form-control text-center" value="<?=_escape($v['deptCode']);?>" /></td>
                            <td class="text-center">
                                <button type="button" title="Comment" class="btn <?=(_escape($v['Comment']) == 'empty' ? 'btn-primary' : 'btn-danger');?>" data-toggle="modal" data-target="#comments-<?=_escape($v['id']);?>"><i class="fa fa-comment"></i></button>
                                <!-- Modal -->
                                <div class="modal fade" id="comments-<?=_escape($v['id']);?>">

                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <!-- Modal heading -->
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                <h3 class="modal-title"><?=_t( 'Comments' );?></h3>
                                            </div>
                                            <!-- // Modal heading END -->

                                            <!-- Modal body -->
                                            <div class="modal-body">
                                                <textarea id="<?=_escape($v['id']);?>" class="form-control" name="comment[]" rows="5" data-height="auto" parsley-required="true"><?=_escape($v['comment']);?></textarea>
                                                <input type="button" class="btn btn-default" value="Insert Timestamp" onclick="addMsg('<?=\Jenssegers\Date\Date::now()->format('D, M d, o @ h:i A');?> <?=get_name(get_persondata('personID'));?>','<?=_escape($v['id']);?>'); return false;" />
                                            </div>
                                            <!-- // Modal body END -->

                                            <!-- Modal footer -->
                                            <div class="modal-footer">
                                                <a href="#" class="btn btn-default" data-dismiss="modal"><?=_t( 'Close' );?></a> 
                                            </div>
                                            <!-- // Modal footer END -->

                                        </div>
                                    </div>
                                    <input type="hidden" name="id[]" value="<?=_escape($v['id']);?>" />
                                </div>
                                <!-- // Modal END -->
                            </td>
                        </tr>
                        <!-- // Table row END -->
                        <?php } endif; ?>

                    </tbody>
                    <!-- // Table body END -->

                </table>
                <!-- // Table END -->

                <!-- Form actions -->
                <div class="form-actions">
                    <?php if(_escape($nae[0]['personID']) != '') : ?>
                    <button type="submit"<?=pids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
                    <?php endif; ?>
                    <button type="button"<?=pids();?> class="btn btn-icon btn-primary glyphicons circle_plus" data-toggle="modal" data-target="#md-ajax"><i></i><?=_t( 'Add' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>nae/<?=_escape($nae[0]['personID']);?>/'"><i></i><?=_t( 'Cancel' );?></button>
                </div>
                <!-- // Form actions END -->
            </div>
        </div>
		
	</form>
	<!-- // Form END -->
	
	<!-- Modal -->
	<div class="modal fade" id="md-ajax">
		<form class="form-horizontal" data-collabel="3" data-alignlabel="left" action="<?=get_base_url();?>nae/perc/<?=_escape($nae[0]['personID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		<div class="modal-dialog">
			<div class="modal-content">
	
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Comments' );?></h3>
				</div>
				<!-- // Modal heading END -->
				
				<!-- Modal body -->
				<div class="modal-body">
					<div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Restriction' );?></label>
                        <div class="col-md-8">
	                        <select name="code" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
	                            <option value="">&nbsp;</option>
	                            <?=table_dropdown('rest', 'deptCode = ?', 'code', 'code', 'description','',[ get_persondata('deptCode') ]);?>
	                        </select>
                       </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?=_t( 'Severity' );?></label>
                        <div class="col-md-8">
                        	<input type="text" name="severity" class="form-control" parsley-type="digits" parsley-maxlength="2" />
                    	</div>
                    </div>
                    
                    <div class="form-group">
                    	<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Start Date' );?></label>
                    	<div class="col-md-8">
	                        <div class="input-group date" id="datepicker9">
	                            <input type="text" name="startDate" class="form-control" required/>
	                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
	                        </div>
                       </div>
                    </div>
                    
                    <div class="form-group">
                    	<label class="col-md-3 control-label"><?=_t( 'End Date' );?></label>
                    	<div class="col-md-8">
	                        <div class="input-group date" id="datepicker9">
	                            <input type="text" name="endDate" class="form-control" />
	                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
	                        </div>
                       </div>
                    </div>
                    
                    <div class="form-group">
                    	<label class="col-md-3 control-label"><?=_t( 'Comment' );?></label>
                    	<div class="col-md-8">
	                        <textarea id="comment" class="form-control" name="comment" rows="5" data-height="auto" parsley-required="true"></textarea>
	                        <input type="button" class="btn btn-default" value="Insert Timestamp" onclick="addMsg('<?=\Jenssegers\Date\Date::now()->format('D, M d, o @ h:i A');?> <?=get_name(get_persondata('personID'));?>','comment'); return false;" />
                       </div>
                    </div>
				</div>
				<!-- // Modal body END -->
				
				<!-- Modal footer -->
				<div class="modal-footer">
					<input type="hidden" name="personID" value="<?=_escape($nae[0]['personID']);?>" />
                    <input type="hidden" name="addDate" value="<?=\Jenssegers\Date\Date::now()->format('Y-m-d');?>" />
                    <input type="hidden" name="addedBy" value="<?=get_persondata('personID');?>" />
					<button type="submit" class="btn btn-default"><?=_t( 'Submit' );?></button>
					<a href="#" class="btn btn-primary" data-dismiss="modal"><?=_t( 'Cancel' );?></a>
				</div>
				<!-- // Modal footer END -->
	
			</div>
		</div>
		</form>
	</div>
	<!-- // Modal END -->
	
</div>	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>