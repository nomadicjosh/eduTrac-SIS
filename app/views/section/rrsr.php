<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * RRSR View
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
$screen = 'rgn';
?>

<script type="text/javascript">
    $(document).ready(function () {
        //Remove row.
        $('#items').on('click', '.delme', function () {
            $(this).parents('.item-row').remove();
        });
        //add row add here.
        $("#addrow").click(function () {
            $(".item-row:last").after('<tr class="item-row"><td class="center item-name"><select name="rule[]" class="rule selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required><option value="">&nbsp;</option><?php get_rules(); ?></select></td><td><textarea style="resize: none;height:8em;" name="value[]" class="form-control" required></textarea></td><td><a href="javascript:;" title="Remove row" class="delme btn btn-danger"><i class="fa fa-minus"></i></a></td></tr>');
        });
    });
</script>

<ul class="breadcrumb">
    <li><?= _t('You are here'); ?></li>
    <li><a href="<?= get_base_url(); ?>dashboard/" class="glyphicons dashboard"><i></i> <?= _t('Dashboard'); ?></a></li>
    <li class="divider"></li>
    <li><a href="<?=get_base_url();?>sect/rgn/" class="glyphicons road"><i></i> <?=_t( 'Course Registration' );?></a></li>
	<li class="divider"></li>
    <li><?= _t('Registration Restriction Rules (RRSR)'); ?></li>
</ul>

<h3><?= _t('Registration Restriction Rules (RRSR)'); ?></h3>
<div class="innerLR">

<?= _etsis_flash()->showMessage(); ?>

<?php jstree_sidebar_menu($screen); ?>

    <!-- Form -->
    <form class="form-horizontal margin-none register" action="<?= get_base_url(); ?>sect/rgn/rrsr/" id="validateSubmitForm" method="post" autocomplete="off">
        <!-- Widget -->
        <div class="widget widget-heading-simple widget-body-gray <?= (has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10'; ?>">
            <div class="widget-body">
                
                <div class="tab-pane">
                    <div class="widget widget-heading-simple widget-body-white margin-none">
                        <div class="widget-body">
                            
                            <div class="alerts alerts-info">
                                <p><?=_t("The Value field is where you would write a custom error message which will appear on the screen when a staff member tries to register a student with a registration restriction. There are several placeholders you can use in your message such as student's name {name}, department name {deptName}, department email {deptEmail} and department phone {deptPhone}.");?></p>
                            </div>
                            
                        </div>
                    </div>
                </div>

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
                    <?php if(count($rrsr) > 0) : ?>
                    <tbody>
                    <?php
                    $numItems = count($rrsr);
                    $i = 0;
                    foreach ($rrsr as $v) {
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
                        <td>
                            <textarea style="resize: none;height:8em;" name="value[]" class="form-control" required><?=_h($v->value); ?></textarea>
                            <input name="id[]" type="hidden" value="<?= _h($v->id); ?>" />
                        </td>
                        <td>
                            <?php if (++$i === $numItems) { ?>
                            <a id="addrow"<?=gids();?> href="javascript:;" title="Add a row" class="btn btn-inverse"><i class="fa fa-plus"></i></a>
                            <?php } ?>
                            <a href="#rrsr_<?= _h($v->id); ?>" data-toggle="modal" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
                            <!-- Modal -->
                            <div class="modal fade" id="rrsr_<?= _h($v->id); ?>">
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
                                            <a href="<?=get_base_url();?>sect/rgn/rrsr/<?= _h($v->id); ?>/d/" class="btn btn-default"><?=_t( 'Delete' );?></a>
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
                        <td>
                            <textarea style="resize: none;height:8em;" name="value[]" class="form-control" required></textarea>
                        </td>
                        <td>
                            <a id="addrow"<?=gids();?> href="javascript:;" title="Add a row" class="btn btn-inverse"><i class="fa fa-plus"></i></a>
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
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=get_base_url();?>sect/rgn/'"><i></i><?=_t( 'Cancel' );?></button>
                </div>
                <!-- // Form actions END -->

            </div>
        </div>
        <!-- // Widget END -->
    </form>

</div>	


</div>
<!-- // Content END -->
<?php $app->view->stop(); ?>