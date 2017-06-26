<?php namespace app\src\Core;

if (!defined('BASE_PATH'))
    exit('No direct script access allowed');

class etsis_CustomField
{

    private $_fields = [];
    private $_required = false;
    private $_field_id = 1;
    private $_request_variables = [];
    private $_location = '';

    public function __construct($location = 'dashboard')
    {
        $this->_location = $location;
    }
    
    /**
     * Adds field elements.
     * 
     * @since 6.1.10
     * @param string $field_type Custom field type (text, textarea, select, checkbox, radio).
     * @param string $db_column Custom database column or field name.
     * @param string $label Label tag.
     * @param string $default_value Value pulled from the database.
     * @param bool $required Determines if field is required.
     * @param array $options Key value pairs for select options, checkbox options, or radio options.
     */
    public function add($field_type, $db_column, $label, $default_value = '', $required = false, $options = '')
    {
        $this->_fields[$this->_field_id] = [
            'type' => $field_type,
            'column' => $db_column,
            'label' => $label,
            'value' => $default_value,
            'required' => $required,
            'options' => $options
        ];
        if ($required) {
            $this->_required[] = $this->_field_id;
        }
        $this->_field_id++;

        return $this;
    }

    public function build()
    {
        $this->field_build();
    }

    private function field_build()
    {
        if ($this->_location == 'dashboard') {
            foreach ($this->_fields as $key => $field) {
                echo $this->custom_field($key, $field);
            }
        } else {
            foreach ($this->_fields as $key => $field) {
                echo $this->myetsis_appl_field($key, $field);
            }
        }
    }

    /**
     * Text Field
     * 
     * @since 6.1.10
     * @param type $key The key to the field.
     * @param type $field The value of the key.
     */
    private function text_field($key, $field)
    {
        $text = '<div class="form-group">' . "\n";
        $text .= '<label class="col-md-3 control-label">' . ($field['required'] == true ? '<font color="red">*</font> ' : '') . $field['label'] . '</label>' . "\n";
        $text .= '<div class="col-md-8"><input class="form-control" type="text" name="' . $field['column'] . '" value="' . (isset($this->_request_variables[$key]) ? $this->_request_variables[$key] : $field['value']) . '"' . ($field['required'] == true ? ' required' : ' ') . '/></div>' . "\n";
        $text .= '</div>' . "\n";
        return $text;
    }

    /**
     * Textarea Field
     * 
     * @since 6.1.10
     * @param type $key The key to the field.
     * @param type $field The value of the key.
     */
    private function textarea_field($key, $field)
    {
        $textarea = '<div class="form-group">';
        $textarea .= '<label class="col-md-3 control-label">' . ($field['required'] == true ? '<font color="red">*</font> ' : '') . $field['label'] . '</label>';
        $textarea .= '<div class="col-md-8"><textarea class="form-control"' . ($field['required'] == true ? ' required' : '') . '>' . (isset($this->_request_variables[$key]) ? $this->_request_variables[$key] : $field['value']) . '</textarea></div>';
        $textarea .= '</div>';
        return $textarea;
    }

    /**
     * Select Field
     * 
     * @since 6.1.10
     * @param type $key The key to the field.
     * @param type $field The value of the key.
     */
    private function select_field($key, $field)
    {
        $select = '<div class="form-group">' . "\n";
        $select .= '<label class="col-md-3 control-label">' . ($field['required'] == true ? '<font color="red">*</font> ' : '') . $field['label'] . '</label>' . "\n";
        $select .= '<div class="col-md-8"><select name="' . $field['column'] . '" class="selectpicker form-control" data-style="btn-info" data-size="10" data-live-search="true"' . ($field['required'] == true ? ' required' : '') . '>' . "\n";
        $selected = (isset($this->_request_variables[$key]) ? $this->_request_variables[$key] : $field['value']);
        foreach ($field['options'] as $ikey => $ival) {
            $select .= '<option value="' . $ikey . '" ' . ($ikey == $selected ? 'selected' : '') . '>' . $ival . '</option>' . "\n";
        }
        $select .= '</select>' . "\n";
        $select .= '</div>' . "\n";
        $select .= '</div>' . "\n";
        return $select;
    }

    /**
     * Radio Field
     * 
     * @since 6.1.10
     * @param type $key The key to the field.
     * @param type $field The value of the key.
     */
    private function radio_field($key, $field)
    {
        $radio = '<div class="form-group">' . "\n";
        $radio .= '<label class="col-md-3 control-label">' . ($field['required'] == true ? '<font color="red">*</font> ' : '') . $field['label'] . '</label>' . "\n";
        $radio .= '<div class="col-md-8">' . "\n";
        $selected = (isset($this->_request_variables[$key]) ? $this->_request_variables[$key] : $field['value']);
        foreach ($field['options'] as $ikey => $ival) {
            $radio .= '<input type="radio" name="' . $field['column'] . '" class="radio" value="' . $ikey . '" ' . ($ikey == $selected ? 'checked' : '') . ' /> ' . $ival . '<br />';
        }
        $radio .= '</div>' . "\n";
        $radio .= '</div>' . "\n";
        return $radio;
    }
    
