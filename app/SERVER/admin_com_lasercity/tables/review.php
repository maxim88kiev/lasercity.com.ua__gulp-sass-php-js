<?php defined('_JEXEC') or die;

class LaserCityTableReview extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_reviews', 'id', $db);
    }
}