<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$app = Factory::getApplication();

$this->category->text = $this->category->description;
$app->triggerEvent('onContentPrepare', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$this->category->description = $this->category->text;

$results = $app->triggerEvent('onContentAfterTitle', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$afterDisplayTitle = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentBeforeDisplay', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$beforeDisplayContent = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentAfterDisplay', [$this->category->extension . '.categories', &$this->category, &$this->params, 0]);
$afterDisplayContent = trim(implode("\n", $results));

$items = array_merge($this->lead_items ?: [], $this->intro_items ?: []);
?>

<div class="sf-content-blog blog<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Blog">
    <header class="sf-content-blog__head">
        <?php if ($this->params->get('show_page_heading')) : ?>
            <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        <?php elseif ($this->params->get('show_category_title', 1)) : ?>
            <h1><?php echo $this->escape($this->category->title); ?></h1>
        <?php endif; ?>

        <?php if ($this->params->get('page_subheading')) : ?>
            <p><?php echo $this->escape($this->params->get('page_subheading')); ?></p>
        <?php elseif ($this->category->description && $this->params->get('show_description', 1)) : ?>
            <div class="sf-content-blog__intro">
                <?php echo HTMLHelper::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
            </div>
        <?php else : ?>
            <p>Updates, events and stories from across Suffolk Scouts.</p>
        <?php endif; ?>
    </header>

    <?php echo $afterDisplayTitle; ?>

    <?php if ($beforeDisplayContent || $afterDisplayContent) : ?>
        <div class="sf-content-blog__plugins">
            <?php echo $beforeDisplayContent; ?>
            <?php echo $afterDisplayContent; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($items) && empty($this->link_items)) : ?>
        <?php if ($this->params->get('show_no_articles', 1)) : ?>
            <p class="sf-empty-state"><?php echo Text::_('COM_CONTENT_NO_ARTICLES'); ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($items)) : ?>
        <div class="sf-content-blog__grid">
            <?php foreach ($items as &$item) : ?>
                <article class="sf-content-blog__item" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
                    <?php
                    $this->item = &$item;
                    echo $this->loadTemplate('item');
                    ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($this->link_items)) : ?>
        <div class="sf-content-blog__more">
            <?php echo $this->loadTemplate('links'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->maxLevel != 0 && !empty($this->children[$this->category->id])) : ?>
        <div class="sf-content-blog__children">
            <?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
                <h2><?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?></h2>
            <?php endif; ?>
            <?php echo $this->loadTemplate('children'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->params->def('show_pagination', 1) == 1 || $this->params->get('show_pagination') == 2) : ?>
        <nav class="sf-pagination" aria-label="<?php echo Text::_('JPAGINATION'); ?>">
            <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                <p class="sf-pagination__counter"><?php echo $this->pagination->getPagesCounter(); ?></p>
            <?php endif; ?>
            <?php echo $this->pagination->getPagesLinks(); ?>
        </nav>
    <?php endif; ?>
</div>
