<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * CLVR View
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

<script type="text/javascript">
    $(document).ready(function () {
        //Remove row.
        $('#items').on('click', '.delme', function () {
            $(this).parents('.item-row').remove();
        });
        //add row add here.
        $("#addrow").click(function () {
            $(".item-row:last").after('<tr class="item-row"><td class="center item-name"><select name="rule[]" class="rule selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required><option value="">&nbsp;</option><?php get_rules(); ?></select></td><td><select name="value[]" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required><option value="">&nbsp;</option><?php clas_dropdown("clas", "acadLevelCode = ?", "code", "code", "name", [_h($aclv->code)]); ?></select><input name="level" type="hidden" value="<?= _h($aclv->code); ?>" /></td><td><a href="javascript:;" title="Remove row" class="delme btn btn-danger"><i class="fa fa-minus"></i></a></td></tr>');
        });
    });
</script>

<ul class="breadcrumb">
    <li><?= _t('You are here'); ?></li>
    <li><a href="<?= get_base_url(); ?>dashboard/" class="glyphicons dashboard"><i></i> <?= _t('Dashboard'); ?></a></li>
    <li class="divider"></li>
    <li><a href="<?=get_base_url();?>form/aclv/" class="glyphicons road"><i></i> <?=_t( 'Academic Level' );?></a></li>
	<li class="divider"></li>
    <li><a href="<?=get_base_url();?>form/aclv/<?= _h($aclv->id); ?>/" class="glyphicons show_lines"><i></i> <?= _h($aclv->code); ?> <?= _h($aclv->name); ?> <?=_t( 'Academic Level' );?></a></li>
	<li class="divider"></li>
    <li><?= _t('Class Level Rules (CLVR)'); ?></li>
</ul>

<h3><?= _t('Class Level Rules (CLVR)'); ?></h3>
<div class="innerLR">

<?= _etsis_flash()->showMessage(); ?>

