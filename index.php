<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper as ContentRouteHelper;

$app = Factory::getApplication();
$doc = Factory::getDocument();
$user = Factory::getUser();
$menu = $app->getMenu();
$active = $menu ? $menu->getActive() : null;
$isHome = $active ? (bool) $active->home : false;

$this->language = $doc->language;
$this->direction = $doc->direction;

$base = rtrim($this->baseurl, '/');
$templateUrl = $base . '/templates/' . $this->template;
$templatePath = __DIR__;
$siteName = $app->get('sitename') ?: 'Suffolk Scouts';
$cssVersion = is_file($templatePath . '/css/template.css') ? filemtime($templatePath . '/css/template.css') : time();
$jsVersion = is_file($templatePath . '/js/template.js') ? filemtime($templatePath . '/js/template.js') : time();

$doc->addStyleSheet('https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,400;0,600;0,700;0,800;0,900;1,800;1,900&display=swap');
$doc->addStyleSheet($templateUrl . '/css/font-awesome.min.css');
$doc->addStyleSheet($templateUrl . '/css/template.css?v=' . $cssVersion);

if ($this->params->get('customcss')) {
    $doc->addStyleSheet($base . htmlspecialchars($this->params->get('customcss'), ENT_COMPAT, 'UTF-8'));
}

HTMLHelper::_('jquery.framework');

$doc->addScript($templateUrl . '/js/template.js?v=' . $jsVersion);

$rosette = static function (int $size = 36, string $color = 'currentColor'): string {
    $size = max(16, $size);
    $safeColor = htmlspecialchars($color, ENT_COMPAT, 'UTF-8');

    return '<svg class="sf-rosette" width="' . $size . '" height="' . $size . '" viewBox="0 0 40 40" fill="none" aria-hidden="true">'
        . '<circle cx="20" cy="20" r="18" stroke="' . $safeColor . '" stroke-width="1.5"/>'
        . '<path d="M20 4 L22 18 L36 20 L22 22 L20 36 L18 22 L4 20 L18 18 Z" fill="' . $safeColor . '"/>'
        . '<circle cx="20" cy="20" r="2.5" fill="#4C1F7A" stroke="' . $safeColor . '" stroke-width="1"/>'
        . '</svg>';
};

$arrow = '<svg class="sf-arrow" width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true">'
    . '<path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
    . '</svg>';

$external = '<svg class="sf-external" width="13" height="13" viewBox="0 0 14 14" fill="none" aria-hidden="true">'
    . '<path d="M5 2h7v7M12 2L6 8M2 6v6h6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>'
    . '</svg>';

function sfRouteMenuItem(object $item): string
{
    $link = trim((string) ($item->link ?? ''));

    if ($link === '') {
        return '#';
    }

    if (preg_match('#^(https?:)?//#i', $link)) {
        return $link;
    }

    if (str_starts_with($link, 'index.php')) {
        $link .= (str_contains($link, '?') ? '&' : '?') . 'Itemid=' . (int) $item->id;
    }

    return Route::_($link);
}

function sfIsExternalMenuItem(object $item): bool
{
    return preg_match('#^(https?:)?//#i', trim((string) ($item->link ?? ''))) === 1 || (int) ($item->browserNav ?? 0) === 1;
}

function sfMenuItemToLink(object $item): array
{
    return [
        'label' => (string) $item->title,
        'href' => sfRouteMenuItem($item),
        'external' => sfIsExternalMenuItem($item),
    ];
}

