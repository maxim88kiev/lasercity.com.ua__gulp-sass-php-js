<?php defined('_JEXEC') or die;
$app = JFactory::getApplication(); ?>
<!DOCTYPE html>
<html lang="<?= $this->language; ?>" dir="<?= $this->direction; ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <jdoc:include type="head"/>
</head>
<body>
<h1><?php $app->get('offline_message') ?></h1>
</body>
</html>
