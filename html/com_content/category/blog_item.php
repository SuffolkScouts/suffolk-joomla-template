<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;

if (!function_exists('sf_content_blog_image')) {
    function sf_content_blog_image($item): string
    {
        $images = json_decode((string) $item->images);
        $image = '';

        if (is_object($images)) {
            $image = (string) ($images->image_intro ?: $images->image_fulltext ?: '');
        }

        if ($image === '') {
            return Uri::root(true) . '/templates/suffolkdev/images/scouts-badge.svg';
        }

        $image = explode('#', $image, 2)[0];

        if (preg_match('#^(https?:)?//#i', $image)) {
            return $image;
        }

        return Uri::root(true) . '/' . ltrim($image, '/');
    }
}

if (!function_exists('sf_content_blog_excerpt')) {
    function sf_content_blog_excerpt(string $html, int $max = 190): string
    {
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\xc2\xa0", ' ', $text);
        $text = trim(preg_replace('/\s+/', ' ', $text));

        if (function_exists('mb_strlen') && mb_strlen($text) > $max) {
            return rtrim(mb_substr($text, 0, $max - 1), " \t\n\r\0\x0B.,;:") . '.';
        }

        if (strlen($text) > $max) {
            return rtrim(substr($text, 0, $max - 1), " \t\n\r\0\x0B.,;:") . '.';
        }

        return $text;
    }
}

$params = $this->item->params;
$canEdit = $params->get('access-edit');
$assocParam = Associations::isEnabled() && $params->get('show_associations');
$currentDate = Factory::getDate()->format('Y-m-d H:i:s');
$isUnpublished = ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED || $this->item->publish_up > $currentDate)
    || ($this->item->publish_down < $currentDate && $this->item->publish_down !== null);

if ($params->get('access-view')) {
    $link = Route::_(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
} else {
    $active = Factory::getApplication()->getMenu()->getActive();
    $itemId = $active ? (int) $active->id : 0;
    $loginLink = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
    $loginLink->setVar('return', base64_encode(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
    $link = (string) $loginLink;
}

$date = $this->item->publish_up && $this->item->publish_up !== '0000-00-00 00:00:00' ? $this->item->publish_up : $this->item->created;
$useDefList = $params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
    || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author') || $assocParam;
?>

<div class="sf-news-list-card<?php echo $isUnpublished ? ' system-unpublished' : ''; ?>">
    <?php if ($canEdit) : ?>
        <div class="sf-news-list-card__edit">
            <?php echo LayoutHelper::render('joomla.content.icons', ['params' => $params, 'item' => $this->item]); ?>
        </div>
    <?php endif; ?>

    <a class="sf-news-list-card__image" href="<?php echo $link; ?>" style="background-image: url('<?php echo htmlspecialchars(sf_content_blog_image($this->item), ENT_COMPAT, 'UTF-8'); ?>')" aria-label="<?php echo htmlspecialchars($this->item->title, ENT_COMPAT, 'UTF-8'); ?>"></a>

    <div class="sf-news-list-card__body">
        <div class="sf-news-list-card__meta">
            <?php if (!empty($this->item->category_title)) : ?>
                <span><?php echo htmlspecialchars($this->item->category_title, ENT_COMPAT, 'UTF-8'); ?></span>
            <?php endif; ?>
            <time datetime="<?php echo HTMLHelper::_('date', $date, 'c'); ?>"><?php echo HTMLHelper::_('date', $date, 'd M Y'); ?></time>
        </div>

        <h2 itemprop="name">
            <a href="<?php echo $link; ?>" itemprop="url"><?php echo $this->escape($this->item->title); ?></a>
        </h2>

        <?php if ($useDefList) : ?>
            <div class="sf-news-list-card__info">
                <?php echo LayoutHelper::render('joomla.content.info_block', ['item' => $this->item, 'params' => $params, 'position' => 'above']); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->item->event->beforeDisplayContent) : ?>
            <div class="sf-news-list-card__plugins">
                <?php echo $this->item->event->beforeDisplayContent; ?>
            </div>
        <?php endif; ?>

        <p><?php echo htmlspecialchars(sf_content_blog_excerpt((string) $this->item->introtext), ENT_COMPAT, 'UTF-8'); ?></p>

        <?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
            <div class="sf-news-list-card__tags">
                <?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
            </div>
        <?php endif; ?>

        <a class="sf-news-list-card__readmore" href="<?php echo $link; ?>">
            Read more
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>

        <?php if ($this->item->event->afterDisplayContent) : ?>
            <div class="sf-news-list-card__plugins">
                <?php echo $this->item->event->afterDisplayContent; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
