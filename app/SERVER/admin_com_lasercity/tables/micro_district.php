<?php defined('_JEXEC') or die;

class lasercityTablemicro_district extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_micro_districts', 'id', $db);
    }
}