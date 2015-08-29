<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Email Templates View
 * 
 * This view is used to render the email templates screen.
 *  
 * @license GPLv3
 * 
 * @since       6.0.00
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$screen = 'setting';
?>

<script src="//tinymce.cachefly.net/4.2/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
	selector: "textarea",
	plugins: [
		"advlist autolink lists link image charmap print preview anchor",
		"searchreplace visualblocks code fullscreen",
		"insertdatetime media table contextmenu paste"
	],
	toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | gplaceholder | pplaceholder | splaceholder | eplaceholder",
    autosave_ask_before_unload: false,
    relative_urls: false,
    remove_script_host: false,
    file_picker_callback : elFinderBrowser,
    setup: function(editor) {
        editor.addButton('gplaceholder', {
            type: 'menubutton',
            text: 'General Placeholder',
            icon: false,
            menu: [
                {text: 'Administrator Email', onclick: function() {editor.insertContent('#adminemail#');}},
                {text: 'myeduTrac URL', onclick: function() {editor.insertContent('#url#');}},
                {text: 'HelpDesk URL', onclick: function() {editor.insertContent('#helpdesk#');}},
                {text: 'Current Term', onclick: function() {editor.insertContent('#currentterm#');}},
                {text: 'Institution Name', onclick: function() {editor.insertContent('#instname#');}},
                {text: 'Mailing Address', onclick: function() {editor.insertContent('#mailaddr#');}}
            ]
        });
        editor.addButton('pplaceholder', {
            type: 'menubutton',
            text: 'Person Placeholder',
            icon: false,
            menu: [
                {text: 'Username', onclick: function() {editor.insertContent('#uname#');}},
                {text: 'Person ID', onclick: function() {editor.insertContent('#id#');}},
                {text: 'Alternate ID', onclick: function() {editor.insertContent('#altID#');}},
                {text: 'Password', onclick: function() {editor.insertContent('#password#');}},
                {text: 'Full Name', onclick: function() {editor.insertContent('#name#');}},
                {text: 'First Name', onclick: function() {editor.insertContent('#fname#');}},
                {text: 'Last Name', onclick: function() {editor.insertContent('#lname#');}},
                {text: 'Address 1', onclick: function() {editor.insertContent('#address1#');}},
                {text: 'Address 2', onclick: function() {editor.insertContent('#address2#');}},
                {text: 'City', onclick: function() {editor.insertContent('#city#');}},
                {text: 'State', onclick: function() {editor.insertContent('#state#');}},
                {text: 'Zip', onclick: function() {editor.insertContent('#zip#');}},
                {text: 'Country', onclick: function() {editor.insertContent('#country#');}},
                {text: 'Phone #', onclick: function() {editor.insertContent('#phone#');}},
                {text: 'Email', onclick: function() {editor.insertContent('#email#');}}
            ]
        });
        editor.addButton('splaceholder', {
            type: 'menubutton',
            text: 'Student Placeholder',
            icon: false,
            menu: [
                {text: 'Student ID', onclick: function() {editor.insertContent('#id#');}},
                {text: 'Student Program', onclick: function() {editor.insertContent('#sacp#');}},
                {text: 'Degree', onclick: function() {editor.insertContent('#degree#');}},
                {text: 'Academic Level', onclick: function() {editor.insertContent('#acadlevel#');}},
                {text: 'Start Term', onclick: function() {editor.insertContent('#startterm#');}}
            ]
        });
        editor.addButton('eplaceholder', {
            type: 'menubutton',
            text: 'Event Placeholder',
            icon: false,
            menu: [
                {text: 'Title', onclick: function() {editor.insertContent('#title#');}},
                {text: 'Description', onclick: function() {editor.insertContent('#description#');}},
                {text: 'Request Type', onclick: function() {editor.insertContent('#request_type#');}},
                {text: 'Category', onclick: function() {editor.insertContent('#category#');}},
                {text: 'Room', onclick: function() {editor.insertContent('#room#');}},
                {text: 'Start Date', onclick: function() {editor.insertContent('#firstday#');}},
                {text: 'End Date', onclick: function() {editor.insertContent('#lastday#');}},
                {text: 'Start Time', onclick: function() {editor.insertContent('#sTime#');}},
                {text: 'End Time', onclick: function() {editor.insertContent('#eTime#');}},
                {text: 'Repeat', onclick: function() {editor.insertContent('#repeat#');}},
                {text: 'Occurrence', onclick: function() {editor.insertContent('#occurrence#');}}
            ]
        });
    },
	autosave_ask_before_unload: false,
    height:400
});
function elFinderBrowser (callback, value, meta) {
  tinymce.activeEditor.windowManager.open({
    file: '<?=url('/');?>staff/elfinder/',// use an absolute path!
    title: 'elFinder 2.0',
    width: 900,  
    height: 450,
    resizable: 'yes'
  }, {
    oninsert: function (file) {
    // Provide file and text for the link dialog
        if (meta.filetype == 'file') {
//            callback('mypage.html', {text: 'My text'});
            callback(file.url);
        }

        // Provide image and alt text for the image dialog
        if (meta.filetype == 'image') {
//            callback('myimage.jpg', {alt: 'My alt text'});
            callback(file.url);
        }

        // Provide alternative source and posted for the media dialog
        if (meta.filetype == 'media') {
//            callback('movie.mp4', {source2: 'alt.ogg', poster: 'image.jpg'});
            callback(file.url);
        }
    }
  });
  return false;
}
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Email Templates' );?></li>
</ul>

