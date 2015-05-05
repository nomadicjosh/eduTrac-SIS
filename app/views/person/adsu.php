<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Address Summary View
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
    <li><?=_t( 'You are here');?></li>
    <li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=url('/');?>nae/<?=bm();?>" class="glyphicons search"><i></i> <?=_t( 'Search Person' );?></a></li>
    <li class="divider"></li>
    <li><a href="<?=url('/');?>nae/<?=_h($adsu[0]['personID']);?>/<?=bm();?>" class="glyphicons user"><i></i> <?=get_name(_h($adsu[0]['personID']));?></a></li>
    <li class="divider"></li>
    <li><?=_t( 'Address Summary' );?></li>
</ul>

<h3><?=get_name(_h((int)$adsu[0]['personID']));?> <?=_t( "ID: " );?><?=_h($adsu[0]['personID']);?></h3>
<div class="innerLR">
        
        <!-- Widget -->
        <div class="widget widget-heading-simple widget-body-gray">
            
            <div class="widget-body">
                <?php if($adsu !='') : foreach($adsu as $k => $v) { ?>
                <!-- Row -->
                <div class="row">
                    
                    <!-- Column -->
                    <div class="col-md-6">
                    
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="address"><?=_t( 'Address' );?> <a href="<?=url('/');?>nae/addr/<?=_h($v['addressID']);?>/<?=bm();?>"><img src="<?=url('/');?>static/common/theme/images/cascade.png" /></a></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" readonly value="<?=_h($v['address1']);?> <?=_h($v['address2']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                        	<label class="col-md-3 control-label">&nbsp;</label>
                            <div class="col-md-3">
                                <input class="form-control" type="text" readonly value="<?=_h($v['city']);?>" />
                            </div>
                            <div class="col-md-3">
                                <input class="form-control" type="text" readonly value="<?=_h($v['state']);?>" />
                            </div>
                            <div class="col-md-3">
                                <input class="form-control" type="text" readonly value="<?=_h($v['zip']);?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                    <!-- // Column END -->
                    
                    <!-- Column -->
                    <div class="col-md-6">
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label" for="status"><?=_t( 'Status' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" readonly value="<?=translate_addr_status(_h($v['addressStatus']));?>" />
                            </div>
                            
                            <label class="col-md-3 control-label" for="type"><?=_t( 'Type' );?></label>
                            <div class="col-md-8">
                                <input class="form-control" type="text" readonly value="<?=translate_addr_type(_h($v['addressType']));?>" />
                            </div>
                        </div>
                        <!-- // Group END -->
                        
                    </div>
                    <!-- // Column END -->
                    
                </div>
                <!-- // Row END -->
                <hr class="separator" />
                <?php } endif; ?>
                
                <!-- Form actions -->
                <div class="form-actions">
                    <button type="button"<?=aids();?> class="btn btn-icon btn-primary glyphicons circle_ok" onclick="window.location='<?=url('/');?>nae/addr-form/<?=_h($adsu[0]['personID']);?>/<?=bm();?>'"><i></i><?=_t( 'Add' );?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location='<?=url('/');?>nae/<?=_h($adsu[0]['personID']);?>/<?=bm();?>'"><i></i><?=_t( 'Cancel' );?></button>
                </div>
                <!-- // Form actions END -->

            </div>
        </div>
        <!-- // Widget END -->
    
</div>   
        
        </div>
        <!-- // Content END -->
<?php $app->view->stop(); ?>