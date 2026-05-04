<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="sf-profile">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php endif; ?>

    <?php if (Factory::getUser()->id === $this->data->id) : ?>
        <div class="sf-profile__actions">
            <a class="sf-button sf-button--outline-dark" href="<?php echo Route::_('index.php?option=com_users&task=profile.edit&user_id=' . (int) $this->data->id); ?>">
                <?php echo Text::_('COM_USERS_EDIT_PROFILE'); ?>
            </a>
        </div>
    <?php endif; ?>

    <?php echo $this->loadTemplate('core'); ?>
    <?php echo $this->loadTemplate('params'); ?>
    <?php echo $this->loadTemplate('custom'); ?>
</div>
