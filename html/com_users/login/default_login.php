<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
?>
<div class="sf-login">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php endif; ?>

    <?php if ($this->params->get('logindescription_show') == 1 && trim($this->params->get('login_description')) !== '') : ?>
        <div class="sf-login__description"><?php echo $this->params->get('login_description'); ?></div>
    <?php endif; ?>

    <?php if ($this->params->get('login_image') !== '') : ?>
        <img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="sf-login__image" alt="<?php echo Text::_('COM_USERS_LOGIN_IMAGE_ALT'); ?>">
    <?php endif; ?>

    <form action="<?php echo Route::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="sf-form">
        <?php foreach ($this->form->getFieldset('credentials') as $field) : ?>
            <?php if (!$field->hidden) : ?>
                <div class="sf-form__group">
                    <?php echo $field->label; ?>
                    <?php echo $field->input; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($this->tfa) : ?>
            <div class="sf-form__group">
                <?php echo $this->form->getField('secretkey')->label; ?>
                <?php echo $this->form->getField('secretkey')->input; ?>
            </div>
        <?php endif; ?>

        <?php if (PluginHelper::isEnabled('system', 'remember')) : ?>
            <div class="sf-form__check">
                <input id="remember" type="checkbox" name="remember" value="yes">
                <label for="remember"><?php echo Text::_('COM_USERS_LOGIN_REMEMBER_ME'); ?></label>
            </div>
        <?php endif; ?>

        <div class="sf-form__actions">
            <button type="submit" class="sf-button sf-button--yellow sf-button--full">
                <?php echo Text::_('JLOGIN'); ?>
            </button>
        </div>

        <input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>

    <nav class="sf-login__links" aria-label="<?php echo Text::_('COM_USERS_LOGIN_ACCOUNT_LINKS'); ?>">
        <a href="https://account.suffolkscouts.org.uk/forgotUsername"><?php echo Text::_('COM_USERS_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
        <a href="https://account.suffolkscouts.org.uk/forgotPassword"><?php echo Text::_('COM_USERS_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
        <a href="https://account.suffolkscouts.org.uk/request"><?php echo Text::_('COM_USERS_REGISTER_LINK'); ?></a>
    </nav>
</div>
