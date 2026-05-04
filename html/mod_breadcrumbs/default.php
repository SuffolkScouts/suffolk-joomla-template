<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
?>
<nav aria-label="<?php echo Text::_('MOD_BREADCRUMBS_LABEL'); ?>">
    <ol itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumb">
        <?php
        for ($i = 0; $i < $count; $i++) {
            if ($i === 1 && !empty($list[$i]->link) && !empty($list[$i - 1]->link) && $list[$i]->link === $list[$i - 1]->link) {
                unset($list[$i]);
            }
        }

        end($list);
        $lastKey  = key($list);
        $showLast = $params->get('showLast', 1);

        foreach ($list as $key => $item) :
            if ($key !== $lastKey) : ?>
                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item">
                    <?php if (!empty($item->link)) : ?>
                        <a itemprop="item" href="<?php echo $item->link; ?>"><span itemprop="name"><?php echo $item->name; ?></span></a>
                    <?php else : ?>
                        <span itemprop="name"><?php echo $item->name; ?></span>
                    <?php endif; ?>
                    <meta itemprop="position" content="<?php echo $key + 1; ?>">
                </li>
            <?php elseif ($showLast) : ?>
                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="breadcrumb-item active" aria-current="page">
                    <span itemprop="name"><?php echo $item->name; ?></span>
                    <meta itemprop="position" content="<?php echo $key + 1; ?>">
                </li>
            <?php endif;
        endforeach; ?>
    </ol>
</nav>
