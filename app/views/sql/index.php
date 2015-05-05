<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 *
 * SQL Interface View
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
$logger = new \app\src\Log;
$uname = get_persondata('uname');

$pdo = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);

$type = $_POST['type'];
$qtext = $_POST['qtext'];
$qtext = str_replace("\\","",$qtext);
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 10000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here' );?></li>
	<li><a href="<?=url('/');?>dashboard/<?=bm();?>" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
	<li><?=_t( 'SQL Interface' );?></li>
</ul>

<h3><?=_t( 'SQL Interface' );?></h3>
<div class="innerLR">
    
    <?=$message->flashMessage();?>
	
	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=url('/');?>sql/" id="validateSubmitForm" method="post">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray">
		
			<!-- Widget heading -->
			<div class="widget-head">
				<h4 class="heading"><font color="red">*</font> <?=_t( 'Indicates field is required' );?></h4>
			</div>
			<!-- // Widget heading END -->
			
			<div class="widget-body">
			
				<!-- Row -->
				<div class="row">
					
					<!-- Column -->
					<div class="col-md-6">
					
						<!-- Group -->
						<div class="form-group">
							<label class="col-md-3 control-label" for="term"><font color="red">*</font> <?=_t( 'Query' );?></label>
							<div class="col-md-8">
								<textarea id="mustHaveId" class="form-control" rows="5" style="width:65em;" name="qtext" required><?php if(isset($qtext)) { echo $qtext; } ?></textarea>
							</div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
					<input type="hidden" name="type" value="query" >
					<button type="submit" name="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?=_t( 'Submit' );?></button>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
	
	<?php if(isset($type)) { ?>
	<!-- Widget -->
	<div class="widget widget-heading-simple widget-body-gray">
		<div class="widget-body">
		
			<!-- Table -->
			<?php
                if (strstra(strtolower($_POST['qtext']), forbidden_keyword())) {
                    $app->flash('error_message', 'Your query contains a forbidden keywork, please try again.');
                    redirect($app->req->server['HTTP_REFERER']);
                    exit();
                }
                
				if($type == "query") {
				
					$qtext2 = str_replace("\\", " ", $qtext);
                    /* Write to activity log table. */
                    $logger->setLog("Query", "SQL Interface", $qtext2, $uname );
				
						if($result = $pdo->query("$qtext2"))
							echo _t( "Successly Executed - " );
						else
							echo "<font color=red>Not able to execute the query<br>Either the 
								table does not exist or the query is malformed.</font><br><br>";
				
						echo _t( "Query is : " );
						echo("<font color=blue>"._h($qtext2)."</font>\n");
						
						echo "<table class=\"dynamicTable tableTools table table-striped table-bordered table-condensed table-white\">
						<thead>
						<tr>\n";
						
						foreach(range(0, $result->columnCount() - 1) as $column_index)
						{
						$meta[] = $result->getColumnMeta($column_index);
						echo "<th>".$meta[$column_index]['name']."</th>";
						}
						echo "</tr>\n</thead>\n";
				
						$vv = true;
						while ($row = $result->fetch(\PDO::FETCH_NUM)) {
							if($vv === true) {
					   		echo "<tr>\n";
							$vv = false;
							}
							else{
						   	echo "<tr>\n";
							$vv = true;
							}
						  	foreach ($row as $col_value) {
				       		echo "<td>"._h($col_value)."</td>\n";
				   			}
					   	echo "</tr>\n";
						}
						echo "</table>\n";
						/* Free resultset */
						$result->closeCursor();
					}
			
			?>
			<!-- Table End -->
			
		</div>
	</div>
	<?php } ?>
	<div class="separator bottom"></div>
	<!-- // Widget END -->
	
</div>	
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>