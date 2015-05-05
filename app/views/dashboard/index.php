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
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="#" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
    <li class="pull-right hidden-phone"><a href="<?=_h($app->hook->{'get_option'}('help_desk'));?>" class="glyphicons shield"><?=_t( 'Get Help' );?><i></i></a></li>
	<li class="pull-right hidden-phone divider"></li>

</ul>

<?=show_update_message();?>

<?php
//$cache = new \eduTrac\Classes\Libraries\Cache('dashboard');
//if(!$cache->setCache()) :
?>

<h2><?=_t( 'Dashboard' );?></h2>
<div class="innerLR">
	
	<?php dashboard_top_widgets();?>

	<div class="row">
		<div class="col-md-12 tablet-column-reset">
	
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
<?php //endif; echo $cache->getCache();
$app->view->stop();