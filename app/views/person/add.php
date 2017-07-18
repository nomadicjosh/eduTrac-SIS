<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
/**
 * NAE New Record View
 *  
 * This view is used when creating a new person record
 * via the NAE screen.
 * 
 * @license GPLv3
 * 
 * @since       3.0.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
$app = \Liten\Liten::getInstance();
$app->view->extend('_layouts/dashboard');
$app->view->block('dashboard');
$screen = 'anae';
$tags = "{tag: '".implode("'},{tag: '", get_nae_tags())."'}";
?>

<script type="text/javascript">
    $(document).ready(function () {
        $('#uname').keyup(username_check);
    });

    function username_check() {
        var uname = $('#uname').val();
        if (uname == "" || uname.length < 4) {
            $('#uname').css('border', '3px #CCC solid');
            $('#tick').hide();
        } else {

            jQuery.ajax({
                type: "POST",
                url: "<?= get_base_url(); ?>nae/usernameCheck/",
                data: 'uname=' + uname,
                cache: false,
                success: function (response) {
                    if (response == 1) {
                        $('#uname').css('border', '3px #C33 solid');
                        $('#tick').hide();
                        $('#cross').fadeIn();
                    } else {
                        $('#uname').css('border', '3px #090 solid');
                        $('#cross').hide();
                        $('#tick').fadeIn();
                    }

                }
            });
        }
    };
</script>

<ul class="breadcrumb">
    <li><?= _t('You are here'); ?></li>
    <li><a href="<?= get_base_url(); ?>dashboard/" class="glyphicons dashboard"><i></i> <?= _t('Dashboard'); ?></a></li>
    <li class="divider"></li>
    <li><?= _t('Name & Address'); ?></li>
</ul>

<h3><?= _t('Name & Address'); ?></h3>
<div class="innerLR">

    <?= _etsis_flash()->showMessage(); ?>

    <?php jstree_sidebar_menu($screen); ?>

    <!-- Form -->
    <form class="form-horizontal margin-none grid-form" action="<?= get_base_url(); ?>nae/add/" id="validateSubmitForm" method="post" autocomplete="off">

        <!-- Widget -->
        <div class="widget widget-heading-simple widget-body-white <?= ($app->hook->has_filter('sidebar_menu')) ? 'col-md-12' : 'col-md-10'; ?>">

            <!-- Widget heading -->
            <div class="widget-head">
                <h4 class="heading"><font color="red">*</font> <?= _t('Indicates field is required'); ?></h4>
            </div>
            <!-- // Widget heading END -->

            <div class="widget-body">

                <fieldset>
                    <legend><?= _t('Personal Details'); ?></legend>
                    <div data-row-span="3">
                        <div data-field-span="1">
                            <label><?= _t('Username'); ?></label>
                            <input type="text" id="uname" name="uname" value="<?=(isset($app->req->post['uname'])) ? $app->req->post['uname'] : '';?>" />
                        </div>
                        <div data-field-span="1" class="<?=etsis_field_css_class('nae_altID');?>">
                            <label><?= _t('Alternate ID'); ?> <a href="#altID" data-toggle="modal"><img src="<?= get_base_url(); ?>static/common/theme/images/help.png" /></a></label>
                            <input type="text" name="altID" value="<?= $app->req->post['altID']; ?>" />
                        </div>
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?= _t('Person Type'); ?> <a href="#myModal" data-toggle="modal"><img src="<?= get_base_url(); ?>static/common/theme/images/help.png" /></a></label>
                            <?= person_type_select((isset($app->req->post['personType']) ? $app->req->post['personType'] : '')); ?>
                        </div>
                    </div>

                    <div data-row-span="5">
                        <div data-field-span="1" class="<?=etsis_field_css_class('nae_prefix');?>">
                            <label><?= _t('Prefix'); ?></label>
                            <select name="prefix" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                <option value="">&nbsp;</option>
                                <option value="Ms"<?= selected('Ms', $app->req->post['prefix'], false); ?>><?= _t('Ms.'); ?></option>
                                <option value="Miss"<?= selected('Miss', $app->req->post['prefix'], false); ?>><?= _t('Miss.'); ?></option>
                                <option value="Mrs"<?= selected('Mrs', $app->req->post['prefix'], false); ?>><?= _t('Mrs.'); ?></option>
                                <option value="Mr"<?= selected('Mr', $app->req->post['prefix'], false); ?>><?= _t('Mr.'); ?></option> 
                                <option value="Dr"<?= selected('Dr', $app->req->post['prefix'], false); ?>><?= _t('Dr.'); ?></option>
                            </select>
                        </div>
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?= _t('First Name'); ?></label>
                            <input type="text" name="fname"value="<?= $app->req->post['fname']; ?>" required />
                        </div>
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?= _t('Last Name'); ?></label>
                            <input type="text" name="lname" value="<?= $app->req->post['lname']; ?>" required />
                        </div>
                        <div data-field-span="1" class="<?=etsis_field_css_class('nae_mname');?>">
                            <label><?= _t('Middle Initial'); ?></label>
                            <input type="text" name="mname" value="<?= $app->req->post['mname']; ?>" />
                        </div>
                        <div data-field-span="1" class="<?=etsis_field_css_class('nae_ssn');?>">
                            <label><?= _t('Social Security #'); ?></label>
                            <input type="text" name="ssn" value="<?= $app->req->post['ssn']; ?>" />
                        </div>
                    </div>

                    <div data-row-span="4">
                        <div data-field-span="1">
                            <label><?= _t('Date of Birth'); ?></label>
                            <div class="input-group date col-md-8" id="datepicker6">
                                <input class="form-control" name="dob" type="text" value="<?= $app->req->post['dob']; ?>" />
                                <span class="input-group-addon"><i class="fa fa-th"></i></span>
                            </div>
                        </div>
                        <div data-field-span="1">
                            <label><?= _t('Gender'); ?></label>
                            <select name="gender" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                <option value="">&nbsp;</option>
                                <option value="M"<?php
                                if ($app->req->post['gender'] == 'M') {
                                    echo ' selected="selected"';
                                }

                                ?>><?= _t('Male'); ?></option>
                                <option value="F"<?php
                                        if ($app->req->post['gender'] == 'F') {
                                            echo ' selected="selected"';
                                        }

                                ?>><?= _t('Female'); ?></option>
                            </select>
                        </div>
                        <div data-field-span="1" class="<?=etsis_field_css_class('nae_veteran');?>">
                            <label><font color="red">*</font> <?= _t('Veteran?'); ?></label>
                            <select name="veteran" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"<?= pio(); ?> required>
                                <option value="">&nbsp;</option>
                                <option value="1"<?php
                                if ($app->req->post['veteran'] == 1) {
                                    echo ' selected="selected"';
                                }

                                ?>><?= _t('Yes'); ?></option>
                                <option value="0"<?php
                                if ($app->req->post['veteran'] == 0) {
                                    echo ' selected="selected"';
                                }

                                ?>><?= _t('No'); ?></option>
                            </select>
                        </div>
                        <div data-field-span="1" class="<?=etsis_field_css_class('nae_ethnicity');?>">
                            <label><?= _t('Ethnicity?'); ?></label>
                            <select name="ethnicity"<?= pio(); ?> class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                <option value="">&nbsp;</option>
                                <option value="White, Non-Hispanic"<?= selected('White, Non-Hispanic', _h($app->req->post['ethnicity']), false); ?>><?= _t('White, Non-Hispanic'); ?></option>
                                <option value="Black, Non-Hispanic"<?= selected('Black, Non-Hispanic', _h($app->req->post['ethnicity']), false); ?>><?= _t('Black, Non-Hispanic'); ?></option>
                                <option value="Hispanic"<?= selected('Hispanic', _h($app->req->post['ethnicity']), false); ?>><?= _t('Hispanic'); ?></option>
                                <option value="Native American"<?= selected('Native American', _h($app->req->post['ethnicity']), false); ?>><?= _t('Native American'); ?></option>
                                <option value="Native Alaskan"<?= selected('Native Alaskan', _h($app->req->post['ethnicity']), false); ?>><?= _t('Native Alaskan'); ?></option>
                                <option value="Pacific Islander"<?= selected('Pacific Islander', _h($app->req->post['ethnicity']), false); ?>><?= _t('Pacific Islander'); ?></option>
                                <option value="Asian"<?= selected('Asian', _h($app->req->post['ethnicity']), false); ?>><?= _t('Asian'); ?></option>
                                <option value="Indian"<?= selected('Indian', _h($app->req->post['ethnicity']), false); ?>><?= _t('Indian'); ?></option>
                                <option value="Middle Eastern"<?= selected('Middle Eastern', _h($app->req->post['ethnicity']), false); ?>><?= _t('Middle Eastern'); ?></option>
                                <option value="African"<?= selected('African', _h($app->req->post['ethnicity']), false); ?>><?= _t('African'); ?></option>
                                <option value="Mixed Race"<?= selected('Mixed Race', _h($app->req->post['ethnicity']), false); ?>><?= _t('Mixed Race'); ?></option>
                                <option value="Other"<?= selected('Other', _h($app->req->post['ethnicity']), false); ?>><?= _t('Other'); ?></option>
                            </select>
                        </div>
                    </div>

                    <br /><br />

                    <div data-field-span="2">
                        <legend><?= _t('Emergency Contact'); ?></legend>
                        <div data-row-span="2">
                            <div data-field-span="1">
                                <label><?= _t("Emergency Contact's Name"); ?></label>
                                <input type="text" name="emergency_contact" value="<?= _h($app->req->post['emergency_contact']); ?>" />
                            </div>
                            <div data-field-span="1">
                                <label><?= _t("Emergency Contact's Phone"); ?></label>
                                <input type="text" name="emergency_contact_phone" value="<?= _h($app->req->post['emergency_contact_phone']); ?>" />
                            </div>
                        </div>
                    </div>
                </fieldset>

                <br /><br />

                <fieldset>
                    <legend><?= _t('Mailing Address & Contact Details'); ?></legend>
                    <div data-row-span="2">
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?= _t('Address1'); ?></label>
                            <input type="text" name="address1" value="<?= _h($app->req->post['address1']); ?>" required />
                        </div>
                        <div data-field-span="1">
                            <label><?= _t('Address2'); ?></label>
                            <input type="text" name="address2" value="<?= _h($app->req->post['address2']); ?>" />
                        </div>
                    </div>
                    <div data-row-span="4">
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?= _t('City'); ?></label>
                            <input type="text" name="city" value="<?= _h($app->req->post['city']); ?>" required/>
                        </div>
                        <div data-field-span="1">
                            <label><?= _t('State'); ?></label>
                            <select name="state" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true">
                                    <option value="">&nbsp;</option>
                                    <?php table_dropdown('state',null,'code','code','name',(isset($app->req->post['state'])) ? $app->req->post['state'] : ''); ?>
                                </select>
                        </div>
                        <div data-field-span="1">
                            <label><?= _t('Zip/Postal Code'); ?></label>
                            <input type="text" name="zip" value="<?= _h($app->req->post['zip']); ?>" />
                        </div>
                        <div data-field-span="1">
                            <label><?= _t('Country'); ?></label>
                            <select name="country" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" >
                                <option value="">&nbsp;</option>
                                <?php table_dropdown('country',null,'iso2','iso2','short_name',(isset($app->req->post['country'])) ? $app->req->post['country'] : ''); ?>
                            </select>
                        </div>
                    </div>
                    <div data-row-span="2">
                        <div data-field-span="1">
                            <label><font color="red">*</font> <?= _t('Preferred Email'); ?></label>
                            <input type="email" name="email" value="<?= _h($app->req->post['email']); ?>" required />
                        </div>
                        <div data-field-span="1">
                            <label><?= _t('Phone'); ?></label>
                            <input type="text" name="phone" value="<?= _h($app->req->post['phone']); ?>" />
                        </div>
                    </div>
                </fieldset>

                <br /><br />
                
                <fieldset>
                    <legend><?= _t('Tags, Role and Login Details'); ?></legend>
                    <div data-row-span="3">
                        <div data-field-span="1">
                            <label><?= _t("Tags"); ?></label>
                            <input type="hidden" id="input-tags" name="tags" value="<?=(_h($app->req->post['tags']) != '' ? _h($app->req->post['tags']) : '');?>" />
                        </div>
                        <div data-field-span="1">
                            <label><?= _t("Person Role"); ?></label>
                            <select name="roleID" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true" required>
                                <option value="">&nbsp;</option>
                                <?=get_perm_roles();?>
                            </select>
                        </div>
                        <div data-field-span="1">
                            <label><?= _t("Login Details"); ?></label>
                            <input type="hidden" name="sendemail" value="dontsend" />
                            <input type="checkbox" name="sendemail" value="send" />
                            <?=_t( 'Send username & password to the user' );?>
                        </div>
                    </div>
                </fieldset>
                
                <br /><br />
                
                <?php 
                    /**
                     * NAE Form Field
                     * 
                     * Action will print a form field or any type of data
                     * at the end of the form.
                     * 
                     * @since 6.3.0
                     */
                    $app->hook->do_action('bottom_nae_new_form'); 
                ?>

                <fieldset>
                    <legend><?= _t('System Status'); ?></legend>
                    <div data-row-span="2">
                        <div data-field-span="1" class="readonly">
                            <label><?= _t('Approved Date'); ?></label>
                            <input type="text" value="<?=Jenssegers\Date\Date::now()->format('D, M d, o');?>" readonly/>
                        </div>
                        <div data-field-span="1" class="readonly">
                            <label><?= _t('Approved By'); ?></label>
                            <input type="text" value="<?= get_name(get_persondata('personID')); ?>" readonly/>
                        </div>
                    </div>
                </fieldset>

                <br /><br />

                <div class="modal fade" id="altID">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Modal heading -->
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="modal-title"><?= _t('Alternate ID'); ?></h3>
                            </div>
                            <!-- // Modal heading END -->
                            <div class="modal-body">
                                <p><?= _t("The unique ID for each person is autogenerated by the system. However, some institutions have their own format for person/student ID's. If this is the case for your institution, you can use this alternate ID field."); ?></p>
                            </div>
                            <div class="modal-footer">
                                <a href="#" data-dismiss="modal" class="btn btn-primary"><?= _t('Cancel'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Modal heading -->
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="modal-title"><?= _t('Person Type'); ?></h3>
                            </div>
                            <!-- // Modal heading END -->
                            <div class="modal-body">
                                <?= _file_get_contents(APP_PATH . 'Info/person-type.txt'); ?>
                            </div>
                            <div class="modal-footer">
                                <a href="#" data-dismiss="modal" class="btn btn-primary"><?= _t('Cancel'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="status">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <!-- Modal heading -->
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h3 class="modal-title"><?= _t('Person Status'); ?></h3>
                            </div>
                            <!-- // Modal heading END -->
                            <div class="modal-body">
                                <p><?= _t("The status on person records can be useful for when running reports, mail merge, etc in order to differentiate between 'active' and 'inactive' person records. However, when using student, staff or faculty records, it is best to join the 'person' table to those tables in order to pull their current status since the status from those tables might be more accurate than the status in the person table."); ?></p>
                            </div>
                            <div class="modal-footer">
                                <a href="#" data-dismiss="modal" class="btn btn-primary"><?= _t('Cancel'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form actions -->
                <div class="form-actions hidden-print">
                    <button type="submit" class="btn btn-icon btn-primary glyphicons circle_ok"><i></i><?= _t('Save'); ?></button>
                    <button type="button" class="btn btn-icon btn-primary glyphicons circle_minus" onclick="window.location = '<?= get_base_url(); ?>nae/'"><i></i><?= _t('Cancel'); ?></button>
                </div>
                <!-- // Form actions END -->

            </div>
        </div>
        <!-- // Widget END -->

    </form>
    <!-- // Form END -->

</div>	

</div>

<script src="<?=get_base_url();?>static/assets/components/modules/querybuilder/selectize/js/standalone/selectize.min.js" type="text/javascript"></script>
<script type="text/javascript">
$('#input-tags').selectize({
    plugins: ['remove_button'],
    delimiter: ',',
    persist: false,
    maxItems: null,
    valueField: 'tag',
    labelField: 'tag',
    searchField: ['tag'],
    options: [
        <?=$tags;?>
    ],
    render: {
        item: function(item, escape) {
            return '<div>' +
                (item.tag ? '<span class="tag">' + escape(item.tag) + '</span>' : '') +
            '</div>';
        },
        option: function(item, escape) {
            var caption = item.tag ? item.tag : null;
            return '<div>' +
                (caption ? '<span class="caption">' + escape(caption) + '</span>' : '') +
            '</div>';
        }
    },
    create: function(input) {
        return {
            tag: input
        };
    }
});
</script>

<!-- // Content END -->
<?php $app->view->stop(); ?>