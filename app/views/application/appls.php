<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * myetSIS Applications List View
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
?>

<div class="col-md-12">
	<div class="separator bottom"></div>
	<div class="separator bottom"></div>

	<h3 class="glyphicons log_book"><i></i><?=_t( 'My Applications' );?></h3>
	<div class="separator bottom"></div>

<!-- Widget -->
<div class="widget widget-heading-simple widget-body-gray">
	<div class="widget-body">
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-primary">
			
				<!-- Table heading -->
				<thead>
					<tr>
						<th class="text-center"><?=_t( 'Academic Program' );?></th>
                        <th class="text-center"><?=_t( 'Start Term' );?></th>
                        <th class="text-center"><?=_t( 'Status' );?></th>
						<th class="text-center"><?=_t( 'Actions' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
				<?php if($appls != '') : foreach($appls as $k => $v) { ?>
                <tr class="gradeX">
                    <td class="text-center"><?=_h($v['acadProgCode']);?></td>
                    <td class="text-center"><?=_h($v['startTerm']);?></td>
                    <td class="text-center"><?=_h($v['applStatus']);?></td>
                    <td class="text-center">
						<a href="#appl-<?=_h($v['id']);?>" data-toggle="modal" class="glyphicons single eye_open"><i></i><?=_t( 'View' );?></a>
						<!-- Modal -->
						<div class="modal fade" id="appl-<?=_h($v['id']);?>">
							<div class="modal-dialog">
								<div class="modal-content">
									<!-- Modal heading -->
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h3 class="modal-title"><?=_h($v['acadProgCode']);?></h3>
									</div>
									<!-- // Modal heading END -->
									<!-- Modal body -->
									<div class="modal-body">
										<!-- Group -->
							            <div class="row innerB">
							                <div class="col-md-6">
												<label class="control-label-alt"><?=_t( 'Start Term' );?></label>
												<input type="text" class="form-control" readonly value="<?=_h($v['startTerm']);?>" />
											</div>
											<div class="col-md-6">
												<label class="control-label-alt"><?=_t( 'Academic Program' );?></label>
												<input type="text" class="form-control" readonly value="<?=_h($v['acadProgCode']);?>" />
											</div>
											<div class="col-md-6">
												<label class="control-label-alt"><?=_t( 'Admission Status' );?></label>
												<input type="text" class="form-control" readonly value="<?=_h($v['admitStatus']);?>" />
											</div>
											<div class="col-md-6  psat-verbal">
												<label class="control-label-alt"><?=_t( 'PSAT Verbal' );?></label>
												<input type="text" class="form-control" readonly value="<?=_h($v['PSAT_Verbal']);?>" />
											</div>
											<div class="col-md-6  psat-math">
												<label class="control-label-alt"><?=_t( 'PSAT Math' );?></label>
												<input type="text" class="form-control" readonly value="<?=_h($v['PSAT_Math']);?>" />
											</div>
											<div class="col-md-6 sat-verbal">
												<label class="control-label-alt"><?=_t( 'SAT Verbal' );?></label>
												<input type="text" class="form-control" readonly value="<?=_h($v['SAT_Verbal']);?>" />
											</div>
											<div class="col-md-6 sat-math">
												<label class="control-label-alt"><?=_t( 'SAT Math' );?></label>
												<input type="text" class="form-control" readonly value="<?=_h($v['SAT_Math']);?>" />
											</div>
											<div class="col-md-6 act-english">
												<label class="control-label-alt"><?=_t( 'ACT English' );?></label>
												<input type="text" class="form-control" readonly value="<?=_h($v['ACT_English']);?>" />
											</div>
											<div class="col-md-6 act-math">
												<label class="control-label-alt"><?=_t( 'ACT Math' );?></label>
												<input type="text" class="form-control" readonly value="<?=_h($v['ACT_Math']);?>" />
											</div>
                                            <?php 
                                            /**
                                             * Prints custom fields or any type of data
                                             * when triggered.
                                             * 
                                             * @since 6.1.10
                                             * @param array $v Application data object.
                                             */
                                            $app->hook->do_action('my_appl_custom_field', $v); 
                                            ?>
											<div class="col-md-6">
												<label class="control-label-alt"><?=_t( 'Comments' );?></label>
												<textarea class="form-control" readonly rows="5"><?=_h($v['appl_comments']);?></textarea>
											</div>
							            </div>
							            <!-- // Group END -->
									</div>
									<!-- // Modal body END -->
									<!-- Modal footer -->
									<div class="modal-footer">
										<a href="#" class="btn btn-primary" data-dismiss="modal"><?=_t( 'Cancel' );?></a>
									</div>
									<!-- // Modal footer END -->
								</div>
							</div>
						</div>
						<!-- // Modal END -->
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