function sfBuildNavigation(): array
{
    $db = Factory::getDbo();
    $query = $db->getQuery(true)
        ->select($db->quoteName(['id', 'title', 'link', 'parent_id', 'level', 'browserNav', 'lft']))
        ->from($db->quoteName('#__menu'))
        ->where($db->quoteName('client_id') . ' = 0')
        ->where($db->quoteName('published') . ' = 1')
        ->where($db->quoteName('menutype') . ' = ' . $db->quote('mainmenu'))
        ->order($db->quoteName('lft') . ' ASC');

    $items = $db->setQuery($query)->loadObjectList() ?: [];
    $children = [];

    foreach ($items as $item) {
        $children[(int) $item->parent_id][] = $item;
    }

    $topItems = $children[1] ?? [];
    $navigation = [];

    foreach ($topItems as $top) {
        $directChildren = $children[(int) $top->id] ?? [];
        $sections = [];
        $looseLinks = [];
        $topHref = sfRouteMenuItem($top);

        foreach ($directChildren as $child) {
            $grandChildren = $children[(int) $child->id] ?? [];

            if ($grandChildren) {
                $sectionItems = [];
                $childLink = sfMenuItemToLink($child);

                if ($childLink['href'] !== '#') {
                    $sectionItems[] = array_merge($childLink, ['sub' => 'Overview']);
                }

                foreach ($grandChildren as $grandChild) {
                    $sectionItems[] = sfMenuItemToLink($grandChild);
                }

                $sections[] = [
                    'title' => (string) $child->title,
                    'items' => $sectionItems,
                ];
            } else {
                $looseLinks[] = sfMenuItemToLink($child);
            }
        }

        if ($looseLinks) {
            $chunkSize = max(4, (int) ceil(count($looseLinks) / 2));
            foreach (array_chunk($looseLinks, $chunkSize) as $chunkIndex => $chunk) {
                $sections[] = [
                    'title' => $chunkIndex === 0 ? (string) $top->title : '',
                    'items' => $chunk,
                ];
            }
        }

        if ($topHref === '#' && $directChildren) {
            foreach ($directChildren as $child) {
                $childHref = sfRouteMenuItem($child);
                if ($childHref !== '#') {
                    $topHref = $childHref;
                    break;
                }
            }
        }

        $navigation[] = [
            'label' => (string) $top->title,
            'href' => $topHref,
            'external' => sfIsExternalMenuItem($top),
            'sections' => $sections,
        ];
    }

    return $navigation;
}

function sfIntroImageUrl(?string $images, string $base): string
{
    $fallback = 'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=1200&q=70&auto=format&fit=crop';
    $imageData = json_decode((string) $images);
    $image = '';

    if (is_object($imageData)) {
        $image = (string) ($imageData->image_intro ?: $imageData->image_fulltext ?: '');
    }

    if ($image === '') {
        return $fallback;
    }

    $image = explode('#', $image, 2)[0];

    if (preg_match('#^(https?:)?//#i', $image)) {
        return $image;
    }

    return rtrim($base, '/') . '/' . ltrim($image, '/');
}

function sfExcerpt(string $html, int $max = 150): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));

    if (function_exists('mb_strlen') && mb_strlen($text) > $max) {
        return rtrim(mb_substr($text, 0, $max - 1), " \t\n\r\0\x0B.,;:") . '.';
    }

    if (strlen($text) > $max) {
        return rtrim(substr($text, 0, $max - 1), " \t\n\r\0\x0B.,;:") . '.';
    }

    return $text;
}

