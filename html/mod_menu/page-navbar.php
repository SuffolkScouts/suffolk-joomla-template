<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// $class_sfx = $class_sfx . ' navbar-nav mr-auto';

$navbarClass = "bg-light";

if(strlen($class_sfx) > 0) 
{
    $navbarClass = $class_sfx; 

    $class_sfx = "";    
}


?>

<nav class="navbar navbar-expand-lg page-navbar <?php echo($navbarClass)?> ">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#pageNavbarContent" aria-controls="pageNavbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="pageNavbarContent">

        <?php

        require JModuleHelper::getLayoutPath('mod_menu', 'default');
        ?>

        </div>
    </div>
</nav>