<?php defined('_JEXEC') or die;

class JFormFieldLanguages extends JFormField
{
    protected $type = 'Languages';

    protected function getInput()
    {
        JFactory::getDocument()->addStyleDeclaration('.multi-languages .nav {
    margin: 0;
}
.multi-languages .tab-content {
    padding: 5px;
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
}');
        $db = JFactory::getDbo();
        $query = 'SELECT `lang_code`, `title` FROM `#__languages` WHERE `published`';
        $db->setQuery($query);
        $languages = $db->loadObjectList();
        $subForm = new JForm($this->name, array('control' => 'jform'));
        $hint = isset($this->element['hint']) ? JText::_($this->element['hint']) : '';
        $subForm->load("<form><fieldset><field name=\"value\" hint=\"{$hint}\" type=\"{$this->element['field']}\"/></fieldset></form>");

        $fieldHTML = '<div class="multi-languages"><ul class="nav nav-tabs">';
        foreach ($languages as $id => $language) {
            $active = !$id ? 'active' : '';
            $fieldHTML .= "<li class=\"{$active}\"><a data-toggle=\"tab\" href=\"#{$this->id}_{$language->lang_code}_tab\">{$language->title}</a></li>";
        }
        $fieldHTML .= '</ul><div class="tab-content">';
        foreach ($languages as $id => $language) {
            $active = !$id ? 'active' : '';
            $fieldHTML .= "<div id=\"{$this->id}_{$language->lang_code}_tab\" class=\"tab-pane fade in {$active}\">";
            $field = $subForm->getField('value', null, isset($this->value[$language->lang_code]) ? $this->value[$language->lang_code] : null);
            $field->id = "{$this->id}_{$language->lang_code}";
            $field->name = "{$this->name}[{$language->lang_code}]";
            $fieldHTML .= $field->getInput();
            $fieldHTML .= '</div>';
        }
        $fieldHTML .= '</div></div>';
        return $fieldHTML;
    }
}