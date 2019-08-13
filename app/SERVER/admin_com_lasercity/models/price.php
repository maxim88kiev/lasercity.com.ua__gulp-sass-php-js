<?php defined('_JEXEC') or die;

jimport('helpers.libraries.model_admin', JPATH_COMPONENT_ADMINISTRATOR);

class lasercityModelprice extends ModelAdmin
{
    public $config = array(
        'translator' => array(
            'title',
            'description'
        ),
        'object' => array(
            'data' => array(
                'columns' => array(
                    'service',    'duration',
                    'price',      'percent',
                    'start_sale', 'end_sale'
                ), 'key_value' => true
            )
        )
    );

    function save($data)
    {
        if (parent::save($data)) {
            $services = array();
            $price_id = $this->getState("{$this->name}.id");
            foreach ($data['data'] as $item) {
                $value = "({$price_id},{$item['service']})";
                if (!in_array($value, $services)) {
                    $services[] = $value;
                }
            }
            $services = implode(',', $services);
            if (!empty($services)) {
                $this->_db->setQuery("DELETE FROM `#__lasercity_to_filters` WHERE `price_id`={$price_id}")->execute();
                $this->_db->setQuery("INSERT INTO `#__lasercity_to_filters` (`price_id`, `service_id`) VALUES {$services}")->execute();
            }
            return true;
        }
        return false;
    }

    function delete(&$pks)
    {
        $this->_db->setQuery('DELETE FROM `#__lasercity_to_filters` WHERE `price_id` IN(' . implode(',', $pks) . ')')->execute();
        return parent::delete($pks);
    }
}