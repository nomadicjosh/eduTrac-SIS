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
if($app->hook->{'get_option'}('myet_theme') === null) {
    $app->view->extend('_layouts/myet/default');
} else {
    $app->view->extend('_layouts/myet/' . $app->hook->{'get_option'}('myet_theme'));
}
$app->view->block('myet');
?>

<div class="col-md-12">

	<h3 class="glyphicons pin_flag"><i></i><?=_t( 'Registered Terms' );?></h3>
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
					<th class="text-center"><?=_t( 'Courses' );?></th>
					<th class="text-center"><?=_t( 'Schedule' );?></th>
				</tr>
			</thead>
			<!-- // Table heading END -->
			
			<!-- Table body -->
			<tbody>
			<?php if($term!= '') : foreach($term as $k => $v) { ?>
            <tr class="gradeX">
                <td class="text-center"><?=_h($v['termCode']);?></td>
                <td class="text-center"><?=_h($v['Courses']);?></td>
                <td class="text-center">
                	<a href="<?=url('/');?>stu/schedule/<?=_h($v['termCode']);?>/" title="View" class="btn btn-primary"><i class="fa fa-calendar"></i></a>
				</td>
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