<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Student Academic Credits View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$message = new \app\src\Messages;
$stuInfo = new \app\src\Student;
$stuInfo->Load_from_key(_h($stu));
?>

<script type="text/javascript">
	$(".panel").show();
	setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>stu/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Student' );?></a></li>
    <li class="divider"></li>
	<li><?=_t( 'Student Academic Credits (STAC)' );?></li>
</ul>

<div class="innerLR">
	
	<?php $stuInfo->getStuHeader(); ?>
    
    <div class="separator line bottom"></div>
    
    <?=$message->flashMessage();?>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
        
        <!-- Tabs Heading -->
        <div class="tabsbar">
            <ul>
                <li class="glyphicons user"><a href="<?=url('/');?>stu/<?=_h($stu);?>/<?=bm();?>"><i></i> <?=_t( 'Student Profile (SPRO)' );?></a></li>
                <li class="glyphicons package active"><a href="<?=url('/');?>stu/stac/<?=_h($stu);?>/<?=bm();?>" data-toggle="tab"><i></i> <?=_t( 'Student Academic Credits (STAC)' );?></a></li>
                <li class="glyphicons tags tab-stacked"><a href="<?=url('/');?>stu/sttr/<?=_h($stu);?>/<?=bm();?>"><i></i> <?=_t( 'Student Terms (STTR)' );?></a></li>
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
						<th class="text-center"><?=_t( 'Course Name' );?></th>
						<th class="text-center"><?=_t( 'Title' );?></th>
						<th class="text-center"><?=_t( 'Status' );?></th>
						<th class="text-center"><?=_t( 'Credits' );?></th>
						<th class="text-center"><?=_t( 'CEU\'s' );?></th>
						<th class="text-center"><?=_t( 'Term' );?></th>
						<th class="text-center"><?=_t( 'Grade' );?></th>
						<th class="text-center"><?=_t( 'Actions' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
				<?php if($stac != '') : foreach($stac as $k => $v) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_h($v['courseCode']);?></td>
                    <td class="text-center"><?=_h($v['shortTitle']);?></td>
                    <td class="text-center"><?=_h($v['status']);?></td>
                    <td class="text-center"><?=_h($v['attCred']);?></td>
                    <td class="text-center"><?=_h($v['ceu']);?></td>
                    <td class="text-center"><?=_h($v['termCode']);?></td>
                    <td class="text-center"><?=_h($v['grade']);?></td>
                    <td class="text-center">
                    	<div class="btn-group dropup">
                            <button class="btn btn-default btn-xs" type="button"><?=_t( 'Actions' ); ?></button>
                            <button data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle" type="button">
                                <span class="caret"></span>
                                <span class="sr-only"><?=_t( 'Toggle Dropdown' ); ?></span>
                            </button>
                            <ul role="menu" class="dropdown-menu dropup-text pull-right">
                                <li><a href="<?=url('/');?>stu/sacd/<?=_h($v['stuAcadCredID']);?>/<?=bm();?>"><?=_t( 'View (SACD)' ); ?></a></li>
                                <li<?=ae('delete_student');?>><a href="#modal<?=_h($v['stuAcadCredID']);?>" data-toggle="modal"><?=_t( 'Delete' ); ?></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php } endif; ?>
				</tbody>
				<!-- // Table body END -->
				
			</table>
			<!-- // Table END -->
			
			<hr class="separator" />
			
			<!-- Form actions -->
			<div class="form-actions">
				<button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>stu/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
			</div>
			<!-- // Form actions END -->
			
		</div>
	</div>
	
	<!-- // Widget END -->
	<?php if($stac != '') : foreach($stac as $k => $v) { ?>
	<!-- Modal -->
	<div class="modal fade" id="modal<?=_h($v['stuAcadCredID']);?>">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<?php if(_h($v['courseSection']) == '') : ?>
					<h3 class="modal-title"><?=_t( 'Transfer Credit' );?> - <?=_h($v['courseCode']);?></h3>
					<?php else : ?>
				    <h3 class="modal-title"><?=_h($v['courseSection']);?></h3>
				    <?php endif; ?>
				</div>
				<!-- // Modal heading END -->
				<!-- Modal body -->
				<div class="modal-body">
					<p><?= _t( "Are you sure you want to delete this student academic credit? There is no undoing this." );?></p>
				</div>
				<!-- // Modal body END -->
				<!-- Modal footer -->
				<div class="modal-footer">
					<a href="<?=url('/');?>stu/deleteSTAC/<?=_h($v['stuAcadCredID']);?>" class="btn btn-default"><?=_t( 'Delete' );?></a>
					<a href="#" class="btn btn-primary" data-dismiss="modal"><?=_t( 'Cancel' );?></a> 
				</div>
				<!-- // Modal footer END -->
			</div>
		</div>
	</div>
	<!-- // Modal END -->
	<?php } endif; ?>
	
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