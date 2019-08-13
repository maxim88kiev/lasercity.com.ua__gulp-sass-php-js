<?php defined('_JEXEC') or die;
jimport('joomla.html.pagination');
JHtml::_('formbehavior.chosen', 'select'); ?>
<form action="index.php?option=<?= $this->lc_component_name; ?>&view=<?= $this->lc_name; ?>" method="post" name="adminForm"
      id="adminForm">
    <div id="j-sidebar-container" class="span2">
        <?= $this->sidebar ?>
    </div>
    <div id="j-main-container" class="span10">
        <?php if ($this->add_filters) {
            echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        }
        if (!empty($this->items)): ?>
            <table class="table table-striped" cellspacing="1" id="<?= $this->lc_name; ?>List">
                <thead>
                <tr>
                    <?php $this->setTableHEAD(); ?>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="<?= $this->col_span ?>">
                        <?= $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
                </tfoot>
                <tbody>
                <?php
                foreach ($this->items as $i => $item) {
                    echo '<tr>';
                    $this->setTableBODY($i, $item);
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-no-items"><?= JText::_(mb_strtoupper($this->lc_component_name) . '_WARNING_TEXT_NO_FIND') ?></div>
        <?php endif; ?>
    </div>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <?= JHTML::_('form.token') ?>
</form>