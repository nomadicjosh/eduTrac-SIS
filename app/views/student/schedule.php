<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * myetSIS Student Schedule View
 *  
 * @license GPLv3
 * 
 * @since       4.3
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/myetsis/' . _escape(get_option('myetsis_layout')) . '.layout');
$app->view->block('myetsis');
$stu = get_student(get_persondata('personID'));
?>

<div class="col-md-12">
    
    <?php get_stu_header($stu->stuID); ?>
    
    <div class="separator line bottom"></div>

	<h3 class="glyphicons calendar"><i></i><?=_escape($schedule[0]['termCode']);?> <?=_t( 'Schedule' );?></h3>
	<div class="separator bottom"></div>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
		<div class="widget-body">
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
			
				<!-- Table heading -->
				<thead>
					<tr>
						<th class="text-center"><?=_t( 'Course Code' );?></th>
						<th class="text-center"><?=_t( 'Title' );?></th>
						<th class="text-center"><?=_t( 'Building' );?></th>
                        <th class="text-center"><?=_t( 'Room' );?></th>
                        <th class="text-center"><?=_t( 'Day(s) of the Week' );?></th>
                        <th class="text-center"><?=_t( 'Time' );?></th>
						<th class="text-center"><?=_t( 'Instructor' );?></th>
                        <?php if($schedule !== null) : ?>
                        <?php if(function_exists('gradebook_module') && gradebookExist(_escape($schedule[0]['courseSecID']))) : ?>
						<th class="text-center"><?=_t( 'Grades' );?></th>
                        <?php endif; endif; ?>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
				<?php if($schedule != '') : foreach($schedule as $k => $v) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_escape($v['courseSecCode']);?></td>
                    <td class="text-center"><?=_escape($v['secShortTitle']);?></td>
                    <td class="text-center"><?=_escape($v['buildingName']);?></td>
                    <td class="text-center"><?=_escape($v['roomNumber']);?></td>
                    <td class="text-center"><?=_escape($v['dotw']);?></td>
                    <td class="text-center"><?=_escape($v['startTime'].' To '.$v['endTime']);?></td>
                    <td class="text-center"><?=get_name(_escape($v['facID']));?></td>
                    <?php if(function_exists('gradebook_module')) : ?>
                    <?php if(gb_assignment(_escape($v['courseSecID'])) > 0) : ?>
                    <td class="text-center">
                    	<a href="<?=get_base_url();?>stu/grades/<?=_escape($v['courseSecID']);?>/" title="Grades" class="btn btn-primary"><i class="fa fa-book"></i></a>
					</td>
                    <?php else : ?>
                    <td class="text-center"></td>
                    <?php endif; ?>
                    <?php endif; ?>
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