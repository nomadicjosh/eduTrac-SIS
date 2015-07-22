<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Profile View
 *  
 * PHP 5.4+
 *
 * eduTrac ERP(tm) : College Management System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       4.3
 * @package     eduTrac ERP
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
if($app->hook->{'get_option'}('myet_layout') === null) {
    $app->view->extend('_layouts/myet/default.layout');
} else {
    $app->view->extend('_layouts/myet/' . $app->hook->{'get_option'}('myet_layout') . '.layout');
}
$app->view->block('myet');
?>

<div class="col-md-12">
	<div class="separator bottom"></div>
	<div class="separator bottom"></div>

	<h3 class="glyphicons user"><i></i><?=_t( 'My Profile' );?></h3>
	<div class="separator bottom"></div>
	
<div class="widget widget-heading-simple widget-body-white">
	<div class="widget-body">
		<div class="row">
			<div class="col-md-12">
				<form class="margin-none">
					<div class="row innerB">
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'ID' );?></label>
							<input type="text" class="form-control" readonly name="personID" value="<?=_h($profile[0]['personID']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Username' );?></label>
							<input type="text" class="form-control" readonly name="uname" value="<?=_h($profile[0]['uname']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Prefix' );?></label>
							<input type="text" class="form-control" readonly name="prefix" value="<?=_h($profile[0]['prefix']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'First Name' );?></label>
							<input type="text" class="form-control" readonly name="fname" value="<?=_h($profile[0]['fname']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Last Name' );?></label>
							<input type="text" class="form-control" readonly name="lname" value="<?=_h($profile[0]['lname']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Middle Name' );?></label>
							<input type="text" class="form-control" readonly name="mname" value="<?=_h($profile[0]['mname']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Email' );?></label>
							<input type="text" class="form-control" readonly name="email" value="<?=_h($profile[0]['email']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Phone' );?></label>
							<input type="text" class="form-control" readonly name="phone1" value="<?=_h($addr[0]['phone1']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'SSN' );?></label>
							<input type="text" class="form-control" readonly name="ssn" value="<?=(_h($profile[0]['ssn']) > 0 ? _h($profile[0]['ssn']) : '');?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Veteran' );?></label>
							<input type="text" class="form-control" readonly name="veteran" value="<?=_h($profile[0]['Veteran']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Ethnicity' );?></label>
							<input type="text" class="form-control" readonly name="ethnicity" value="<?=_h($profile[0]['ethnicity']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Date of Birth' );?></label>
							<input type="text" class="form-control" readonly name="dog" value="<?=(_h($profile[0]['dob']) != '0000-00-00' ? _h($profile[0]['dob']) : '');?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Gender' );?></label>
							<input type="text" class="form-control" readonly name="gender" value="<?=_h($profile[0]['Gender']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Emergency Contact' );?></label>
							<input type="text" class="form-control" readonly name="emergency_contact" value="<?=_h($profile[0]['emergency_contact']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Emergency Contact\'s Phone' );?></label>
							<input type="text" class="form-control" readonly name="emergency_contact_phone" value="<?=_h($profile[0]['emergency_contact_phone']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Address1' );?></label>
							<input type="text" class="form-control" readonly name="address1" value="<?=_h($addr[0]['address1']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Address2' );?></label>
							<input type="text" class="form-control" readonly name="address2" value="<?=_h($addr[0]['address2']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'City' );?></label>
							<input type="text" class="form-control" readonly name="city" value="<?=_h($addr[0]['city']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'State' );?></label>
							<input type="text" class="form-control" readonly name="state" value="<?=_h($addr[0]['state']);?>" />
						</div>
						<div class="col-md-6">
							<label class="control-label"><?=_t( 'Zip' );?></label>
							<input type="text" class="form-control" readonly name="Zip" value="<?=_h($addr[0]['zip']);?>" />
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

</div>
	</div>
</div>

	
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>