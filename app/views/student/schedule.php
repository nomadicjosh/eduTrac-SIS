<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * myeduTrac Student Schedule View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       4.3
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
if(get_option('myet_layout') === null) {
    $app->view->extend('_layouts/myet/default.layout');
} else {
    $app->view->extend('_layouts/myet/' . get_option('myet_layout') . '.layout');
}
$app->view->block('myet');
$stuInfo = new \app\src\Student;
$stuInfo->Load_from_key(get_persondata('personID'));
?>

<div class="col-md-12">
    
    <?php $stuInfo->getStuHeader(); ?>
    
    <div class="separator line bottom"></div>

	<h3 class="glyphicons calendar"><i></i><?=_h($schedule[0]['termCode']);?> <?=_t( 'Schedule' );?></h3>
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
                        <?php if(function_exists('gradebook_module') && gradebookExist(_h($schedule[0]['courseSecID']))) : ?>
						<th class="text-center"><?=_t( 'Grades' );?></th>
                        <?php endif; endif; ?>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
				<?php if($schedule != '') : foreach($schedule as $k => $v) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_h($v['courseSecCode']);?></td>
                    <td class="text-center"><?=_h($v['secShortTitle']);?></td>
                    <td class="text-center"><?=_h($v['buildingName']);?></td>
                    <td class="text-center"><?=_h($v['roomNumber']);?></td>
                    <td class="text-center"><?=_h($v['dotw']);?></td>
                    <td class="text-center"><?=_h($v['startTime'].' To '.$v['endTime']);?></td>
                    <td class="text-center"><?=get_name(_h($v['facID']));?></td>
                    <?php if(function_exists('gradebook_module') && gradebookExist(_h($v['courseSecID']))) : ?>
                    <td class="text-center">
                    	<a href="<?=url('/');?>stu/grades/<?=_h($v['courseSecID']);?>/" title="Grades" class="btn btn-primary"><i class="fa fa-book"></i></a>
					</td>
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