<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Modules View
 *  
 * PHP 5.4+
 *
 * eduTrac(tm) : Student Information System (http://www.7mediaws.org/)
 * @copyright (c) 2013 7 Media Web Solutions, LLC
 * 
 * @link        http://www.7mediaws.org/
 * @since       5.0
 * @package     eduTrac
 * @author      Joshua Parker <josh@7mediaws.org>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$modules_header = $app->module->{'get_modules_header'}(APP_PATH . 'modules/');
?>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'Modules' );?></li>
</ul>

<h3><?=_t( 'Modules' );?></h3>
<div class="innerLR">
    
    <div class="tab-pane" id="search-users">
        <div class="widget widget-heading-simple widget-body-white margin-none">
            <div class="widget-body">

                <div class="alerts alerts-info center">
                    <p><?=_t("Modules can extend the functionality of eduTrac SIS. There are several modules available in our ");?> <a href="http://www.edutracsis.com/client/cart/index/index?c=8"><strong><?=_t( 'Marketplace' );?></strong></a>.</p>
                </div>

            </div>
        </div>
    </div>
    
    <div class="separator bottom"></div>

	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
		<div class="widget-body">
        
			<!-- Table -->
			<table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
			
				<!-- Table heading -->
				<thead>
					<tr>
						<th class="text-center"><?=_t( 'Module' );?></th>
						<th class="text-center"><?=_t( 'Description' );?></th>
                        <th class="text-center"><?=_t( 'Author' );?></th>
                        <th class="text-center"><?=_t( 'Module URI' );?></th>
						<th class="text-center"><?=_t( 'Version' );?></th>
					</tr>
				</thead>
				<!-- // Table heading END -->
				
				<!-- Table body -->
				<tbody>
                <?php foreach($modules_header as $module) : ?>
                    <tr class="separated gradeX">
                        <td class="text-center" style="width:20px !important;"><?=$module['Name'];?></td>
                        <td style="width:65px !important;"><?=$module['Description'];?></td>
                        <td class="text-center" style="width:5px !important;"><?=$module['Author'];?></td>
                        <td class="text-center" style="width:5px !important;"><a href="<?=$module['ModuleURI'];?>"><?=_t( 'Visit module site' );?></a></td>
                        <td class="text-center" style="width:5px !important;"><?=$module['Version'];?></td>
                    </tr>
        		<?php endforeach; ?>
				</tbody>
				<!-- // Table body END -->
				
			</table>
			<!-- // Table END -->
			
		</div>
	</div>
	
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>