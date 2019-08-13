<?php defined('_JEXEC') or die;

/**
 * Class ViewLegacy - переопределенный клас JViewLegacy
 */
class ViewLegacy extends JViewLegacy
{
    #region Переменные
    public $title = null; // Заголовок текущего вида
    public $add_filters = false; // Загружать ли фильтры
    public $sidebar; // Боковое меню
    public $lc_name; // Название вида
    public $lc_sig_name; // Название вида без 's'
    public $lc_list = false; // Если ето список
    public $lc_component_name; // Название компонента
    public $items = array(); // Список елементов полученых с модели
    public $item; // Елемент полученый из модели
    public $pagination; // Пагинация
    public $state; // Состояние (ordering)
    public $form; // Форма
    public $filterForm; // Данные фильтра из шапки
    public $activeFilters; // Активные фильра в шапке
    public $listOrder; // Колонка по которой производиться сортировка
    public $listDirection; // ASС или DESC
    public $saveOrder = false; // Проверка для sortable
    public $iconClass = ''; // Получение иконки после проверки
    public $col_span = 0; // Количество отступов
    public $tool_bar = null; // Тип тулбара
    public $column_duplicate = null;
    public $check_column_duplicate = null;
    public $duplicate_msg = null;
    #endregion;

    #region Переопределённые функции
    /**
     * Задание рутинных переменных и вызов метода setData
     * ViewLegacy constructor.
     * @param array $config
     * @throws Exception
     */
    function __construct(array $config = array())
    {
        $this->lc_name = $this->getName();
        $this->lc_component_name = JFactory::getApplication()->input->getCmd('option');
        $last_symbol = mb_substr($this->lc_name, -1);
        if ($last_symbol == 's') {
            $this->lc_list = true;
            $this->lc_sig_name = mb_substr($this->lc_name, 0, mb_strlen($this->lc_name) - 1);
        }
        $this->setData();
        parent::__construct($config);
    }

    /**
     * Отрисовка страницы
     * @param null $tpl
     * @return mixed|void
     */
    function display($tpl = null)
    {
        if ($this->lc_list) {
            $this->items = $this->get('Items');
            $this->pagination = $this->get('Pagination');
            $this->state = $this->get('State');
            if ($this->add_filters) {
                $this->filterForm = $this->get('FilterForm');
                $this->activeFilters = $this->get('ActiveFilters');
            }
            $this->addSubmenu();
            $this->sidebar = JHtmlSidebar::render();
            $this->listOrder = $this->escape($this->state->get('list.ordering'));
            $this->listDirection = $this->escape($this->state->get('list.direction'));
            $this->saveOrder = ($this->listOrder == 'ordering' && isset($this->items[0]->ordering));
            if ($this->saveOrder) {
                $saveOrderingUrl = "index.php?option={$this->lc_component_name}&task={$this->lc_name}.saveOrderAjax&tmpl=component";
                JHtml::_('sortablelist.sortable', "{$this->lc_name}List", 'adminForm', strtolower($this->listDirection), $saveOrderingUrl);
            } else {
                $this->iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED');
            }
        } else {
            $this->form = $this->get('Form');
            $id = $this->form->getValue('id');
            $this->item = new stdClass();
            $this->item->id = $id;
            if ($this->column_duplicate != null && $this->item->id != null) {
                $value = $this->form->getValue($this->column_duplicate);
                $check_value = $this->form->getValue($this->check_column_duplicate);
                $this->checkDuplicates($id, $value, $check_value);
            }
        }
        JToolbarHelper::title(JText::_($this->title));
        $this->addToolBar();
        require_once JPATH_ADMINISTRATOR . "/components/{$this->lc_component_name}/helpers/templates/" . ($this->lc_list ? 'list' : 'item') . '.php';
    }

    #endregion;

