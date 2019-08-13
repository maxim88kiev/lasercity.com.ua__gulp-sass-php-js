<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

class JFormFieldNewCalendar extends JFormField
{
    protected function getInput()
    {
        JHtml::_('stylesheet', 'administrator/components/com_lasercity/models/fields/calendar.css');
        //JHtml::_('script', 'templates/lasercity/js/jquery-3.3.1.min.js');
        JHtml::_('script', 'templates/lasercity/js/dtime_separator_.js');
        $fieldHTML = "<input type=\"text\" id=\"{$this->id}\" name=\"{$this->name}\" value=\"{$this->value}\">";
        $fieldHTML .= '<script>jQuery(document).ready(function($){$(\'#' . $this->id . '\').MyDtime({times: true,readonly: false});});</script>';
        return $fieldHTML;
    }
}