    /**
     * Checkbox Field
     * 
     * @since 6.1.10
     * @param type $key The key to the field.
     * @param type $field The value of the key.
     */
    private function checkbox_field($key, $field)
    {
        $checkbox = '<div class="form-group">' . "\n";
        $checkbox .= '<label class="col-md-3 control-label">' . ($field['required'] == true ? '<font color="red">*</font> ' : '') . $field['label'] . '</label>' . "\n";
        $checkbox .= '<div class="col-md-8">' . "\n";
        $selected = (array) (isset($this->_request_variables[$key]) ? $this->_request_variables[$key] : $field['value']);
        foreach ($field['options'] as $ikey => $ival) {
            $checkbox .= '<input type="checkbox" name="' . $field['column'] . '[]" class="checkbox" value="' . $ikey . '" ' . (in_array($ikey, $selected) ? 'checked' : '') . ' /> ' . $ival . '<br />';
        }
        $checkbox .= '</div>' . "\n";
        $checkbox .= '</div>' . "\n";
        return $checkbox;
    }

    /**
     * Default Custom Field
     * 
     * @since 6.1.10
     * @param type $key The key to the field.
     * @param type $field The value of the key.
     */
    private function custom_field($key, $field)
    {
        switch ($field['type']) {
            case 'text':
                echo $this->text_field($key, $field);
                break;
            case 'textarea':
                echo $this->textarea_field($key, $field);
                break;
            case 'select':
                echo $this->select_field($key, $field);
                break;
            case 'radio':
                echo $this->radio_field($key, $field);
                break;
            case 'checkbox':
                echo $this->checkbox_field($key, $field);
                break;
        }
    }

    /**
     * Custom form field for application form
     * via myetSIS self service.
     * 
     * @since 6.1.10
     */
    private function myetsis_appl_field($key, $field)
    {
        switch ($field['type']) {
            case 'text':
                echo '<div class="col-md-6"><label class="strong">' . ($field['required'] == true ? '<font color="red">*</font> ' : '') . $field['label'] . '</label>';
                echo '<input type="text" class="form-control" name="' . $field['column'] . '" readonly value="' . (isset($this->_request_variables[$key]) ? $this->_request_variables[$key] : $field['value']) . '"' . ($field['required'] == true ? ' required' : ' ') . '/></div>';
                break;
            case 'textarea':
                echo '<div class="col-md-6"><label class="strong">' . ($field['required'] == true ? '<font color="red">*</font> ' : '') . $field['label'] . '</label>';
                echo '<textarea name="' . $field['column'] . '" readonly class="form-control"' . ($field['required'] == true ? ' required' : '') . '>' . (isset($this->_request_variables[$key]) ? $this->_request_variables[$key] : $field['value']) . '</textarea></div>';
                break;
            case 'select':
                echo '<div class="col-md-6"><label class="strong">' . ($field['required'] == true ? '<font color="red">*</font> ' : '') . $field['label'] . '</label>';
                echo '<select name="' . $field['column'] . '" readonly class="select"' . ($field['required'] == true ? ' required' : '') . '>';
                $selected = (isset($this->_request_variables[$key]) ? $this->_request_variables[$key] : $field['value']);
                foreach ($field['options'] as $ikey => $ival) {
                    echo '<option value="' . $ikey . '" ' . ($ikey == $selected ? 'selected' : '') . '>' . $ival . '</option>';
                }
                echo '</select></div>';
                break;
            case 'radio':
                echo '<div class="col-md-6"><label class="strong">' . ($field['required'] == true ? '<font color="red">*</font> ' : '') . $field['label'] . '</label>';
                $selected = (isset($this->_request_variables[$key]) ? $this->_request_variables[$key] : $field['value']);
                foreach ($field['options'] as $ikey => $ival) {
                    echo '<input type="radio" name="' . $field['column'] . '" class="radio" readonly value="' . $ikey . '" ' . ($ikey == $selected ? 'checked' : '') . ' /> ' . $ival . '<br />';
                }
                echo '</div>';
                break;
            case 'checkbox':
                echo '<div class="col-md-6"><label class="strong">' . ($field['required'] == true ? '<font color="red">*</font> ' : '') . $field['label'] . '</label>';
                $selected = (array) (isset($this->_request_variables[$key]) ? $this->_request_variables[$key] : $field['value']);
                foreach ($field['options'] as $ikey => $ival) {
                    echo '<input type="checkbox" name="' . $field['column'] . '[]" class="checkbox" readonly value="' . $ikey . '" ' . (in_array($ikey, $selected) ? 'checked' : '') . ' /> ' . $ival . '<br />';
                }
                echo '</div>';
                break;
        }
    }
}
