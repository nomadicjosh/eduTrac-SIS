<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Rule Definition Edit View
 *  
 * @license GPLv3
 * 
 * @since       6.2.12
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$flash = new \app\src\Core\etsis_Messages();
$screen = 'rlde';
?>

<script type="text/javascript">
$(".panel").show();
setTimeout(function() { $(".panel").hide(); }, 5000);
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
    <li><a href="<?=get_base_url();?>rlde" class="glyphicons flag"><i></i> <?=_t( 'Rule Definition (RLDE)' );?></a></li>
	<li class="divider"></li>
	<li><?=_h($rule->code);?> - <?=_h($rule->description);?></li>
</ul>

<h3><?=_h($rule->code);?> - <?=_h($rule->description);?></h3>
<div class="innerLR">
    
    <?=$flash->showMessage();?>
    
    <?php jstree_sidebar_menu($screen); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>rlde/<?=_h($rule->id);?>/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
            
            <div class="widget widget-heading-simple widget-body-white">
                <div class="widget-body">
                    <div class="alerts alerts-info">
                        <p><?=_t('If you make any changes, you must click "Load Rule" in order to bring the changes to the screen and then click "Save".');?></p>
                    </div>
                </div>
            </div>
		
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
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Description' );?></label>
							<div class="col-md-8"><input class="form-control" name="description" type="text" maxlength="50" value="<?=_h($rule->description);?>" required /></div>
						</div>
						<!-- // Group END -->
					
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Rule Code' );?> <a href="#rlde" data-toggle="modal"><img src="<?=get_base_url();?>static/common/theme/images/help.png" /></a></label>
							<div class="col-md-8"><input class="form-control" name="code" type="text" value="<?=_h($rule->code);?>" required /></div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Department' );?></label>
							<div class="col-md-8">
                                <select name="dept" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('department','deptCode <> "NULL"','deptCode','deptCode','deptName', _h($rule->dept)); ?>
                                </select>
                            </div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
                    
                    <!-- Column -->
					<div class="col-md-6">
					
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Primary File' );?></label>
                            <div class="col-md-8">
                                <select name="file" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <option value="sttr"<?=selected('sttr', _h($rule->file), false);?>><?=_t('(sttr) - Student Terms');?></option>
                                    <option value="restriction"<?=selected('restriction', _h($rule->file), false);?>><?=_t('(strs) - Student Restrictions');?></option>
                                </select>
                            </div>
						</div>
						<!-- // Group END -->
                        
                        <!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Comment' );?></label>
                            <div class="col-md-8"><textarea style="resize: none;height:8em;" class="form-control" name="comment"><?=_h($rule->comment);?></textarea></div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
					<!-- Column -->
					<div class="col-md-12">
					
						<!-- Group -->
						<div class="form-group">
                            <div id="builder"></div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
					
				</div>
				<!-- // Row END -->
			
				<hr class="separator" />
				
				<!-- Form actions -->
				<div class="form-actions">
                    <div class="btn-group">
                        <div id="result" class="hide">
                            <input style="width: 800px;" id="rldeRule" class="rldeRule form-control" name="rule" type="text" readonly="readonly" required/>
                            <button type="submit" class="btn btn-success"><?=_t( 'Update' );?></button><br /><br />
                         </div>
                    <a class="btn btn-danger reset"><?=_t( 'Reset' );?></a>
                    <a class="btn btn-primary parse-sql" data-stmt="false"><?=_t( 'Load Rule' );?></a>
                    </div>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
</div>

<!-- Modal -->
<div class="modal fade" id="rlde">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal heading -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><?=_t( 'Rule Code' );?></h3>
            </div>
            <!-- // Modal heading END -->
            <!-- Modal body -->
            <div class="modal-body">
                <p><?=_t('If you change or delete a rule code, it will not be updated on the record it is connected with. If you make such a change, you will need to update the record(s) your change will affect.');?></p>
            </div>
            <!-- // Modal body END -->
            <!-- Modal footer -->
            <div class="modal-footer">
                <a href="#" class="btn btn-default" data-dismiss="modal"><?=_t( 'Close' );?></a> 
            </div>
            <!-- // Modal footer END -->
        </div>
    </div>
</div>
<!-- // Modal END -->

<script type="text/javascript" src="<?=get_base_url();?>static/assets/plugins/querybuilder/bootstrap-select/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/plugins/querybuilder/bootbox/bootbox.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/plugins/querybuilder/selectize/js/standalone/selectize.min.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/plugins/querybuilder/jquery-extendext/jQuery.extendext.min.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/plugins/querybuilder/sql-parser/browser/sql-parser.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/plugins/querybuilder/doT/doT.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/plugins/querybuilder/interact/interact.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/plugins/querybuilder/js/query-builder.js"></script>

<script>
$('[data-toggle="tooltip"]').tooltip();
<?php if(!empty($rule->rule)) : ?>
var sql_import_export = "<?=$rule->rule;?>";
<?php endif; ?>

