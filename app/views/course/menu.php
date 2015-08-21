<?php if (!defined('BASE_PATH')) exit('No direct script access allowed'); ?>
<script src="<?= url('/'); ?>static/assets/plugins/jstree/jstree.min.js"></script>
<link rel="stylesheet" href="<?= url('/'); ?>static/assets/plugins/jstree/themes/default/style.min.css" />
<script>
    $(function () {
        $("#jstree").jstree().bind("select_node.jstree", function (e, data) {
            var href = data.node.a_attr.href;
            document.location.href = href;
        });
    });
</script>

<!-- Widget -->
<div class="widget widget-body-white jstree-menu col-md-2">

    <div class="widget-body">

        <!-- Row -->
        <div class="row">
            <!-- Column -->
            <div class="col-md-6">
                <div id="jstree">
                    <ul>
                        <li class="jstree-open"><?=_t( 'Course' );?>
                            <ul>
                                <?php if(_h($crse[0]['courseID']) !== '' && hasPermission('add_course')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_1"><a<?=($screen === 'vcrse') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>crse/<?=_h($crse[0]['courseID']);?>/"><?=_h($crse[0]['courseCode']);?></a></li>
                                <?php endif; ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_2"><a href="<?= url('/'); ?>crse/"><?=_t( 'Search Course' );?></a></li>
                                <?php if(hasPermission('add_course')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_3"><a<?=($screen === 'acrse') ? ' class="jstree-clicked"' : '';?> href="<?=url('/crse/add/');?>"><?=_t( 'Add Course' );?></a></li>
                                <?php endif; ?>
                                <?php if(_h($crse[0]['courseID']) !== '' && hasPermission('add_course')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_4"><a data-toggle="modal" href="#crse1"><?=_t( 'Clone Course' );?></a></li>
                                <?php endif; ?>
                                <?php if(_h($crse[0]['courseID']) !== '' && hasPermission('add_course_sec')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_5"><a href="<?= url('/'); ?>sect/add/<?=_h($crse[0]['courseID']);?>/"><?=_t( 'Create Section' );?></a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php if(function_exists('transfer_module') && hasPermission('register_student')) : ?>
                        <li class="jstree-open"><?=_t( 'Transfer' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_6"><a<?=($screen === 'extr') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>crse/extr/"><?=_t( 'External Course' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_7"><a<?=($screen === 'atceq') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>crse/tceq/add/"><?=_t( 'New Tran. Equiv.' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_8"><a<?=($screen === 'tceq') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>crse/tceq/"><?=_t( 'Tran. Crse. Equiv.' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_9"><a<?=($screen === 'tcre') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>crse/tcre/"><?=_t( 'Transfer Credit' );?></a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <!-- // Column END -->
        </div>
        <!-- // Row END -->
    </div>
</div>