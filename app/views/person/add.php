<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * NAE New Record View
 *  
 * This view is used when creating a new person record
 * via the NAE screen.
 * 
 * @license GPLv3
 * 
 * @since       3.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$message = new \app\src\Messages;
$screen = 'anae';
?>

<script type="text/javascript">
$(document).ready(function() {
$('#uname').keyup(username_check);
});
    
function username_check(){  
var uname = $('#uname').val();
if(uname == "" || uname.length < 4){
$('#uname').css('border', '3px #CCC solid');
$('#tick').hide();
} else {

jQuery.ajax({
   type: "POST",
   url: "<?=url('/');?>nae/usernameCheck/",
   data: 'uname='+ uname,
   cache: false,
   success: function(response){
if(response == 1) {
    $('#uname').css('border', '3px #C33 solid'); 
    $('#tick').hide();
    $('#cross').fadeIn();
    }else{
    $('#uname').css('border', '3px #090 solid');
    $('#cross').hide();
    $('#tick').fadeIn();
         }

}
});
}
}

$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Name & Address' );?></li>
</ul>

<h3><?=_t( 'Name & Address' );?></h3>
<div class="innerLR">
    
    <?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>nae/add/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					<!-- Column -->
					<div class="col-md-6">
					    
					    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Username' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" id="uname" name="uname" value="<?=(isset($_POST['uname'])) ? $_POST['uname'] : '';?>" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Alternate ID' );?> <a href="#altID" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" name="altID" value="<?=_h($nae[0]['altID']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Person Type' );?> <a href="#myModal" data-toggle="modal"><img src="<?=url('/');?>static/common/theme/images/help.png" /></a></label>
                            <div class="col-md-8">
                                <?=person_type_select((isset($_POST['personType'])) ? $_POST['personType'] : '');?>
                            </div>
                        </div>
                        <!-- // Group END -->
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'Prefix' );?></label>
							<div class="col-md-8">
								<select name="prefix" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <option value="Ms"<?=selected('Ms',$_POST['prefix'],false);?>><?=_t( 'Ms.' );?></option>
                                    <option value="Miss"<?=selected('Miss',$_POST['prefix'],false);?>><?=_t( 'Miss.' );?></option>
                                    <option value="Mrs"<?=selected('Mrs',$_POST['prefix'],false);?>><?=_t( 'Mrs.' );?></option>
                                    <option value="Mr"<?=selected('Mr',$_POST['prefix'],false);?>><?=_t( 'Mr.' );?></option>
                                    <option value="Dr"<?=selected('Dr',$_POST['prefix'],false);?>><?=_t( 'Dr.' );?></option>
                                </select>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'First Name' );?></label>
							<div class="col-md-8">
								<input class="form-control" type="text" name="fname" value="<?=(isset($_POST['fname'])) ? $_POST['fname'] : '';?>" required/>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Last Name' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" name="lname" value="<?=(isset($_POST['lname'])) ? $_POST['lname'] : '';?>" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Middle Initial' );?></label>
                            <div class="col-md-2">
                                <input class="form-control" type="text" name="mname" value="<?=(isset($_POST['mname'])) ? $_POST['mname'] : '';?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Address1' );?></label>
							<div class="col-md-8">
								<input class="form-control" type="text" name="address1" value="<?=(isset($_POST['address1'])) ? $_POST['address1'] : '';?>" required/>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Address2' );?></label>
                            <div class="col-md-6">
                                <input class="form-control" type="text" name="address2" value="<?=(isset($_POST['address2'])) ? $_POST['address2'] : '';?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'City' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" name="city" required value="<?=(isset($_POST['city'])) ? $_POST['city'] : '';?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'State' );?></label>
                            <div class="col-md-8">
                                <select name="state" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('state',null,'code','code','name',(isset($_POST['state'])) ? $_POST['state'] : ''); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Zip Code' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" name="zip" value="<?=(isset($_POST['zip'])) ? $_POST['zip'] : '';?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Country' );?></label>
                            <div class="col-md-8">
                                <select name="country" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" >
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('country',null,'iso2','iso2','short_name',(isset($_POST['country'])) ? $_POST['country'] : ''); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
					
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Phone' );?></label>
                            <div class="col-md-6">
                                <input class="form-control" type="text" name="phone" value="<?=(isset($_POST['phone'])) ? $_POST['phone'] : '';?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Preferred Email' );?></label>
                            <div class="col-md-6">
                                <input class="form-control" type="email" name="email" value="<?=(isset($_POST['email'])) ? $_POST['email'] : '';?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Social Security #' );?></label>
                            <div class="col-md-6">
                                <input class="form-control" type="text" name="ssn" value="<?=(isset($_POST['ssn'])) ? $_POST['ssn'] : '';?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Veteran?' );?></label>
                            <div class="col-md-8">
                                <select name="veteran" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="1"<?=selected('1',$_POST['veteran'],false);?>><?=_t( 'Yes' );?></option>
                                    <option value="0"<?=selected('0',$_POST['veteran'],false);?>><?=_t( 'No' );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Ethnicity?' );?></label>
                            <div class="col-md-8">
                                <select name="ethnicity" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" >
                                    <option value="">&nbsp;</option>
                                    <option value="White, Non-Hispanic"<?=selected('White, Non-Hispanic',$_POST['ethnicity'],false);?>><?=_t( 'White, Non-Hispanic' );?></option>
                                    <option value="Black, Non-Hispanic"<?=selected('Black, Non-Hispanic',$_POST['ethnicity'],false);?>><?=_t( 'Black, Non-Hispanic' );?></option>
                                    <option value="Hispanic"<?=selected('Hispanic',$_POST['ethnicity'],false);?>><?=_t( 'Hispanic' );?></option>
                                    <option value="Native American"<?=selected('Native American',$_POST['ethnicity'],false);?>><?=_t( 'Native American' );?></option>
                                    <option value="Native Alaskan"<?=selected('Native Alaskan',$_POST['ethnicity'],false);?>><?=_t( 'Native Alaskan' );?></option>
                                    <option value="Pacific Islander"<?=selected('Pacific Islander',$_POST['ethnicity'],false);?>><?=_t( 'Pacific Islander' );?></option>
                                    <option value="Asian"<?=selected('Asian',$_POST['ethnicity'],false);?>><?=_t( 'Asian' );?></option>
                                    <option value="Indian"<?=selected('Indian',$_POST['ethnicity'],false);?>><?=_t( 'Indian' );?></option>
                                    <option value="Middle Eastern"<?=selected('Middle Eastern',$_POST['ethnicity'],false);?>><?=_t( 'Middle Eastern' );?></option>
                                    <option value="African"<?=selected('African',$_POST['ethnicity'],false);?>><?=_t( 'African' );?></option>
                                    <option value="Mixed Race"<?=selected('Mixed Race',$_POST['ethnicity'],false);?>><?=_t( 'Mixed Race' );?></option>
                                    <option value="Other"<?=selected('Other',$_POST['ethnicity'],false);?>><?=_t( 'Other' );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Date of Birth' );?></label>
                            <div class="col-md-8">
                                <div class="input-group date col-md-8" id="datepicker6">
                                    <input class="form-control" name="dob" type="text" value="<?=(isset($_POST['dob'])) ? $_POST['dob'] : '';?>" />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Gender' );?></label>
                            <div class="col-md-8">
                                <select name="gender" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <option value="M"<?=selected('M',$_POST['gender'],false);?>><?=_t( 'Male' );?></option>
                                    <option value="F"<?=selected('F',$_POST['gender'],false);?>><?=_t( 'Female' );?></option>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Emergency Contact' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" name="emergency_contact" value="<?=(isset($_POST['emergency_contact'])) ? $_POST['emergenct_contact'] : '';?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Emergency Contact Phone' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" name="emergency_contact_phone" value="<?=(isset($_POST['emergency_contact_phone'])) ? $_POST['emergency_contact_phone'] : '';?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Person Role' );?></label>
                            <div class="col-md-8">
                                <select name="roleID" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?=get_perm_roles();?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                         <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-6">
                                <input type="hidden" name="sendemail" value="dontsend" />
                                <input type="checkbox" name="sendemail" value="send" />
                                <?=_t( 'Send username & password to the user' );?>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Approved Date' );?></label>
                            <div class="col-md-6">
                                <input class="form-control" type="text" value="<?=date('D, M d, o',strtotime(date("Y-m-d")));?>" readonly/>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Approved By' );?></label>
                            <div class="col-md-6">
                                <input class="form-control" type="text" value="<?=get_name(get_persondata('personID'));?>" readonly/>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
                <div class="modal fade" id="altID">
					<div class="modal-dialog">
						<div class="modal-content">
							<!-- Modal heading -->
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="modal-title"><?=_t( 'Alternate ID' );?></h3>
							</div>
							<!-- // Modal heading END -->
		                    <div class="modal-body">
                                <p><?=_t( "The unique ID for each person is autogenerated by the system. However, some institutions have their own format for person/student ID's. If this is the case for your institution, you can use this alternate ID field." );?></p>
		                    </div>
		                    <div class="modal-footer">
		                        <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		                    </div>
	                   	</div>
                  	</div>
                </div>
				<div class="modal fade" id="myModal">
					<div class="modal-dialog">
						<div class="modal-content">
							<!-- Modal heading -->
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="modal-title"><?=_t( 'Person Type' );?></h3>
							</div>
							<!-- // Modal heading END -->
		                    <div class="modal-body">
		                        <?=file_get_contents( APP_PATH . 'Info/person-type.txt' );?>
		                    </div>
		                    <div class="modal-footer">
		                        <a href="#" data-dismiss="modal" class="btn btn-primary"><?=_t( 'Cancel' );?></a>
		                    </div>
	                   	</div>
                  	</div>
                </div>
				
				<!-- Form actions -->
				<div class="form-actions">
					<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
	
</div>	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>