<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
?>
<fieldset class="sf-profile__core">
    <legend><?php echo Text::_('COM_USERS_PROFILE_CORE_LEGEND'); ?></legend>
    <dl class="sf-dl">
        <div class="sf-dl__row">
            <dt><?php echo Text::_('COM_USERS_PROFILE_NAME_LABEL'); ?></dt>
            <dd><?php echo $this->escape($this->data->name); ?></dd>
        </div>
        <div class="sf-dl__row">
            <dt><?php echo Text::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?></dt>
            <dd><?php echo $this->escape($this->data->username); ?></dd>
        </div>
        <div class="sf-dl__row">
            <dt><?php echo Text::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?></dt>
            <dd><?php echo HTMLHelper::_('date', $this->data->registerDate); ?></dd>
        </div>
        <div class="sf-dl__row">
            <dt><?php echo Text::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); ?></dt>
            <dd>
                <?php if ($this->data->lastvisitDate !== '0000-00-00 00:00:00') : ?>
                    <?php echo HTMLHelper::_('date', $this->data->lastvisitDate); ?>
                <?php else : ?>
                    <?php echo Text::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
                <?php endif; ?>
            </dd>
        </div>
    </dl>
</fieldset>
