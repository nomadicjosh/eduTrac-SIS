<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Student Terms Summary View
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
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$stuInfo = new \app\src\Student;
$stuInfo->Load_from_key(_h($stu));
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>student/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Student' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=url('/');?>stu/<?=_h($stu);?>/<?=bm();?>" class="glyphicons user"><i></i> <?=_t( 'Student Profile' );?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'Student Terms Summary (STRS)' );?></li>
</ul>

<div class="innerLR">
    
    <?php $stuInfo->getStuHeader(); ?>
    
    <div class="separator line bottom"></div>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
        
        <!-- Tabs Heading -->
        <div class="tabsbar">
            <ul>
                <li class="glyphicons user"><a href="<?=url('/');?>stu/<?=_h($stu);?>/<?=bm();?>"><i></i> <?=_t( 'Student Profile (SPRO)' );?></a></li>
                <li class="glyphicons package"><a href="<?=url('/');?>stu/stac/<?=_h($stu);?>/<?=bm();?>"><i></i> <?=_t( 'Student Academic Credits (STAC)' );?></a></li>
                <li class="glyphicons tags tab-stacked active"><a href="<?=url('/');?>stu/sttr/<?=_h($stu);?>/<?=bm();?>" data-toggle="tab"><i></i> <?=_t( 'Student Terms (STTR)' );?></a></li>
                <li class="glyphicons disk_remove tab-stacked"><a href="<?=url('/');?>stu/strc/<?=_h($stu);?>/<?=bm();?>"><i></i> <span><?=_t( 'Student Restriction (STRC)' );?></span></a></li>
                <li class="glyphicons history tab-stacked"><a href="<?=url('/');?>stu/shis/<?=_h($stu);?>/<?=bm();?>"><i></i> <span><?=_t( 'Student Hiatus (SHIS)' );?></span></a></li>
            </ul>
        </div>
        <!-- // Tabs Heading END -->
        
		<div class="widget-body">
			
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
			
				<!-- Table heading -->
				<thead>
					<tr>
						<th class="text-center"><?=_t( 'Terms' );?></th>
						<th class="text-center"><?=_t( 'Att Creds' );?></th>
						<th class="text-center"><?=_t( 'Cmpl Creds' );?></th>
						<th class="text-center"><?=_t( 'Grade Pts' );?></th>
						<th class="text-center"><?=_t( 'Acad Load' );?></th>
						<th class="text-center"><?=_t( 'Acad Level' );?></th>
						<th class="text-center"><?=_t( 'GPA' );?></th>
						<th class="text-center"><?=_t( 'Start Date' );?></th>
						<th class="text-center"><?=_t( 'End Date' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
				<?php if($sttr != '') : foreach($sttr as $k => $v) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_h($v['termCode']);?></td>
                    <td class="text-center"><?=_h($v['attCred']);?></td>
                    <td class="text-center"><?=_h($v['compCred']);?></td>
                    <td class="text-center"><?=_h($v['gradePoints']);?></td>
                    <td class="text-center"><?=_h($v['stuLoad']);?></td>
                    <td class="text-center"><?=_h($v['acadLevelCode']);?></td>
                    <td class="text-center"><?=_h($v['termGPA']);?></td>
                    <td class="text-center"><?=_h($v['termStartDate']);?></td>
                    <td class="text-center"><?=_h($v['termEndDate']);?></td>
                </tr>
				<?php } endif; ?>
				</tbody>
				<!-- // Table body END -->
				
			</table>
			<!-- // Table END -->
			
			<hr class="separator" />
                
            <div class="separator line bottom"></div>
            
            
            <!-- Form actions -->
            <div class="form-actions">
                <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>stu/<?=_h($stu);?>/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
            </div>
            <!-- // Form actions END -->
			
		</div>
	</div>
	<div class="separator bottom"></div>
	
	<!-- // Widget END -->
	
	<!-- Modal -->
    <div class="modal fade" id="FERPA">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?=_t( 'Family Educational Rights and Privacy Act (FERPA)' );?></h3>
                </div>
                <!-- // Modal heading END -->
                <!-- Modal body -->
                <div class="modal-body">
                    <p><?=_t('"FERPA gives parents certain rights with respect to their children\'s education records. 
                    These rights transfer to the student when he or she reaches the age of 18 or attends a school beyond 
                    the high school level. Students to whom the rights have transferred are \'eligible students.\'"');?></p>
                    <p><?=_t('If the FERPA restriction states "Yes", then the student has requested that none of their 
                    information be given out without their permission. To get a better understanding of FERPA, visit 
                    the U.S. DOE\'s website @ ') . 
                    '<a href="http://www2.ed.gov/policy/gen/guid/fpco/ferpa/index.html">http://www2.ed.gov/policy/gen/guid/fpco/ferpa/index.html</a>.';?></p>
                </div>
                <!-- // Modal body END -->
                <!-- Modal footer -->
                <div class="modal-footer">
                    <a href="#" class="btn btn-default" data-dismiss="modal"><?=_t( 'Close' );?></a> 
                </div>
                <!-- // Modal footer END -->
            </div>
        </div>
    </div>
    <!-- // Modal END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>