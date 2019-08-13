<?php defined('_JEXEC') or die;

class lasercityTablelocation extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_locations', 'id', $db);
    }
}