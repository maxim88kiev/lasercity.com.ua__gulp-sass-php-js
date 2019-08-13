<?php defined('_JEXEC') or die;

class lasercityTablefaq extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_faq', 'id', $db);
    }
}