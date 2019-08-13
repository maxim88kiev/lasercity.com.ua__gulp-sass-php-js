<?php defined('_JEXEC') or die;

class lasercityTablecomfort extends JTable
{
    public function __construct($db)
    {
        parent::__construct('#__lasercity_comforts', 'id', $db);
    }
}