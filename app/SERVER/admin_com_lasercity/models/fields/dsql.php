<?php defined('_JEXEC') or die;

JFormHelper::loadFieldClass('sql');

class JFormFieldDSQL extends JFormFieldSQL
{
    protected function getInput()
    {
        $component = isset($this->element['type_component']) ? $this->element['type_component'] : JFactory::getApplication()->input->get('option');
        $state_column = isset($this->element['state_column']) ? $this->element['state_column'] : false;
        //var_dump($state_column);
        $filter = JFactory::getApplication()->input->get('filter');

        if (isset($this->element['lang_column']) && isset($this->element['lang_object'])) {
            $lang = JFactory::getLanguage()->getTag();
            $component_name = str_replace('com_', '', $component);
            $this->query = "SELECT `translate`.`value`, `join`.* FROM ({$this->query}) as `join`
LEFT JOIN `#__{$component_name}_translations_{$lang}` as `translate` ON 
      `translate`.`object_name`='{$this->element['lang_object']}' AND 
      `translate`.`object_column`='{$this->element['lang_column']}' AND 
      `translate`.`object_id`=`join`.`id`
";
            if ($state_column != false) {
                $id = "{$this->element['state_filter_pos']}"; // Позиция field в filters
                $state_value = $filter[$id];
                if (is_numeric($state_value)) {
                    $this->query .= "WHERE `{$state_column}`={$state_value}";
                }
            }
        } else {
            if ($state_column != false) {
                $id = "{$this->element['state_filter_pos']}";
                $state_value = $filter[$id];
                if (is_numeric($state_value)) {
                    $this->query .= " AND `{$state_column}`={$state_value}";
                }
            }
        }

        if (isset($this->element['update_onchange'])) {
            $default_option = null;
            if (isset($this->element['default_option'])) {
                $data_do = explode('#', "{$this->element['default_option']}");
                $value = $data_do[0];
                $text = JText::_($data_do[1]);
                $default_option = "<option value=\"{$value}\">{$text}</option>";
            }

            $update_onchange = explode('#', "{$this->element['update_onchange']}");
            $onchange_text = $update_onchange[0];
            $onchange_id = 'jform_' . $update_onchange[0];
            $change_id = 'jform_' . mb_substr($this->name, 6, -1);
            $task = $update_onchange[1];
            $change = isset($update_onchange[2]) ? "jQuery(\"select#{$onchange_id}\").change();" : '';
            JFactory::getDocument()->addScriptDeclaration("
                jQuery(document).ready(function () {
                    jQuery('select#{$onchange_id}').change(function () {
                        let value = jQuery(this).val();
                        let trigger = jQuery('select#{$change_id}').val();
                        jQuery.ajax({
                            url: 'index.php?option={$component}&task={$task}',
                            type: 'GET',
                            dataType: 'json',
                            data: {'{$onchange_text}': value},
                            success: function (response) {
                                if (response.success) {
                                    jQuery('select#{$change_id} option').remove();
                                    let options = response . data;
                                    jQuery('select#{$change_id}').append('{$default_option}');
                                    jQuery.each(options, function (i, obj) {
                                        let selected = obj . value === trigger ? 'selected' : '';
                                        jQuery('select#{$change_id}').append('<option value=\"' + obj . value + '\" ' + selected + '>' + obj . text + '</option>');
                                    });
                                    jQuery('select#{$change_id}').change();
                                    jQuery('select').trigger('liszt:updated');
                                }  else {
                                    alert(response.message);
                                }
                            }
                        });
                    });
                    {$change}
                });
            ");
        }

        return parent::getInput();
    }
}