<?php jstree_sidebar_menu($screen); ?>

    <!-- Form -->
    <form class="form-horizontal margin-none register" action="<?= get_base_url(); ?>form/aclv/<?= _h($aclv->id); ?>/clvr/" id="validateSubmitForm" method="post" autocomplete="off">
        <!-- Widget -->
        <div class="widget widget-heading-simple widget-body-gray <?= (has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10'; ?>">
            
            <?php if($clvr->count() > 0) : ?>
                <a href="#test" data-toggle="modal" class="btn btn-inverse pull-right"><i class="fa fa-caret-square-o-right"></i></a>
                <div class="breakline">&nbsp;</div>
                <div class="breakline">&nbsp;</div>
            <?php endif; ?>
                
            <div class="widget-body">

                <div class="separator bottom"></div>

                <!-- Table -->
                <table id="items" class="table table-striped table-bordered bordered table-condensed table-white">

                    <!-- Table heading -->
                    <thead>
                        <tr class="ajaxTitle">
                            <th class="text-center"><?= _t('Rule Code'); ?></th>
                            <th class="text-center"><?= _t('Value'); ?></th>
                            <th class="text-center"><?= _t('Actions'); ?></th>
                        </tr>
                    </thead>
                    <!-- // Table heading END -->
                    <!-- Table body -->
                    <?php if(count($clvr) > 0) : ?>
                    <tbody>
                    <?php
                    $numItems = count($clvr);
                    $i = 0;
                    foreach ($clvr as $k => $v) { 
                    $rlde = get_rule_by_code(_h($v->rule));
                    ?>
                    <tr class="gradeX item-row">
                        <td class="text-center item-name">
                            <a style="float: left; margin-right:10px;" href="<?=get_base_url();?>rlde/<?=_h($rlde->id);?>/"><img src="<?=get_base_url();?>static/common/theme/images/cascade.png" /></a>
                            <select name="rule[]" class="form-control" style="width: 90%;" data-style="btn-info" data-size="10" data-live-search="true" required>
                                <option value="">&nbsp;</option>
                                <?php get_rules(_h($v->rule)); ?>
                            </select>
                        </td>
                        <td class="text-center">
                            <select name="value[]" class="form-control" style="width: 90%;" data-style="btn-info" data-size="10" data-live-search="true" required>
                                <option value="">&nbsp;</option>
                                <?php table_dropdown("clas", "acadLevelCode = ?", "code", "code", "name", _h($v->value), [_h($aclv->code)]); ?>
                            </select>
                            <input name="level" type="hidden" value="<?= _h($aclv->code); ?>" />
                            <input name="id[]" type="hidden" value="<?= _h($v->id); ?>" />
                        </td>
                        <td>
                            <?php if (++$i === $numItems) { ?>
                            <a id="addrow" href="javascript:;" title="Add a row" class="btn btn-inverse"><i class="fa fa-plus"></i></a>
                            <?php } ?>
                            <a href="#clvr_<?= _h($v->id); ?>" data-toggle="modal" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
                            <!-- Modal -->
                            <div class="modal fade" id="clvr_<?= _h($v->id); ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <!-- Modal heading -->
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h3 class="modal-title"><?=_h($v->rule);?></h3>
                                        </div>
                                        <!-- // Modal heading END -->
                                        <!-- Modal body -->
                                        <div class="modal-body">
                                            <p><?=_t('Are you sure you want to delete this record from your rule set?');?></p>
                                        </div>
                                        <!-- // Modal body END -->
                                        <!-- Modal footer -->
                                        <div class="modal-footer">
                                            <a href="<?=get_base_url();?>form/aclv/<?= _h($v->aid); ?>/clvr/<?= _h($v->rid); ?>/<?= _h($v->id); ?>/d/" class="btn btn-default"><?=_t( 'Delete' );?></a>
                                            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a> 
                                        </div>
                                        <!-- // Modal footer END -->
                                    </div>
                                </div>
                            </div>
                            <!-- // Modal END -->
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                    <?php else : ?>
                    <tbody>
                    <tr class="gradeX item-row">
                        <td class="text-center item-name">
                            <select name="rule[]" class="form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                <option value="">&nbsp;</option>
                                <?php get_rules(); ?>
                            </select>
                        </td>
                        <td class="text-center">
                            <select name="value[]" class="form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                <option value="">&nbsp;</option>
                                <?php table_dropdown("clas", "acadLevelCode = ?", "code", "code", "name", "", [_h($aclv->code)]); ?>
                            </select>
                            <input name="level" type="hidden" value="<?= _h($aclv->code); ?>" />
                        </td>
                        <td>
                            <a id="addrow" href="javascript:;" title="Add a row" class="btn btn-inverse"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                    </tbody>
                    <?php endif; ?>
                    <!-- // Table body END -->

                </table>
                <!-- // Table END -->

                <hr class="separator" />

                <!-- Form actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-icon btn-success glyphicons circle_ok"><i></i><?= _t('Save'); ?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>form/aclv/<?=_h($aclv->id);?>/'"><i></i><?=_t( 'Cancel' );?></button>
                </div>
                <!-- // Form actions END -->

            </div>
        </div>
        <!-- // Widget END -->
    </form>
    
    <!-- Modal -->
    <div class="modal fade" id="test">
        <form class="form-horizontal margin-none" action="<?=get_base_url();?>form/aclv/<?=_escape($aclv->id);?>/clvr/test/" id="validateSubmitForm" method="post" autocomplete="off">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?=_t( 'Test Class Rule' );?></h3>
                </div>
                <!-- // Modal heading END -->
                <!-- Modal body -->
                <div class="modal-body">
                    <!-- Column -->
					<div class="col-md-12">
					
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Student' );?></label>
							<div class="col-md-8">
								<select name="stuID" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php foreach($stu as $s) : ?>
                                    <option value="<?=_escape($s->stuID);?>"><?=get_name(_escape($s->stuID));?></option>
                                    <?php endforeach; ?>
                                </select>
							</div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Rule' );?></label>
							<div class="col-md-8">
								<select name="rule" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php foreach($clvr as $rule) : $desc = get_rule_by_code(_escape($rule->rule)); ?>
                                    <option value="<?=_escape($rule->rule);?>">(<?=_escape($rule->rule);?>) <?=_escape($desc->description);?></option>
                                    <?php endforeach; ?>
                                </select>
							</div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
                </div>
                <!-- // Modal body END -->
                <div class="breakline">&nbsp;</div>
                <div class="breakline">&nbsp;</div>
                <div class="breakline">&nbsp;</div>
                <div class="breakline">&nbsp;</div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <input name="acadLevelCode" type="hidden" value="<?=_h($aclv->code);?>" />
                    <button type="submit" class="btn btn-primary"><i></i><?=_t( 'Submit' );?></button>
                    <a href="#" class="btn btn-default" data-dismiss="modal"><?=_t( 'Close' );?></a>
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