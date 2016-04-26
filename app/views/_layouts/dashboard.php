<?php $app = \Liten\Liten::getInstance();
ob_start();
ob_implicit_flush(0);
\PHPBenchmark\Monitor::instance()->snapshot('eduTrac SIS Loaded');
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="ie lt-ie9 lt-ie8 lt-ie7 fluid top-full"> <![endif]-->
<!--[if IE 7]>    <html class="ie lt-ie9 lt-ie8 fluid top-full sticky-top"> <![endif]-->
<!--[if IE 8]>    <html class="ie lt-ie9 fluid top-full sticky-top"> <![endif]-->
<!--[if gt IE 8]> <html class="ie gt-ie8 fluid top-full sticky-top"> <![endif]-->
<!--[if !IE]><!--><html class="fluid top-full sticky-top"><!-- <![endif]-->
    <head>
        <title><?=(isset($title)) ? $title . ' - ' . _h(get_option('institution_name')) : _h(get_option('institution_name'));?></title>

        <!-- Meta -->
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
        <link rel="shortcut icon" href="<?= get_base_url(); ?>favicon.ico" type="image/x-icon">

        <!--[if lt IE 9]><link rel="stylesheet" href="<?= get_base_url(); ?>static/assets/components/library/bootstrap/css/bootstrap.min.css" /><![endif]-->
        <link rel="stylesheet" href="<?= get_css_directory_uri(); ?>admin/module.admin.page.layout.section.layout-fluid-menu-top-full.min.css" />
        <link rel="stylesheet" href="<?= get_css_directory_uri(); ?>admin/custom.css" />

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?= get_javascript_directory_uri(); ?>library/jquery/jquery.min.js?v=v2.1.0"></script>
        <script src="<?= get_javascript_directory_uri(); ?>library/jquery/jquery-migrate.min.js?v=v2.1.0"></script>
        <script src="<?= get_javascript_directory_uri(); ?>library/modernizr/modernizr.js?v=v2.1.0"></script>
        <script src="<?= get_javascript_directory_uri(); ?>plugins/less-js/less.min.js?v=v2.1.0"></script>
        <script src="<?= get_javascript_directory_uri(); ?>modules/admin/charts/flot/assets/lib/excanvas.js?v=v2.1.0"></script>
        <script src="<?= get_javascript_directory_uri(); ?>plugins/browser/ie/ie.prototype.polyfill.js?v=v2.1.0"></script>
        <script src="<?= get_javascript_directory_uri(); ?>plugins/typeahead/bootstrap-typeahead.js?v=v2.3.2"></script>
        <script src="<?= get_base_url(); ?>static/assets/plugins/jstree/jstree.min.js"></script>

        <?php
        if (isset($cssArray)) {
            foreach ($cssArray as $css) {
                echo '<link href="' . get_base_url() . 'static/assets/' . $css . '" rel="stylesheet">' . "\n";
            }
        }

        ?>
        <link rel="stylesheet" href="<?= get_base_url(); ?>static/assets/plugins/jstree/themes/proton/style.css" />
    	<?php admin_head(); ?>
    </head>
    <body class="">

        <!-- Main Container Fluid -->
        <div class="container-fluid fluid">


            <!-- Content -->
            <div id="content">
            
				<?php core_admin_bar(); ?>
				
                <?php
                /**
                 * Can be used to add a custom admin bar.
                 * 
                 * @since 6.2.0
                 */
                $app->hook->do_action('admin_bar');
                ?>
                
                <?=show_update_message();?>
                
                <?php
                /**
                 * Prints any dashboard error notices or messages that should be
                 * displayed via plugins or some other method.
                 * 
                 * @since 6.2.0
                 */
                $app->hook->do_action('dashboard_admin_notices');
                ?>
                
                <?= $app->view->show('dashboard'); ?>
                
                <div class="clearfix"></div>

                <div id="custom-footer" class="hidden-print">

                    <?=etsis_dashboard_copyright_footer();?>

                </div>
                <!-- // Footer END -->

            </div>
            <!-- // Main Container Fluid END -->

            <!-- Global -->
            <script>
                var basePath = '',
                        commonPath = '<?= get_base_url(); ?>static/assets/',
                        rootPath = '<?= get_base_url(); ?>',
                        DEV = false,
                        componentsPath = '<?= get_base_url(); ?>static/assets/components/';

                var primaryColor = '#4a8bc2',
                        dangerColor = '#b55151',
                        infoColor = '#74a6d0',
                        successColor = '#609450',
                        warningColor = '#ab7a4b',
                        inverseColor = '#45484d';

                var themerPrimaryColor = primaryColor;
            </script>
            <script src="<?= get_javascript_directory_uri(); ?>library/bootstrap/js/bootstrap.min.js?v=v2.1.0"></script>
            <script src="<?= get_javascript_directory_uri(); ?>plugins/slimscroll/jquery.slimscroll.js?v=v2.1.0"></script>
            <script src="<?= get_javascript_directory_uri(); ?>plugins/breakpoints/breakpoints.js?v=v2.1.0"></script>
            <script src="<?= get_javascript_directory_uri(); ?>core/js/core.init.js?v=v2.1.0"></script>
            <script src="<?= get_javascript_directory_uri(); ?>plugins/mousetrap/mousetrap.min.js"></script>
            <script src="<?= get_javascript_directory_uri(); ?>plugins/mousetrap/shortcut.js"></script>

            <?php
            if (isset($jsArray)) {
                foreach ($jsArray as $js) {
                    echo '<script type="text/javascript" src="' . get_base_url() . 'static/assets/' . $js . '"></script>' . "\n";
                }
            }

            ?>

	<?php footer(); ?>
    </body>
</html>
<?php 
\PHPBenchmark\Monitor::instance()->snapshot('eduTrac SIS Fully Loaded');
print_gzipped_page();
?>