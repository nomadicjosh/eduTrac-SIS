<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * myetSIS View
 *  
 * @license GPLv3
 * 
 * @since       4.3
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/myet/' . _h(get_option('myet_layout')) . '.layout');
$app->view->block('myet');
?>

<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
	selector: "textarea",
	plugins: [
		"advlist autolink lists link image charmap print preview anchor",
		"searchreplace visualblocks code fullscreen",
		"insertdatetime media table contextmenu paste"
	],
	toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
	autosave_ask_before_unload: false
});
</script>

		<div class="col-md-12">
			
			<div class="separator bottom"></div>
			<div class="separator bottom"></div>
		
			<div class="widget widget-heading-simple widget-body-white">
				<div class="widget-body">
					<div class="row">	
						<div class="col-md-12">
							<h5 class="strong"><?=get_met_welcome_message_title();?></h5>
							<div class="separator bottom"></div>
							<?=_escape(the_myetsis_welcome_message());?>
							<p<?=ae('edit_myet_welcome_message');?> class="margin-none strong">
								<a href="#welcome" data-toggle="modal" class="glyphicons single edit"><i></i><?=_t( 'Edit' );?></a>
							</p>
						</div>
					</div>
				</div>
			</div>
			<?php if(function_exists('myet_module')) : ?>
			<?php if(metNewsExist()) : ?>
			<h3 class="glyphicons chat"><i></i><?=_t( 'News &amp; Announcements' );?></h3>
			<div class="separator bottom"></div>
			
			<div class="row">
				<?php foreach(wNews() as $k => $v) : ?>
				<div class="col-md-6">
					<div class="widget widget-heading-simple widget-body-white">
						<div class="widget-body">
							<h5 class="strong text-uppercase"><?=_h($v['news_title']);?></h5>
							<span class="glyphicons single regular user"><i></i> <?=_t( 'by');?> <?=getUserValue(_h($v['addedBy']),'uname');?></span>
							<span class="glyphicons single regular calendar"><i></i> <?=date('D, M d, o',strtotime(_h($v['addDate'])));?></span>
							<div class="separator bottom"></div>
							<?=_escape(safe_truncate($v['news_content'],125,' . . .'));?>
							<p class="margin-none strong"><a href="<?=get_base_url();?>news/<?=_h($v['news_slug']);?>/"><?=_t( 'read more' );?></a></p>
						</div>
					</div>
				</div>
				<?php endforeach; endif; ?>
			</div>
            <?php endif; ?>
		</div>
	</div>
</div>

	<!-- Modal -->
	<div class="modal fade" id="welcome">
		<form class="form-horizontal margin-none" action="<?=get_base_url();?>message/" id="validateSubmitForm" method="post" autocomplete="off">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal heading -->
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3 class="modal-title"><?=_t( 'Welcome Message' );?></h3>
				</div>
				<!-- // Modal heading END -->
				<!-- Modal body -->
				<div class="modal-body">
					<!-- Group -->
		            <div class="form-group">
		                <div class="col-md-12">
		                    <textarea name="myet_welcome_message" class="form-control" rows="5"><?=_escape(get_option('myet_welcome_message'));?></textarea>
		                </div>
		            </div>
		            <!-- // Group END -->
				</div>
				<!-- // Modal body END -->
				<!-- Modal footer -->
				<div class="modal-footer">
		            <button type="submit" class="btn btn-default"><?=_t( 'Save' );?></button>
					<a href="#" class="btn btn-primary" data-dismiss="modal"><?=_t( 'Cancel' );?></a>
				</div>
				<!-- // Modal footer END -->
			</div>
		</div>
		</form>
	</div>
	<!-- // Modal END -->
	
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>