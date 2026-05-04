<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
?>
<div class="sf-news-card-item">
    <?php if (!empty($item->imageSrc)) : ?>
        <a href="<?php echo $item->link; ?>" class="sf-news-card-item__image-link">
            <img src="<?php echo htmlspecialchars($item->imageSrc, ENT_COMPAT, 'UTF-8'); ?>"
                 alt="<?php echo htmlspecialchars($item->imageAlt, ENT_COMPAT, 'UTF-8'); ?>"
                 class="sf-news-card-item__image">
        </a>
    <?php endif; ?>
    <div class="sf-news-card-item__body">
        <a href="<?php echo $item->link; ?>" class="sf-news-card-item__title">
            <h3><?php echo $item->title; ?></h3>
        </a>
        <p><?php echo $item->introtext; ?></p>
        <?php if (isset($item->link) && $item->readmore != 0 && $params->get('readmore')) : ?>
            <a class="sf-readmore-link" href="<?php echo $item->link; ?>"><?php echo Text::_('COM_CONTENT_READ_MORE'); ?></a>
        <?php endif; ?>
    </div>
</div>