function sfLatestNews(string $base): array
{
    $db = Factory::getDbo();
    $newsCategoryQuery = $db->getQuery(true)
        ->select($db->quoteName('id'))
        ->from($db->quoteName('#__categories'))
        ->where($db->quoteName('extension') . ' = ' . $db->quote('com_content'))
        ->where($db->quoteName('published') . ' = 1')
        ->where('(' . $db->quoteName('alias') . ' = ' . $db->quote('news') . ' OR ' . $db->quoteName('title') . ' = ' . $db->quote('News') . ')')
        ->order($db->quoteName('lft') . ' ASC');

    $newsCategoryId = (int) $db->setQuery($newsCategoryQuery, 0, 1)->loadResult();

    if (!$newsCategoryId) {
        return [];
    }

    $query = $db->getQuery(true)
        ->select([
            'a.id',
            'a.alias',
            'a.catid',
            'a.title',
            'a.introtext',
            'a.images',
            'a.publish_up',
            'a.created',
            'a.language',
            'c.title AS category_title',
        ])
        ->from($db->quoteName('#__content', 'a'))
        ->join('INNER', $db->quoteName('#__categories', 'c') . ' ON c.id = a.catid')
        ->where('a.state = 1')
        ->where('a.catid = ' . (int) $newsCategoryId)
        ->order('a.publish_up DESC, a.created DESC');

    $articles = $db->setQuery($query, 0, 6)->loadObjectList() ?: [];
    $cards = [];

    foreach ($articles as $article) {
        $slug = (int) $article->id . ':' . $article->alias;
        $date = $article->publish_up && $article->publish_up !== '0000-00-00 00:00:00' ? $article->publish_up : $article->created;

        $cards[] = [
            'image' => sfIntroImageUrl($article->images, $base),
            'tag' => (string) $article->category_title,
            'date' => HTMLHelper::_('date', $date, 'd M Y'),
            'title' => (string) $article->title,
            'body' => sfExcerpt((string) $article->introtext),
            'href' => Route::_(ContentRouteHelper::getArticleRoute($slug, (int) $article->catid, $article->language)),
        ];
    }

    return $cards;
}

function sfJEventsCalendarHref(): string
{
    $db = Factory::getDbo();
    $query = $db->getQuery(true)
        ->select($db->quoteName(['id', 'link']))
        ->from($db->quoteName('#__menu'))
        ->where($db->quoteName('client_id') . ' = 0')
        ->where($db->quoteName('published') . ' = 1')
        ->where($db->quoteName('link') . ' LIKE ' . $db->quote('%option=com_jevents%'))
        ->order($db->quoteName('lft') . ' ASC');

    $item = $db->setQuery($query, 0, 1)->loadObject();

    if ($item) {
        return sfRouteMenuItem($item);
    }

    return Route::_('index.php?option=com_jevents&view=month&layout=calendar');
}

function sfLatestEvents(): array
{
    $db = Factory::getDbo();
    $calendarItemId = 0;
    $calendarQuery = $db->getQuery(true)
        ->select($db->quoteName('id'))
        ->from($db->quoteName('#__menu'))
        ->where($db->quoteName('client_id') . ' = 0')
        ->where($db->quoteName('published') . ' = 1')
        ->where($db->quoteName('link') . ' LIKE ' . $db->quote('%option=com_jevents%'))
        ->order($db->quoteName('lft') . ' ASC');

    $calendarItemId = (int) $db->setQuery($calendarQuery, 0, 1)->loadResult();

    $query = $db->getQuery(true)
        ->select([
            'r.rp_id',
            'r.eventid',
            'r.startrepeat',
            'r.endrepeat',
            'd.summary',
            'd.location',
            'd.noendtime',
        ])
        ->from($db->quoteName('#__jevents_repetition', 'r'))
        ->join('INNER', $db->quoteName('#__jevents_vevent', 'e') . ' ON e.ev_id = r.eventid')
        ->join('INNER', $db->quoteName('#__jevents_vevdetail', 'd') . ' ON d.evdet_id = r.eventdetail_id')
        ->where('e.state = 1')
        ->where('d.state = 1')
        ->where('r.startrepeat >= NOW()')
        ->where('d.summary NOT LIKE ' . $db->quote('%Bank Holiday%'))
        ->where('d.summary NOT LIKE ' . $db->quote('School %'))
        ->where('d.summary NOT LIKE ' . $db->quote('Clocks %'))
        ->order('r.startrepeat ASC');

    $rows = $db->setQuery($query, 0, 4)->loadObjectList() ?: [];
    $events = [];

    foreach ($rows as $row) {
        $start = strtotime((string) $row->startrepeat);
        $end = strtotime((string) $row->endrepeat);
        $isAllDay = date('H:i:s', $start) === '00:00:00' && date('H:i:s', $end) === '23:59:59';
        $sameDay = date('Y-m-d', $start) === date('Y-m-d', $end);

        if (!$sameDay) {
            $when = HTMLHelper::_('date', $row->startrepeat, 'D j M') . ' - ' . HTMLHelper::_('date', $row->endrepeat, 'D j M');
        } elseif ($isAllDay || (int) $row->noendtime === 1) {
            $when = HTMLHelper::_('date', $row->startrepeat, 'D') . ' - All day';
        } else {
            $when = HTMLHelper::_('date', $row->startrepeat, 'D - H:i');
        }

        $href = 'index.php?option=com_jevents&task=icalrepeat.detail'
            . '&evid=' . (int) $row->eventid
            . '&rp_id=' . (int) $row->rp_id
            . '&year=' . HTMLHelper::_('date', $row->startrepeat, 'Y')
            . '&month=' . HTMLHelper::_('date', $row->startrepeat, 'm')
            . '&day=' . HTMLHelper::_('date', $row->startrepeat, 'd');
        if ($calendarItemId) {
            $href .= '&Itemid=' . $calendarItemId;
        }

        $events[] = [
            'day' => HTMLHelper::_('date', $row->startrepeat, 'd'),
            'month' => HTMLHelper::_('date', $row->startrepeat, 'M'),
            'title' => (string) $row->summary,
            'when' => $when,
            'where' => trim((string) $row->location) ?: 'Suffolk Scouts',
            'href' => Route::_($href),
        ];
    }

    return $events;
}

