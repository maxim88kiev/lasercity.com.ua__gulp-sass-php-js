<?php defined('_JEXEC') or die;

class lasercityTableprice extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_prices', 'id', $db);
    }
}