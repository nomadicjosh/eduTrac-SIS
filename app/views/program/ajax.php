<?php if (!defined('BASE_PATH')) exit('No direct script access allowed'); ?>
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
            if (deptCode === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            if (deptTypeCode === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            if (deptName === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
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
                        setTimeout(function () {
                            $(".flash").hide();
                        }, 3000);
                        $("#deptForm")[0].reset();
                        $('#deptCode').append($('<option>', {
                            value: data.deptCode,
                            text: deptCode + ' ' + deptName
                        }));
                        $("#divDept").load(location.href + " #divDept>*", "");
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

        $("#btn_year").click(function (e) {
            e.preventDefault();
            var acadYearCode = $("#newYearCode").val();
            var acadYearDesc = $("#acadYearDesc").val();
            var dataString = 'acadYearCode=' + acadYearCode + '&acadYearDesc=' + acadYearDesc;
            if (acadYearCode === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            if (acadYearDesc === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            else
            {
                $.ajax({
                    type: "POST",
                    url: "<?= url('/program/year/'); ?>",
                    data: dataString,
                    dataType: 'json',
                    success: function (data) {
                        $('.alerts-success').fadeIn(200).show();
                        $('.alerts-error').fadeOut(200).hide();
                        setTimeout(function () {
                            $(".flash").hide();
                        }, 3000);
                        $("#yearForm")[0].reset();
                        $('#acadYearCode').append($('<option>', {
                            value: data.acadYearCode,
                            text: acadYearCode + ' ' + acadYearDesc
                        }));
                        $("#divYear").load(location.href + " #divYear>*", "");
                    }
                });
            }
            return false;
        });
    });
</script>

<div class="modal fade" id="year">
    <form class="form-horizontal margin-none" id="yearForm" autocomplete="off">
        <div class="dialog-form modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?= _t('Academic Year'); ?></h3>
                </div>
                <!-- // Modal heading END -->
                <div class="modal-body">

                    <div class="flash alerts alerts-error center"><?= _t('You must fill out the required fields.'); ?></div>
                    <div class="flash alerts alerts-success center"><?= _t('The Academic Year was created successfully.'); ?></div>

                    <div class="center">&nbsp;</div>

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Acad Year Code'); ?></label>
                        <div class="col-md-8">
                            <input class="form-control" id="newYearCode" type="text" name="acadYearCode" required/>
                        </div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Acad Year Description'); ?></label>
                        <div class="col-md-8">
                            <input class="form-control" id="acadYearDesc" type="text" name="acadYearDesc" required/> <br />example: 2012/13 Academic Year
                        </div>
                    </div>
                    <!-- // Group END -->

                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_year" class="btn btn-icon btn-default"><i></i><?= _t('Add'); ?></button>
                    <button type="button" data-dismiss="modal" class="btn btn-icon btn-primary"><i></i><?= _t('Cancel'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function () {
        $('.flash').fadeOut(200).hide();

        $("#btn_degree").click(function (e) {
            e.preventDefault();
            var degreeCode = $("#newDegreeCode").val();
            var degreeName = $("#degreeName").val();
            var dataString = 'degreeCode=' + degreeCode + '&degreeName=' + degreeName;
            if (degreeCode === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            if (degreeName === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            else
            {
                $.ajax({
                    type: "POST",
                    url: "<?= url('/program/degree/'); ?>",
                    data: dataString,
                    dataType: 'json',
                    success: function (data) {
                        $('.alerts-success').fadeIn(200).show();
                        $('.alerts-error').fadeOut(200).hide();
                        setTimeout(function () {
                            $(".flash").hide();
                        }, 3000);
                        $("#degreeForm")[0].reset();
                        $('#degreeCode').append($('<option>', {
                            value: data.degreeCode,
                            text: degreeCode + ' ' + degreeName
                        }));
                        $("#divDegree").load(location.href + " #divDegree>*", "");
                    }
                });
            }
            return false;
        });
    });
</script>

<div class="modal fade" id="degree">
    <form class="form-horizontal margin-none" id="degreeForm" autocomplete="off">
        <div class="dialog-form modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?= _t('Degree'); ?></h3>
                </div>
                <!-- // Modal heading END -->
                <div class="modal-body">

                    <div class="flash alerts alerts-error center"><?= _t('You must fill out the required fields.'); ?></div>
                    <div class="flash alerts alerts-success center"><?= _t('The Degree was created successfully.'); ?></div>

                    <div class="center">&nbsp;</div>

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Degree Code'); ?></label>
                        <div class="col-md-8">
                            <input class="form-control" id="newDegreeCode" type="text" name="degreeCode" required/> <br />example: B.S.
                        </div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Degree Name'); ?></label>
                        <div class="col-md-8">
                            <input class="form-control" id="degreeName" type="text" name="degreeName" required/>
                        </div>
                    </div>
                    <!-- // Group END -->

                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_degree" class="btn btn-icon btn-default"><i></i><?= _t('Add'); ?></button>
                    <button type="button" data-dismiss="modal" class="btn btn-icon btn-primary"><i></i><?= _t('Cancel'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function () {
        $('.flash').fadeOut(200).hide();

        $("#btn_ccd").click(function (e) {
            e.preventDefault();
            var ccdCode = $("#newCCDCode").val();
            var ccdName = $("#ccdName").val();
            var addDate = $("#addDate").val();
            var dataString = 'ccdCode=' + ccdCode + '&ccdName=' + ccdName +
                    '&addDate=' + addDate;
            if (ccdCode === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            if (ccdName === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            else
            {
                $.ajax({
                    type: "POST",
                    url: "<?= url('/program/ccd/'); ?>",
                    data: dataString,
                    dataType: 'json',
                    success: function (data) {
                        $('.alerts-success').fadeIn(200).show();
                        $('.alerts-error').fadeOut(200).hide();
                        setTimeout(function () {
                            $(".flash").hide();
                        }, 3000);
                        $("#ccdForm")[0].reset();
                        $('#ccdCode').append($('<option>', {
                            value: data.ccdCode,
                            text: ccdCode + ' ' + ccdName
                        }));
                        $("#divCCD").load(location.href + " #divCCD>*", "");
                    }
                });
            }
            return false;
        });
    });
</script>

<div class="modal fade" id="ccd">
    <form class="form-horizontal margin-none" id="ccdForm" autocomplete="off">
        <div class="dialog-form modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?= _t('CCD'); ?></h3>
                </div>
                <!-- // Modal heading END -->
                <div class="modal-body">

                    <div class="flash alerts alerts-error center"><?= _t('You must fill out the required fields.'); ?></div>
                    <div class="flash alerts alerts-success center"><?= _t('The CCD was created successfully.'); ?></div>

                    <div class="center">&nbsp;</div>

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Location Code'); ?></label>
                        <div class="col-md-8"><input class="form-control" name="ccdCode" id="newCCDCode" type="text" required /></div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Location Name'); ?></label>
                        <div class="col-md-8"><input class="form-control" name="ccdName" id="ccdName" type="text" required /></div>
                    </div>
                    <!-- // Group END -->

                </div>
                <div class="modal-footer">
                    <input name="addDate" id="addDate" value="<?= date('Y-m-d'); ?>" type="hidden" />
                    <button type="button" id="btn_ccd" class="btn btn-icon btn-default"><i></i><?= _t('Add'); ?></button>
                    <button type="button" data-dismiss="modal" class="btn btn-icon btn-primary"><i></i><?= _t('Cancel'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function () {
        $('.flash').fadeOut(200).hide();

        $("#btn_major").click(function (e) {
            e.preventDefault();
            var majorCode = $("#newMajorCode").val();
            var majorName = $("#majorName").val();
            var dataString = 'majorCode=' + majorCode + '&majorName=' + majorName;
            if (majorCode === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            if (majorName === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            else
            {
                $.ajax({
                    type: "POST",
                    url: "<?= url('/program/major/'); ?>",
                    data: dataString,
                    dataType: 'json',
                    success: function (data) {
                        $('.alerts-success').fadeIn(200).show();
                        $('.alerts-error').fadeOut(200).hide();
                        setTimeout(function () {
                            $(".flash").hide();
                        }, 3000);
                        $("#majorForm")[0].reset();
                        $('#majorCode').append($('<option>', {
                            value: data.majorCode,
                            text: majorCode + ' ' + majorName
                        }));
                        $("#divMajor").load(location.href + " #divMajor>*", "");
                    }
                });
            }
            return false;
        });
    });
</script>

<div class="modal fade" id="major">
    <form class="form-horizontal margin-none" id="majorForm" autocomplete="off">
        <div class="dialog-form modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?= _t('Major'); ?></h3>
                </div>
                <!-- // Modal heading END -->
                <div class="modal-body">

                    <div class="flash alerts alerts-error center"><?= _t('You must fill out the required fields.'); ?></div>
                    <div class="flash alerts alerts-success center"><?= _t('The Major was created successfully.'); ?></div>

                    <div class="center">&nbsp;</div>

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Major Code'); ?></label>
                        <div class="col-md-8"><input class="form-control" name="majorCode" id="newMajorCode" type="text" required /></div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Major Name'); ?></label>
                        <div class="col-md-8"><input class="form-control" name="majorName" id="majorName" type="text" required /></div>
                    </div>
                    <!-- // Group END -->

                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_major" class="btn btn-icon btn-default"><i></i><?= _t('Add'); ?></button>
                    <button type="button" data-dismiss="modal" class="btn btn-icon btn-primary"><i></i><?= _t('Cancel'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function () {
        $('.flash').fadeOut(200).hide();

        $("#btn_minor").click(function (e) {
            e.preventDefault();
            var minorCode = $("#newMinorCode").val();
            var minorName = $("#minorName").val();
            var dataString = 'minorCode=' + minorCode + '&minorName=' + minorName;
            if (minorCode === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            if (minorName === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            else
            {
                $.ajax({
                    type: "POST",
                    url: "<?= url('/program/minor/'); ?>",
                    data: dataString,
                    dataType: 'json',
                    success: function (data) {
                        $('.alerts-success').fadeIn(200).show();
                        $('.alerts-error').fadeOut(200).hide();
                        setTimeout(function () {
                            $(".flash").hide();
                        }, 3000);
                        $("#minorForm")[0].reset();
                        $('#minorCode').append($('<option>', {
                            value: data.minorCode,
                            text: minorCode + ' ' + minorName
                        }));
                        $("#divMinor").load(location.href + " #divMinor>*", "");
                    }
                });
            }
            return false;
        });
    });
</script>

<div class="modal fade" id="minor">
    <form class="form-horizontal margin-none" id="minorForm" autocomplete="off">
        <div class="dialog-form modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?= _t('Minor'); ?></h3>
                </div>
                <!-- // Modal heading END -->
                <div class="modal-body">

                    <div class="flash alerts alerts-error center"><?= _t('You must fill out the required fields.'); ?></div>
                    <div class="flash alerts alerts-success center"><?= _t('The Minor was created successfully.'); ?></div>

                    <div class="center">&nbsp;</div>

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Minor Code'); ?></label>
                        <div class="col-md-8"><input class="form-control" name="minorCode" id="newMinorCode" type="text" required /></div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Minor Name'); ?></label>
                        <div class="col-md-8"><input class="form-control" name="minorName" id="minorName" type="text" required /></div>
                    </div>
                    <!-- // Group END -->

                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_minor" class="btn btn-icon btn-default"><i></i><?= _t('Add'); ?></button>
                    <button type="button" data-dismiss="modal" class="btn btn-icon btn-primary"><i></i><?= _t('Cancel'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function () {
        $('.flash').fadeOut(200).hide();

        $("#btn_spec").click(function (e) {
            e.preventDefault();
            var specCode = $("#newSpecCode").val();
            var specName = $("#specName").val();
            var dataString = 'specCode=' + specCode + '&specName=' + specName;
            if (specCode === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            if (specName === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            else
            {
                $.ajax({
                    type: "POST",
                    url: "<?= url('/program/spec/'); ?>",
                    data: dataString,
                    dataType: 'json',
                    success: function (data) {
                        $('.alerts-success').fadeIn(200).show();
                        $('.alerts-error').fadeOut(200).hide();
                        setTimeout(function () {
                            $(".flash").hide();
                        }, 3000);
                        $("#specForm")[0].reset();
                        $('#specCode').append($('<option>', {
                            value: data.specCode,
                            text: specCode + ' ' + specName
                        }));
                        $("#divSpec").load(location.href + " #divSpec>*", "");
                    }
                });
            }
            return false;
        });
    });
</script>

<div class="modal fade" id="spec">
    <form class="form-horizontal margin-none" id="specForm" autocomplete="off">
        <div class="dialog-form modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?= _t('Specialization'); ?></h3>
                </div>
                <!-- // Modal heading END -->
                <div class="modal-body">

                    <div class="flash alerts alerts-error center"><?= _t('You must fill out the required fields.'); ?></div>
                    <div class="flash alerts alerts-success center"><?= _t('The Specialization was created successfully.'); ?></div>

                    <div class="center">&nbsp;</div>

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Specialization Code'); ?></label>
                        <div class="col-md-8"><input class="form-control" name="specCode" id="newSpecCode" type="text" required /></div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Specialization Name'); ?></label>
                        <div class="col-md-8"><input class="form-control" name="specName" id="specName" type="text" required /></div>
                    </div>
                    <!-- // Group END -->

                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_spec" class="btn btn-icon btn-default"><i></i><?= _t('Add'); ?></button>
                    <button type="button" data-dismiss="modal" class="btn btn-icon btn-primary"><i></i><?= _t('Cancel'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(function () {
        $('.flash').fadeOut(200).hide();

        $("#btn_cip").click(function (e) {
            e.preventDefault();
            var cipCode = $("#newCIPCode").val();
            var cipName = $("#cipName").val();
            var dataString = 'cipCode=' + cipCode + '&cipName=' + cipName;
            if (cipCode === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            if (cipName === '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            else
            {
                $.ajax({
                    type: "POST",
                    url: "<?= url('/program/cip/'); ?>",
                    data: dataString,
                    dataType: 'json',
                    success: function (data) {
                        $('.alerts-success').fadeIn(200).show();
                        $('.alerts-error').fadeOut(200).hide();
                        setTimeout(function () {
                            $(".flash").hide();
                        }, 3000);
                        $("#cipForm")[0].reset();
                        $('#cipCode').append($('<option>', {
                            value: data.cipCode,
                            text: cipCode + ' ' + cipName
                        }));
                        $("#divCIP").load(location.href + " #divCIP>*", "");
                    }
                });
            }
            return false;
        });
    });
</script>

<div class="modal fade" id="cip">
    <form class="form-horizontal margin-none" id="cipForm" autocomplete="off">
        <div class="dialog-form modal-dialog">
            <div class="modal-content">
                <!-- Modal heading -->
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title"><?= _t('CIP'); ?></h3>
                </div>
                <!-- // Modal heading END -->
                <div class="modal-body">

                    <div class="flash alerts alerts-error center"><?= _t('You must fill out the required fields.'); ?></div>
                    <div class="flash alerts alerts-success center"><?= _t('The CIP was created successfully.'); ?></div>

                    <div class="center">&nbsp;</div>

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('CIP Code'); ?></label>
                        <div class="col-md-8"><input class="form-control" name="cipCode" id="newCIPCode" type="text" required /></div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('CIP Name'); ?></label>
                        <div class="col-md-8"><input class="form-control" name="cipName" id="cipName" type="text" required /></div>
                    </div>
                    <!-- // Group END -->

                </div>
                <div class="modal-footer">
                    <button type="button" id="btn_cip" class="btn btn-icon btn-default"><i></i><?= _t('Add'); ?></button>
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
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
            }
            if (locationName == '')
            {
                $('.alerts-success').fadeOut(200).hide();
                $('.alerts-error').fadeIn(200).show();
                setTimeout(function () {
                    $(".flash").hide();
                }, 3000);
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
                        setTimeout(function () {
                            $(".flash").hide();
                        }, 3000);
                        $("#locForm")[0].reset();
                        $('#locationCode').append($('<option>', {
                            value: data.locationCode,
                            text: locationCode + ' ' + locationName
                        }));
                        $("#divLoc").load(location.href + " #divLoc>*", "");
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
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Location Code'); ?></label>
                        <div class="col-md-8"><input class="form-control" name="locationCode" id="newLocCode" type="text" required /></div>
                    </div>
                    <!-- // Group END -->

                    <!-- Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label"><font color="red">*</font> <?= _t('Location Name'); ?></label>
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