<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Dashboard View
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
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$screen = 'dash';
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="#" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
    <li class="pull-right hidden-phone"><a href="<?=_h(get_option('help_desk'));?>" class="glyphicons shield"><?=_t( 'Get Help' );?><i></i></a></li>
	<li class="pull-right hidden-phone divider"></li>

</ul>

<?=show_update_message();?>

<h2><?=_t( 'Dashboard' );?></h2>
<div class="innerLR">

	<div class="row">
        
        <?php jstree_sidebar_menu($screen); ?>
        
        <div class="<?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?> tablet-column-reset">
	
			<div class="row">
                <?php dashboard_top_widgets();?>
            </div>
            
        </div>
        
        <div class="<?=(has_filter('sidebar_menu')) ? 'col-md-4' : 'col-md-3';?> tablet-column-reset">
	
			<div class="row">
				<div class="col-md-12">
					<?php
                    $cache = new \app\src\Cache('rss');
                    if(!$cache->setCache()) :
                    ?>
					<!-- Website Traffic Chart -->
					<div class="widget widget-body-white" data-toggle="collapse-widget">
						<div class="widget-head">
							<h4 class="heading glyphicons imac"><i></i><?=_t( 'Latest eduTrac SIS Updates' );?></h4>
						</div>
						<div class="widget-body">
						
							<!-- Simple Chart -->
							<div class="widget-chart bg-lightseagreen">
								<?php  $rss1 = new \DOMDocument();
                                $rss1->load('http://feeds.feedburner.com/eduTracSIS');
                                $feed = array();
                                foreach ($rss1->getElementsByTagName('item') as $node) {
                                $item = array (
                                'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
                                'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
                                'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
                                'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue,
                                );
                                array_push($feed, $item);
                                }
                                $limit = 3;
                                for($x=0;$x<$limit;$x++) {
                                $title = str_replace(' & ', ' &amp; ', $feed[$x]['title']);
                                $link = $feed[$x]['link'];
                                $description = $feed[$x]['desc'];
                                $date = date('l F d, Y', strtotime($feed[$x]['date']));
                                echo '<p><strong><a href="'.$link.'" title="'.$title.'">'.$title.'</a></strong><br />';
                                echo '<small><em>Posted on '.$date.'</em></small></p>';
                                echo '<p>'.$description.'</p>';
                                } ?>
							</div>
						</div>
					</div>
					<!-- // Website Traffic Chart END -->
                    <?php endif; echo $cache->getCache(); ?>

				</div>
                
			</div>
		</div>
        
		<div class="<?=(has_filter('sidebar_menu')) ? 'col-md-8' : 'col-md-7';?> tablet-column-reset">
	
			<div class="row">
				<div class="col-md-12">
					
					<!-- Website Traffic Chart -->
					<div class="widget widget-body-white" data-toggle="collapse-widget">
						<div class="widget-head">
							<h4 class="heading glyphicons cardio"><i></i><?=_t( 'Students by Academic Program' );?></h4>
						</div>
						<div class="widget-body">
						
							<!-- Simple Chart -->
							<div class="widget-chart bg-lightseagreen">
								<table class="flot-chart" data-type="bars" data-tick-color="rgba(255,255,255,0.2)" data-width="100%" data-tool-tip="show" data-height="220px">
										<thead>
												<tr>
														<th></th>
														<th style="color : #DDD;"><?=_t( 'Students' );?></th>
												</tr>
										</thead>
										<tbody>
												<?php if($prog != '') : foreach($prog as $k => $v) { ?>
												<tr>
														<th><?=$v['acadProgCode'];?></th>
														<td><?=$v['ProgCount'];?></td>
												</tr>
												<?php } endif; ?>
										</tbody>
								</table>
							</div>
						</div>
					</div>
					<!-- // Website Traffic Chart END -->

				</div>
				
				<div class="col-md-12">
					
					<!-- Website Traffic Chart -->
					<div class="widget widget-body-white" data-toggle="collapse-widget">
						<div class="widget-head">
							<h4 class="heading glyphicons parents"><i></i><?=_t( 'Gender by Academic Departments' );?></h4>
						</div>
						<div class="widget-body">
						
							<!-- Simple Chart -->
							<div class="widget-chart">
								<table class="flot-chart" data-type="bars" data-stack="true" data-tick-color="rgba(255,255,255,0.2)" data-width="100%" data-tool-tip="show" data-height="220px" data-position="after">
										<thead>
												<tr>
														<th></th>
														<th style="color : #0090d9;"><?=_t( 'Male' );?></th>
														<th style="color : #ff69b4;"><?=_t( 'Female' );?></th>
												</tr>
										</thead>
										<tbody>
												<?php if($dept != '') : foreach($dept as $k => $v) { ?>
												<tr>
														<th><?=$v['deptCode'];?></th>
														<td><?=$v['Male'];?></td>
														<td><?=$v['Female'];?></td>
												</tr>
												<?php } endif; ?>
										</tbody>
								</table>
							</div>
						</div>
					</div>
					<!-- // Website Traffic Chart END -->

				</div>
			</div>
		</div>
	</div>
	
</div>
	
		
		</div>
		<!-- // Content END -->
<?php
$app->view->stop();