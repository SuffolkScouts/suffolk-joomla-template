<?php
/**
 * Suffolk Scouts – error page (404, 403, 500, …)
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

$app  = Factory::getApplication();
$doc  = Factory::getDocument();

$this->language  = $doc->language;
$this->direction = $doc->direction;

$base        = rtrim($this->baseurl, '/');
$templateUrl = $base . '/templates/' . $this->template;
$templatePath = __DIR__;
$siteName    = $app->get('sitename') ?: 'Suffolk Scouts';

$cssVersion = is_file($templatePath . '/css/template.css') ? filemtime($templatePath . '/css/template.css') : time();

$errorCode    = (int) $this->error->getCode();
$errorMessage = htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8');

$heading = match ($errorCode) {
    404 => 'Page not found',
    403 => 'Access denied',
    500 => 'Something went wrong',
    default => 'An error occurred',
};

$body = match ($errorCode) {
    404 => 'The page you\'re looking for may have moved, been renamed, or no longer exists.',
    403 => 'You don\'t have permission to view this page.',
    500 => 'We\'re working on it. Please try again shortly or get in touch if this keeps happening.',
    default => 'An unexpected error has occurred. Please try again or return to the home page.',
};

$rosette = static function (int $size = 36, string $color = 'currentColor'): string {
    $size      = max(16, $size);
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

$navLinks = [
    ['label' => 'About', 'href' => '/about/about-us'],
    ['label' => 'Contact', 'href' => '/contact-us/suffolkvacancies'],
    ['label' => 'News', 'href' => '/news'],
    ['label' => 'Districts', 'href' => '/districts/districts-2'],
    ['label' => 'Learning', 'href' => '/training/adult-training'],
    ['label' => 'Youth Shaped', 'href' => '/youth-shaped/youth-shaped-scouting-2'],
    ['label' => 'Programme', 'href' => '/programme/all-section-activities'],
];

$footerColumns = [
    'About' => [
        ['label' => 'About Us', 'href' => '/about/about-us'],
        ['label' => 'Transformation', 'href' => '/about/transformation'],
        ['label' => 'Find Your Local Group', 'href' => '/about/find-your-local-group'],
        ['label' => 'Strategic Plan', 'href' => '/about/strategic-plan'],
    ],
    'Programme' => [
        ['label' => 'All Section Activities', 'href' => '/programme/all-section-activities'],
        ['label' => 'Sections', 'href' => '/programme/sections'],
        ['label' => 'Duke of Edinburgh Scheme', 'href' => '/programme/dofe-programme'],
    ],
    'Learning' => [
        ['label' => 'Learning for Roles', 'href' => '/training/adult-training'],
        ['label' => 'Pathways to Permits', 'href' => '/training/pathways-to-permits'],
        ['label' => 'Nights Away', 'href' => '/training/nights-away'],
    ],
    'Contact' => [
        ['label' => 'Volunteer Vacancies', 'href' => '/contact-us/suffolkvacancies'],
        ['label' => 'County Office', 'href' => '/contact-us/county-office'],
        ['label' => 'Technical Help & Support', 'href' => '/contact-us/technical-help'],
    ],
];
?>
<!doctype html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $errorCode . ' – ' . htmlspecialchars($heading, ENT_COMPAT, 'UTF-8') . ' | ' . htmlspecialchars($siteName, ENT_COMPAT, 'UTF-8'); ?></title>
    <meta name="robots" content="noindex">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $templateUrl; ?>/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $templateUrl; ?>/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $templateUrl; ?>/images/favicon/favicon-16x16.png">
    <link rel="shortcut icon" href="<?php echo $templateUrl; ?>/images/favicon/favicon.ico">
    <meta name="theme-color" content="#4C1F7A">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,400;0,600;0,700;0,800;0,900;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $templateUrl; ?>/css/template.css?v=<?php echo $cssVersion; ?>">
    <?php if ($app->get('debug_lang', '0') == '1' || $app->get('debug', '0') == '1'): ?>
        <link rel="stylesheet" href="<?php echo $base; ?>/media/cms/css/debug.css">
    <?php endif; ?>
</head>
<body class="sf-site sf-page sf-error-page">
    <a class="sf-skip" href="#content">Skip to content</a>

    <header class="sf-header" data-sf-header>
        <div class="sf-header__inner">
            <a class="sf-brand" href="<?php echo Uri::base(); ?>" aria-label="<?php echo htmlspecialchars($siteName, ENT_COMPAT, 'UTF-8'); ?> home">
                <img class="sf-brand__logo" src="<?php echo $templateUrl; ?>/images/365-logo.svg" alt="Suffolk Scouts">
            </a>
            <nav class="sf-nav" aria-label="Main navigation">
                <?php foreach ($navLinks as $link): ?>
                    <div class="sf-nav__item">
                        <a class="sf-nav__link" href="<?php echo $base . htmlspecialchars($link['href'], ENT_COMPAT, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($link['label'], ENT_COMPAT, 'UTF-8'); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </nav>
            <div class="sf-account">
                <a class="sf-button sf-button--yellow sf-account__join" href="<?php echo $base; ?>/join-us">Join us</a>
            </div>
        </div>
    </header>

    <main id="content" class="sf-main" role="main">
        <section class="sf-error" aria-labelledby="sf-error-heading">
            <div class="sf-error__decoration" aria-hidden="true">
                <?php echo $rosette(320, '#4C1F7A'); ?>
            </div>
            <div class="sf-container sf-error__inner">
                <div class="sf-error__code" aria-hidden="true"><?php echo $errorCode ?: ''; ?></div>
                <h1 id="sf-error-heading"><?php echo htmlspecialchars($heading, ENT_COMPAT, 'UTF-8'); ?></h1>
                <p><?php echo htmlspecialchars($body, ENT_COMPAT, 'UTF-8'); ?></p>
                <div class="sf-actions">
                    <a class="sf-button sf-button--yellow sf-button--large" href="<?php echo $base; ?>/">Home page <?php echo $arrow; ?></a>
                    <a class="sf-button sf-button--outline-dark sf-button--large" href="<?php echo $base; ?>/contact-us">Contact us</a>
                </div>
                <?php if ($this->debug): ?>
                    <details class="sf-error__debug">
                        <summary><strong><?php echo $errorCode; ?></strong> <?php echo $errorMessage; ?></summary>
                        <?php echo $this->renderBacktrace(); ?>
                    </details>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="sf-footer" role="contentinfo">
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
                    <?php foreach ($footerColumns as $title => $links): ?>
                        <nav class="sf-footer__col" aria-label="<?php echo htmlspecialchars($title, ENT_COMPAT, 'UTF-8'); ?>">
                            <h2><?php echo htmlspecialchars($title, ENT_COMPAT, 'UTF-8'); ?></h2>
                            <?php foreach ($links as $link): ?>
                                <a href="<?php echo $base . htmlspecialchars($link['href'], ENT_COMPAT, 'UTF-8'); ?>"><?php echo htmlspecialchars($link['label'], ENT_COMPAT, 'UTF-8'); ?></a>
                            <?php endforeach; ?>
                        </nav>
                    <?php endforeach; ?>
                </div>
                <div class="sf-footer__legal">
                    <span>&copy; 2011 - <?php echo date('Y'); ?> Suffolk County Scout Council. All rights reserved.</span>
                    <span>
                        <a href="<?php echo $base; ?>/privacy-policy">Privacy</a>
                        <a href="<?php echo $base; ?>/data-protection-policy">Data Protection</a>
                        <a href="<?php echo $base; ?>/about/safeguarding">Safeguarding</a>
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?php echo $templateUrl; ?>/js/template.js?v=<?php echo $cssVersion; ?>"></script>
</body>
</html>
