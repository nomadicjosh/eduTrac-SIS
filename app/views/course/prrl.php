<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed');
/**
 * Prerequisite View
 *  
 * @license GPLv3
 * 
 * @since       6.3.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */

$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$list = '"'.implode('","', courseList(_escape($crse->courseID))).'"';
$screen = 'vcrse';
?>

<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script src="<?=get_base_url();?>static/assets/plugins/tinymce/plugin.js"></script>
<script type="text/javascript">
$(function() {
    $("#select2_5").select2({tags:[<?=$list;?>]});
});
tinymce.init(
    {
        selector: "#printText",
        toolbar: "undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | placeholder",
        setup: function(editor) {
        editor.addButton('placeholder', {
            type: 'menubutton',
            text: 'Placeholder',
            icon: false,
            menu: [
                {text: 'Student ID', onclick: function() {editor.insertContent('{stuID}');}},
                {text: 'Student Name', onclick: function() {editor.insertContent('{name}');}},
                {text: 'Course', onclick: function() {editor.insertContent('{course}');}},
                {text: 'Department Name', onclick: function() {editor.insertContent('{deptName}');}},
                {text: 'Department Email', onclick: function() {editor.insertContent('{deptEmail}');}},
                {text: 'Department Phone', onclick: function() {editor.insertContent('{deptPhone}');}}
            ]
        });
    }
});
</script>

<ul class="breadcrumb">
	<li><?=_t( 'You are here');?></li>
	<li><a href="<?=get_base_url();?>dashboard/" class="glyphicons dashboard"><i></i> <?=_t( 'Dashboard' );?></a></li>
	<li class="divider"></li>
    <li><a href="<?=get_base_url();?>crse/" class="glyphicons search"><i></i> <?=_t( 'Course Lookup' );?></a></li>
	<li class="divider"></li>
	<li><a href="<?=get_base_url();?>crse/<?=_escape($crse->courseID);?>/" class="glyphicons adjust_alt"><i></i> <?=_escape($crse->courseCode);?></a></li>
    <li class="divider"></li>
	<li><?=_escape($crse->courseCode);?></li>
</ul>

