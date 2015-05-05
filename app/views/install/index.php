<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Install View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       3.0.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/blank');
$app->view->block('blank');
use \app\src\Session;
Session::init();
require(APP_PATH . 'installer-function.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="en-us" xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=_t( 'eduTrac ERP Installer' );?></title>

<!-- JQuery -->
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script> 
<script type="text/javascript" src="<?=url('/');?>static/assets/js/validate.js"></script> 
<script type="text/javascript" src="<?=url('/');?>static/assets/js/hoverIntent.js"></script>
<script type="text/javascript" src="<?=url('/');?>static/assets/js/wizardPro.js"></script>
<link href="<?=url('/');?>static/assets/css/wizardPro.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
	$(document).ready(function(){
		$("#wizard").wizardPro();
	});
</script>
</head>
<body>

<div id="container">
    
    <h2><?=_t( 'eduTrac ERP Installation Wizard' );?></h2>
    
    <div id="wizard" class="wizard-default-style js">
        
        <div class="step_content">
            <?php if($app->req->_get('step') == 1) { ?>
            <!-- Wizard - Step 1 -->
            <div id="step-1" class="step one_column">
                
                <div class="column_one">
                    <h3><?=_t( 'Step 1 - Introduction' );?></h3>
                    
                    <p><strong><?=_t( 'Welcome to the eduTrac Student Information System. Before getting started, we need some information on the database. You will need to know the following items before proceeding.' );?></strong></p>
                    
                    <ol>
                        <li><?=_t( 'Database name' );?></li>
                        <li><?=_t( 'Database username' );?></li>
                        <li><?=_t( 'Database password' );?></li>
                        <li><?=_t( 'Database host' );?></li>
                    </ol>
                    
                    <p><?=_t( "If for some reason the config.php file does not get created after the installer has finished, don't worry. All this does is fill in the database information to the config file. Just go to the root of the install, make a copy of config.sample.php, rename the copy to config.php, and fill in the database information." );?></p>
                    
                    <p><?=_t( "In all likelihood, these items were supplied to you by your Web Host. If you do not have this information, then you will need to contact them before you can continue. If you're all ready..." );?></p>
                    <button class="next" onclick="window.location='<?=url('/');?>install/?step=2'"><span><?=_t( 'Next Step' );?></span></button>
                </div>
                
            </div>
            <!-- </Wizard - Step 1 -->
            <?php } ?>
            
            <?php if($app->req->_get('step') == 2) { ?>
            <!-- Wizard - Step 2 -->
            <div id="step-2" class="step two_column">
                
                <div class="column_one">
                    <h3><?=_t( 'Step 2 - Environment Test' );?></h3>

                    <p><?=_t( "If your system fails any of the tests to the right, then you need to correct them, refresh your browser to make sure all have a green 'ok' or an orange 'warning' and then proceed with the install. If you proceed without correcting the errors, the install may fail and/or eduTrac may not function properly." );?></p>
                </div>
                
                <div class="column_two legend">
					<ul>
						<?php
						$results = [];
						$php_ok = validate_php($results);
						$memory_ok = validate_memory_limit($results);
						$extensions_ok = validate_extensions($results);
						foreach($results as $result) {
							print '<li class="' . $result->status . '"><span>' . $result->status . '</span> &mdash; ' . $result->message . '</li>';
						}
						?>
					</ul>
					<button class="next" onclick="window.location='<?=url('/');?>install/?step=3'"><span><?=_t( 'Next Step' );?></span></button>
                </div>
      
				<div class="column_two legend">
					<h3><?=_t( 'Legend' );?></h3>
					<ul>
					  <li class="ok"><span><?=_t( 'ok' );?></span> &mdash; <?=_t( 'All OK' );?></li>
					  <li class="warning"><span><?=_t( 'warning' );?></span> &mdash; <?=_t( 'Not a deal breaker and is only a recommendation' );?></li>
					  <li class="error"><span><?=_t( 'error' );?></span> &mdash; <?=_t( "eduTrac ERP requires this feature and can't work without it" );?></li>
					</ul>
				</div>
                
            </div>
            <!-- </Wizard - Step 2 -->
            <?php } ?>
            
            <?php if($app->req->_get('step') == 3) { ?>
            <!-- Wizard - Step 3 -->
            <div id="step-2" class="step two_column">
                
                <!-- Helper -->
                <div id="help-dbname" class="helper">
                    <div class="text">
                        <h3><?=_t( 'Database Name' );?></h3>
                        <p><?=_t( 'The name of the database you want to run eduTrac ERP in.' );?></p>
                    </div>
                </div>
                <!-- </Helper -->
                
                <div class="column_one">
                    <h3><?=_t( 'Step 3 - Database Connection' );?></h3>

                    <p><?=_t( "On the right, you should enter your database connection details. If you're not sure about these, contact your host." );?></p>
                </div>
                
                <div class="column_two">
                	<?=Session::error();?>
                    <form action="<?=url('/');?>install/checkDB/" class="defaultRequest" method="post">
                        <fieldset>
                            <p><label><a href="#help-dbname" class="show_helper"><span>(?)</span> <?=_t( 'Database Name' );?></a></label>
                            <input type="text" name="dbname" class="required input-block-level" /></p>
                            
                            <p><label><?=_t( 'Username' );?></label>
                            <input type="text" name="dbuser" class="required input-block-level" /></p>
                            
                            <p><label><?=_t( 'Password' );?></label>
                            <input type="text" name="dbpass" class="required input-block-level" /></p>
                            
                            <p><label><?=_t( 'Database Host' );?></label>
                            <input type="text" name="dbhost" class="required input-block-level" /></p>
                        </fieldset>
                        
                        <fieldset>
                             <p><label>&nbsp;</label>
                             <button type="submit"><span><?=_t( 'Next Step' );?></span></button></p>
                        </fieldset>
                    </form>

                </div>
                
            </div>
            <!-- </Wizard - Step 3 -->
            <?php } ?>
            
            <?php if($app->req->_get('step') == 4) { ?>
            <!-- Wizard - Step 4 -->
            <div id="step-2" class="step two_column">
                
                <div class="column_one">
                    <h3><?=_t( 'Step 4 - Install Database Tables' );?></h3>

                    <p><?=_t( 'It will take at least 30 seconds to a minute to install the tables. So please be patient.' );?></p>
                </div>
                
                <div class="column_two legend">
					<form action="<?=url('/');?>install/installData/" class="defaultRequest" method="post">
                        <fieldset>
                             <p><label>&nbsp;</label>
                             <button type="submit"><span><?=_t( 'Install Tables' );?></span></button></p>
                        </fieldset>
                    </form>
				</div>
                
            </div>
            <!-- </Wizard - Step 4 -->
            <?php } ?>
            
            <?php if($app->req->_get('step') == 5) { ?>
            <!-- Wizard - Step 5 -->
            <div id="step-3" class="step two_column">
            
                <!-- Helper -->
                <div id="help-username" class="helper">
                    <div class="text">
                        <h3><?=_t( 'Username' );?></h3>
                        <p><?=_t( 'Usernames can have only alphanumeric characters, spaces, underscores, hyphens, periods and the @ symbol.' );?></p>
                    </div>
                </div>
                <!-- </Helper -->
                
                <!-- Helper -->
                <div id="help-password" class="helper">
                    <div class="text">
                        <h3><?=_t( 'Password' );?></h3>
                        <p><?=_t( 'Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).' );?></p>
                    </div>
                </div>
                <!-- </Helper -->
                
                <div class="column_one">
                    <h3><?=_t( 'Step 5 - Admin Account' );?></h3>
                    <p><?=_t( 'Fill in the information to the right and you’ll be on your way to using the eduTrac SIS.' );?></p>
                </div>
                
                <div class="column_two">
                    
                    <form action="<?=url('/');?>install/createAdmin/" class="defaultRequest" method="post">
                        <fieldset>
                            <p><label><?=_t( 'Institution Name' );?></label>
                            <input type="text" name="institutionname" class="required" /></p>
                            
                            <p><label><a href="#help-username" class="show_helper"><span>(?)</span> <?=_t( 'Username' );?></a></label>
                            <input type="text" name="uname" class="required" /></p>
                            
                            <p><label><?=_t( 'First Name' );?></label>
                            <input type="text" name="fname" class="required" /></p>
                            
                            <p><label><?=_t( 'Last Name' );?></label>
                            <input type="text" name="lname" class="required" /></p>
                            
                            <p><label><a href="#help-password" class="show_helper"><span>(?)</span> <?=_t( 'Password' );?></a></label>
                            <input type="password" name="password" class="required" /></p>
                            
                            <p><label><?=_t( 'Your E-mail' );?></label>
                            <input type="text" name="email" class="required email" /></p>
                        </fieldset>
                        
                        <fieldset>
                             <p><label>&nbsp;</label>
                             <button type="submit"><span><?=_t( 'Create Admin' );?></span></button></p>
                        </fieldset>
                    </form>
                    
                </div>
                
            </div>
            <!-- </Wizard - Step 5-->
            <?php } ?>
            
            <?php if($app->req->_get('step') == 6) { ?>
            <!-- Wizard - Step 6 -->
            <div id="step-4" class="step one_column">
                
                <div class="column_one">
                    <h3><?=_t( 'Success!' );?></h3>
                    
                    <p><?=_t( 'eduTrac ERP has been installed. Click the button below in order to create the config file, flush the installer and be redirected to the login page.' );?></p>
                    <form action="<?=url('/');?>install/finishInstall/" class="defaultRequest" method="post">
                        <p><button type="submit"><span><?=_t( 'Finish Installer' );?></span></button></p>
                    </form>
                </div>
                
            </div>
            <!-- </Wizard - Step 6 -->
            <?php } ?>
            
        </div>
        
        <div class="no_javascript">
            <img src="<?=url('/');?>static/assets/img/warning.png" alt="Javascript Required" />
            <p><?=_t( 'Javascript is required in order to use this installer.' );?><br />
            <a href="https://support.google.com/adsense/answer/12654"><?=_t( 'How to enable javascript' );?></a>
            -
            <a href="http://www.mozilla.com/firefox/"><?=_t( 'Upgrade Browser' );?></a></p>
        </div>
    </div>
    
</div>

</body>
</html>
<?php $app->view->stop(); ?>