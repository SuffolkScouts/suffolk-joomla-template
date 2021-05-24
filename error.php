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

// Add JavaScript Frameworks
JHtml::_('jquery.framework');

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title><?php echo $this->title; ?> <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,400;1,600&display=swap" rel="stylesheet">


        <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/template.css" type="text/css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/scoutstrap/scoutstrap@0.1.1/dist/css/scoutstrap.min.css" type="text/css" />
        <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/font-awesome.min.css" type="text/css" />

        <?php if ($app->get('debug_lang', '0') == '1' || $app->get('debug', '0') == '1') : ?>
            <link rel="stylesheet" href="<?php echo $this->baseurl; ?>/media/cms/css/debug.css" type="text/css" />
        <?php endif; ?>

        <link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
        <!--[if lt IE 9]>
                <script src="<?php echo $this->baseurl; ?>/media/jui/js/html5.js"></script>
        <![endif]-->

    </head>

    <body>

       <!-- Header -->
       <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-faded">
                <a class="navbar-brand" href="<?php echo JURI::base(); ?>">
                    <div class="logo-inline-black logo-inline-w200">
                        <h6>Suffolk</h6>
                    </div> 
                </a>

                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    <jdoc:include type="modules" name="navbar-1" style="none" />

                    <span class="navbar-text pr-3 pl-2 d-none d-lg-inline">|</span>

                    <?php
                        //Check login status
                        $user = JFactory::getUser();

                        if ($user->guest) { //Display Login Link
                    ?>
                            <a class="navbar-text text-decoration-none" href="/index.php?option=com_users&lang=en&view=login"><i class="fa fa-user"></i> Login</a>

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

        <header>
        <jdoc:include type="modules" name="banner" style="xhtml" />
        </header>

        <main class="body">

            <jdoc:include type="modules" name="above-content" style="xhtml" />

            <div class="content">
                
                <div class="container<?php echo ($params->get('fluidContainer') ? '-fluid' : ''); ?>">
                    
                    <div class="row">
                    <h1><?php echo JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'); ?></h1>
                            <div class="col-md-6">
                                <p><strong><?php echo JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></strong></p>
                                <p><?php echo JText::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></p>
                                <ul>
                                    <li><?php echo JText::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
                                    <li><?php echo JText::_('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
                                    <li><?php echo JText::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
                                    <li><?php echo JText::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
                                </ul>
                            </div>
                            <div class="col-md-6 text-xs-center">
                                <div class="display-1"><i class="fa fa-medkit" aria-hidden="true"></i></div>
                                <?php if (JModuleHelper::getModule('search')) : ?>
                                    <p><strong><?php echo JText::_('JERROR_LAYOUT_SEARCH'); ?></strong></p>
                                    <p><?php echo JText::_('JERROR_LAYOUT_SEARCH_PAGE'); ?></p>
                                    <?php echo $doc->getBuffer('module', 'search'); ?>
                                <?php endif; ?>
                                <p><?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?></p>
                                <p><a href="<?php echo $this->baseurl; ?>/" class="btn btn-primary"><span class="fa fa-home"></span> <?php echo JText::_('JERROR_LAYOUT_HOME_PAGE'); ?></a></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr />
                                <p><?php echo JText::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?></p>
                                <blockquote>
                                    <span class="badge badge-danger"><?php echo $this->error->getCode(); ?></span> <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?>
                                </blockquote>
                                <?php if ($this->debug) : ?>
                                    <?php echo $this->renderBacktrace(); ?>
                                <?php endif; ?>
                            </div>
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
                    <div class="col-sm-4 text-center">
                        <jdoc:include type="modules" name="footer" style="none" />
                        <p></p>
                    </div>
                    <div class="col-sm-4">
                        <p class="text-right">
                            <a href="#top" id="back-top" class="text-white">
                                <i class="fa fa-arrow-up"></i> Top
                            </a>
                        </p>
                    </div>
                </div>
                <div class="row font-weight-light mt-4" style="font-size: 0.8em;">
                <div class="col-sm-4">
                    <a href="#" class="text-white">Privacy Policy</a> &nbsp;
                    <a href="#" class="text-white">Data Protection</a>
                </div>
                <div class="col-sm-4 text-center ">
                    &copy; 2011 - <?php echo date('Y'); ?> <?php echo $sitename; ?>
                </div>
                <div class="col-sm-4">
                        <p class="text-right">
                            <a href="#top" id="back-top" class="text-white">
                                <i class="fa fa-arrow-up"></i>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </footer>

        <?php echo $doc->getBuffer('modules', 'debug', array('style' => 'none')); ?>

        <!-- Scripts -->
     <script src="<?php echo($this->baseurl . '/templates/' . $this->template . '/js/template.js'); ?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/gh/scoutstrap/scoutstrap@0.1.1/dist/js/bootstrap.min.js" integrity="sha384-vZA7fWbUdVwzQZlO+dkC65mKiaTlKyDvRFeWWT/+J8nBCX0A/OJE2YaFG+m4Zhv0" crossorigin="anonymous"></script>
    </body>
</html>
