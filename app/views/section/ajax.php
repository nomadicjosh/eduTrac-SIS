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
        
        $("#btn_loc").click(function (e) {
            e.preventDefault();
            var locationCode = $("#newLocCode").val();
            var locationName = $("#locationName").val();
            var dataString = 'locationCode=' + locationCode + '&locationName=' + locationName;
            if (locationCode == '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function() { $(".flash").hide(); }, 3000);
            }
            if (locationName == '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function() { $(".flash").hide(); }, 3000);
            }
            else
            {
                $.ajax({
                    type: "POST",
                    url: "<?= url('/sect/loc/'); ?>",
                    data: dataString,
                    dataType: 'json',
                    success: function (data) {
                        $('.alerts-success').fadeIn(200).show();
                        $('.alerts-error').fadeOut(200).hide();
                        setTimeout(function() { $(".flash").hide(); }, 3000);
                        $("#locForm")[0].reset();
                        $('#locationCode').append($('<option>', {
                            value: data.locationCode,
                            text: locationCode + ' ' + locationName
                        }));
                        $("#divLoc").load(location.href + " #divLoc>*","");
                    }
                });
            }
            return false;
        });
    });
</script>

<div class="modal fade" id="loc">
    <form class="form-horizontal margin-none" id="locForm" autocomplete="off">
        <div class="dialog-form modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?= _t('Location'); ?></h3>
                </div>
                <!-- // Modal heading END -->
                <div class="modal-body">

                    <div class="flash alerts alerts-error center"><?= _t('You must fill out the required fields.'); ?></div>
                    <div class="flash alerts alerts-success center"><?= _t('The Location was created successfully.'); ?></div>
                    
                    <div class="center">&nbsp;</div>

                    <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Location Code' );?></label>
                            <div class="col-md-8"><input class="form-control" name="locationCode" id="newLocCode" type="text" required /></div>
                        </div>
                        <!-- // Group END -->
                        
                        <!-- Group -->
                        <div class="form-group">
                            <label class="col-md-3 control-label"><font color="red">*</font> <?=_t( 'Location Name' );?></label>
                            <div class="col-md-8"><input class="form-control" name="locationName" id="locationName" type="text" required /></div>
                        </div>
                        <!-- // Group END -->

                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_loc" class="btn btn-icon btn-default"><i></i><?= _t('Add'); ?></button>
                    <button type="button" data-dismiss="modal" class="btn btn-icon btn-primary"><i></i><?= _t('Cancel'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>