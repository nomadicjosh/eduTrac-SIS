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
<div class="widget widget-body-white col-md-2">

    <div class="widget-body">

        <!-- Row -->
        <div class="row">
            <!-- Column -->
            <div class="col-md-6">
                <div id="jstree">
                    <ul>
                        <li><?=_t( 'Dashboard' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_1"><a<?=($screen === 'dash') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>dashboard/"><?=_t( 'Dashboard' );?></a></li>
                                <?php if(hasPermission('access_plugin_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_2"><a<?=($screen === 'mods') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>dashboard/modules/"><?=_t( 'System Modules' );?></a></li>
                                <?php endif; ?>
                                <?php if(hasPermission('access_plugin_admin_page')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_3"><a<?=($screen === 'imod') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>dashboard/install-module/"><?=_t( 'Install Module' );?></a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php if(hasPermission('edit_settings')) : ?>
                        <li><?=_t( 'Administrative' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_58"><a<?=($screen === 'setting') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>setting/"><?=_t( 'Settings' );?></a></li>
                                <?php if(hasPermission('import_data') && function_exists('import_module')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_59"><a<?=($screen === 'import') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>form/import/"><?=_t( 'Importer' );?></a></li>
                                <?php endif; ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_60"><a<?=($screen === 'cron') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>cron/"><?=_t( 'Cronjob Handler' );?></a></li>
                                <?php if(hasPermission('access_permission_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_61"><a<?=($screen === 'perm') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>permission/"><?=_t( '(PERM) - Permissions' );?></a></li>
                                <?php endif; ?>
                                <?php if(hasPermission('access_role_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_62"><a<?=($screen === 'role') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>role/"><?=_t( '(ROLE) - Roles' );?></a></li>
                                <?php endif; ?>
                                <?php if(hasPermission('access_error_log_screen') && function_exists('event_log_module')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_63"><a<?=($screen === 'err') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>err/logs/"><?=_t( 'Error Log' );?></a></li>
                                <?php endif; ?>
                                <?php if(hasPermission('access_audit_trail_screen') && function_exists('event_log_module')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_64"><a<?=($screen === 'audit') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>audit-trail/"><?=_t( 'Audit Trail' );?></a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(hasPermission('access_form')) : ?>
                        <li><?=_t( 'Forms' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_4"><a<?=($screen === 'ayr') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/acad-year/"><?=_t( '(AYR) - Academic Year' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_5"><a<?=($screen === 'sem') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/semester/"><?=_t( '(SEM) - Semester' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_6"><a<?=($screen === 'term') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/term/"><?=_t( '(TERM) - Term' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_7"><a<?=($screen === 'dept') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/department/"><?=_t( '(DEPT) - Department' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_8"><a<?=($screen === 'subj') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/subject/"><?=_t( '(SUBJ) - Subject' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_9"><a<?=($screen === 'slr') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/student-load-rule/"><?=_t( '(SLR) - Stu Load Rules' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_10"><a<?=($screen === 'deg') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/degree/"><?=_t( '(DEG) - Degree' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_11"><a<?=($screen === 'majr') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/major/"><?=_t( '(MAJR) - Major' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_12"><a<?=($screen === 'minr') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/minor/"><?=_t( '(MINR) - Minor' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_13"><a<?=($screen === 'ccd') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/ccd/"><?=_t( '(CCD) - CCD' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_14"><a<?=($screen === 'spec') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/specialization/"><?=_t( '(SPEC) - Specialization' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_15"><a<?=($screen === 'cip') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/cip/"><?=_t( '(CIP) - CIP' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_16"><a<?=($screen === 'rstr') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/rstr-code/"><?=_t( '(RSTR) - Restrict. Codes' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_17"><a<?=($screen === 'loc') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/location/"><?=_t( '(LOC) - Location' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_18"><a<?=($screen === 'bldg') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/building/"><?=_t( '(BLDG) - Building' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_19"><a<?=($screen === 'room') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/room/"><?=_t( '(ROOM) - Room' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_20"><a<?=($screen === 'sch') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/school/"><?=_t( '(SCH) - School' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_21"><a<?=($screen === 'grsc') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>form/grade-scale/"><?=_t( '(GRSC) - Grade Scale' );?></a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(hasPermission('access_person_screen')) : ?>
                        <li><?=_t( 'Person' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_47"><a<?=($screen === 'nae') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>nae/"><?=_t( 'Search Person' );?></a></li>
                                
                                <?php if($nae !== '') : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_57"><a<?=($screen === 'vnae') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>nae/<?=_h($nae[0]['personID']);?>/"><?=get_name(_h($nae[0]['personID']));?></a></li>
                                <?php endif; ?>
                                
                                <?php if(hasPermission('add_person')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_48"><a<?=($screen === 'anae') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>nae/add/"><?=_t( 'New Person' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if($nae !== '') : ?>
                                <?php if(!isset($_COOKIE['SWITCH_USERBACK']) && _h($nae[0]['personID']) != get_persondata('personID') && hasPermission('login_as_user')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_49"><a href="<?= url('/'); ?>switchUserTo/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Switch To' );?></a></li>
                                <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if($nae !== '') : ?>
                                <?php if($staff[0]['staffID'] <= 0 && hasPermission('create_staff_record')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_50"><a<?=($screen === 'cstaff') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>staff/add/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Create Staff' );?></a></li>
                                <?php elseif($staff[0]['staffID'] > 0 && hasPermission('access_staff_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_51"><a<?=($screen === 'staff') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>staff/<?=_h($nae[0]['personID']);?>/"><?=_t( '(STAF) - Staff' );?></a></li>
                                <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if($nae !== '') : ?>
                                <?php if(isStudent($nae[0]['personID']) && hasPermission('access_student_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_52"><a<?=($screen === 'spro') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>stu/<?=_h($nae[0]['personID']);?>/"><?=_t( '(SPRO) - Stu. Profile' );?></a></li>
                                <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if($nae !== '') : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_53"><a<?=($screen === 'adsu') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>nae/adsu/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Address Summary' );?></a></li>
                                <?php if(hasPermission('add_address')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_54"><a<?=($screen === 'addr') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>nae/addr-form/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Address Form' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if(hasPermission('access_user_role_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_55"><a<?=($screen === 'prole') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>nae/role/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Person Role' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if(hasPermission('access_user_permission_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_56"><a<?=($screen === 'pperm') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>nae/perms/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Person Perm' );?></a></li>
                                <?php endif; ?>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(hasPermission('access_acad_prog_screen')) : ?>
                        <li><?=_t( 'Academic Program' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_22"><a<?=($screen === 'prog') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>program/"><?=_t( 'Search Program' );?></a></li>
                                <?php if($prog !== '') : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_23"><a<?=($screen === 'vprog') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>program/<?=_h( $prog[0]['acadProgID'] );?>"><?=_h( $prog[0]['acadProgCode'] );?></a></li>
                                <?php endif; ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_24"><a<?=($screen === 'aprog') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>program/add/"><?=_t( '(APRG) - Add Program' );?></a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(hasPermission('access_course_screen')) : ?>
                        <li><?=_t( 'Course' );?>
                            <ul>
                                <?php if($crse !== '') : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_25"><a<?=($screen === 'vcrse') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>crse/<?=_h($crse[0]['courseID']);?>/"><?=_h($crse[0]['courseCode']);?></a></li>
                                <?php endif; ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_26"><a<?=($screen === 'crse') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>crse/"><?=_t( 'Search Course' );?></a></li>
                                <?php if(hasPermission('add_course')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_27"><a<?=($screen === 'acrse') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>crse/add/"><?=_t( '(ACRS) - Add Course' );?></a></li>
                                <?php endif; ?>
                                <?php if($crse !== '' && hasPermission('add_course_sec')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_28"><a<?=($screen === 'csect') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>sect/add/<?=_h($crse[0]['courseID']);?>/"><?=_t( 'Create Section' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if(function_exists('transfer_module') && hasPermission('register_student')) : ?>
                                <li><?=_t( 'Transfer' );?>
                                    <ul>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_29"><a<?=($screen === 'extr') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>crse/extr/"><?=_t( 'External Course' );?></a></li>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_30"><a<?=($screen === 'atceq') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>crse/tceq/add/"><?=_t( 'New Tran. Equiv.' );?></a></li>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_31"><a<?=($screen === 'tceq') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>crse/tceq/"><?=_t( 'Tran. Crse. Equiv.' );?></a></li>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_32"><a<?=($screen === 'tcre') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>crse/tcre/"><?=_t( 'Transfer Credit' );?></a></li>
                                    </ul>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(hasPermission('access_course_sec_screen')) : ?>
                        <li><?=_t( 'Section' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_33"><a<?=($screen === 'sect') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/"><?=_t( 'Search Section' );?></a></li>
                                <?php if($sect !== '') : ?>
                                <?php if(count($sect[0]['courseSecID']) > 0) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_34"><a<?=($screen === 'vsect') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>sect/<?=_h($sect[0]['courseSecID']);?>/"><?=_h($sect[0]['courseSection']);?></a></li>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if($sect !== '' && hasPermission('add_course_sec')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml35"><a<?=($screen === 'csect') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>sect/add/<?=_h($sect[0]['courseID']);?>/"><?=_t( 'Create Section' );?></a></li>
                                <?php endif; ?>
                                <?php if(hasPermission('register_student')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_36"><a<?=($screen === 'rgn') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/rgn/"><?=_t( '(RGN) - Register' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_37"><a<?=($screen === 'brgn') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/brgn/"><?=_t( '(BRGN) - Batch Reg.' );?></a></li>
                                <?php endif; ?>
                                <?php if(hasPermission('access_stu_roster_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_38"><a<?=($screen === 'sros') ? ' class="jstree-clicked"' : '';?> href="<?= url('/'); ?>sect/sros/"><?=_t( '(SROS) - Stu. Roster' );?></a></li>
                                <?php endif; ?>
                                <?php if(function_exists('booking_module')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_39"><a<?=($screen === 'timetable') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/timetable/"><?=_t( 'Timetable' );?></a></li>
                                <?php endif; ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_40"><a<?=($screen === 'cat') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/catalog/"><?=_t( 'Course Catalogs' );?></a></li>
                                
                                <?php if(function_exists('gradebook_module') && hasPermission('access_grading_screen')) : ?>
                                <li><?=_t( 'Gradebook' );?>
                                    <ul>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_41"><a<?=($screen === 'mysect') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/courses/"><?=_t( 'My Course Sections' );?></a></li>
                                        <?php if($sect && hasPermission('access_gradebook')) : ?>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_42"><a<?=($screen === 'addassign') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/add-assignment/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Add Assignment' );?></a></li>
                                        <?php if($sect) : ?>
                                        <?php if(assignmentExist(_h($sect[0]['courseSecID'])) && hasPermission('access_gradebook')) : ?>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_43"><a<?=($screen === 'assigns') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/assignments/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Assignments' );?></a></li>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if($sect) : ?>
                                        <?php if(gradebookExist(_h($sect[0]['courseSecID'])) && hasPermission('access_gradebook')) : ?>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_44"><a<?=($screen === 'gradebook') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/gradebook/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Gradebook' );?></a></li>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if($sect) : ?>
                                        <?php if(studentsExist(_h($sect[0]['courseSecID']))) : ?>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_45"><a<?=($screen === 'email') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/students/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Email Students' );?></a></li>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_46"><a<?=($screen === 'fgrade') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>sect/final-grade/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Final Grades' );?></a></li>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <li><?=_t( 'Files' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_100"><a<?=($screen === 'fm') ? ' class="jstree-clicked"' : '';?> href="<?=url('/');?>staff/file-manager/"><?=_t( 'File Manager' );?></a></li>
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