<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$app = Factory::getApplication();
$doc = Factory::getDocument();

$this->language  = $doc->language;
$this->direction = $doc->direction;

$base        = rtrim($this->baseurl, '/');
$templateUrl = $base . '/templates/' . $this->template;
$templatePath = __DIR__;
$cssVersion  = is_file($templatePath . '/css/template.css') ? filemtime($templatePath . '/css/template.css') : time();

$doc->addStyleSheet($templateUrl . '/css/template.css?v=' . $cssVersion);

HTMLHelper::_('jquery.framework');
?>
<!doctype html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <jdoc:include type="head" />
</head>
<body class="sf-component-modal">
    <jdoc:include type="message" />
    <jdoc:include type="component" />
</body>
</html>
