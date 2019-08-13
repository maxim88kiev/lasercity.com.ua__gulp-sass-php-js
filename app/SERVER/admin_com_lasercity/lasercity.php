<?php defined('_JEXEC') or die;

jimport('helpers.helper', JPATH_COMPONENT_ADMINISTRATOR);
jimport('helpers.libraries.translator', JPATH_COMPONENT_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('LaserCity');

Translator::initialization($controller->getName());

$input = JFactory::getApplication()->input;
$controller->execute($input->get('task', 'display'));

//var_dump(JFactory::getApplication()->input->get('view'));

/*$params = JComponentHelper::getParams('com_lasercity');
var_dump($params);
//var_dump($params->get('show_title2'));
jimport('components.com_config.helper.config', JPATH_ADMINISTRATOR);
$components = ConfigHelperConfig::getComponentsWithConfig();
ConfigHelperConfig::loadLanguageForComponents($components);
foreach ($components as $component) {
    echo '<pre>';
    print_r(JText::_($component));
    echo '</pre>';
}*/

$controller->redirect();
