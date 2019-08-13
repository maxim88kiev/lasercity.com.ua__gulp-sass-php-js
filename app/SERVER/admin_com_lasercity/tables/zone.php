<?php defined('_JEXEC') or die;

class lasercityTablezone extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_zones', 'id', $db);
    }
}