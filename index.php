<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$user = JFactory::getUser();
$this->language = $doc->language;
$this->direction = $doc->direction;

// Getting params from template
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option = $app->input->getCmd('option', '');
$view = $app->input->getCmd('view', '');
$layout = $app->input->getCmd('layout', '');
$task = $app->input->getCmd('task', '');
$itemid = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');

if ($task == "edit" || $layout == "form") {
    $fullWidth = 1;
} else {
    $fullWidth = 0;
}

// Add Stylesheets
$doc->addStyleSheet('https://cdn.jsdelivr.net/gh/scoutstrap/scoutstrap@0.1.1/dist/css/scoutstrap.min.css');

$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/font-awesome.min.css');

$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/template.css');

if($this->params->get('customcss')) {
    $doc->addStyleSheet($this->baseurl . htmlspecialchars($this->params->get('customcss'), ENT_COMPAT, 'UTF-8'));
} 

// Add scripts
JHtml::_('jquery.framework');

//Disable Joomla Bootstrap
unset($doc->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.min.js']);

// Adjusting content width
if ($this->countModules('sidebar-left') && $this->countModules('sidebar-right')) {
    $span = "col-md-6";
} elseif ($this->countModules('sidebar-left') && !$this->countModules('sidebar-right')) {
    $span = "col-md-9";
} elseif (!$this->countModules('sidebar-left') && $this->countModules('sidebar-right')) {
    $span = "col-md-9";
} else {
    $span = "col-md-12";
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,400;1,600&display=swap" rel="stylesheet">

        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="<?php echo($this->baseurl . '/templates/' . $this->template . '/images/favicon/apple-touch-icon.png') ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo($this->baseurl . '/templates/' . $this->template . '/images/favicon/favicon-32x32.png') ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?php echo($this->baseurl . '/templates/' . $this->template . '/images/favicon/favicon-16x16.png') ?>">
        <link rel="manifest" href="<?php echo($this->baseurl . '/templates/' . $this->template . '/images/favicon/site.webmanifest') ?>">
        <link rel="mask-icon" href="<?php echo($this->baseurl . '/templates/' . $this->template . '/images/favicon/safari-pinned-tab.svg') ?>" color="#490499">
        <link rel="shortcut icon" href="<?php echo($this->baseurl . '/templates/' . $this->template . '/images/favicon/favicon.ico') ?>">
        <meta name="msapplication-TileColor" content="#490499">
        <meta name="msapplication-config" content="<?php echo($this->baseurl . '/templates/' . $this->template . '/images/favicon/browserconfig.xml') ?>">
        <meta name="theme-color" content="#ffffff">

        <jdoc:include type="head" />

        <?php if($this->params->get('favicon')) { ?>
            <link rel="shortcut icon" href="<?php echo JUri::root(true) . htmlspecialchars($this->params->get('favicon'), ENT_COMPAT, 'UTF-8'); ?>" />
        <?php } ?>

        <!--[if lt IE 9]>
                <script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script>
        <![endif]-->
    </head>

    <body>

        <!-- Header -->
        <div class="container">
            <nav id="main-menu" class="navbar navbar-expand-lg navbar-light bg-faded">
                <a class="navbar-brand" href="<?php echo JURI::base(); ?>">
                    <div class="logo-inline-black logo-inline-w150">
                        <h6>Suffolk</h6>
                    </div> 
                </a>

                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    <jdoc:include type="modules" name="navbar-1" style="none" />

                    <!-- <span class="navbar-text pr-3 pl-2 d-none d-lg-inline">|</span> -->
                    <div class="navbar-separator d-none d-lg-block"></div>

                    <?php
                        //Check login status
                        $user = JFactory::getUser();

                        if ($user->guest) { //Display Login Link
                    ?>
                            <a class="navbar-text text-decoration-none" href="/index.php?option=com_users&lang=en&view=login">Login <i class="fa fa-user-o" style="font-size: 1.2em; padding-left: 0.2em;"></i></a>

                    <?php
                        } else { //Display User Details
                            $userToken = JSession::getFormToken();
                    ?>
                        <ul class="navbar-nav">
                            <li class="dropdown nav-item parent">
                                <a id="user-options" class="dropdown-toggle nav-link font-weight-bolder" href="#" role="button" ><?php echo  $user->name; ?></a>
                                <ul class="dropdown-menu" aria-labelledby="user-options">

                                    <li class="">
                                        <a class="dropdown-item" href="#">Update my Details</a>
                                    </li>

                                    <li class="">
                                        <a class="dropdown-item" href="#">Change Password</a>
                                    </li>

                                    <li><hr class="dropdown-divider"></li>

                                    <li class="">
                                        <a class="dropdown-item" href="/index.php?option=com_users&task=user.logout&<?php echo $userToken; ?>=1">Logout</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    <?php
                        }
                    ?>

                    <jdoc:include type="modules" name="user-menu" style="none" />
                </div>
            </nav>
        </div>

        <?php if ($this->countModules('breadcrumbs')) : ?>
        <div class="bg-light">
            <div class="container">
                <jdoc:include type="modules" name="breadcrumbs" style="xhtml" />
            </div>
        </div>

        <?php endif; ?>

        <header>
        <jdoc:include type="modules" name="banner" style="xhtml" />
        </header>

       

        <main class="body">

            <jdoc:include type="modules" name="above-content" style="xhtml" />

            <div class="content">
                
                <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
                    
                    <div class="row">
                        <?php if ($this->countModules('sidebar-left')) : ?>
                            <div id="sidebar" class="col-md-3">
                                <div class="sidebar-nav">
                                    <jdoc:include type="modules" name="sidebar-left" style="xhtml" />
                                </div>
                            </div>
                        <?php endif; ?>
                        <main id="content" role="main" class="<?php echo $span; ?>">
                            <jdoc:include type="modules" name="position-3" style="xhtml" />
                            <jdoc:include type="message" />
                            <jdoc:include type="component" />
                            <jdoc:include type="modules" name="position-2" style="none" />
                        </main>
                        <?php if ($this->countModules('sidebar-right')) : ?>
                            <div id="aside" class="col-md-3">
                                <jdoc:include type="modules" name="sidebar-right" style="xhtml" />
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <jdoc:include type="modules" name="below-content" style="xhtml" />

        </main>

        <footer class="footer bg-primary text-white pt-4 pb-2" role="contentinfo">
            <div class="container">
                <div class="row">
                    <div class="col-sm-4">

                        <div class="logo-inline-white logo-inline-w200 mb-4">
                            <h6>Suffolk</h6>
                        </div> 

                        <address style="font-size: 0.9em;">
                            <strong>County Office:</strong> <br/>
                            143 Cauldwell Hall Rd <br />
                            Ipswich, Suffolk IP4 5BS
                        </address>

                        <p style="font-size: 0.9em;">
                            <strong>Phone:</strong> 01473 711678 <br />
                            <strong>Email:</strong> contact@suffolkscouts.org.uk
                        </p>

                    </div>

                    <div class="col-sm-8 text-center">
                        <jdoc:include type="modules" name="footer" style="none" />
                        <p></p>
                    </div>
                        
                </div>
                
                <div class="row font-weight-light mt-4" style="font-size: 0.8em;">
                    <div class="col-sm-4">
                        <a href="https://www.suffolkscouts.org.uk/contact-us/web-admin/website-privacy-statement" class="text-white">Privacy Policy</a> &nbsp;
                        <a href="https://www.suffolkscouts.org.uk/contact-us/web-admin/data-protection-policy" class="text-white">Data Protection</a>
                    </div>
                    <div class="col-sm-4 text-center ">
                        &copy; 2011 - <?php echo date('Y'); ?> Suffolk County Scout Council. All Rights Reserved.
                    </div>
                    <div class="col-sm-4">
                        <p class="text-right">
                            <a href="#top" id="back-top" class="text-white" style="text-decoration: none;">
                                <i class="fa fa-arrow-up"></i> Top
                            </a>
                        </p>    
                    </div>
                </div>
            </div>
        </footer>

        <jdoc:include type="modules" name="debug" style="none" />

        <!-- Scripts -->
        <script src="<?php echo($this->baseurl . '/templates/' . $this->template . '/js/template.js'); ?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/gh/scoutstrap/scoutstrap@0.1.1/dist/js/bootstrap.min.js" integrity="sha384-vZA7fWbUdVwzQZlO+dkC65mKiaTlKyDvRFeWWT/+J8nBCX0A/OJE2YaFG+m4Zhv0" crossorigin="anonymous"></script>
    </body>
</html>
