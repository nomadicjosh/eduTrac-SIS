<?php 
/*
Layout Name: Default
Layout Slug: default
*/

$app = \Liten\Liten::getInstance(); ?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="front ie lt-ie9 lt-ie8 lt-ie7 fluid top-full"> <![endif]-->
<!--[if IE 7]>    <html class="front ie lt-ie9 lt-ie8 fluid top-full sticky-top"> <![endif]-->
<!--[if IE 8]>    <html class="front ie lt-ie9 fluid top-full sticky-top"> <![endif]-->
<!--[if gt IE 8]> <html class="front ie gt-ie8 fluid top-full sticky-top"> <![endif]-->
<!--[if !IE]><!--><html class="front fluid top-full sticky-top"><!-- <![endif]-->
<head>
	<title><?php if (isset($title)) {
            echo $title . ' - ' . get_option('institution_name');
        } else {
            echo get_option('institution_name');
        } ?>
        </title>
	
	<!-- Meta -->
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<meta name="robots" content="noindex, nofollow">
	<link rel="shortcut icon" href="<?=get_base_url();?>favicon.ico" type="image/x-icon">

	<!--[if lt IE 9]><link rel="stylesheet" href="<?=get_base_url();?>static/assets/components/library/bootstrap/css/bootstrap.min.css" /><![endif]-->
	<link rel="stylesheet" href="<?=get_base_url();?>static/assets/css/front/module.front.page.index.min.css" />
	<link rel="stylesheet" href="<?=get_base_url();?>static/assets/css/admin/custom.css" />
	<link rel="stylesheet" href="<?=get_base_url();?>static/assets/css/admin/custom/myet.custom.css" />
	
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

<script src="<?=get_javascript_directory_uri();?>library/jquery/jquery.min.js?v=v2.1.0"></script>
<script src="<?=get_javascript_directory_uri();?>library/jquery/jquery-migrate.min.js?v=v2.1.0"></script>
<script src="<?=get_javascript_directory_uri();?>library/modernizr/modernizr.js?v=v2.1.0"></script>
<script src="<?=get_javascript_directory_uri();?>plugins/less-js/less.min.js?v=v2.1.0"></script>
<script src="<?=get_javascript_directory_uri();?>modules/admin/charts/flot/assets/lib/excanvas.js?v=v2.1.0"></script>
<script src="<?=get_javascript_directory_uri();?>plugins/browser/ie/ie.prototype.polyfill.js?v=v2.1.0"></script>
	<?php
	if (isset($cssArray)) {
        foreach ($cssArray as $css){
            echo '<link href="' . url('/') . 'static/assets/'.$css.'" rel="stylesheet">' . "\n";
        }
    }
	?>