<h3><?=_escape($crse->courseCode);?> <?=_t('Prerequisites');?></h3>
<div class="innerLR">
    
    <?=_etsis_flash()->showMessage();?>
    
    <?php jstree_sidebar_menu($screen,$crse); ?>

	<!-- Form -->
	<form class="form-horizontal margin-none" action="<?=get_base_url();?>crse/<?=_escape($crse->courseID);?>/prrl/" id="validateSubmitForm" method="post" autocomplete="off">
		
		<!-- Widget -->
		<div class="widget widget-heading-simple widget-body-gray <?=($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10';?>">
            <?php if(_escape($crse->rule) != null) : ?>
            <a href="#test" data-toggle="modal" class="btn btn-inverse pull-right"><i class="fa fa-caret-square-o-right"></i></a>
            <?php endif; ?>
            <a href="#help" data-toggle="modal" class="btn btn-inverse pull-right"><i class="fa fa-question-circle"></i></a>
            
            <div class="breakline">&nbsp;</div>
            <div class="breakline">&nbsp;</div>
			
			<div class="widget-body">
                
				<!-- Row -->
				<div class="row">
                    
                    <!-- Column -->
					<div class="col-md-6">
                        
                        <!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Course Prereqs' );?></label>
							<div class="col-md-8"><input id="select2_5" style="width:100%;" type="hidden" name="preReq" value="<?=_escape($crse->preReq);?>" /></div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
                    
                    <!-- Column -->
					<div class="col-md-6">
					
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Print Text' );?></label>
							<div class="col-md-8">
								<textarea style="height:10em;" id="printText" name="printText" class="form-control"><?=_escape($crse->printText);?></textarea>
							</div>
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
                            <textarea id="rldeRule" style="resize: none;height:10em; width:800px;" name="rule" class="rldeRule form-control" readonly="readonly"></textarea>
                            <button type="submit" class="btn btn-success"><?=_t( 'Update' );?></button><br /><br />
                         </div>
                    <a class="btn btn-danger reset"><?=_t( 'Reset' );?></a>
                    <a class="btn btn-primary parse-sql" data-stmt="false"><?=_t( 'Load Rule' );?></a>
                    <?php if(_escape($crse->rule) != null) : ?>
                    <button type="button" class="btn btn-inverse" onclick="window.location='<?=get_base_url();?>crse/<?=_escape($crse->courseID);?>/prrl/c/'"><i></i><?=_t( 'Clear' );?></button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-primary" onclick="window.location='<?=get_base_url();?>crse/<?=_escape($crse->courseID);?>/'"><i></i><?=_t( 'Cancel' );?></button>
                    </div>
				</div>
				<!-- // Form actions END -->
				
			</div>
		</div>
		<!-- // Widget END -->
		
	</form>
	<!-- // Form END -->
    
    <!-- Modal -->
    <div class="modal fade" id="help">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?=_t( 'Prerequisites' );?></h3>
                </div>
                <!-- // Modal heading END -->
                <!-- Modal body -->
                <div class="modal-body"><?=_file_get_contents( APP_PATH . $app->hook->{'apply_filter'}('modal_info_folder', 'Info') . DS . 'prereq.txt' );?></div>
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
    <!-- Modal -->
    <div class="modal fade" id="test">
        <form class="form-horizontal margin-none" action="<?=get_base_url();?>crse/<?=_escape($crse->courseID);?>/prrl/test/" id="validateSubmitForm" method="post" autocomplete="off">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?=_t( 'Test Prerequisite Rule' );?></h3>
                </div>
                <!-- // Modal heading END -->
                <!-- Modal body -->
                <div class="modal-body">
                    <!-- Column -->
					<div class="col-md-12">
					
						<!-- Group -->
						<div class="form-group">
                            <label class="col-md-3 control-label"><?=_t( 'Student' );?></label>
							<div class="col-md-8">
								<select name="stuID" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                    <option value="">&nbsp;</option>
                                    <?php foreach($stu as $s) : ?>
                                    <option value="<?=_escape($s['stuID']);?>"><?=get_name(_escape($s['stuID']));?></option>
                                    <?php endforeach; ?>
                                </select>
							</div>
						</div>
						<!-- // Group END -->
						
					</div>
					<!-- // Column END -->
                </div>
                <!-- // Modal body END -->
                <hr class="separator" />
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i></i><?=_t( 'Submit' );?></button>
                    <a href="#" class="btn btn-default" data-dismiss="modal"><?=_t( 'Close' );?></a>
                </div>
                <!-- // Modal footer END -->
            </div>
        </div>
        </form>
    </div>
    <!-- // Modal END -->
</div>

<script type="text/javascript" src="<?=get_base_url();?>static/assets/components/modules/querybuilder/bootstrap-select/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/components/modules/querybuilder/bootbox/bootbox.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/components/modules/querybuilder/selectize/js/standalone/selectize.min.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/components/modules/querybuilder/jquery-extendext/jQuery.extendext.min.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/components/modules/querybuilder/sql-parser/browser/sql-parser.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/components/modules/querybuilder/doT/doT.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/components/modules/querybuilder/interact/interact.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/components/modules/querybuilder/js/query-builder.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/components/modules/momentjs/moment.js"></script>
<script type="text/javascript" src="<?=get_base_url();?>static/assets/components/modules/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<script>
$('[data-toggle="tooltip"]').tooltip();
<?php if(!empty(_escape($crse->rule))) : ?>
var sql_import_export = "<?=_escape($crse->rule);?>";
<?php endif; ?>
    
// Fix for Selectize
$('#builder').on('afterCreateRuleInput.queryBuilder', function(e, rule) {
  if (rule.filter.plugin == 'selectize') {
    rule.$el.find('.rule-value-container').css('min-width', '200px')
      .find('.selectize-control').removeClass('form-control');
  }
});

