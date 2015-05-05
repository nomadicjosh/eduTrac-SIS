<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Cron Jobs View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @license     http://edutrac.7mediaws.org/general/edutrac_erp_commercial_license/ Commercial License
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$cron = new \app\src\Cron;
?>

<script type="text/javascript">
$(function() { // wrap inside the jquery ready() function

	//Attach an onclick handler to each of your buttons that are meant to "approve"
	$('a[class="push-button"]').click(function(){
	
	   //Get the ID of the button that was clicked on
	   var id_of_item_to_push = $(this).attr("id");
	   
	   //Get the url
	   var id_of_url = $(this).attr("url");

	   $.ajax({
	      url: "url=" + id_of_url, //This is the page where you will handle your SQL insert
	      type: "POST",
	      data: "id=" + id_of_item_to_push, //The data your sending to some-page.php
	      success: function(){
	          console.log("Push request was successful.");
	      },
	      error:function(){
	          console.log("Push request failed.");
	      }   
	    });

	});

});
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Cron Jobs' );?></li>
</ul>

<h3><?=_t( 'Cron Jobs' );?></h3>
<div class="innerLR">

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
		<div class="widget-body">
		
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
			
				<!-- Table heading -->
				<thead>
					<tr>
						<th class="text-center"><?=_t( 'Job Name' );?></th>
						<th class="text-center"><?=_t( 'Last Fired' );?></th>
						<th class="text-center"><?=_t( 'Next Execution' );?></th>
						<th class="text-center"><?=_t( 'Interval' );?></th>
						<th class="text-center"><?=_t( 'Action' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
				<?php if($cronjob != '') : foreach($cronjob as $key => $value) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_h($value['name']);?></td>
                    <td class="text-center">
                        <?php if(_h((int)$value['time_last_fired']) == 0) {
                            echo '<font color="#FF8000">Not yet fired</font>'; 
                        } else {
                            echo strftime("%H:%M:%S ",_h($value['time_last_fired']));
                            echo strftime("on %b %d, %Y",_h($value['time_last_fired']));
                        } ?>
                    </td>
                    <td class="text-center">
                        <?php
                            echo strftime("%H:%M:%S ",_h($value['fire_time']));
                            echo strftime("%b %d, %Y",_h($value['fire_time']));
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                            $time_interval = $cron->time_unit(_h((int)$value['time_interval']));
                            echo _h((int)$time_interval[0]) . ' ' . $time_interval[1];
                        ?>
                    </td>
                    <td class="text-center">
                    	<div class="btn-group dropup">
                            <button class="btn btn-default btn-xs" type="button"><?=_t( 'Actions' ); ?></button>
                            <button data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle" type="button">
                                <span class="caret"></span>
                                <span class="sr-only"><?=_t( 'Toggle Dropdown' ); ?></span>
                            </button>
                            <ul role="menu" class="dropdown-menu dropup-text pull-right">
                                <li><a href="<?=url('/');?>cron/<?=_h($value['id']);?>/<?=bm();?>"><?=_t( 'View' ); ?></a></li>
                                <li><a class="push-button" id="<?=_h($value['id']);?>" href="#" url="<?=_h($v['scriptpath']);?>"><?=_t( 'Push' ); ?></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php } endif; ?>
					
				</tbody>
				<!-- // Table body END -->
				
			</table>
			<!-- // Table END -->
			
		</div>
	</div>
	<div class="separator bottom"></div>
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>