<?php defined('_JEXEC') or die;

class LaserCityTableStock extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_stock', 'id', $db);
    }
}