    function checkDuplicates($id, $value, $check_value)
    {
        $db = JFactory::getDbo();
        $value = $db->quote($value);
        $check_value = $db->quote($check_value);
        $current_table_name = $this->get('Table')->getTableName();
        $db_name = JFactory::getApplication()->get('db');
        $prefix = JFactory::getApplication()->get('dbprefix');
        $component = str_replace('com_', '', $this->lc_component_name);
        $query = "SELECT DISTINCT TABLE_NAME as `name` FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME LIKE '{$prefix}{$component}%' AND COLUMN_NAME = '{$this->column_duplicate}' AND TABLE_SCHEMA='{$db_name}'";
        $db->setQuery($query);
        $tables_list = $db->loadObjectList();
        $tables = array();
        if (!empty($tables_list)) {
            foreach ($tables_list as $table) {
                $table_name = str_replace($prefix, '#__', $table->name);
                $tables[] = $table_name;
            }
        }
        $query = array();
        $component_name = $this->lc_component_name;
        $block_name = mb_strtoupper($component_name . '_VIEW_NAME_');
        foreach ($tables as $table) {
            $view_name = str_replace('#__' . str_replace('com_', '', $this->lc_component_name) . '_', '', $table);
            $view_name = mb_substr($view_name, -1) == 's' ? $view_name : "{$view_name}s";
            $view_name = $block_name . mb_strtoupper($view_name);
            $to_query = "(SELECT `id`, '{$view_name}' as `view_text` FROM `{$table}` WHERE `{$this->column_duplicate}`={$value}";
            if ($table == $current_table_name) {
                $to_query .= " AND `id`<>{$id}";
                if ($this->check_column_duplicate != null) {
                    $to_query .= " AND `{$this->check_column_duplicate}`={$check_value}";
                }
            }
            $query[] = $to_query . ')';
        }
        $query = implode(' UNION ALL ', $query);
        $db->setQuery($query);
        $duplicates_array = $db->loadObjectList();
        $duplicates = array();
        $count = 0;
        foreach ($duplicates_array as $duplicate) {
            $view_text = $duplicate->view_text;
            if (!isset($duplicates[$view_text]['url'])) {
                $view_name = str_replace("{$component_name}_view_name_", '', mb_strtolower($view_text));
                $view_name = mb_substr($view_name, -1) == 's' ? mb_substr($view_name, 0, -1) : $view_name;
                $url = "index.php?option={$this->lc_component_name}&view={$view_name}&layout=edit&id=";
                $duplicates[$view_text]['url'] = $url;
            }
            if (!isset($duplicates[$view_text]['data'])) {
                $duplicates[$view_text]['data'] = array();
            }
            $duplicates[$view_text]['data'][] = $duplicate->id;
            $count++;
        }
        if ($count) {
            $this->duplicate_msg = JText::_(mb_strtoupper($this->lc_component_name) . '_WARNING_DUPLICATES') . " {$count}\r\n";
            foreach ($duplicates as $text => $data) {
                $this->duplicate_msg .= '<div>' . JText::_($text) . ' ID (';
                $urls = array();
                foreach ($data['data'] as $item) {
                    $urls[] = "<a href=\"{$duplicates[$text]['url']}{$item}\" target=\"_blank\">{$item}</a>";
                }
                $this->duplicate_msg .= implode(', ', $urls);
                $this->duplicate_msg .= ')</div>';

            }
        }
    }

    #region Кастомные функции

    /**
     * Добавление тулбара по указаному типу
     */
    function addToolBar()
    {
        switch ($this->tool_bar) {
            case 'list':
                JToolbarHelper::addNew("{$this->lc_sig_name}.add");
                JToolbarHelper::editList("{$this->lc_sig_name}.edit");
                JToolbarHelper::deleteList('', "{$this->lc_name}.delete");
                //JToolbarHelper::trash("{$this->lc_name}.trash");
                JToolbarHelper::publish("{$this->lc_name}.publish", 'JTOOLBAR_PUBLISH', true);
                JToolbarHelper::unpublish("{$this->lc_name}.unpublish", 'JTOOLBAR_UNPUBLISH', true);
                JToolbarHelper::preferences($this->lc_component_name);
                break;
            case 'item':
                JToolbarHelper::apply("{$this->lc_name}.apply");
                JToolbarHelper::save("{$this->lc_name}.save");
                JToolbarHelper::save2copy("{$this->lc_name}.save2copy");
                JToolbarHelper::cancel("{$this->lc_name}.cancel");
                break;
            default:
                echo '<script>alert("no tool bar")</script>';
                break;
        }
    }

