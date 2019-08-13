<?php defined('_JEXEC') or die;

class lasercityTablearticle extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_articles', 'id', $db);
    }
}