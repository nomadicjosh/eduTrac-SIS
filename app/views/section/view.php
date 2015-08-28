<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * View Section View
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
$screen = 'vsect';
?>

<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#term').live('change', function(event) {
        $.ajax({
            type    : 'POST',
            url     : '<?=url('/');?>sect/secTermLookup/',
            dataType: 'json',
            data    : $('#validateSubmitForm').serialize(),
            cache: false,
            success: function( data ) {
                   for(var id in data) {        
                          $(id).val( data[id] );
                   }
            }
        });
    });
});
$(function(){
    $('#sectionNumber').keyup(function() {
        $('#section').text($(this).val());
    });
});
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=url('/');?>sect/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Section' );?></a></li>
	<li class="divider"></li>
	<li><?=_h($sect[0]['courseSection']);?></li>
</ul>

<h3><?=_h($sect[0]['courseSection']);?></h3>
<div class="innerLR">
	
	<?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen, '', $sect); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>sect/<?=_h($sect[0]['courseSecID']);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
            
            <!-- Tabs Heading -->
            <div class="tabsbar">
                <ul>
                    <li class="glyphicons adjust_alt active"><a href="<?=url('/');?>sect/<?=_h($sect[0]['courseSecID']);?>/<?=bm();?>" data-toggle="tab"><i></i> <?=_h($sect[0]['courseSection']);?></a></li>
                    <li class="glyphicons circle_info"><a href="<?=url('/');?>sect/addnl/<?=_h($sect[0]['courseSecID']);?>/<?=bm();?>"><i></i> <?=_t( 'Additional Info' );?></a></li>
                    <li class="glyphicons more_items tab-stacked"><a href="<?=url('/');?>sect/soff/<?=_h($sect[0]['courseSecID']);?>/<?=bm();?>"><i></i> <?=_t( 'Offering Info' );?></a></li>
                    <li<?=ml('financial_module');?> class="glyphicons money tab-stacked"><a href="<?=url('/');?>sect/sbill/<?=_h($sect[0]['courseSecID']);?>/<?=bm();?>"><i></i> <?=_t( 'Billing Info' );?></a></li>
                    <?php if($sect[0]['roomCode'] != '') : ?>
                    <li<?=ml('booking_module');?> class="glyphicons calendar tab-stacked"><a href="<?=url('/');?>sect/sbook/<?=_h($sect[0]['courseSecID']);?>/<?=bm();?>"><i></i> <span><?=_t( 'Booking Info' );?></span></a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- // Tabs Heading END -->
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					<!-- Column -->
					<div class="col-md-6">
					    
					    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Section' );?></label>
                            <div class="col-md-2">
                                <input type="text" readonly class="form-control col-md-3" value="<?=_h($sect[0]['sectionNumber']);?>" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Term' );?></label>
							<div class="col-md-8">
								<select name="termCode" id="term" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=csid();?> required>
									<option value="">&nbsp;</option>
                            		<?php table_dropdown('term', 'termCode <> "NULL"', 'termCode', 'termCode', 'termName',_h($sect[0]['termCode'])); ?>
                            	</select>
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Start / End Date' );?></label>
                            <div class="col-md-4">
                                <div class="input-group date col-md-12" id="datepicker6">
                                    <input class="form-control"<?=csio();?> id="startDate" name="startDate" type="text" value="<?=_h($sect[0]['startDate']);?>" required />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="input-group date col-md-12" id="datepicker7">
                                    <input class="form-control"<?=csio();?> id="endDate" name="endDate" type="text" value="<?=_h($sect[0]['endDate']);?>" required />
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Department' );?></label>
                            <div class="col-md-8">
                                <select name="deptCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=csid();?> required>
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('department', 'deptCode <> "NULL"', 'deptCode', 'deptCode', 'deptName', _h($sect[0]['deptCode'])); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( "Credits / CEU's" );?></label>
                            <div class="col-md-4">
                                <input type="text" name="minCredit"<?=csio();?> class="form-control" value="<?=_h($sect[0]['minCredit']);?>" required/>
                            </div>
                            
                            <div class="col-md-4">
                                <input type="text" name="ceu" readonly class="form-control" value="<?=_h($sect[0]['ceu']);?>" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Course Level' );?></label>
                            <div class="col-md-8">
                                <?=course_level_select(_h($sect[0]['courseLevelCode']), csid());?>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Academic Level' );?></label>
                            <div class="col-md-8">
                                <?=acad_level_select(_h($sect[0]['acadLevelCode']), csid().' ','required');?>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Short Title' );?></label>
                            <div class="col-md-8">
                                <input type="text" name="secShortTitle"<?=csio();?> class="form-control" value="<?=_h($sect[0]['secShortTitle']);?>" maxlength="25" required/>
                            </div>
                        </div>
                        <!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-6">
					    
					    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Location' );?></label>
                            <div class="col-md-8">
                                <select name="locationCode" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?=csid();?> required>
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('location', 'locationCode <> "NULL"', 'locationCode', 'locationCode', 'locationName', _h($sect[0]['locationCode'])); ?>
                                </select>
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Status / Date' );?></label>
                            <div class="col-md-4">
                                <?=course_sec_status_select(_h($sect[0]['currStatus']), csid());?>
                            </div>
                            
                            <div class="col-md-4">
                                <input class="form-control" type="text" readonly value="<?=date('D, M d, o',strtotime(_h($sect[0]['statusDate'])));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Prerequisites' );?></label>
                            <div class="col-md-8">
                            	<input class="form-control" readonly type="text" value="<?=_h($req[0]['preReq']);?>" />
                                
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Approval Person' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" readonly value="<?=get_name(_h($sect[0]['approvedBy']));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
						
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label"><?=_t( 'Approval Date' );?></label>
							<div class="col-md-8">
								<input type="text" readonly value="<?=date('D, M d, o',strtotime(_h($sect[0]['approvedDate'])));?>" class="form-control" />
							</div>
						</div>
						<!-- // Group END -->
						
						<!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Comments' );?></label>
                            <div class="col-md-8">
                                <textarea class="form-control" name="comment"<?=csio();?> rows="3" data-height="auto"><?=_h($sect[0]['comment']);?></textarea>
                            </div>
                        </div>
                        <!-- // Group END -->
                        <?php if(hasPermission('access_grading_screen')) : ?>
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Final Grades' );?> <a href="<?=url('/');?>sect/fgrade/<?=_h($sect[0]['courseSecID']);?>/<?=bm();?>"><img src="<?=url('/');?>static/common/theme/images/cascade.png" /></a></label>
                            <div class="col-md-2">
                                <input type="text" disabled value="X" class="form-control col-md-1 center" />
                            </div>
                        </div>
                        <!-- // Group END -->
						<?php endif; ?>
					</div>
					<!-- // Column END -->
				</div>
				<!-- // Row END -->
                
                <div class="separator line bottom"><br /></div>
                <hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
				    <input type="hidden" name="courseSecCode" value="<?=_h($sect[0]['courseSecCode']);?>" />
					<button type="submit"<?=csids();?> class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Save' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>sect/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
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