<?php
if (! defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * Core Update View
 *
 * @license GPLv3
 *         
 * @since 6.2.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$screen = 'update';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=get_base_url();?>dashboard/<?=bm();?>"
		class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'eduTrac SIS Update' );?></li>
</ul>

<h3><?=_t( 'eduTrac SIS Update' );?></h3>
<div class="innerLR">

	<?php jstree_sidebar_menu($screen); ?>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-white <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">

		<div class="widget-body">

			<!-- Row -->
			<div class="row">

				<!-- Alert -->
				<div class="alert alert-info" style="color: #666666;">
                        <?php
                        $error = \app\src\ReleaseAPI::inst()->getServerStatus();
                        if (is_et_exception($error)) {
                            echo $error->getMessage();
                        } else {
                            $update = new \VisualAppeal\AutoUpdate(rtrim($app->config('file.savepath'), '/'), BASE_PATH, 1800);
                            $update->setCurrentVersion(RELEASE_TAG);
                            $update->setUpdateUrl('http://edutrac.s3.amazonaws.com/core/1.1/update-check');
                            
                            // Optional:
                            $update->addLogHandler(new Monolog\Handler\StreamHandler(APP_PATH . 'tmp' . DS . 'logs' . DS . 'core-update.' . date('m-d-Y') . '.txt'));
                            $update->setCache(new Desarrolla2\Cache\Adapter\File(APP_PATH . 'tmp/cache'), 3600);
                            
                            $cacheFile = APP_PATH . 'tmp/cache/__update-versions.php.cache';
                            
                            echo '<p>' . sprintf(_t('Last checked on %s @ %s'), date('M d, Y', file_mod_time($cacheFile)), date('h:i A', file_mod_time($cacheFile)));
                            
                            if ($update->checkUpdate() !== false) {
                                
                                if ($update->newVersionAvailable()) {
                                    // Install new update
                                    echo '<p>' . sprintf(_t('New Release: r%s'), $update->getLatestVersion()) . '</p>';
                                    echo '<p>' . _t('Installing Updates: ') . '</p>';
                                    echo '<pre>';
                                    var_dump(array_map(function ($version) {
                                        return (string) $version;
                                    }, $update->getVersionsToUpdate()));
                                    echo '</pre>';
                                    
                                    $result = $update->update();
                                    if ($result === true) {
                                        echo '<p>' . _t('Update successful') . '</p>';
                                    } else {
                                        echo '<p>' . sprintf(_t('Update failed: %s!'), $result) . '</p>';
                                        
                                        if ($result = \VisualAppeal\AutoUpdate::ERROR_SIMULATE) {
                                            echo '<pre>';
                                            var_dump($update->getSimulationResults());
                                            echo '</pre>';
                                        }
                                    }
                                } else {
                                    echo sprintf('<p>' . _t('You currently have the latest release of eduTrac SIS installed: r%s'), RELEASE_TAG . '</p>');
                                }
                            } else {
                                echo '<p>' . _t('Could not check for updates! See log file for details.') . '</p>';
                            }
                        }
                        ?>
					</div>
				<!-- // Alert END -->

			</div>

		</div>

	</div>

</div>


</div>
<!-- // Content END -->
<?php $app->view->stop(); ?>