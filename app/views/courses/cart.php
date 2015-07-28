<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * myeduTrac Shopping Cart View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       4.4
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
if($app->hook->{'get_option'}('myet_layout') === null) {
    $app->view->extend('_layouts/myet/default.layout');
} else {
    $app->view->extend('_layouts/myet/' . $app->hook->{'get_option'}('myet_layout') . '.layout');
}
$app->view->block('myet');
$message = new \app\src\Messages();
?>

<script type='text/javascript'> 
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 50000);
</script>

<div class="col-md-12">

	<h3 class="glyphicons search"><i></i><?=_t( 'Shopping Cart' );?></h3>
	<div class="separator bottom"></div>
	
	<?=$message->flashMessage();?>
	
	<!-- Form -->
    <form class="margin-none" action="<?=url('/');?>courses/reg/" id="validateSubmitForm" method="post" autocomplete="off">

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
		<div class="widget-body">
		<!-- Table -->
		<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-primary">
		
			<!-- Table heading -->
			<thead>
				<tr>
					<th class="text-center"><?=_t( 'Course Section' );?></th>
					<th class="text-center"><?=_t( 'Title' );?></th>
					<th class="text-center"><?=_t( 'Meeting Day(s)' );?></th>
                    <th class="text-center"><?=_t( 'Time' );?></th>
                    <th class="text-center"><?=_t( 'Credits' );?></th>
                    <th class="text-center"><?=_t( 'Location' );?></th>
                    <th class="text-center"><?=_t( 'Action' );?></th>
				</tr>
			</thead>
			<!-- // Table heading END -->
			
			<!-- Table body -->
			<tbody>
			<?php if($cart != '') : foreach($cart as $k => $v) { ?>
            <tr class="gradeX">
                <td class="text-center"><?=_h($v['courseSection']);?></td>
                <td class="text-center"><?=_h($v['secShortTitle']);?></td>
                <td class="text-center"><?=_h($v['dotw']);?></td>
                <td class="text-center"><?=_h($v['startTime'].' To '.$v['endTime']);?></td>
                <td class="text-center"><?=_h($v['minCredit']);?></td>
                <td class="text-center"><?=_h($v['locationName']);?></td>
                </td>
                <td class="text-center">
                	<div class="col-md-12">
                    	<select name="regAction[]" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
							<option value="">&nbsp;</option>
                    		<option value="remove"><?=_t( 'Remove' );?></option>
                    		<?php if(removeFromCart($v['courseSecID'])) : ?>
                    		<option value="register"><?=_t( 'Register' );?></option>
                    		<?php endif; ?>
                    	</select>
                    </div> 
                	<input type="hidden" name="courseSecID[]" value="<?=_h($v['courseSecID']);?>" />
                	<input type="hidden" name="courseSecCode[]" value="<?=convertCourseSec(_h($v['courseSecID']));?>" />
                </td>
            </tr>
			<?php } endif; ?>
			</tbody>
			<!-- // Table body END -->
			
		</table>
		<!-- // Table END -->
		<hr class="separator" />
        
        <?php if(shoppingCart()) : ?>	
		<!-- Form actions -->
		<div<?=isRegistrationOpen();?> class="form-actions">
			<input type="hidden" name="termCode" value="<?=_h($cart[0]['termCode']);?>" />
			<button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Register' );?></button>
		</div>
		<!-- // Form actions END -->
		<?php endif; ?>
		</div>
	</div>
	</form>

</div>
	</div>
</div>

	
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>