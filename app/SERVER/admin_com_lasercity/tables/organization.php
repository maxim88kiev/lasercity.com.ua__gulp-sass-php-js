<?php defined('_JEXEC') or die;

class lasercityTableorganization extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_organizations', 'id', $db);
    }
}