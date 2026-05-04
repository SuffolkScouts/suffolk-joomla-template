<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$params      = $displayData->params;
$canEdit     = $params->get('access-edit');
$currentDate = Factory::getDate()->format('Y-m-d H:i:s');
$nullDate    = Factory::getDbo()->getNullDate();
?>
<?php if ($displayData->state == 0 || $params->get('show_title') || ($params->get('show_author') && !empty($displayData->author))) : ?>
    <header class="sf-article__title-block">
        <?php if ($params->get('show_title')) : ?>
            <h2 itemprop="name">
                <?php if ($params->get('link_titles') && ($params->get('access-view') || $params->get('show_noauth', '0') == '1')) : ?>
                    <a href="<?php echo Route::_(RouteHelper::getArticleRoute($displayData->slug, $displayData->catid, $displayData->language)); ?>" itemprop="url">
                        <?php echo $this->escape($displayData->title); ?>
                    </a>
                <?php else : ?>
                    <?php echo $this->escape($displayData->title); ?>
                <?php endif; ?>
            </h2>
        <?php endif; ?>

        <?php if ($displayData->state == 0) : ?>
            <span class="sf-badge sf-badge--warning"><?php echo Text::_('JUNPUBLISHED'); ?></span>
        <?php endif; ?>

        <?php if ($displayData->publish_up > $currentDate) : ?>
            <span class="sf-badge sf-badge--warning"><?php echo Text::_('JNOTPUBLISHEDYET'); ?></span>
        <?php endif; ?>

        <?php if ($displayData->publish_down < $currentDate && $displayData->publish_down !== $nullDate) : ?>
            <span class="sf-badge sf-badge--warning"><?php echo Text::_('JEXPIRED'); ?></span>
        <?php endif; ?>
    </header>
<?php endif; ?>
