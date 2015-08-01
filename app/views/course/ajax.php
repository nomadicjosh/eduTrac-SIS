<?php if ( ! defined('BASE_PATH') ) exit('No direct script access allowed'); ?>
<script type="text/javascript">
    $(function () {
        $('.flash').fadeOut(200).hide();
        
        $("#btn_dept").click(function (e) {
            e.preventDefault();
            var deptCode = $("#newDeptCode").val();
            var deptTypeCode = $("#deptType").val();
            var deptName = $("#deptName").val();
            var deptEmail = $("#deptEmail").val();
            var deptPhone = $("#deptPhone").val();
            var deptDesc = $("#deptDesc").val();
            var dataString = 'deptCode=' + deptCode + '&deptTypeCode=' + deptTypeCode + '&deptName=' + deptName +
                    '&deptEmail=' + deptEmail + '&deptPhone=' + deptPhone + '&deptDesc=' + deptDesc;
            if (deptCode == '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function() { $(".flash").hide(); }, 3000);
            }
            if (deptTypeCode == '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function() { $(".flash").hide(); }, 3000);
            }
            if (deptName == '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function() { $(".flash").hide(); }, 3000);
            }
            else
            {
                $.ajax({
                    type: "POST",
                    url: "<?= url('/crse/dept/'); ?>",
                    data: dataString,
                    dataType: 'json',
                    success: function (data) {
                        $('.alerts-success').fadeIn(200).show();
                        $('.alerts-error').fadeOut(200).hide();
                        setTimeout(function() { $(".flash").hide(); }, 3000);
                        $("#deptForm")[0].reset();
                        $('#deptCode').append($('<option>', {
                            value: data.deptCode,
                            text: deptCode + ' ' + deptName
                        }));
                        $("#divDept").load(location.href + " #divDept>*","");
                    }
                });
            }
            return false;
        });
    });
</script>

<div class="modal fade" id="dept">
    <form class="form-horizontal margin-none" id="deptForm" autocomplete="off">
        <div class="dialog-form modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?= _t('Department'); ?></h3>
                </div>
                <!-- // Modal heading END -->
                <div class="modal-body">

                    <div class="flash alerts alerts-error center"><?= _t('You must fill out the required fields.'); ?></div>
                    <div class="flash alerts alerts-success center"><?= _t('The Department was created successfully.'); ?></div>
                    
                    <div class="center">&nbsp;</div>

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Department Code'); ?></label>
                        <div class="col-md-8">
                            <input class="form-control" id="newDeptCode" type="text" name="deptCode" required/>
                        </div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Department Type'); ?></label>
                        <div class="col-md-8">
                            <?= dept_type_select(); ?>
                        </div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Department Name'); ?></label>
                        <div class="col-md-8">
                            <input class="form-control" id="deptName" type="text" name="deptName" required/>
                        </div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?= _t('Department Email'); ?></label>
                        <div class="col-md-8">
                            <input class="form-control" id="deptEmail" type="text" name="deptEmail" />
                        </div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?= _t('Department Phone'); ?></label>
                        <div class="col-md-8">
                            <input class="form-control" id="deptPhone" type="text" name="deptPhone" />
                        </div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?= _t('Short Description'); ?></label>
                        <div class="col-md-8">
                            <input class="form-control" id="deptDesc" type="text" name="deptDesc" />
                        </div>
                    </div>
                    <!-- // Group END -->
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_dept" class="btn btn-icon btn-default"><i></i><?= _t('Add'); ?></button>
                    <button type="button" data-dismiss="modal" class="btn btn-icon btn-primary"><i></i><?= _t('Cancel'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function () {
        $('.flash').fadeOut(200).hide();
        
        $("#btn_subj").click(function (e) {
            e.preventDefault();
            var subjectCode = $("#newSubjCode").val();
            var subjectName = $("#subjectName").val();
            var dataString = 'subjectCode=' + subjectCode + '&subjectName=' + subjectName;
            if (subjectCode == '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function() { $(".flash").hide(); }, 3000);
            }
            if (subjectName == '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function() { $(".flash").hide(); }, 3000);
            }
            else
            {
                $.ajax({
                    type: "POST",
                    url: "<?= url('/crse/subj/'); ?>",
                    data: dataString,
                    dataType: 'json',
                    success: function (data) {
                        $('.alerts-success').fadeIn(200).show();
                        $('.alerts-error').fadeOut(200).hide();
                        setTimeout(function() { $(".flash").hide(); }, 3000);
                        $("#subjForm")[0].reset();
                        $('#subjectCode').append($('<option>', {
                            value: data.subjectCode,
                            text: subjectCode + ' ' + subjectName
                        }));
                        $("#divSubj").load(location.href + " #divSubj>*","");
                    }
                });
            }
            return false;
        });
    });
</script>

<div class="modal fade" id="subj">
    <form class="form-horizontal margin-none" id="subjForm" autocomplete="off">
        <div class="dialog-form modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?= _t('Subject'); ?></h3>
                </div>
                <!-- // Modal heading END -->
                <div class="modal-body">

                    <div class="flash alerts alerts-error center"><?= _t('You must fill out the required fields.'); ?></div>
                    <div class="flash alerts alerts-success center"><?= _t('The Subject was created successfully.'); ?></div>
                    
                    <div class="center">&nbsp;</div>

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Subject Code'); ?></label>
                        <div class="col-md-8">
                            <input class="form-control" id="newSubjCode" type="text" name="subjectCode" required/>
                        </div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Subject Name'); ?></label>
                        <div class="col-md-8">
                            <input class="form-control" id="subjectName" type="text" name="subjectName" required/>
                        </div>
                    </div>
                    <!-- // Group END -->

                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_subj" class="btn btn-icon btn-default"><i></i><?= _t('Add'); ?></button>
                    <button type="button" data-dismiss="modal" class="btn btn-icon btn-primary"><i></i><?= _t('Cancel'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>