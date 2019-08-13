<?php defined('_JEXEC') or die;
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.formvalidation');
$fieldSets = $this->form->getFieldsets();
$single = count($fieldSets) == 1 ? 'active' : null;
JFactory::getDocument()->addScriptDeclaration(
    '
	(function ($) {
        $(document).ready(function () {
            // Select first tab
		    //jQuery("#itemTabs a:first").tab("show");
            var json, tabsState;
            $(\'a[data-toggle="pill"], a[data-toggle="tab"]\').on(\'shown\', function(e) {
                var href, json, parentId, tabsState;
                tabsState = localStorage.getItem("tabs-state");
                json = JSON.parse(tabsState || "{}");
                parentId = $(e.target).parents("ul.nav.nav-pills, ul.nav.nav-tabs").attr("id");
                href = $(e.target).attr(\'href\');
                json[parentId] = href;
                return localStorage.setItem("tabs-state", JSON.stringify(json));
            });

            tabsState = localStorage.getItem("tabs-state");
            json = JSON.parse(tabsState || "{}");
            $.each(json, function(containerId, href) {
                return $("#" + containerId + " a[href=\"" + href + "\"]").tab(\'show\');
            });
            $("ul.nav.nav-pills, ul.nav.nav-tabs").each(function () {
                var $this = $(this);
                if (!json[$this.attr("id")]) {
                    return $this.find("a[data-toggle=tab]:first, a[data-toggle=pill]:first").tab("show");
                }
            });
        });
    })(jQuery);'
); ?>
<form action="index.php?option=<?= $this->lc_component_name; ?>&layout=edit&id=<?= $this->item->id; ?>"
        method="post" id="adminForm" name="adminForm" class="form-validate">
    <div class="form-horizontal">
        <?php if ($this->duplicate_msg != null): ?>
        <div class="alert">
            <span class="icon-info" aria-hidden="true"></span> <?= $this->duplicate_msg ?>
        </div>
        <?php endif; ?>
        <ul class="nav nav-tabs" id="itemTabs">
            <?php foreach ($fieldSets as $fieldSet): ?>
                <li class="<?= $single ?>"><a data-toggle="tab" href="#<?= $fieldSet->name ?>"><?= JText::_($fieldSet->label) ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="tab-content" id="itemContent">
            <?php foreach ($fieldSets as $fieldSet): ?>
                <div class="tab-pane <?= $single ?>" id="<?= $fieldSet->name ?>">
                    <?php if (isset($fieldSet->description) && !empty($fieldSet->description)) : ?>
                        <div class="tab-description alert alert-info">
                            <span class="icon-info" aria-hidden="true"></span> <?= JText::_($fieldSet->description); ?>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($this->form->getFieldset($fieldSet->name) as $field) {
                        echo $this->form->renderField(mb_substr($field->name, 6, -1));
                    } ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <input type="hidden" name="task" value=""/>
    <?= JHtml::_('form.token'); ?>
</form>