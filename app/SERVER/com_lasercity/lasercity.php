<?php defined('_JEXEC') or die;

jimport('components.com_lasercity.helpers.ClinicsFilter', JPATH_BASE);

$input = JFactory::$application->input;


if ($input->get('debugFile', false)) {
    $affiliatesFilter = new AffiliatesFilter(['page' => 2]);
    echo '<pre>';
    print_r($affiliatesFilter->getResult());
    echo '</pre>';
    echo '<pre>';
    print_r($affiliatesFilter->getActiveFilters());
    echo '</pre>';
    die;
}


$controller = JControllerLegacy::getInstance('Lasercity');

jimport('helpers.libraries.translator', JPATH_COMPONENT_ADMINISTRATOR);
jimport('helpers.libraries.open_graph', JPATH_COMPONENT_ADMINISTRATOR);
jimport('helpers.libraries.image_conversions', JPATH_COMPONENT_ADMINISTRATOR);
jimport('components.com_lasercity.helpers.search', JPATH_BASE);
jimport('components.com_lasercity.helpers.city_helper', JPATH_BASE);
jimport('components.com_lasercity.helpers.lang_helper', JPATH_BASE);
jimport('components.com_lasercity.helpers.content_loader', JPATH_BASE);
jimport('components.com_lasercity.helpers.social_network_detector', JPATH_BASE);
jimport('components.com_lasercity.helpers.ip', JPATH_BASE);
jimport('components.com_lasercity.helpers.santize', JPATH_BASE);


function lastVisit($type, $id = 0) {
    $ip = IP::get();
    $db = JFactory::getDbo();
    $date = date('Y-m-d');
    $db->setQuery("SELECT COUNT(*) FROM `#__lasercity_last_visit` WHERE `object_type`='{$type}' AND `object_id`={$id} AND `last_visit` = '{$date}' AND `ip`='{$ip}'");
    if (!$db->loadResult()) {
        $db->setQuery("INSERT INTO `#__lasercity_last_visit` (`object_type`, `object_id`, `last_visit`, `ip`) VALUES ('{$type}', '{$id}', '{$date}', '{$ip}')")->execute();
    }
}

function deleteFormat($str) {
    $str = explode('.', $str);
    array_pop($str);
    return implode('.', $str);
}


/*ScriptsLoader::add('page-header');
ScriptsLoader::add('page-footer');
ScriptsLoader::add('singup');*/

/*JHtml::_('stylesheet', 'templates/lasercity/css/main.css', array('version' => 'auto'));
JHtml::_('script', 'templates/lasercity/js/jquery-3.3.1.js', array('version' => 'auto'));
JHtml::_('script', 'templates/lasercity/js/main.js', array('version' => 'auto'));*/

Translator::initialization('lasercity');

$session = JFactory::getSession();
$default_city_id = JComponentHelper::getParams('com_lasercity')->get('city');

if ($input->get('current_city_id') == null) {
    $input->set('current_city_id', $default_city_id);
    if ($session->get( 'current_city_id') == null) {
        $session->set( 'current_city_id', $default_city_id );
    }
}

$controller->execute($input->get('task','display'));

$controller->redirect();