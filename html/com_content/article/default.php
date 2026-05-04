<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$params = $this->item->params;
$images = json_decode((string) $this->item->images);
$urls = json_decode((string) $this->item->urls);
$canEdit = $params->get('access-edit');
$user = Factory::getUser();
$info = $params->get('info_block_position', 0);
$useDefList = $params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
    || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author');

$date = $this->item->publish_up && $this->item->publish_up !== Factory::getDbo()->getNullDate() ? $this->item->publish_up : $this->item->created;
$articleHeading = $params->get('show_title') ? $this->item->title : (string) $this->params->get('page_heading', $this->item->title);
$articleHeading = trim($articleHeading) !== '' ? $articleHeading : $this->item->title;
$fullImage = '';
$fullImageAlt = '';

if (is_object($images)) {
    $fullImage = (string) ($images->image_fulltext ?: $images->image_intro ?: '');
    $fullImageAlt = (string) ($images->image_fulltext_alt ?: $images->image_intro_alt ?: $this->item->title);
}

if ($fullImage !== '') {
    $fullImage = explode('#', $fullImage, 2)[0];
    if (!preg_match('#^(https?:)?//#i', $fullImage)) {
        $fullImage = Uri::root(true) . '/' . ltrim($fullImage, '/');
    }
}

$statusLabels = [];
if ($this->item->state == 0) {
    $statusLabels[] = Text::_('JUNPUBLISHED');
}
if (strtotime($this->item->publish_up) > strtotime(Factory::getDate())) {
    $statusLabels[] = Text::_('JNOTPUBLISHEDYET');
}
if ((strtotime($this->item->publish_down) < strtotime(Factory::getDate())) && $this->item->publish_down != Factory::getDbo()->getNullDate()) {
    $statusLabels[] = Text::_('JEXPIRED');
}
?>

<article class="sf-article item-page<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Article">
    <meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? Factory::getConfig()->get('language') : $this->item->language; ?>">

    <?php if (!$this->print && ($canEdit || $params->get('show_print_icon') || $params->get('show_email_icon'))) : ?>
        <div class="sf-article__tools">
            <?php echo LayoutHelper::render('joomla.content.icons', ['params' => $params, 'item' => $this->item, 'print' => false]); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->params->get('show_page_heading')) : ?>
        <p class="sf-article__section"><?php echo $this->escape($this->params->get('page_heading')); ?></p>
    <?php elseif (!empty($this->item->category_title)) : ?>
        <p class="sf-article__section"><?php echo htmlspecialchars($this->item->category_title, ENT_COMPAT, 'UTF-8'); ?></p>
    <?php endif; ?>

    <header class="sf-article__header">
        <h1 itemprop="name"><?php echo $this->escape($articleHeading); ?></h1>

        <div class="sf-article__meta">
            <?php if (!empty($this->item->category_title) && $params->get('show_category')) : ?>
                <span><?php echo htmlspecialchars($this->item->category_title, ENT_COMPAT, 'UTF-8'); ?></span>
            <?php endif; ?>
            <?php if ($params->get('show_publish_date') || $params->get('show_create_date')) : ?>
                <time datetime="<?php echo HTMLHelper::_('date', $date, 'c'); ?>"><?php echo HTMLHelper::_('date', $date, 'd M Y'); ?></time>
            <?php endif; ?>
            <?php if ($canEdit) : ?>
                <?php foreach ($statusLabels as $label) : ?>
                    <em><?php echo htmlspecialchars($label, ENT_COMPAT, 'UTF-8'); ?></em>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if ($info == 0 && $params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
            <div class="sf-article__tags">
                <?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
                <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
            </div>
        <?php endif; ?>
    </header>

    <?php if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative) : ?>
        <div class="sf-article__pagination"><?php echo $this->item->pagination; ?></div>
    <?php endif; ?>

    <?php if (!$params->get('show_intro')) : ?>
        <?php echo $this->item->event->afterDisplayTitle; ?>
    <?php endif; ?>

    <?php echo $this->item->event->beforeDisplayContent; ?>

    <?php if (
        isset($urls) && ((!empty($urls->urls_position) && $urls->urls_position == '0') || ($params->get('urls_position') == '0' && empty($urls->urls_position)))
        || (empty($urls->urls_position) && !$params->get('urls_position'))
    ) : ?>
        <?php echo $this->loadTemplate('links'); ?>
    <?php endif; ?>

    <?php if ($params->get('access-view')) : ?>
        <?php if ($fullImage !== '') : ?>
            <figure class="sf-article__image">
                <img src="<?php echo htmlspecialchars($fullImage, ENT_COMPAT, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($fullImageAlt, ENT_COMPAT, 'UTF-8'); ?>" itemprop="image">
                <?php if (is_object($images) && !empty($images->image_fulltext_caption)) : ?>
                    <figcaption><?php echo htmlspecialchars($images->image_fulltext_caption, ENT_COMPAT, 'UTF-8'); ?></figcaption>
                <?php endif; ?>
            </figure>
        <?php endif; ?>

        <?php if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && !$this->item->paginationrelative) : ?>
            <div class="sf-article__pagination"><?php echo $this->item->pagination; ?></div>
        <?php endif; ?>

        <?php if (isset($this->item->toc)) : ?>
            <div class="sf-article__toc"><?php echo $this->item->toc; ?></div>
        <?php endif; ?>

        <div class="sf-article__body" itemprop="articleBody">
            <?php echo $this->item->text; ?>
        </div>

        <?php if ($info == 1 || $info == 2) : ?>
            <?php if ($useDefList) : ?>
                <div class="sf-article__info">
                    <?php echo LayoutHelper::render('joomla.content.info_block.block', ['item' => $this->item, 'params' => $params, 'position' => 'below']); ?>
                </div>
            <?php endif; ?>
            <?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
                <div class="sf-article__tags">
                    <?php $this->item->tagLayout = new FileLayout('joomla.content.tags'); ?>
                    <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && !$this->item->paginationrelative) : ?>
            <div class="sf-article__pagination"><?php echo $this->item->pagination; ?></div>
        <?php endif; ?>

        <?php if (isset($urls) && ((!empty($urls->urls_position) && $urls->urls_position == '1') || $params->get('urls_position') == '1')) : ?>
            <?php echo $this->loadTemplate('links'); ?>
        <?php endif; ?>
    <?php elseif ($params->get('show_noauth') == true && $user->get('guest')) : ?>
        <div class="sf-article__body">
            <?php echo $this->item->introtext; ?>
        </div>
        <?php if ($params->get('show_readmore') && $this->item->fulltext != null) : ?>
            <?php
            $active = Factory::getApplication()->getMenu()->getActive();
            $itemId = $active ? (int) $active->id : 0;
            $link = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
            $link->setVar('return', base64_encode(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
            ?>
            <p class="sf-article__login"><a href="<?php echo $link; ?>"><?php echo Text::_('COM_CONTENT_REGISTER_TO_READ_MORE'); ?></a></p>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($this->item->pagination) && $this->item->pagination && $this->item->paginationposition && $this->item->paginationrelative) : ?>
        <div class="sf-article__pagination"><?php echo $this->item->pagination; ?></div>
    <?php endif; ?>

    <?php echo $this->item->event->afterDisplayContent; ?>
</article>
