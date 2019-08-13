<?php defined('_JEXEC') or die;

class lasercityTableapparat extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_apparats', 'id', $db);
    }
}