    /**
     * Считиваються все модели, которые заканчиваються на "s" и занесение их в sidebar + задание имени в формате {COM_COMPONENTNAME}_VIEW_NAME_{VIEWNAME}
     */
    function addSubmenu()
    {
        $views = scandir(JPATH_COMPONENT_ADMINISTRATOR . '/views');
        foreach ($views as $id => $view) {
            if ($view != '.' && $view != '..' && mb_substr($view, -1) == 's') {
                $this->addEntry(mb_strtoupper($this->lc_component_name) . '_VIEW_NAME_' . mb_strtoupper($view), $view);
            }
        }
    }

    /**
     * Добавление пункта в sidebar
     * @param $title
     * @param $view
     */
    function addEntry($title, $view)
    {
        JHtmlSidebar::addEntry(
            JText::_($title),
            "index.php?option={$this->lc_component_name}&view={$view}",
            $this->lc_name == $view
        );
    }

    /**
     * Метод для заполнения переменних
     */
    function setData()
    {
        //
    }

    /**
     * Метод для отрисовки шапки таблицы
     */
    function setTableHEAD()
    {
        //
    }

    /**
     * Метод для отрисовки контента таблицы
     */
    function setTableBODY($id, $item)
    {
        //
    }

    /**
     * Мтод для получения шаблона тега TH
     * @param $type
     * @param null $title
     * @param null $column
     * @param int $width
     */
    function getTH($type, $title = null, $column = null, $width = 1)
    {
        echo "<th class=\"nowrap\" width=\"{$width}%\">";
        switch ($type) {
            case 'text':
                echo JText::_($title);
                break;
            case 'sort':
                echo JHtml::_('searchtools.sort', JText::_($title), $column, $this->listDirection, $this->listOrder);
                break;
            case 'check-all':
                echo '<input type="checkbox" name="checkall-toggle" value="" title="' . JText::_('JGLOBAL_CHECK_ALL') . '" onclick="Joomla.checkAll(this)"/>';
                break;
            case 'order':
                echo JHtml::_('searchtools.sort', '', 'ordering', $this->listDirection, $this->listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2');;
                break;
            default:
                echo 'th';
                break;
        }
        echo "</th>";
        $this->col_span++;
    }

    /**
     * Мтод для получения шаблона тега TD
     * @param $type
     * @param $value
     * @param null $id
     */
    function getTD($type, $value, $id = null, $data = array())
    {
        echo '<td>';
        switch ($type) {
            case 'key':
                $text = isset($data[$value]) ? JText::_($data[$value]) : 'empty';
                echo $text;
                break;
            case 'text':
                $text = $value != null ? $value : 'empty';
                echo $text;
                break;
            case 'link':
                $text = $value != null ? $value : 'empty';
                echo '<a href="' . JRoute::_("index.php?option={$this->lc_component_name}&task={$this->lc_sig_name}.edit&id={$id}") . '">' . $text . '</a>';
                break;
            case 'check':
                echo JHtml::_('grid.id', $id, $value);
                break;
            case 'publish':
                echo JHtml::_('jgrid.published', $value, $id, "{$this->lc_name}.", true);
                break;
            case 'order': ?>
                <span class="sortable-handler<?= $this->iconClass ?>">
                    <span class="icon-menu" aria-hidden="true"></span>
                </span>
                <?php if ($this->saveOrder) : ?>
                    <input type="text" style="display:none" name="order[]" size="5" value="<?= $value; ?>"
                           class="width-20 text-area-order"/>
                <?php endif; ?>
                <?php break;
            default:
                echo 'td';
        }
        echo '</td>';
    }
    #endregion;
}