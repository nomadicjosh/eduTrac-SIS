<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Update View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       5.0.3
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$update = new \app\src\Update('http://edutrac.s3.amazonaws.com', RELEASE_TAG);
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Automatic Update' );?></li>
</ul>

<h3><?=_t( 'Automatic Update' );?></h3>
<div class="innerLR">
	
	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-white">
		
		<div class="widget-body">
			
				<!-- Row -->
				<div class="row">

					<!-- Alert -->
                    <div class="alert alert-info" style="color: #666666;">
                        <p style="font-size:1.5em;"><?=_t( 'Current Version:' );?> <?=RELEASE_TAG;?></p>
                        <p><?=_t( 'Checking for updates... ') . 'http://edutrac.s3.amazonaws.com . . .'?></p>
                        <?php
                        if($update->check_for_updates()) {
                            echo _t('Updates found (version <font color="green">') . $update->server_version.'</font>)<br /><br />';
                            echo '<p style="font-size:1.5em;">' . _t( 'Building file list :') . '<p>';
                            echo $update->print_updated_files_list();
                            
                            echo '<p style="font-size:1.5em;">' . _t( 'Checking for write permissions :' ) . '<p>';
                            if($update->check_if_are_writable()) {
                                echo _t( "All files are writable." ) . "<br>";
                            } else {
                                echo '<font color="red">' . _t( "Some files are not writable." ) . '</font><br>';
                            }
                            foreach ($update->writable_files as $file => $value) {
                                echo $file." = ".$value."<br>";
                            }
                            echo '<p style="font-size:1.5em;">' . _t( 'Starting to update files... ' ) . '<p>';
                            if($update->update_files() === true) {
                                echo "<br>" . _t( 'All the files where succesfuly updated. ') . "<br>";
                            } else {
                                echo '<br><font color="red">' . _t( 'Some errors ocured while updating the files. Please inform your IT department.' ) . '</font><br>';
                            }
                        } else {
                            echo "<br>" . _t( 'You are using the latest release of eduTrac SIS.' ) . "<br>";
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