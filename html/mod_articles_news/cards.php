<?php
defined('_JEXEC') or die;

use Joomla\CMS\Module\ModuleHelper;
?>
<div class="sf-news-cards<?php echo $params->get('moduleclass_sfx'); ?>">
    <?php foreach ($list as $item) : ?>
        <?php require ModuleHelper::getLayoutPath('mod_articles_news', 'cards_item'); ?>
    <?php endforeach; ?>
</div>
