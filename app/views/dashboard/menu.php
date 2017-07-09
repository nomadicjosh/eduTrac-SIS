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
<div class="widget widget-body-white col-md-2 hidden-print">

    <div class="widget-body">

        <!-- Row -->
        <div class="row">
            <!-- Column -->
            <div class="col-md-6">
                <div id="jstree">
                    <ul>
                        <li><?=_t( 'Dashboard' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_1"><a<?=($screen === 'dash') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>dashboard/"><?=_t( 'Dashboard' );?></a></li>
                                <?php if(_he('access_plugin_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_2"><a<?=($screen === 'mods') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>dashboard/modules/"><?=_t( 'System Modules' );?></a></li>
                                <?php endif; ?>
                                <?php if(_he('access_plugin_admin_page')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_3"><a<?=($screen === 'imod') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>dashboard/install-module/"><?=_t( 'Install Module' );?></a></li>
                                <?php endif; ?>
                                <?php if(_he('send_sms')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_104"><a<?=($screen === 'sms') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>dashboard/sms/"><?=_t( 'SMS' );?></a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php if(_he('edit_settings')) : ?>
                        <li><?=_t( 'Administrative' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_58"><a<?=($screen === 'setting') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>setting/"><?=_t( 'Settings' );?></a></li>
                                <?php if(_he('access_payment_gateway') && _mf('financial_module')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_97"><a<?=($screen === 'ppal') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>financial/paypal/"><?=_t( 'Paypal Gateway' );?></a></li>
                                <?php endif; ?>
                                <?php if(_he('import_data') && _mf('import_module')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_59"><a<?=($screen === 'import') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>form/import/"><?=_t( 'Importer' );?></a></li>
                                <?php endif; ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_60"><a<?=($screen === 'cron') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>cron/"><?=_t( 'Cronjob Handler' );?></a></li>
                                <?php if(_he('access_permission_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_61"><a<?=($screen === 'perm') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>permission/"><?=_t( '(PERM) - Permissions' );?></a></li>
                                <?php endif; ?>
                                <?php if(_he('access_role_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_62"><a<?=($screen === 'role') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>role/"><?=_t( '(ROLE) - Roles' );?></a></li>
                                <?php endif; ?>
                                <?php if(_he('access_error_log_screen') && _mf('event_log_module')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_63"><a<?=($screen === 'err') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>err/logs/"><?=_t( 'Error Log' );?></a></li>
                                <?php endif; ?>
                                <?php if(_he('access_audit_trail_screen') && _mf('event_log_module')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_64"><a<?=($screen === 'audit') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>audit-trail/"><?=_t( 'Audit Trail' );?></a></li>
                                <?php endif; ?>
                                <?php if(_he('not_hidden')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_65"><a<?=($screen === 'update') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>dashboard/core-update/"><?=_t( 'Automatic Update' );?></a></li>
                                <?php endif; ?>
                                <?php if(_he('access_ftp')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_105"><a<?=($screen === 'ftp') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>dashboard/ftp/"><?=_t( 'FTP' );?></a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('manage_business_rules')) : ?>
                        <li><?=_t( 'Rule Definition (RLDE)' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_102"><a<?=($screen === 'rlde') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>rlde/"><?=_t( 'Rules' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_103"><a<?=($screen === 'arlde') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>rlde/add/"><?=_t( 'Add Rule' );?></a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('access_form')) : ?>
                        <li><?=_t( 'Forms' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_4"><a<?=($screen === 'ayr') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/acad-year/"><?=_t( '(AYR) - Academic Year' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_5"><a<?=($screen === 'sem') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/semester/"><?=_t( '(SEM) - Semester' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_6"><a<?=($screen === 'term') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/term/"><?=_t( '(TERM) - Term' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_7"><a<?=($screen === 'dept') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/department/"><?=_t( '(DEPT) - Department' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_8"><a<?=($screen === 'subj') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/subject/"><?=_t( '(SUBJ) - Subject' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_10"><a<?=($screen === 'deg') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/degree/"><?=_t( '(DEG) - Degree' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_11"><a<?=($screen === 'majr') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/major/"><?=_t( '(MAJR) - Major' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_12"><a<?=($screen === 'minr') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/minor/"><?=_t( '(MINR) - Minor' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_13"><a<?=($screen === 'ccd') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/ccd/"><?=_t( '(CCD) - CCD' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_14"><a<?=($screen === 'spec') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/specialization/"><?=_t( '(SPEC) - Specialization' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_15"><a<?=($screen === 'cip') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/cip/"><?=_t( '(CIP) - CIP' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_16"><a<?=($screen === 'rest') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/rest/"><?=_t( '(REST) - Restriction' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_17"><a<?=($screen === 'loc') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/location/"><?=_t( '(LOC) - Location' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_18"><a<?=($screen === 'bldg') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/building/"><?=_t( '(BLDG) - Building' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_19"><a<?=($screen === 'room') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/room/"><?=_t( '(ROOM) - Room' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_20"><a<?=($screen === 'sch') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/school/"><?=_t( '(SCH) - School' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_21"><a<?=($screen === 'grsc') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/grade-scale/"><?=_t( '(GRSC) - Grade Scale' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_101"><a<?=($screen === 'aclv') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>form/aclv/"><?=_t( '(ACLV) - Academic Level' );?></a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_mf('booking_module')) : ?>
                        <li><?=_t( 'Events Calendar' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_66"><a<?=($screen === 'cal') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>calendar/"><?=_t( 'Calendar' );?></a></li>
                                <?php if(_he('room_request')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_67"><a<?=($screen === 'calbook') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>calendar/booking-form/"><?=_t( 'Room Booking Form' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_68"><a<?=($screen === 'calevent') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>calendar/events/"><?=_t( 'Manage Events' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_69"><a<?=($screen === 'calreq') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>calendar/requests/"><?=_t( 'Pending Requests' );?></a></li>
                                <?php endif; ?>
                                <?php if(_he('edit_settings')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_70"><a<?=($screen === 'calset') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>calendar/setting/"><?=_t( 'Settings' );?></a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('access_human_resources')) : ?>
                        <li><?=_t( 'Human Resources' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_71"><a<?=($screen === 'hr') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>hr/"><?=_t( 'Employees' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_72"><a<?=($screen === 'hrpay') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>hr/grades/"><?=_t( 'Pay Grades' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_73"><a<?=($screen === 'hrjob') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>hr/jobs/"><?=_t( 'Job Titles' );?></a></li>
                                <?php if(_mf('timesheet_module')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_74"><a<?=($screen === 'hrtime') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>hr/timesheets/"><?=_t( 'Timesheets' );?></a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('submit_timesheets') && _mf('timesheet_module')) : ?>
                        <li><?=_t( 'Timesheets' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_75"><a<?=($screen === 'stime') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>staff/timesheets/"><?=_t( 'Timesheets' );?></a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('access_sql')) : ?>
                        <li><?=_t( 'SQL' );?>
                            <ul>
                            	<?php if(_he('access_sql_interface_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_76"><a<?=($screen === 'sql') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sql/"><?=_t( 'SQL Interface' );?></a></li>
                                <?php endif; ?>
                                <?php if(_mf('savedquery_module')) : ?>
                                <?php if(_he('access_save_query_screens')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_77"><a<?=($screen === 'sqry') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sql/saved-queries/add/"><?=_t( 'Create Query' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_78"><a<?=($screen === 'vqry') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sql/saved-queries/"><?=_t( 'Queries' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_79"><a<?=($screen === 'csv') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sql/saved-queries/csv-email/"><?=_t( 'CSV to Email Report' );?></a></li>
                                <?php endif; ?>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('access_person_screen')) : ?>
                        <li><?=_t( 'Person' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_47"><a<?=($screen === 'nae') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>nae/"><?=_t( 'Person Lookup' );?></a></li>
                                
                                <?php if($nae !== '') : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_57"><a<?=($screen === 'vnae') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>nae/<?=_h($nae[0]['personID']);?>/"><?=get_name(_h($nae[0]['personID']));?></a></li>
                                <?php endif; ?>
                                
                                <?php if(_he('add_person')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_48"><a<?=($screen === 'anae') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>nae/add/"><?=_t( 'New Person' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if($nae !== '') : ?>
                                <?php if(!isset($_COOKIE['SWITCH_USERBACK']) && _h($nae[0]['personID']) != get_persondata('personID') && _he('login_as_user')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_49"><a href="<?= get_base_url(); ?>switchUserTo/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Switch To' );?></a></li>
                                <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if(_he('access_staff_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_80"><a<?=($screen === 'staff') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>staff/"><?=_t( 'Staff Lookup' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if($nae !== '') : ?>
                                <?php if($staff->staffID <= 0 && _he('create_staff_record')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_50"><a<?=($screen === 'cstaff') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>staff/add/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Create Staff' );?></a></li>
                                <?php elseif($staff->staffID > 0 && _he('access_staff_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_51"><a<?=($screen === 'vstaff') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>staff/<?=_h($nae[0]['personID']);?>/"><?=_t( '(STAF) - Staff' );?></a></li>
                                <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if($nae !== '') : ?>
                                <?php if(is_student($nae[0]['personID']) && _he('access_student_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_52"><a<?=($screen === 'spro') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>stu/<?=_h($nae[0]['personID']);?>/"><?=_t( '(SPRO) - Stu. Profile' );?></a></li>
                                <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if($nae !== '') : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_53"><a<?=($screen === 'adsu') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>nae/adsu/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Address Summary' );?></a></li>
                                <?php if(_he('add_address')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_54"><a<?=($screen === 'addr') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>nae/addr-form/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Address Form' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if(_he('access_user_role_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_55"><a<?=($screen === 'prole') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>nae/role/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Person Role' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if(_he('access_person_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_106"><a<?=($screen === 'perc') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>nae/perc/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Person Restriction (PERC)' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if(_he('access_user_permission_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_56"><a<?=($screen === 'pperm') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>nae/perms/<?=_h($nae[0]['personID']);?>/"><?=_t( 'Person Perm' );?></a></li>
                                <?php endif; ?>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('access_student_screen')) : ?>
                        <li><?=_t( 'Student' );?>
                            <ul>
                                <?php if(_he('graduate_students')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_82"><a<?=($screen === 'grad') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>stu/graduation/"><?=_t( 'Graduate Students' );?></a></li>
                                <?php endif; ?>
                                <?php if(_he('generate_transcript')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_83"><a<?=($screen === 'tran') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>stu/tran/"><?=_t( '(TRAN) - Transcript' );?></a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('access_acad_prog_screen')) : ?>
                        <li><?=_t( 'Academic Program' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_22"><a<?=($screen === 'prog') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>program/"><?=_t( 'Program Lookup' );?></a></li>
                                <?php if($prog !== '') : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_23"><a<?=($screen === 'vprog') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>program/<?=_h( $prog->id );?>"><?=_h( $prog->acadProgCode );?></a></li>
                                <?php endif; ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_24"><a<?=($screen === 'aprog') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>program/add/"><?=_t( '(APRG) - Add Program' );?></a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('access_course_screen')) : ?>
                        <li><?=_t( 'Course' );?>
                            <ul>
                                <?php if($crse !== '') : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_25"><a<?=($screen === 'vcrse') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>crse/<?=_h($crse->courseID);?>/"><?=_h($crse->courseCode);?></a></li>
                                <?php endif; ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_26"><a<?=($screen === 'crse') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>crse/"><?=_t( 'Course Lookup' );?></a></li>
                                <?php if(_he('add_course')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_27"><a<?=($screen === 'acrse') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>crse/add/"><?=_t( '(ACRS) - Add Course' );?></a></li>
                                <?php endif; ?>
                                <?php if($crse !== '' && _he('add_course_sec')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_28"><a<?=($screen === 'csect') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>sect/add/<?=_h($crse->courseID);?>/"><?=_t( 'Create Section' );?></a></li>
                                <?php endif; ?>
                                
                                <?php if(_he('register_student') && _mf('transfer_module')) : ?>
                                <li><?=_t( 'Transfer' );?>
                                    <ul>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_29"><a<?=($screen === 'extr') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>crse/extr/"><?=_t( 'External Course' );?></a></li>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_30"><a<?=($screen === 'atceq') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>crse/tceq/add/"><?=_t( 'New Tran. Equiv.' );?></a></li>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_31"><a<?=($screen === 'tceq') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>crse/tceq/"><?=_t( 'Tran. Crse. Equiv.' );?></a></li>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_32"><a<?=($screen === 'tcre') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>crse/tcre/"><?=_t( 'Transfer Credit' );?></a></li>
                                    </ul>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('access_course_sec_screen')) : ?>
                        <li><?=_t( 'Section' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_33"><a<?=($screen === 'sect') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sect/"><?=_t( 'Section Lookup' );?></a></li>
                                <?php if($sect !== '') : ?>
                                <?php if(count($sect->courseSecID) > 0) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_34"><a<?=($screen === 'vsect') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>sect/<?=_h($sect->courseSecID);?>/"><?=_h($sect->courseSection);?></a></li>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if($sect !== '' && _he('add_course_sec')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml35"><a<?=($screen === 'csect') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>sect/add/<?=_h($sect->courseID);?>/"><?=_t( 'Create Section' );?></a></li>
                                <?php endif; ?>
                                <?php if(_he('register_student')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_36"><a<?=($screen === 'rgn') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sect/rgn/"><?=_t( '(RGN) - Register' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_37"><a<?=($screen === 'brgn') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sect/brgn/"><?=_t( '(BRGN) - Batch Reg.' );?></a></li>
                                <?php endif; ?>
                                <?php if(_he('access_stu_roster_screen')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_38"><a<?=($screen === 'sros') ? ' class="jstree-clicked"' : '';?> href="<?= get_base_url(); ?>sect/sros/"><?=_t( '(SROS) - Stu. Roster' );?></a></li>
                                <?php endif; ?>
                                <?php if(_mf('booking_module')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_39"><a<?=($screen === 'timetable') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sect/timetable/"><?=_t( 'Timetable' );?></a></li>
                                <?php endif; ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_40"><a<?=($screen === 'cat') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sect/catalog/"><?=_t( 'Course Catalogs' );?></a></li>
                                
                                <?php if(_he('access_grading_screen')) : ?>
                                <li><?=_t( 'Gradebook' );?>
                                    <ul>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_41"><a<?=($screen === 'mysect') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sect/courses/"><?=_t( 'My Course Sections' );?></a></li>
                                        <?php if(_mf('gradebook_module')) : ?>
                                        <?php if($sect && $screen !== 'vsect' && $screen !== 'csect' && _he('access_gradebook')) : ?>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_42"><a<?=($screen === 'addassign') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sect/add-assignment/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Add Assignment' );?></a></li>
                                        <?php if($sect && $screen !== 'vsect' && $screen !== 'csect') : ?>
                                        <?php if(assignmentExist(_h($sect[0]['courseSecID'])) && _he('access_gradebook')) : ?>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_43"><a<?=($screen === 'assigns') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sect/assignments/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Assignments' );?></a></li>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if($sect && $screen !== 'vsect' && $screen !== 'csect') : ?>
                                        <?php if(gradebookExist(_h($sect[0]['courseSecID'])) && _he('access_gradebook')) : ?>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_44"><a<?=($screen === 'gradebook') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sect/gradebook/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Gradebook' );?></a></li>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if($sect && $screen !== 'vsect' && $screen !== 'csect') : ?>
                                        <?php if(studentsExist(_h($sect[0]['courseSecID']))) : ?>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_45"><a<?=($screen === 'email') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sect/students/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Email Students' );?></a></li>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_46"><a<?=($screen === 'fgrade') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>sect/final-grade/<?=_h($sect[0]['courseSecID']);?>/"><?=_t( 'Final Grades' );?></a></li>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                    </ul>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('access_institutions_screen')) : ?>
                        <li><?=_t( 'Institution' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_84"><a<?=($screen === 'inst') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>appl/inst/"><?=_t( '(INST) - Institution' );?></a></li>
                                <?php if(_he('add_institution')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_85"><a<?=($screen === 'ainst') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>appl/inst/add/"><?=_t( '(AINST) - Add Institution' );?></a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('access_communication_mgmt') && _mf('mrkt_module')) : ?>
                        <li><?=_t( 'Marketing' );?>
                            <ul>
                                <?php if(_he('edit_settings')) : ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_90"><a<?=($screen === 'bnce') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>mrkt/bounce/"><?=_t( 'Bounce Setting' );?></a></li>
                                <?php endif; ?>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_88"><a<?=($screen === 'lists') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>mrkt/list/"><?=_t( 'Lists' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_87"><a<?=($screen === 'temps') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>mrkt/template/"><?=_t( 'Templates' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_86"><a<?=($screen === 'mrkt') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>mrkt/"><?=_t( 'Campaigns' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_89"><a<?=($screen === 'mrge') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>mrkt/mailmerge/"><?=_t( 'Mail Merge' );?></a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('access_application_screen') && _mf('applications')) : ?>
                        <li><?=_t( 'Application' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_91"><a<?=($screen === 'appl') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>appl/"><?=_t( '(APPL) - Application' );?></a></li>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_92"><a<?=($screen === 'insta') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>appl/inst-attended/"><?=_t( 'Institution Attended' );?></a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if(_he('access_financials') && _mf('financial_module')) : ?>
                        <li><?=_t( 'Financials' );?>
                            <ul>
                            	<?php if(_he('access_general_ledger')) : ?>
                                <li><?=_t( 'General Ledger' );?>
                                    <ul>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_93"><a<?=($screen === 'genl') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>financial/gl-accounts/"><?=_t( 'Account Chart' );?></a></li>
                                		<li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_94"><a<?=($screen === 'jent') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>financial/journal-entries/"><?=_t( 'Journal Entries' );?></a></li>
                                    </ul>
                                </li>
                                <?php endif; ?>
                                
                                <?php if(_he('access_student_accounts')) : ?>
                                <li><?=_t( 'Student Accounts' );?>
                                    <ul>
                                        <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_95"><a<?=($screen === 'btbl') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>financial/billing-table/"><?=_t( 'Billing Tables' );?></a></li>
                                		<li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_96"><a<?=($screen === 'inv') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>financial/"><?=_t( 'Bill Lookup' );?></a></li>
                                    </ul>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <li><?=_t( 'Files' );?>
                            <ul>
                                <li data-jstree='{"icon":"glyphicon glyphicon-file"}' id="shtml_100"><a<?=($screen === 'fm') ? ' class="jstree-clicked"' : '';?> href="<?=get_base_url();?>staff/file-manager/"><?=_t( 'File Manager' );?></a></li>
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