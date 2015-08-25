<?php if (!defined('BASE_PATH')) exit('No direct script access allowed'); 
/**
 * NAE sidebar menu tree.
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
                        <li class="jstree-open"><?=_t( 'Person' );?>
                            <ul>
                                <?php if(hasPermission('access_person_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_1"><a href="<?= url('/'); ?>nae/"><?=_t( 'Search Person' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if(hasPermission('add_person')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_2"><a href="<?= url('/'); ?>nae/add/"><?=_t( 'New Person' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if(!isset($_COOKIE['SWITCH_USERBACK']) && _h($nae[0]['personID']) != get_persondata('personID') && hasPermission('login_as_user')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_3"><a href="<?= url('/'); ?>switchUserTo/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Switch To' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if($staff[0]['staffID'] <= 0 && hasPermission('create_staff_record')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_4"><a href="<?=url('/');?>staff/add/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Create Staff' );?></a></li>
                                <?php elseif($staff[0]['staffID'] > 0 && hasPermission('access_staff_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_5"><a href="<?=url('/');?>staff/<?=_h($nae[0]['personID']);?>/"><?=_t( '(STAF) - Staff' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if(isStudent($nae[0]['personID']) && hasPermission('access_student_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_6"><a href="<?= url('/'); ?>stu/<?=_h($nae[0]['personID']);?>/"><?=_t( '(SPRO) - Stu. Profile' );?></a></li>
                                <?php endif; ?>
                                
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_7"><a<?=($screen === 'adsu') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>nae/adsu/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Address Summary' );?></a></li>
                                <?php if(hasPermission('add_address')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_8"><a<?=($screen === 'addr') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>nae/addr-form/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Address Form' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if(hasPermission('access_user_role_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_9"><a<?=($screen === 'role') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>nae/role/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Person Role' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if(hasPermission('access_user_permission_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_10"><a<?=($screen === 'perm') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>nae/perms/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Person Perm' );?></a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li class="jstree-open"><?=_t( 'Files' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_11"><a href="<?=url('/');?>staff/file-manager/"><?=_t( 'File Manager' );?></a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- // Column END -->
        </div>
        <!-- // Row END -->
    </div>
</div>