<h3><?=_t( 'Email Templates' );?></h3>
<div class="innerLR">
    
    <?php jstree_sidebar_menu($screen); ?>
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
            
            <!-- Tabs Heading -->
            <div class="tabsbar">
                <ul>
                    <li<?= hl('general_settings'); ?> class="glyphicons user chevron-left"><a href="<?=url('/');?>setting/"><i></i> <?=_t( 'General' );?></a></li>
                    <li<?= hl('registration_settings'); ?> class="glyphicons lock"><a href="<?=url('/');?>registration/"><i></i> <?=_t( 'Registration' );?></a></li>
                    <li<?= hl('email_settings'); ?> class="glyphicons inbox tab-stacked"><a href="<?=url('/');?>email/"><i></i> <?=_t( 'Email' );?></a></li>
                    <li<?= hl('email_settings'); ?> class="glyphicons show_lines tab-stacked active"><a href="<?=url('/');?>templates/" data-toggle="tab"><i></i> <span><?=_t( 'Email Templates' );?></span></a></li>
                </ul>
            </div>
            <!-- // Tabs Heading END -->
			
			<div class="widget-body">
			
				<!-- Table -->
                <table class="table table-striped table-bordered table-condensed table-primary">

                    <!-- Table heading -->
                    <thead>
                        <tr>
                            <th class="text-center"><?=_t( 'Template Name' );?></th>
                            <th class="text-center"><?=_t( 'Description' );?></th>
                            <th class="text-center"><?=_t( 'Actions' );?></th>
                        </tr>
                    </thead>
                    <!-- // Table heading END -->

                    <!-- Table body -->
                    <tbody>
                    <tr class="gradeX">
                        <td class="text-center"><?=_t( 'Change of Address' );?></td>
                        <td class="text-center"><?=_t( 'This email template is used when someone fills out and submits the change of address form.' );?></td>
                        <td class="text-center">
                            <a href="#coa" data-toggle="modal" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                        </td>
                    </tr>
                    <tr class="gradeX">
                        <td class="text-center"><?=_t( 'Reset Password' );?></td>
                        <td class="text-center"><?=_t( 'This email template is used when the reset password button is pressed on the NAE screen.' );?></td>
                        <td class="text-center">
                            <a href="#password" data-toggle="modal" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                        </td>
                    </tr>
                    <tr class="gradeX">
                        <td class="text-center"><?=_t( 'Room Request Text' );?></td>
                        <td class="text-center"><?=_t( 'This email template is used when the room request form is filled out and submitted.' );?></td>
                        <td class="text-center">
                            <a href="#rrt" data-toggle="modal" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                        </td>
                    </tr>
                    <tr class="gradeX">
                        <td class="text-center"><?=_t( 'Room Request Confirmation' );?></td>
                        <td class="text-center"><?=_t( 'This email template is used when room requests have been approved.' );?></td>
                        <td class="text-center">
                            <a href="#rconfirm" data-toggle="modal" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                        </td>
                    </tr>
                    <tr class="gradeX">
                        <td class="text-center"><?=_t( 'Acceptance Letter' );?></td>
                        <td class="text-center"><?=_t( 'This email template is used when applicant has been accepted and moved to stu.' );?></td>
                        <td class="text-center">
                            <a href="#applLetter" data-toggle="modal" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                        </td>
                    </tr>
                    <tr class="gradeX">
                        <td class="text-center"><?=_t( 'Login Details' );?></td>
                        <td class="text-center"><?=_t( 'This email template is used when a new person record is created and the "Send username & password to user" is checked.' );?></td>
                        <td class="text-center">
                            <a href="#login" data-toggle="modal" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                        </td>
                    </tr>
                    <tr class="gradeX">
                        <td class="text-center"><?=_t( 'Update Username' );?></td>
                        <td class="text-center"><?=_t( 'This email template is used when the username on the application form has been updated.' );?></td>
                        <td class="text-center">
                            <a href="#uname" data-toggle="modal" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                        </td>
                    </tr>
                    </tbody>
                    <!-- // Table body END -->

                </table>
                <!-- // Table END -->
				
			</div>
		</div>
		<!-- // Widget END -->
	
	<div class="modal fade" id="coa">
		<div class="modal-dialog modal-fullscreen">
			<div class="modal-content">
                <form class="form-horizontal margin-none" action="<?=url('/');?>templates/" id="validateSubmitForm" method="post" autocomplete="off">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Change of Address Email Template' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
                    <textarea id="coa" class="col-md-8 form-control" name="coa_form_text" rows="10"><?=_h(get_option('coa_form_text'));?></textarea>
		        </div>
		        <div class="modal-footer">
                    <button type="submit" class="btn btn-icon btn-default"><i></i><?=_t( 'Update' );?></button>
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
                </form>
	       	</div>
      	</div>
    </div>
    <div class="modal fade" id="password">
		<div class="modal-dialog modal-fullscreen">
			<div class="modal-content">
                <form class="form-horizontal margin-none" action="<?=url('/');?>templates/" id="validateSubmitForm" method="post" autocomplete="off">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Reset Password Email Template' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
                    <textarea id="password" class="col-md-8 form-control" name="reset_password_text" rows="10"><?=_h(get_option('reset_password_text'));?></textarea>
		        </div>
		        <div class="modal-footer">
                    <button type="submit" class="btn btn-icon btn-default"><i></i><?=_t( 'Update' );?></button>
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
                </form>
	       	</div>
      	</div>
    </div>
    <div class="modal fade" id="rrt">
		<div class="modal-dialog modal-fullscreen">
			<div class="modal-content">
                <form class="form-horizontal margin-none" action="<?=url('/');?>templates/" id="validateSubmitForm" method="post" autocomplete="off">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Room Request Text' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
                    <textarea id="rrt" class="col-md-8 form-control" name="room_request_text" rows="10"><?=_h(get_option('room_request_text'));?></textarea>
		        </div>
		        <div class="modal-footer">
                    <button type="submit" class="btn btn-icon btn-default"><i></i><?=_t( 'Update' );?></button>
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
                </form>
	       	</div>
      	</div>
    </div>
    <div class="modal fade" id="rconfirm">
		<div class="modal-dialog modal-fullscreen">
			<div class="modal-content">
                <form class="form-horizontal margin-none" action="<?=url('/');?>templates/" id="validateSubmitForm" method="post" autocomplete="off">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Room Request Text' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
                    <textarea id="rcomfirm" class="col-md-8 form-control" name="room_booking_confirmation_text" rows="10"><?=_h(get_option('room_booking_confirmation_text'));?></textarea>
		        </div>
		        <div class="modal-footer">
                    <button type="submit" class="btn btn-icon btn-default"><i></i><?=_t( 'Update' );?></button>
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
                </form>
	       	</div>
      	</div>
    </div>
    <div class="modal fade" id="applLetter">
		<div class="modal-dialog modal-fullscreen">
			<div class="modal-content">
                <form class="form-horizontal margin-none" action="<?=url('/');?>templates/" id="validateSubmitForm" method="post" autocomplete="off">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Acceptance Letter' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
                    <textarea id="applLetter" class="col-md-8 form-control" name="student_acceptance_letter" rows="10"><?=_h(get_option('student_acceptance_letter'));?></textarea>
		        </div>
		        <div class="modal-footer">
                    <button type="submit" class="btn btn-icon btn-default"><i></i><?=_t( 'Update' );?></button>
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
                </form>
	       	</div>
      	</div>
    </div>
    <div class="modal fade" id="login">
		<div class="modal-dialog modal-fullscreen">
			<div class="modal-content">
                <form class="form-horizontal margin-none" action="<?=url('/');?>templates/" id="validateSubmitForm" method="post" autocomplete="off">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'User Login Details' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
                    <textarea id="login" class="col-md-8 form-control" name="person_login_details" rows="10"><?=_h(get_option('person_login_details'));?></textarea>
		        </div>
		        <div class="modal-footer">
                    <button type="submit" class="btn btn-icon btn-default"><i></i><?=_t( 'Update' );?></button>
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
                </form>
	       	</div>
      	</div>
    </div>
    <div class="modal fade" id="uname">
		<div class="modal-dialog modal-fullscreen">
			<div class="modal-content">
                <form class="form-horizontal margin-none" action="<?=url('/');?>templates/" id="validateSubmitForm" method="post" autocomplete="off">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Update Username' );?></h3>
				</div>
				<!-- // Modal heading END -->
		        <div class="modal-body">
                    <textarea id="uname" class="col-md-8 form-control" name="update_username" rows="10"><?=_h(get_option('update_username'));?></textarea>
		        </div>
		        <div class="modal-footer">
                    <button type="submit" class="btn btn-icon btn-default"><i></i><?=_t( 'Update' );?></button>
		            <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		        </div>
                </form>
	       	</div>
      	</div>
    </div>
	
</div>	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>