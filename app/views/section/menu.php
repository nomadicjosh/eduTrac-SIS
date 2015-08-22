<?php if (!defined('BASE_PATH')) exit('No direct script access allowed'); 
/**
 * Course section sidebar menu tree.
 *
 * @license GPLv3
 * 
 * @since       6.1.00
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
?>
<script src="<?= url('/'); ?>static/assets/plugins/jstree/jstree.min.js"></script>
<link rel="stylesheet" href="<?= url('/'); ?>static/assets/plugins/jstree/themes/proton/style.css" />
<script>
    $(function () {
        $("#jstree").jstree({
            'core': {
                'themes': {
                    'name': 'proton',
                    'responsive': true
                }
            }
        }).bind("select_node.jstree", function (e, data) {
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
                        <li class="jstree-open"><?=_t( 'Section' );?>
                            <ul>
                                <?php if(hasPermission('access_course_sec_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_1"><a href="<?= url('/'); ?>sect/"><?=_t( 'Search Section' );?></a></li>
                                <?php endif; ?>
                                <?php if(_h($sect[0]['courseSecID']) !== '' && hasPermission('add_course_sec')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_2"><a href="<?= url('/'); ?>sect/add/<?=_h($sect[0]['courseID']);?>/"><?=_t( 'Create Section' );?></a></li>
                                <?php endif; ?>
                                <?php if(hasPermission('register_student')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_3"><a<?=($screen === 'rgn') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/rgn/"><?=_t( '(RGN) - Register' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_4"><a<?=($screen === 'brgn') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/brgn/"><?=_t( '(BRGN) - Batch Reg.' );?></a></li>
                                <?php endif; ?>
                                <?php if(hasPermission('access_stu_roster_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_5"><a href="<?= url('/'); ?>sect/sros/"><?=_t( '(SROS) - Stu. Roster' );?></a></li>
                                <?php endif; ?>
                                <?php if(function_exists('booking_module')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_6"><a<?=($screen === 'timetable') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/timetable/"><?=_t( 'Timetable' );?></a></li>
                                <?php endif; ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_7"><a<?=($screen === 'cat') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/catalog/"><?=_t( 'Course Catalogs' );?></a></li>
                            </ul>
                        </li>
                        <?php if(function_exists('gradebook_module') && hasPermission('access_grading_screen')) : ?>
                        <li class="jstree-open"><?=_t( 'Gradebook' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_8"><a<?=($screen === 'mysect') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/courses/"><?=_t( 'My Course Sections' );?></a></li>
                                <?php if(_h($sect[0]['courseSecID']) && hasPermission('access_gradebook')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_9"><a<?=($screen === 'addassign') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/add-assignment/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Add Assignment' );?></a></li>
                                <?php if(assignmentExist(_h($sect[0]['courseSecID'])) && hasPermission('access_gradebook')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_10"><a<?=($screen === 'assigns') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/assignments/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Assignments' );?></a></li>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if(gradebookExist(_h($sect[0]['courseSecID'])) && hasPermission('access_gradebook')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_11"><a<?=($screen === 'gradebook') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/gradebook/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Gradebook' );?></a></li>
                                <?php endif; ?>
                                <?php if(studentsExist(_h($sect[0]['courseSecID']))) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_12"><a<?=($screen === 'email') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/students/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Email Students' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_13"><a<?=($screen === 'fgrade') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/final-grade/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Final Grades' );?></a></li>
                                <?php endif; ?>
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