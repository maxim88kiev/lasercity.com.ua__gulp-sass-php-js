<?php defined('_JEXEC') or die;

class lasercityTableservice extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_services', 'id', $db);
    }
}