<?php myet_head(); ?>
</head>
<body>
	
	<!-- Main Container Fluid -->
	<div class="container-fluid">
		
		<!-- Content -->
		<div id="content">
		
		<!-- Top navbar -->
		<div class="navbar main hidden-print">
			
			<div class="secondary">
				<div class="container-960">
				
					<!-- Brand -->
					<a href="<?=get_base_url();?>" class="appbrand pull-left"><?=get_met_title();?></a>
					
					<ul class="topnav pull-right">
						
						<li class="hidden-xs"><a href="<?=_h(get_option('help_desk'));?>" class="glyphicons shield"><i></i> <?=_t( 'Get Help' );?></a></li>
						<?php if(!isUserLoggedIn()) : ?>
						<li class="glyphs2 hidden-xs">
							<ul>
								<li><a href="<?=get_base_url();?>login/" class="glyphicons unlock"><i></i> <?=_t( 'Sign in' );?></a></li>
								<?php if(_h(get_option('enable_myet_appl_form')) == 1) : ?>
                                <?php 
                                    /**
                                     * Apply Online
                                     * 
                                     * This action is triggered to echo out new links
                                     * at the top of myeduTrac self service portal.
                                     * 
                                     * @since 5.0.0
                                     */
                                    do_action('apply_online'); 
                                ?>
								<li<?= ml('myet_module'); ?><?= hl('apply_online'); ?>><a href="<?=get_base_url();?>online-app/" class="glyphicons user_add"><i></i> <?=_t( 'Apply' );?></a></li>
								<?php endif; ?>
							</ul>
						</li>
						<?php endif; ?>
						<?php if(isUserLoggedIn() && isRecordActive(get_persondata('personID'))) : ?>
						<li<?=ae('access_dashboard');?> class="glyphs2 hidden-xs">
							<ul>
								<li<?=ae('access_dashboard');?> class="single"><a href="<?=get_base_url();?>dashboard/" class="no-ajaxify glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
							</ul>
						</li>
						<?php endif; ?>
						<li<?=ae('access_myet_admin');?><?=ml('myet_module');?> class="glyphs2 hidden-xs">
							<ul>
								<li class="single"><a href="<?=get_base_url();?>admin/" class="no-ajaxify glyphicons keys"><i></i> <?=_t( 'Admin' );?></a></li>
							</ul>
						</li>
						<?php if(isUserLoggedIn()) : ?>
						<li class="glyphs2 hidden-xs">
							<ul>
								<li class="single"><a href="<?=get_base_url();?>profile/" class="no-ajaxify glyphicons user"><i></i> <?=_t( 'Howdy,' );?> <?=get_persondata('uname');?></a></li>
							</ul>
						</li>
						<?php endif; ?>
						<?php if(isset($_COOKIE['SWITCH_USERBACK'])) : ?>
						<li class="glyphs2 hidden-xs">
							<ul>
								<li class="single">
									<a href="<?=url('/');?>switchUserBack/<?=$app->cookies->getSecureCookie('SWITCH_USERBACK');?>/" class="no-ajaxify glyphicons history"><i></i> <?=_t( 'Switch Back to' );?> <?=$app->cookies->getSecureCookie('SWITCH_USERNAME');?></a>
								</li>
							</ul>
						</li>
						<?php endif; ?>
					</ul>
					<div class="clearfix"></div>
					
				</div>
			</div>
			
			<div class="container-960">
			
			<!-- Menu Toggle Button -->
			<button type="button" class="btn btn-navbar navbar-toggle visible-xs">
				<span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
			</button>
			<!-- // Menu Toggle Button END -->
			
			<ul class="topnav pull-left">
				
				<li><a href="<?=get_base_url();?>" class="glyphicons home"><i></i><?=_t( 'Home' );?></a></li>
				<?php if(isUserLoggedIn()) : ?>
				<li class="dropdown dd-1">
					<a href="" data-toggle="dropdown" class="glyphicons show_lines"><i></i><?=_t( 'My Menu' );?> <span class="caret"></span></a>
					<ul class="dropdown-menu pull-left">
						<li<?=checkStuMenuAccess(get_persondata('personID'));?> class="dropdown submenu">
                            <a data-toggle="dropdown" class="dropdown-toggle glyphicons chevron-right"><i></i><?=_t( 'Student' );?></a>
                            <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                <li<?=ml('booking_module');?>><a href="<?=get_base_url();?>stu/timetable/"><?=_t( 'Timetable' );?></a></li>
								<li<?=ml('financial_module');?>><a href="<?=get_base_url();?>stu/bill/"><?=_t( 'My Bills' );?></a></li>
								<li><a href="<?=get_base_url();?>stu/terms/"><?=_t( 'Class Schedule' );?></a></li>
								<li><a href="<?=get_base_url();?>stu/final-grades/"><?=_t( 'Final Grades' );?></a></li>
                                <?php 
                                    /**
                                     * Student My Menu Action Hook
                                     * 
                                     * Hook into this action in order to add a new menu item
                                     * to be accessed by any logged in student under myeduTrac
                                     * self service portal.
                                     * 
                                     * @since 6.1.05
                                     */
                                    do_action('student_my_menu'); 
                                ?>
                            </ul>
                        </li>
						<li<?=ml('myet_module');?> class="dropdown submenu">
                            <a data-toggle="dropdown" class="dropdown-toggle glyphicons chevron-right"><i></i><?=_t( 'Forms' );?></a>
                            <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                <li><a href="<?=get_base_url();?>form/address/"><?=_t( 'Change of Address Form' );?></a></li>
								<li><a href="<?=get_base_url();?>form/request-form/"><?=_t( 'Booking Request Form' );?></a></li>
								<li><a href="<?=get_base_url();?>form/photo/"><?=_t( 'School Photo' );?></a></li>
                                <?php 
                                    /**
                                     * Forms My Menu Action Hook
                                     * 
                                     * Hook into this action in order to add a new menu item
                                     * to be accessed by any logged in user under myeduTrac
                                     * self service portal.
                                     * 
                                     * @since 6.1.05
                                     */
                                    do_action('forms_my_menu'); 
                                ?>
                            </ul>
                        </li>
                        <?php 
                            /**
                             * My Menu Action Hook
                             * 
                             * Hook into this action in order to add a new menu item
                             * to be accessed by any logged in user under myeduTrac
                             * self service portal.
                             * 
                             * @since 5.0.0
                             */
                            do_action('my_menu'); 
                        ?>
                        <li><a href="<?=get_base_url();?>appl/applications/"><?=_t( 'Applications' );?></a></li>
						<li><a href="<?=get_base_url();?>profile/"><?=_t( 'Profile' );?></a></li>
						<li><a href="<?=get_base_url();?>password/"><?=_t( 'Change Password' );?></a></li>
						<li><a href="<?=get_base_url();?>logout/"><?=_t( 'Logout' );?></a></li>
					</ul>
				</li>
				<?php endif; ?>
                <?php if(function_exists('myet_module')) : ?>
				<?php if(metPageExist()) : ?>
				<li class="dropdown dd-1">
					<a href="" data-toggle="dropdown" class="glyphicons circle_info"><i></i><?=_t( 'Info Pages' );?> <span class="caret"></span></a>
					<ul class="dropdown-menu pull-left">
						<?=met_page();?>
					</ul>
				</li>
				<?php endif; ?>
				<li><a href="<?=get_base_url();?>news/" class="glyphicons notes"><i></i><?=_t( 'News' );?></a></li>
				<?php if(metLinkExist()) : ?>
				<li class="dropdown dd-1">
					<a href="" data-toggle="dropdown" class="glyphicons bookmark"><i></i><?=_t( 'Quick Links' );?> <span class="caret"></span></a>
					<ul class="dropdown-menu pull-left">
						<?=met_link();?>
					</ul>
				</li>
                <?php endif; ?>
				<?php 
                    /**
                     * myeduTrac Main Menu Hook
                     * 
                     * Hook into this action in order to add a new menu item
                     * to be accessed by any user or visitor.
                     * 
                     * @since 6.1.05
                     */
                    do_action('myet_main_menu'); 
                ?>
				<?php endif; ?>
				<li><a href="<?=get_base_url();?>courses/" class="glyphicons search"><i></i><?=_t( 'Search Courses' );?></a></li>
				<?php if(shoppingCart()) : ?>
				<li><a href="<?=get_base_url();?>courses/cart/" class="glyphicons shopping_cart"><i></i><?=_t( 'Cart' );?></a></li>
				<?php endif; ?>
			</ul>
			
			</div>
			
		</div>
		<!-- Top navbar END -->
