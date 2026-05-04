<?php
defined('_JEXEC') or die;

function modChrome_fullSection($module, &$params, &$attribs)
{
    if (!$module->content) {
        return;
    }

    echo '<section class="sf-module-section">';
    echo '<div class="sf-container">';
    if ($module->showtitle) {
        echo '<h2>' . $module->title . '</h2>';
    }
    echo $module->content;
    echo '</div>';
    echo '</section>';
}

function modChrome_fullSection_primary($module, &$params, &$attribs)
{
    if (!$module->content) {
        return;
    }

    echo '<section class="sf-module-section sf-module-section--purple">';
    echo '<div class="sf-container">';
    if ($module->showtitle) {
        echo '<h2>' . $module->title . '</h2>';
    }
    echo $module->content;
    echo '</div>';
    echo '</section>';
}

function modChrome_fullSection_secondary($module, &$params, &$attribs)
{
    if (!$module->content) {
        return;
    }

    echo '<section class="sf-module-section sf-module-section--teal">';
    echo '<div class="sf-container">';
    if ($module->showtitle) {
        echo '<h2>' . $module->title . '</h2>';
    }
    echo $module->content;
    echo '</div>';
    echo '</section>';
}

function modChrome_no($module, &$params, &$attribs)
{
    if ($module->content) {
        echo $module->content;
    }
}
