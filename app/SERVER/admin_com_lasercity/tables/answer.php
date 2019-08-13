<?php defined('_JEXEC') or die;

class LaserCityTableAnswer extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_answer', 'id', $db);
    }
}