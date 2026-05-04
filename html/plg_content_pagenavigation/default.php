<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$lang = Factory::getLanguage();
?>
<nav class="sf-page-nav" aria-label="<?php echo Text::_('JGLOBAL_ARTICLE_NAVIGATION'); ?>">
    <?php if ($row->prev) :
        $direction = $lang->isRtl() ? 'right' : 'left'; ?>
        <a class="sf-page-nav__prev"
           href="<?php echo $row->prev; ?>"
           rel="prev"
           aria-label="<?php echo Text::sprintf('JPREVIOUS_TITLE', htmlspecialchars($rows[$location - 1]->title)); ?>"
           title="<?php echo htmlspecialchars($rows[$location - 1]->title); ?>">
            <span class="fa fa-chevron-<?php echo $direction; ?>" aria-hidden="true"></span>
            <span><?php echo $row->prev_label; ?></span>
        </a>
    <?php endif; ?>
    <?php if ($row->next) :
        $direction = $lang->isRtl() ? 'left' : 'right'; ?>
        <a class="sf-page-nav__next"
           href="<?php echo $row->next; ?>"
           rel="next"
           aria-label="<?php echo Text::sprintf('JNEXT_TITLE', htmlspecialchars($rows[$location + 1]->title)); ?>"
           title="<?php echo htmlspecialchars($rows[$location + 1]->title); ?>">
            <span><?php echo $row->next_label; ?></span>
            <span class="fa fa-chevron-<?php echo $direction; ?>" aria-hidden="true"></span>
        </a>
    <?php endif; ?>
</nav>
