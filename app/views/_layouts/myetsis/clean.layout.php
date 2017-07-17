<?php 
/*
Layout Name: Bootstrap Clean
Layout Slug: clean
*/

$app = \Liten\Liten::getInstance();
ob_start();
ob_implicit_flush(0);
$cookie = get_secure_cookie_data('SWITCH_USERBACK');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="<?=get_base_url();?>favicon.ico" type="image/x-icon">

    <title><?=(isset($title) ? $title . ' - ' . _h(get_option('institution_name')) : _h(get_option('institution_name')));?></title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="<?=get_base_url();?>static/assets/components/library/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?=get_base_url();?>static/assets/css/front/myetsis-ui.css" />
    <link rel="stylesheet" href="<?= get_base_url(); ?>static/assets/components/library/bootstrap/css/bootstrap-lumen.css" />

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="<?=get_javascript_directory_uri();?>library/bootstrap/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?=get_javascript_directory_uri();?>library/bootstrap/css/sticky-footer-navbar.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?=get_base_url();?>static/assets/css/admin/custom/myetsis.custom.css" />
    <link rel="stylesheet" href="<?= get_base_url(); ?>static/assets/components/library/bootstrap/css/bootstrap-lumen.css" />

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="<?=get_javascript_directory_uri();?>library/bootstrap/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="<?=get_javascript_directory_uri();?>library/jquery/jquery-migrate.min.js"></script>
    <script src="<?=get_javascript_directory_uri();?>library/modernizr/modernizr.js"></script>
    <?php
	if (isset($cssArray)) {
        foreach ($cssArray as $css){
            echo '<link href="' . get_base_url() . 'static/assets/'.$css.'" rel="stylesheet">' . "\n";
        }
    }
	?>