var options = {
  allow_empty: true,

  //default_filter: 'name',
  sort_filters: true,

  optgroups: {
    v_sacp: {
      en: 'Student Academic Program'
    },
    stal: {
      en: 'Student Academic Level'
    },
    v_scrd: {
      en: 'Student Credits'
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
   * Student Academic Program
   */
  {
    id: 'v_sacp.prog',
    label: 'Academic Program',
    type: 'string',
    input: 'select',
    plugin: 'selectize',
    multiple: true,
    plugin_config: {
      valueField: 'id',
      labelField: 'name',
      searchField: 'name',
      sortField: 'name',
      create: true,
      plugins: ['remove_button']
    },
    values: {
        <?php get_rlde_prog(); ?>
    },
    valueSetter: function (rule, value) {
        rule.$el.find('.rule-value-container select')[0].selectize.setValue(value);
    },
    optgroup: 'v_sacp'
  },
  {
    id: 'v_sacp.acadLevel',
    label: 'Academic Level',
    type: 'string',
    input: 'select',
    plugin: 'selectize',
    multiple: true,
    plugin_config: {
      valueField: 'id',
      labelField: 'name',
      searchField: 'name',
      sortField: 'name',
      create: true,
      plugins: ['remove_button']
    },
    values: {
        <?php get_rlde_acad_levels(); ?>
    },
    valueSetter: function (rule, value) {
        rule.$el.find('.rule-value-container select')[0].selectize.setValue(value);
    },
    optgroup: 'v_sacp'
  },
  {
    id: 'v_sacp.status',
    label: 'Program Status',
    type: 'string',
    input: 'select',
    plugin: 'selectize',
    plugin_config: {
      valueField: 'id',
      labelField: 'name',
      searchField: 'name',
      sortField: 'name',
      create: true,
      maxItems: 1,
      plugins: ['remove_button']
    },
    values: {
        "A": "(A) Active",
        "I": "(I) Inactive",
        "C": "(C) Changed",
        "P": "(P) Pending",
        "G": "(G) Graduated"
    },
    valueSetter: function (rule, value) {
        rule.$el.find('.rule-value-container select')[0].selectize.setValue(value);
    },
    optgroup: 'v_sacp'
  },
  /*
   * Student Academic Level
   */
  {
    id: 'stal.acadProgCode',
    label: 'Academic Program',
    type: 'string',
    input: 'select',
    plugin: 'selectize',
    multiple: true,
    plugin_config: {
      valueField: 'id',
      labelField: 'name',
      searchField: 'name',
      sortField: 'name',
      create: true,
      maxItems: 10,
      plugins: ['remove_button']
    },
    values: {
        <?php get_rlde_prog(); ?>
    },
    valueSetter: function (rule, value) {
        rule.$el.find('.rule-value-container select')[0].selectize.setValue(value);
    },
    optgroup: 'stal'
  },
  {
    id: 'stal.acadLevelCode',
    label: 'Academic Level',
    type: 'string',
    input: 'select',
    plugin: 'selectize',
    multiple: true,
    plugin_config: {
      valueField: 'id',
      labelField: 'name',
      searchField: 'name',
      sortField: 'name',
      create: true,
      maxItems: 10,
      plugins: ['remove_button']
    },
    values: {
        <?php get_rlde_acad_levels(); ?>
    },
    valueSetter: function (rule, value) {
        rule.$el.find('.rule-value-container select')[0].selectize.setValue(value);
    },
    optgroup: 'stal'
  },
  {
    id: 'stal.currentClassLevel',
    label: 'Class Level',
    type: 'string',
    input: 'select',
    plugin: 'selectize',
    multiple: true,
    plugin_config: {
      valueField: 'id',
      labelField: 'name',
      searchField: 'name',
      sortField: 'name',
      create: true,
      maxItems: 10,
      plugins: ['remove_button']
    },
    values: {
        <?php get_rlde_clas_levels(); ?>
    },
    valueSetter: function (rule, value) {
        rule.$el.find('.rule-value-container select')[0].selectize.setValue(value);
    },
    optgroup: 'stal'
  },
  {
    id: 'stal.gpa',
    label: 'Cum GPA',
    type: 'double',
    optgroup: 'stal',
    validation: {
      min: 0,
      step: 0.01
    }
  },
  /*
   * Student Creds
   */
  {
    id: 'v_scrd.attempted',
    label: 'Total Attempted Credits',
    type: 'double',
    optgroup: 'v_scrd',
    validation: {
      min: 0,
      step: 0.01
    }
  },
  {
    id: 'v_scrd.completed',
    label: 'Total Completed Credits',
    type: 'double',
    optgroup: 'v_scrd',
    validation: {
      min: 0,
      step: 0.01
    }
  },
  {
    id: 'v_scrd.points',
    label: 'Total Grade Points',
    type: 'double',
    optgroup: 'v_scrd',
    validation: {
      min: 0,
      step: 0.01
    }
  }
  ]
};

// init
$('#builder').queryBuilder(options);
<?php if(!empty(_escape($crse->rule))) : ?>
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