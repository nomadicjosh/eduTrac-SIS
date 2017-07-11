<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * myetSIS Final Grades View
 *  
 * @license GPLv3
 * 
 * @since       4.3
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/myetsis/' . _h(get_option('myetsis_layout')) . '.layout');
$app->view->block('myetsis');
$stu = get_student(get_persondata('personID'));
?>

<div class="col-md-12">
    
    <?php get_stu_header($stu->stuID); ?>
    
    <div class="separator line bottom"></div>

	<h3 class="glyphicons font"><i></i><?=_t( 'Final Grades' );?></h3>
	<div class="separator bottom"></div>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
		<div class="widget-body">
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
			
				<!-- Table heading -->
				<thead>
					<tr>
						<th class="text-center"><?=_t( 'Term' );?></th>
						<th class="text-center"><?=_t( 'Course Section' );?></th>
						<th class="text-center"><?=_t( 'Title' );?></th>
                        <th class="text-center"><?=_t( 'Grade' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
				<?php if($grades != '') : foreach($grades as $k => $v) { ?>
                <tr class="gradeX">
                	<td class="text-center"><?=_h($v['termCode']);?></td>
                    <td class="text-center"><?=_h($v['courseSecCode']);?></td>
                    <td class="text-center"><?=_h($v['secShortTitle']);?></td>
                    <td class="text-center"><?=_h($v['grade']);?></td>
                </tr>
				<?php } endif; ?>
				</tbody>
				<!-- // Table body END -->
				
			</table>
			<!-- // Table END -->
		</div>
	</div>

</div>
	</div>
</div>

	
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>