function sfFindMenuItemByTitle(array $items, string $title): ?object
{
    foreach ($items as $item) {
        if (strcasecmp((string) $item->title, $title) === 0) {
            return $item;
        }
    }

    return null;
}

function sfFooterColumns(): array
{
    $db = Factory::getDbo();
    $query = $db->getQuery(true)
        ->select($db->quoteName(['id', 'menutype', 'title', 'link', 'parent_id', 'level', 'browserNav', 'lft']))
        ->from($db->quoteName('#__menu'))
        ->where($db->quoteName('client_id') . ' = 0')
        ->where($db->quoteName('published') . ' = 1')
        ->where($db->quoteName('menutype') . ' IN (' . $db->quote('mainmenu') . ',' . $db->quote('usermenu') . ')')
        ->order($db->quoteName('menutype') . ' ASC, ' . $db->quoteName('lft') . ' ASC');

    $items = $db->setQuery($query)->loadObjectList() ?: [];
    $children = [];

    foreach ($items as $item) {
        $title = trim((string) $item->title);
        if ($title === '') {
            continue;
        }
        $children[(int) $item->parent_id][] = $item;
    }

    $columns = [];
    foreach (['About', 'Programme', 'Learning', 'Contact'] as $title) {
        $top = sfFindMenuItemByTitle($children[1] ?? [], $title);
        if (!$top) {
            continue;
        }

        $links = [];
        foreach (array_slice($children[(int) $top->id] ?? [], 0, 6) as $child) {
            $link = sfMenuItemToLink($child);
            if ($link['href'] !== '#') {
                $links[] = $link;
            }
        }

        if ($links) {
            $columns[] = ['title' => $title, 'links' => $links];
        }
    }

    $userLinks = [];
    foreach ($items as $item) {
        if ((string) $item->menutype !== 'usermenu' || (int) $item->parent_id !== 1 || trim((string) $item->title) === '') {
            continue;
        }

        $userLinks[] = sfMenuItemToLink($item);
        if (count($userLinks) === 6) {
            break;
        }
    }

    if ($userLinks) {
        $columns[] = ['title' => 'Member resources', 'links' => $userLinks];
    }

    return array_slice($columns, 0, 4);
}

$navItems = sfBuildNavigation();
$newsCards = sfLatestNews($base);
$events = $isHome ? sfLatestEvents() : [];
$calendarHref = $isHome ? sfJEventsCalendarHref() : '';
$footerCols = sfFooterColumns();
?>
<!doctype html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $templateUrl; ?>/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $templateUrl; ?>/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $templateUrl; ?>/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?php echo $templateUrl; ?>/images/favicon/site.webmanifest">
    <link rel="shortcut icon" href="<?php echo $templateUrl; ?>/images/favicon/favicon.ico">
    <meta name="theme-color" content="#4C1F7A">
    <jdoc:include type="head" />
    <?php if ($this->params->get('favicon')): ?>
        <link rel="shortcut icon" href="<?php echo Uri::root(true) . htmlspecialchars($this->params->get('favicon'), ENT_COMPAT, 'UTF-8'); ?>">
    <?php endif; ?>
