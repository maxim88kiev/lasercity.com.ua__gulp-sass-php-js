<?php defined('_JEXEC') or die;

class lasercityTabledistrict extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_districts', 'id', $db);
    }
}