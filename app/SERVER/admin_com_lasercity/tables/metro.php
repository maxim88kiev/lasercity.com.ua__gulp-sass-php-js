<?php defined('_JEXEC') or die;

class lasercityTablemetro extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_metro', 'id', $db);
    }
}