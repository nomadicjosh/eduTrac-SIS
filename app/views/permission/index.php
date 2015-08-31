<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Manage Permissions View
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
$message = new \app\src\Messages;
$perms = new \app\src\ACL();
$cache = new \app\src\Cache('permission');
$screen = 'perm';
if(!$cache->setCache()) :
?>

<script type="text/javascript">
	$(".panel").show();
	setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
    <li><?=_t( 'You are here');?></li>
    <li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
    <li class="divider"></li>
    <li><?=_t( 'Permissions' );?></li>
</ul>

<h3><?=_t( 'Manage Permissions' );?></h3>
<div class="innerLR">
    
    <?=$message->flashMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

    <!-- Widget -->
    <div class="widget widget-heading-simple widget-body-gray <?=(has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
        <div class="widget-body">
        
            <!-- Table -->
            <table class="dynamicTable tableTools table table-striped table-bordered table-condensed table-white">
            
                <!-- Table heading -->
                <thead>
                    <tr>
                        <th class="text-center"><?=_t( 'ID' );?></th>
                        <th class="text-center"><?=_t( 'Key' );?></th>
                        <th class="text-center"><?=_t( 'Name' );?></th>
                        <th class="text-center"><?=_t( 'Edit' );?></th>
                    </tr>
                </thead>
                <!-- // Table heading END -->
                
                <!-- Table body -->
                <tbody>
                <?php 
                    $listPerms = $perms->getAllPerms('full');
                    if($listPerms != '') {
                        foreach ($listPerms as $k => $v) {
                            echo '<tr class="gradeX">';
                            echo '<td>'._h($v['ID']).'</td>';
                            echo '<td>'._h($v['Key']).'</td>';
                            echo '<td>'._h($v['Name']).'</td>';
                            echo '<td class="text-center"><a href="'.url('/').'permission/'._h($v['ID']).'/" title="Edit Permission" class="btn btn-default"><i class="fa fa-edit"></i></a></td>';
                            echo '</tr>'."\n";
                        }
                    }
                ?>
                    
                </tbody>
                <!-- // Table body END -->
                
            </table>
            <!-- // Table END -->
            
        </div>
    </div>
    <div class="separator bottom"></div>
    <!-- // Widget END -->
    
    <!-- Form actions -->
    <div class="form-actions">
        <button type="submit" name="NewPerm" class="btn btn-icon btn-primary glyphicons circle_ok" onclick="window.location='<?=url('/');?>permission/add/<?=bm();?>'"><i></i><?=_t( 'New Permision' );?></button>
    </div>
    <!-- // Form actions END -->
    
</div>  
    
        
        </div>
        <!-- // Content END -->
<?php endif; echo $cache->getCache();
$app->view->stop();