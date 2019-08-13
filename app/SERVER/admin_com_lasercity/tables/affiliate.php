<?php defined('_JEXEC') or die;

class lasercityTableaffiliate extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_affiliates', 'id', $db);
    }
}