var options = {
  allow_empty: true,

  //default_filter: 'name',
  sort_filters: true,

  optgroups: {
    sttr: {
      en: 'Student Terms'
    },
    strs: {
      en: 'Student Restrictions'
    }
  },

  plugins: {
    'bt-tooltip-errors': { delay: 100},
    'sortable': null,
    'filter-description': { mode: 'bootbox' },
    'bt-selectpicker': null,
    'unique-filter': null,
    'bt-checkbox': { color: 'primary' },
    'invert': null,
    'not-group': null
  },

  // standard operators in custom optgroups
  operators: [
    {type: 'equal',            optgroup: 'basic'},
    {type: 'not_equal',        optgroup: 'basic'},
    {type: 'in',               optgroup: 'basic'},
    {type: 'not_in',           optgroup: 'basic'},
    {type: 'less',             optgroup: 'numbers'},
    {type: 'less_or_equal',    optgroup: 'numbers'},
    {type: 'greater',          optgroup: 'numbers'},
    {type: 'greater_or_equal', optgroup: 'numbers'},
    {type: 'between',          optgroup: 'numbers'},
    {type: 'not_between',      optgroup: 'numbers'},
    {type: 'begins_with',      optgroup: 'strings'},
    {type: 'not_begins_with',  optgroup: 'strings'},
    {type: 'contains',         optgroup: 'strings'},
    {type: 'not_contains',     optgroup: 'strings'},
    {type: 'ends_with',        optgroup: 'strings'},
    {type: 'not_ends_with',    optgroup: 'strings'},
    {type: 'is_empty'     },
    {type: 'is_not_empty' },
    {type: 'is_null'      },
    {type: 'is_not_null'  }
  ],

  filters: [
  /*
   * Student Terms
   */
  {
    id: 'sttr.termCode',
    label: 'Term Code',
    type: 'string',
    optgroup: 'sttr',
    operators: ['equal','in','not_in','begins_with','not_begins_with','contains','not_contains','ends_with','not_ends_with']
  },
  {
    id: 'sttr.acadLevelCode',
    label: 'Academic Level',
    type: 'string',
    optgroup: 'sttr',
    operators: ['equal','in','not_in','begins_with','not_begins_with','contains','not_contains','ends_with','not_ends_with']
  },
  {
    id: 'sttr.attCred',
    label: 'Attempted Credits',
    type: 'double',
    optgroup: 'sttr',
    validation: {
      min: 0,
      step: 0.01
    },
    operators: ['equal','less','less_or_equal','greater','greater_or_equal','between']
  },
  {
    id: 'sttr.compCred',
    label: 'Completed Credits',
    type: 'double',
    optgroup: 'sttr',
    validation: {
      min: 0,
      step: 0.01
    },
    operators: ['equal','less','less_or_equal','greater','greater_or_equal','between']
  },
  {
    id: 'sttr.created',
    label: 'Created Date',
    type: 'date',
    optgroup: 'sttr',
    operators: ['equal','less','less_or_equal','greater','greater_or_equal','between']
  },
  {
    id: 'strs.rstrCode',
    label: 'Restriction Code',
    type: 'string',
    optgroup: 'strs',
    operators: ['equal','not_equal','is_empty','is_not_empty','is_not','is_not_null']
  },
  {
    id: 'strs.severity',
    label: 'Severity',
    type: 'integer',
    optgroup: 'strs',
    validation: {
      min: 0,
      step: 1
    },
    operators: ['equal','not_equal','in','not_in','is_empty','is_not_empty','is_not','is_not_null']
  },
  {
    id: 'strs.startDate',
    label: 'Start Date',
    type: 'date',
    optgroup: 'strs',
    operators: ['equal','not_equal','less','less_or_equal','greater','greater_or_equal','in','not_in','is_empty','is_not_empty','is_not','is_not_null','between','not_between']
  },
  {
    id: 'strs.endDate',
    label: 'End Date',
    type: 'date',
    optgroup: 'strs',
    operators: ['equal','not_equal','less','less_or_equal','greater','greater_or_equal','in','not_in','is_empty','is_not_empty','is_not','is_not_null','between','not_between']
  }
  ]
};

// init
$('#builder').queryBuilder(options);
<?php if(!empty($rule->rule)) : ?>
$('#builder').queryBuilder('setRulesFromSQL', sql_import_export);
<?php endif; ?>

// reset builder
$('.reset').on('click', function() {
  $('#builder').queryBuilder('reset');
  $('#result').addClass('hide').find('.rldeRule').empty();
});

$('.parse-sql').on('click', function() {
  var res = $('#builder').queryBuilder('getSQL', $(this).data('stmt'), false);
  $('#result').removeClass('hide')
    .find('.rldeRule').val(
      res.sql + (res.params ? '\n\n' + JSON.stringify(res.params, undefined, 2) : '')
    );
});
</script>
	
		
		</div>
		<!-- // Content END -->
<?php $app->view->stop(); ?>