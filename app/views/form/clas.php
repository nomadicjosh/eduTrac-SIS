<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * CLAS View
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
    $(document).ready(function () {
        //Remove row.
        $('#items').on('click', '.delme', function () {
            $(this).parents('.item-row').remove();
        });
        //add row add here.
        $("#addrow").click(function () {
            $(".item-row:last").after('<tr class="gradeX item-row"><td class="text-center item-name"><input class="form-control"<?=gio();?> name="code[]" type="text" required/></td><td class="text-center"><input class="form-control"<?=gio();?> name="name[]" type="text" required /><input name="acadLevelCode" type="hidden" value="<?= _h($aclv->code); ?>" /></td><td><a href="javascript:;" title="Remove row" class="delme btn btn-danger"><i class="fa fa-minus"></i></a></td></tr>');
        });
    });

    $(".panel").show();
    setTimeout(function () {
        $(".panel").hide();
    }, 10000);
</script>

<ul class="breadcrumb">
    <li><?= _t('You are here'); ?></li>
    <li><a href="<?= get_base_url(); ?>dashboard/" class="glyphicons dashboard"><i></i> <?= _t('Dashboard'); ?></a></li>
    <li class="divider"></li>
    <li><a href="<?=get_base_url();?>form/aclv/" class="glyphicons road"><i></i> <?=_t( 'Academic Level' );?></a></li>
	<li class="divider"></li>
    <li><a href="<?=get_base_url();?>form/aclv/<?= _h($aclv->id); ?>/" class="glyphicons show_lines"><i></i> <?= _h($aclv->code); ?> <?= _h($aclv->name); ?> <?=_t( 'Academic Level' );?></a></li>
	<li class="divider"></li>
    <li><?= _t('Class Level (CLAS)'); ?></li>
</ul>

<h3><?= _h($aclv->name); ?> <?=_t( 'Class Level' );?></h3>
<div class="innerLR">
    
    <?=$flash->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>
    
    <!-- Form -->
    <form class="form-horizontal margin-none register" action="<?= get_base_url(); ?>form/aclv/<?= _h($aclv->id); ?>/clas/" id="validateSubmitForm" method="post" autocomplete="off">
        <!-- Widget -->
        <div class="widget widget-heading-simple widget-body-gray <?= (has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10'; ?>">
            <div class="widget-body">

                <div class="separator bottom"></div>

                <!-- Table -->
                <table id="items" class="table table-striped table-bordered bordered table-condensed table-white">

                    <!-- Table heading -->
                    <thead>
                        <tr class="ajaxTitle">
                            <th class="text-center"><?= _t('CLAS Code'); ?></th>
                            <th class="text-center"><?= _t('CLAS Name'); ?></th>
                            <th class="text-center"><?= _t('Actions'); ?></th>
                        </tr>
                    </thead>
                    <!-- // Table heading END -->
                    <!-- Table body -->
                    <tbody>
                    <?php if(count($clas) > 0) : ?>
                    <?php
                    $numItems = count($clas);
                    $i = 0;
                    foreach ($clas as $v) { 
                    ?>
                    <tr class="gradeX item-row">
                        <td class="text-center item-name"><input class="form-control"<?=gio();?> name="code[]" type="text" value="<?=_h($v['code']);?>" required/></td>
                        <td class="text-center">
                            <input class="form-control"<?=gio();?> name="name[]" type="text" value="<?=_h($v['name']);?>" required />
                            <input name="acadLevelCode" type="hidden" value="<?= _h($aclv->code); ?>" />
                            <input name="id[]" type="hidden" value="<?= _h($v['id']); ?>" />
                        </td>
                        <td>
                            <?php if (++$i === $numItems) { ?>
                            <a id="addrow" href="javascript:;" title="Add a row" class="btn btn-inverse"><i class="fa fa-plus"></i></a>
                            <?php } ?>
                            <a id="addrow" href="javascript:;" title="Add a row" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php else : ?>
                    <tr class="gradeX item-row">
                        <td class="text-center item-name"><input class="form-control"<?=gio();?> name="code[]" type="text" required/></td>
                        <td class="text-center">
                            <input class="form-control"<?=gio();?> name="name[]" type="text" required />
                            <input name="acadLevelCode" type="hidden" value="<?= _h($aclv->code); ?>" />
                        </td>
                        <td>
                            <a id="addrow"<?=gids();?> href="javascript:;" title="Add a row" class="btn btn-inverse"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                    <?php endif; ?>
                    </tbody>
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
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>