<?php myetsis_head(); ?>
  </head>

  <body>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container" style="width:85% !important;">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?=get_base_url();?>"><?=get_met_title();?></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="<?=get_base_url();?>"><i class="fa fa-home"></i> <?=_t( 'Home' );?></a></li>
            <?php if(is_user_logged_in()) : ?>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars"></i> <?=_t( 'My Menu' );?> <span class="caret"></span></a>
              <ul class="dropdown-menu">
                  
                <li<?=checkStuMenuAccess(get_persondata('personID'));?> class="dropdown-submenu">
                    <a tabindex="-1" href="#"><?=_t( 'Student' );?></a>
                    <ul class="dropdown-menu">
                        <li<?=ml('booking_module');?>><a href="<?=get_base_url();?>stu/timetable/"><?=_t( 'Timetable' );?></a></li>
                        <li<?=ml('financial_module');?>><a href="<?=get_base_url();?>stu/bill/"><?=_t( 'My Bills' );?></a></li>
                        <li><a href="<?=get_base_url();?>stu/terms/"><?=_t( 'Class Schedule' );?></a></li>
                        <li><a href="<?=get_base_url();?>stu/final-grades/"><?=_t( 'Final Grades' );?></a></li>
                        <?php 
                            /**
                             * Student My Menu Action Hook
                             * 
                             * Hook into this action in order to add a new menu item
                             * to be accessed by any logged in student under myetSIS
                             * self service portal.
                             * 
                             * @since 6.1.05
                             */
                            $app->hook->do_action('student_my_menu'); 
                        ?>
                    </ul>
                </li>
                
                <li class="dropdown-submenu">
                    <a tabindex="-1" href="#"><?=_t( 'Forms' );?></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?=get_base_url();?>form/address/"><?=_t( 'Change of Address Form' );?></a></li>
                        <li><a href="<?=get_base_url();?>form/request-form/"><?=_t( 'Booking Request Form' );?></a></li>
                        <li><a href="<?=get_base_url();?>form/photo/"><?=_t( 'School Photo' );?></a></li>
                        <?php 
                            /**
                             * Forms My Menu Action Hook
                             * 
                             * Hook into this action in order to add a new menu item
                             * to be accessed by any logged in user under myetSIS
                             * self service portal.
                             * 
                             * @since 6.1.05
                             */
                            $app->hook->do_action('forms_my_menu'); 
                        ?>
                    </ul>
                </li>
                
                <?php 
                    /**
                     * My Menu Action Hook
                     * 
                     * Hook into this action in order to add a new menu item
                     * to be accessed by any logged in user under myetSIS
                     * self service portal.
                     * 
                     * @since 5.0.0
                     */
                    $app->hook->do_action('my_menu'); 
                ?>
                <li><a href="<?=get_base_url();?>appl/applications/"><?=_t( 'Applications' );?></a></li>
                <li><a href="<?=get_base_url();?>profile/"><?=_t( 'Profile' );?></a></li>
                <li><a href="<?=get_base_url();?>password/"><?=_t( 'Change Password' );?></a></li>
                <li><a href="<?=get_base_url();?>logout/"><?=_t( 'Logout' );?></a></li>
              </ul>
            </li>
            <?php endif; ?>
            <?php if(function_exists('myetsis_module')) : ?>
            <?php if(metPageExist()) : ?>
            <li class="dropdown dd-1">
                <a href="" data-toggle="dropdown"><i class="fa fa-info-circle"></i> <?=_t( 'Info Pages' );?> <span class="caret"></span></a>
                <ul class="dropdown-menu pull-left">
                    <?=met_page();?>
                </ul>
            </li>
            <?php endif; ?>
            <li><a href="<?=get_base_url();?>news/"><i class="fa fa-newspaper-o"></i> <?=_t( 'News' );?></a></li>
            <?php if(metLinkExist()) : ?>
            <li class="dropdown dd-1">
                <a href="" data-toggle="dropdown"><?=_t( 'Quick Links' );?> <span class="caret"></span></a>
                <ul class="dropdown-menu pull-left">
                    <?=met_link();?>
                </ul>
            </li>
            <?php endif; ?>
            <?php 
                /**
                 * myetSIS Main Menu Hook
                 * 
                 * Hook into this action in order to add a new menu item
                 * to be accessed by any user or visitor.
                 * 
                 * @since 6.1.05
                 */
                $app->hook->do_action('myetsis_main_menu'); 
            ?>
            <?php endif; ?>
            <?php if(_escape(get_option('open_terms')) != null) : ?>
            <li><a href="<?=get_base_url();?>courses/"><i class="fa fa-search"></i> <?=_t( 'Search Courses' );?></a></li>
            <?php endif; ?>
            <?php if(shoppingCart()) : ?>
            <li><a href="<?=get_base_url();?>courses/cart/"><i class="fa fa-shopping-cart"></i> <?=_t( 'Cart' );?></a></li>
            <?php endif; ?>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="hidden-xs"><a href="<?=_h(get_option('help_desk'));?>"><i class="fa fa-shield"></i> <?=_t( 'Get Help' );?></a></li>
            <?php if(!is_user_logged_in()) : ?>
            <li><a href="<?=get_base_url();?>login/"><i class="fa fa-unlock"></i> <?=_t( 'Sign in' );?></a></li>
            <?php if(_h(get_option('enable_myetsis_appl_form')) == 1) : ?>
            <?php 
                /**
                 * Apply Online
                 * 
                 * This action is triggered to echo out new links
                 * at the top of myetSIS self service portal.
                 * 
                 * @since 5.0.0
                 */
                $app->hook->do_action('apply_online'); 
            ?>
            <li<?= ml('myetsis_module'); ?><?= hl('apply_online'); ?>><a href="<?=get_base_url();?>online-app/"><i class="fa fa-user"></i> <?=_t( 'Apply' );?></a></li>
            <?php endif; ?>
            <?php endif; ?>
            <?php if(is_user_logged_in() && isRecordActive(get_persondata('personID'))) : ?>
			<li<?=ae('access_dashboard');?> class="single"><a href="<?=get_base_url();?>dashboard/"><i class="fa fa-dashboard"></i> <?=_t( 'Dashboard' );?></a></li>
            <?php endif; ?>
            <li<?=ae('access_myetsis_admin');?><?=ml('myetsis_module');?> class="single"><a href="<?=get_base_url();?>admin/"><i class="fa fa-key"></i> <?=_t( 'Admin' );?></a></li>
            <?php if(is_user_logged_in()) : ?>
            <li class="single"><a href="<?=get_base_url();?>profile/"><i class="fa fa-user"></i> <?=_t( 'Howdy,' );?> <?=get_persondata('uname');?></a></li>
            <?php endif; ?>
            <?php if(isset($app->req->cookie['SWITCH_USERBACK'])) : ?>
			<li class="single"><a href="<?=get_base_url();?>switchUserBack/<?=$cookie->personID;?>/"><i class="fa fa-undo"></i> <?=_t( 'Switch Back to' );?> <?=$cookie->uname;?></a></li>
            <?php endif; ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <!-- Begin page content -->
    <div class="container">
      <div class="page-header">
          <?php $app->hook->do_action('myetsis_admin_notices'); ?>
      </div>
        
        <div class="innerT">
            <div class="row">
                <div class="custom-container">
        
                <?= $app->view->show('myetsis'); ?>
        
    </div>

    <footer class="footer">
      <div class="container">
        <p class="text-muted"><?=get_met_footer_release();?></p>
      </div>
    </footer>


    <!-- Global -->
	<script>
	var basePath = '',
		commonPath = '<?=get_base_url();?>static/assets/',
		rootPath = '<?=get_base_url();?>',
		DEV = false,
		componentsPath = '<?=get_base_url();?>static/assets/components/';
	</script>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?=get_javascript_directory_uri();?>library/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=get_javascript_directory_uri();?>modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js" type="text/javascript"></script>
    <script src="<?=get_javascript_directory_uri();?>modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js" type="text/javascript"></script>
    <script src="<?=get_javascript_directory_uri();?>modules/admin/forms/elements/select2/assets/lib/js/select2.js" type="text/javascript"></script>
    <script src="<?=get_javascript_directory_uri();?>modules/admin/forms/elements/select2/assets/custom/js/select2.init.js" type="text/javascript"></script>
    <script src="<?=get_javascript_directory_uri();?>plugins/slimscroll/jquery.slimscroll.js"></script>
    <script src="<?=get_javascript_directory_uri();?>plugins/breakpoints/breakpoints.js"></script>
    <script src="<?=get_javascript_directory_uri();?>core/js/core.init.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?=get_javascript_directory_uri();?>library/bootstrap/js/ie10-viewport-bug-workaround.js"></script>
    <?php
		if (isset($jsArray)) {
        foreach ($jsArray as $js){
            echo '<script type="text/javascript" src="' . get_base_url() . 'static/assets/'.$js.'"></script>' . "\n";
        }
    }
	?>
  </body>
</html>
<?php print_gzipped_page(); ?>