</head>
<body class="sf-site<?php echo $isHome ? ' sf-home' : ' sf-page'; ?>">
    <a class="sf-skip" href="#content">Skip to content</a>

    <header class="sf-header" data-sf-header>
        <div class="sf-header__inner">
            <a class="sf-brand" href="<?php echo Uri::base(); ?>" aria-label="<?php echo htmlspecialchars($siteName, ENT_COMPAT, 'UTF-8'); ?> home">
                <img class="sf-brand__logo" src="<?php echo $templateUrl; ?>/images/365-logo.svg" alt="Suffolk Scouts">
            </a>

            <button class="sf-nav-toggle" type="button" data-sf-nav-toggle aria-expanded="false" aria-controls="sf-main-nav">
                <span></span><span></span><span></span>
                <span class="sf-visually-hidden">Menu</span>
            </button>

            <nav class="sf-nav" id="sf-main-nav" data-sf-nav aria-label="Main navigation">
                <?php foreach ($navItems as $index => $item): ?>
                    <?php $hasMenu = !empty($item['sections']); ?>
                    <div class="sf-nav__item<?php echo $hasMenu ? ' has-menu' : ''; ?>">
                        <a class="sf-nav__link" href="<?php echo htmlspecialchars($item['href'], ENT_COMPAT, 'UTF-8'); ?>"
                            <?php if ($hasMenu): ?>
                                data-sf-menu-trigger aria-haspopup="true" aria-expanded="false" aria-controls="sf-menu-<?php echo $index; ?>"
                            <?php endif; ?>>
                            <?php echo htmlspecialchars($item['label'], ENT_COMPAT, 'UTF-8'); ?>
                            <?php if ($hasMenu): ?><span class="sf-chevron" aria-hidden="true"></span><?php endif; ?>
                            <?php if (!$hasMenu && !empty($item['external'])): ?><?php echo $external; ?><?php endif; ?>
                        </a>

                        <?php if ($hasMenu): ?>
                            <div class="sf-mega" id="sf-menu-<?php echo $index; ?>" data-sf-menu>
                                <div class="sf-mega__grid" style="--sf-menu-cols: <?php echo count($item['sections']); ?>">
                                    <div class="sf-mega__columns">
                                        <?php foreach ($item['sections'] as $section): ?>
                                            <section class="sf-mega__section">
                                                <h2><?php echo htmlspecialchars($section['title'], ENT_COMPAT, 'UTF-8'); ?></h2>
                                                <?php foreach ($section['items'] as $link): ?>
                                                    <a class="sf-mega__link" href="<?php echo htmlspecialchars($link['href'], ENT_COMPAT, 'UTF-8'); ?>">
                                                        <span>
                                                            <strong><?php echo htmlspecialchars($link['label'], ENT_COMPAT, 'UTF-8'); ?></strong>
                                                            <?php if (!empty($link['sub'])): ?>
                                                                <small><?php echo htmlspecialchars($link['sub'], ENT_COMPAT, 'UTF-8'); ?></small>
                                                            <?php endif; ?>
                                                        </span>
                                                        <?php if (!empty($link['external'])): ?><?php echo $external; ?><?php endif; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </section>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </nav>

            <div class="sf-account">
                <?php if ($user->guest): ?>
                    <a class="sf-account__login" href="/?oauthlink=login">Member login</a>
                <?php else: ?>
                    <a class="sf-account__login" href="/index.php?option=com_users&view=profile"><?php echo htmlspecialchars($user->name, ENT_COMPAT, 'UTF-8'); ?></a>
                <?php endif; ?>
                <a class="sf-button sf-button--yellow sf-account__join" href="/join-us">Join us</a>
            </div>
        </div>
        <div class="sf-menu-backdrop" data-sf-menu-backdrop></div>
    </header>

    <?php if ($this->countModules('breadcrumbs') && !$isHome): ?>
        <div class="sf-breadcrumbs">
            <div class="sf-container">
                <jdoc:include type="modules" name="breadcrumbs" style="xhtml" />
            </div>
        </div>
    <?php endif; ?>

    <main id="content" class="sf-main" role="main">
        <jdoc:include type="message" />

        <?php if ($isHome): ?>
            <section class="sf-hero">
                <svg class="sf-hero__contours" aria-hidden="true" viewBox="0 0 1440 720" preserveAspectRatio="xMidYMid slice">
                    <?php foreach ([0, 40, 80, 120, 160, 200, 240, 280, 320, 360, 400] as $offset): ?>
                        <path d="M -50 <?php echo 160 + $offset; ?> C 200 <?php echo 60 + $offset; ?>, 480 <?php echo 280 + $offset; ?>, 760 <?php echo 120 + $offset; ?> S 1280 <?php echo 80 + $offset; ?>, 1500 <?php echo 180 + $offset; ?>" />
                    <?php endforeach; ?>
                </svg>
                <div class="sf-hero__county" aria-hidden="true">
                    <svg viewBox="0 0 480 360">
                        <path d="M40 180 C 60 100, 160 60, 260 80 S 420 60, 460 140 C 470 220, 380 280, 300 280 S 120 320, 60 260 Z" />
                        <circle cx="180" cy="180" r="4" />
                        <circle cx="280" cy="160" r="4" />
                        <circle cx="360" cy="200" r="4" />
                    </svg>
                </div>
                <svg class="sf-hero__coast" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M0,80 L0,48 C120,42 200,56 320,50 C440,44 520,30 640,36 C760,42 820,60 940,54 C1060,48 1180,28 1300,34 C1360,37 1400,44 1440,42 L1440,80 Z" fill="#EDEAE2"/>
                    <path d="M0,80 L0,62 C140,60 220,68 360,64 C500,60 580,52 720,56 C860,60 940,70 1080,66 C1220,62 1340,56 1440,58 L1440,80 Z" fill="#E0DCD0"/>
                </svg>
                <div class="sf-container sf-hero__inner">
                    <div class="sf-hero__copy">
                        <h1>Preparing<br>young people<br>with <span>skills</span><br><span class="sf-hero__teal">for life</span>.</h1>
                        <p>From Bury St Edmunds to Felixstowe, Suffolk Scouts run local groups across the county. Whether you're a parent, a young person, or a future volunteer, there's a place for you with us.</p>
                        <div class="sf-actions">
                            <a class="sf-button sf-button--yellow sf-button--large" href="/join-us/find-a-group">Find a group near you <?php echo $arrow; ?></a>
                            <a class="sf-button sf-button--outline-dark sf-button--large" href="/volunteer">Volunteer with us</a>
                        </div>
                    </div>
                    <div class="sf-photo-grid" aria-label="Suffolk Scouts activities">
                        <div class="sf-photo-tile sf-photo-tile--water">
                            <div>
                                <span>On the water</span>
                                <strong>Sailing &amp; paddlesports</strong>
                            </div>
                            <?php echo $arrow; ?>
                        </div>
                        <div class="sf-photo-tile sf-photo-tile--ground">
                            <div>
                                <span>On the ground</span>
                                <strong>Hiking &amp; expeditions</strong>
                            </div>
                            <?php echo $arrow; ?>
                        </div>
                        <div class="sf-photo-tile sf-photo-tile--fire">
                            <div>
                                <span>Around the fire</span>
                                <strong>Bushcraft &amp; camping</strong>
                            </div>
                            <?php echo $arrow; ?>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sf-finder" aria-labelledby="sf-finder-title">
                <div class="sf-container sf-finder__inner">
                    <div class="sf-finder__label">
                        <span>Get started</span>
                        <h2 id="sf-finder-title">Find your local group</h2>
                    </div>
                    <form class="sf-finder__form" action="https://www.scouts.org.uk/groups/" method="get" target="_blank" rel="noopener">
                        <label class="sf-visually-hidden" for="sf-postcode">Postcode or town</label>
                        <input id="sf-postcode" name="loc" type="search" value="IP1 2BX" autocomplete="postal-code">
                        <button type="submit">Search <?php echo $arrow; ?></button>
                    </form>
                    <p>Enter a postcode or town - we'll show the nearest Squirrels, Cubs or Scouts group.</p>
                </div>
            </section>

            <section class="sf-news" aria-labelledby="sf-news-title">
                <div class="sf-container">
                    <div class="sf-section-head">
                        <h2 id="sf-news-title">Latest from the county.</h2>
                        <div class="sf-filter-tabs" aria-label="News filters">
                            <?php foreach (['All', 'News', 'Events', 'Training', 'Awards'] as $i => $tab): ?>
                                <button class="<?php echo $i === 0 ? 'is-active' : ''; ?>" type="button"><?php echo $tab; ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="sf-news__grid">
                        <?php foreach ($newsCards as $card): ?>
                            <a class="sf-news-card" href="<?php echo htmlspecialchars($card['href'], ENT_COMPAT, 'UTF-8'); ?>">
                                <span class="sf-news-card__image" style="background-image: url('<?php echo htmlspecialchars($card['image'], ENT_COMPAT, 'UTF-8'); ?>')"></span>
                                <span class="sf-news-card__meta">
                                    <span><?php echo htmlspecialchars($card['tag'], ENT_COMPAT, 'UTF-8'); ?></span>
                                    <time><?php echo htmlspecialchars($card['date'], ENT_COMPAT, 'UTF-8'); ?></time>
                                </span>
                                <strong><?php echo htmlspecialchars($card['title'], ENT_COMPAT, 'UTF-8'); ?></strong>
                                <em><?php echo htmlspecialchars($card['body'], ENT_COMPAT, 'UTF-8'); ?></em>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section class="sf-cta" aria-label="Volunteer and safeguarding">
                <div class="sf-container sf-cta__grid">
                    <div class="sf-volunteer">
                        <div class="sf-volunteer__copy">
                            <h2>Volunteer two hours, change a young person's year.</h2>
                            <p>No experience needed. We'll train you, match you to a group close to home, and give you the time of your life doing it.</p>
                            <div class="sf-actions">
                                <a class="sf-button sf-button--yellow" href="/volunteer">Become a volunteer <?php echo $arrow; ?></a>
                                <a class="sf-button sf-button--outline-light" href="/volunteer/stories">Read our stories</a>
                            </div>
                        </div>
                        <div class="sf-volunteer__image" aria-hidden="true"></div>
                        <div class="sf-volunteer__mark" aria-hidden="true"><?php echo $rosette(280, '#FFFFFF'); ?></div>
                    </div>

                    <aside class="sf-safeguarding">
                        <div>
                            <span>Safeguarding</span>
                            <h2>Safety first - always.</h2>
                            <p>Every Suffolk Scout volunteer is vetted, trained and supported. If you have a concern, tell us. We'll listen.</p>
                        </div>
                        <a class="sf-safeguarding__phone" href="tel:03453001818">
                            <span>24-hour line</span>
                            <strong>0345 300 1818</strong>
                        </a>
                    </aside>
                </div>
            </section>

        <?php else: ?>
            <jdoc:include type="modules" name="banner" style="xhtml" />
            <jdoc:include type="modules" name="above-content" style="xhtml" />
            <section class="sf-page-content">
                <div class="sf-container sf-page-content__grid">
                    <?php if ($this->countModules('sidebar-left')): ?>
                        <aside class="sf-sidebar"><jdoc:include type="modules" name="sidebar-left" style="xhtml" /></aside>
                    <?php endif; ?>
                    <article class="sf-component">
                        <jdoc:include type="component" />
                    </article>
                    <?php if ($this->countModules('sidebar-right')): ?>
                        <aside class="sf-sidebar"><jdoc:include type="modules" name="sidebar-right" style="xhtml" /></aside>
                    <?php endif; ?>
                </div>
            </section>
            <jdoc:include type="modules" name="below-content" style="xhtml" />
        <?php endif; ?>
    </main>

    <footer class="sf-footer" role="contentinfo">
        <?php if ($isHome): ?>
        <section class="sf-events" aria-labelledby="sf-events-title">
            <div class="sf-container">
                <div class="sf-events__head">
                    <h2 id="sf-events-title">Upcoming events</h2>
                    <a href="<?php echo htmlspecialchars($calendarHref, ENT_COMPAT, 'UTF-8'); ?>">View full calendar <?php echo $arrow; ?></a>
                </div>
                <div class="sf-events__grid">
                    <?php foreach ($events as $event): ?>
                        <a class="sf-event-card" href="<?php echo htmlspecialchars($event['href'], ENT_COMPAT, 'UTF-8'); ?>">
                            <span class="sf-event-card__date">
                                <strong><?php echo htmlspecialchars($event['day'], ENT_COMPAT, 'UTF-8'); ?></strong>
                                <em><?php echo htmlspecialchars($event['month'], ENT_COMPAT, 'UTF-8'); ?></em>
                            </span>
                            <span class="sf-event-card__body">
                                <strong><?php echo htmlspecialchars($event['title'], ENT_COMPAT, 'UTF-8'); ?></strong>
                                <small><?php echo htmlspecialchars($event['when'], ENT_COMPAT, 'UTF-8'); ?></small>
                                <small><?php echo htmlspecialchars($event['where'], ENT_COMPAT, 'UTF-8'); ?></small>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php endif; ?>

        <div class="sf-footer__main">
            <div class="sf-container">
                <div class="sf-footer__grid">
                    <div class="sf-footer__brand">
                        <div class="sf-footer__logo">
                            <img class="sf-footer__logo-img" src="<?php echo $templateUrl; ?>/images/365-logo.svg" alt="Suffolk Scouts">
                        </div>
                        <address>
                            Suffolk Scouts County Office<br>
                            Hallowtree Scout Activity Centre<br>
                            Alnesbourne Priory<br>
                            Nacton, Ipswich, Suffolk IP10 0JP
                        </address>
                        <p><a href="tel:01473711678">01473 711678</a><br><a href="mailto:contact@suffolkscouts.org.uk">contact@suffolkscouts.org.uk</a></p>
                    </div>
                    <?php foreach ($footerCols as $column): ?>
                        <nav class="sf-footer__col" aria-label="<?php echo htmlspecialchars($column['title'], ENT_COMPAT, 'UTF-8'); ?>">
                            <h2><?php echo htmlspecialchars($column['title'], ENT_COMPAT, 'UTF-8'); ?></h2>
                            <?php foreach ($column['links'] as $link): ?>
                                <a href="<?php echo htmlspecialchars($link['href'], ENT_COMPAT, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($link['label'], ENT_COMPAT, 'UTF-8'); ?><?php if (!empty($link['external'])): ?> <?php echo $external; ?><?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    <?php endforeach; ?>
                </div>

                <?php if ($this->countModules('footer')): ?>
                    <div class="sf-footer__modules"><jdoc:include type="modules" name="footer" style="none" /></div>
                <?php endif; ?>

                <div class="sf-footer__legal">
                    <span>&copy; 2011 - <?php echo date('Y'); ?> Suffolk County Scout Council. All rights reserved.</span>
                    <span>
                        <a href="<?php echo Route::_('index.php?Itemid=2243'); ?>">Privacy</a>
                        <a href="<?php echo Route::_('index.php?Itemid=2242'); ?>">Data Protection</a>
                        <a href="<?php echo Route::_('index.php?Itemid=569'); ?>">Safeguarding</a>
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <jdoc:include type="modules" name="debug" style="none" />
</body>
</html>
