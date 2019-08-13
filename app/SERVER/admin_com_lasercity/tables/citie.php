<?php defined('_JEXEC') or die;

class LaserCityTableCitie extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_cities', 'id', $db);
    }
}