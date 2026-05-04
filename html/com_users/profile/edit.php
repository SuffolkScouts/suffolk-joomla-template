<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');

$lang = Factory::getLanguage();
$lang->load('plg_user_profile', JPATH_ADMINISTRATOR);
?>
<div class="sf-profile-edit">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php endif; ?>

    <form id="member-profile"
          action="<?php echo Route::_('index.php?option=com_users&task=profile.save'); ?>"
          method="post"
          class="sf-form"
          enctype="multipart/form-data">

        <?php foreach ($this->form->getFieldsets() as $group => $fieldset) : ?>
            <?php $fields = $this->form->getFieldset($group); ?>
            <?php if (count($fields)) : ?>
                <fieldset>
                    <?php if (isset($fieldset->label)) : ?>
                        <legend><?php echo Text::_($fieldset->label); ?></legend>
                    <?php endif; ?>

                    <?php foreach ($fields as $field) : ?>
                        <?php if ($field->hidden) : ?>
                            <?php echo $field->input; ?>
                        <?php else : ?>
                            <div class="sf-form__group">
                                <?php echo $field->label; ?>
                                <?php if (!$field->required && $field->type !== 'Spacer') : ?>
                                    <span class="sf-form__optional"><?php echo Text::_('COM_USERS_OPTIONAL'); ?></span>
                                <?php endif; ?>
                                <?php if ($field->fieldname === 'password1') : ?>
                                    <input type="password" style="display:none" aria-hidden="true">
                                <?php endif; ?>
                                <?php echo $field->input; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </fieldset>
            <?php endif; ?>
        <?php endforeach; ?>

        <div class="sf-form__actions">
            <button type="submit" class="sf-button sf-button--yellow validate">
                <?php echo Text::_('JSUBMIT'); ?>
            </button>
            <a class="sf-button sf-button--outline-dark"
               href="<?php echo Route::_('index.php?option=com_users&view=profile'); ?>">
                <?php echo Text::_('JCANCEL'); ?>
            </a>
            <input type="hidden" name="option" value="com_users">
            <input type="hidden" name="task" value="profile.save">
        </div>

        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>