<div class="container-960 innerT">

	<div class="row">
        
        <?= $app->view->show('myet'); ?>
        
    		<div id="custom-footer" class="hidden-print">
				
				<!--  Copyright Line -->
				<div class="copy">
					<?=get_met_footer_release();?>
				</div>
				<!--  End Copyright Line -->
	
            </div>
            <!-- // Footer END -->
		
	</div>
</div>
	<!-- // Main Container Fluid END -->
	
	<!-- Global -->
	<script>
	var basePath = '',
		commonPath = '<?=url('/');?>static/assets/',
		rootPath = '<?=url('/');?>',
		DEV = false,
		componentsPath = '<?=url('/');?>static/assets/components/';

	var primaryColor = '#4a8bc2',
		dangerColor = '#b55151',
		successColor = '#609450',
		warningColor = '#ab7a4b',
		inverseColor = '#45484d';

	var themerPrimaryColor = primaryColor;
	</script>
	
<script src="<?=get_javascript_directory_uri();?>library/bootstrap/js/bootstrap.min.js?v=v2.1.0"></script>
<script src="<?=get_javascript_directory_uri();?>plugins/slimscroll/jquery.slimscroll.js?v=v2.1.0"></script>
<script src="<?=get_javascript_directory_uri();?>plugins/breakpoints/breakpoints.js?v=v2.1.0"></script>
<script src="<?=get_javascript_directory_uri();?>core/js/core.init.js?v=v2.1.0"></script>
	<?php
		if (isset($jsArray)) {
        foreach ($jsArray as $js){
            echo '<script type="text/javascript" src="' . url('/') . 'static/assets/'.$js.'"></script>' . "\n";
        }
    }
	?>

</body>
</html>