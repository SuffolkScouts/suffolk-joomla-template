<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');

if (isset($this->error)) : ?>
    <div class="sf-alert sf-alert--error" role="alert">
        <?php echo $this->error; ?>
    </div>
<?php endif; ?>

<div class="sf-contact-form">
    <form id="contact-form" action="<?php echo Route::_('index.php'); ?>" method="post" class="sf-form">
        <fieldset>
            <legend><?php echo Text::_('COM_CONTACT_FORM_LABEL'); ?></legend>

            <div class="sf-form__group">
                <?php echo $this->form->getLabel('contact_name'); ?>
                <?php echo $this->form->getInput('contact_name'); ?>
            </div>
            <div class="sf-form__group">
                <?php echo $this->form->getLabel('contact_email'); ?>
                <?php echo $this->form->getInput('contact_email'); ?>
            </div>
            <div class="sf-form__group">
                <?php echo $this->form->getLabel('contact_subject'); ?>
                <?php echo $this->form->getInput('contact_subject'); ?>
            </div>
            <div class="sf-form__group">
                <?php echo $this->form->getLabel('contact_message'); ?>
                <?php echo $this->form->getInput('contact_message'); ?>
            </div>

            <?php if ($this->params->get('show_email_copy')) : ?>
                <div class="sf-form__check">
                    <?php echo $this->form->getInput('contact_email_copy'); ?>
                    <?php echo $this->form->getLabel('contact_email_copy'); ?>
                </div>
            <?php endif; ?>

            <?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
                <?php if ($fieldset->name !== 'contact') : ?>
                    <?php foreach ($this->form->getFieldset($fieldset->name) as $field) : ?>
                        <div class="sf-form__group">
                            <?php if ($field->hidden) : ?>
                                <?php echo $field->input; ?>
                            <?php else : ?>
                                <?php echo $field->label; ?>
                                <?php if (!$field->required && $field->type !== 'Spacer') : ?>
                                    <span class="sf-form__optional"><?php echo Text::_('COM_CONTACT_OPTIONAL'); ?></span>
                                <?php endif; ?>
                                <?php echo $field->input; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="sf-form__actions">
                <button class="sf-button sf-button--yellow validate" type="submit">
                    <?php echo Text::_('COM_CONTACT_CONTACT_SEND'); ?>
                </button>
            </div>

            <input type="hidden" name="option" value="com_contact">
            <input type="hidden" name="task" value="contact.submit">
            <input type="hidden" name="return" value="<?php echo $this->return_page; ?>">
            <input type="hidden" name="id" value="<?php echo $this->item->slug; ?>">
            <?php echo HTMLHelper::_('form.token'); ?>
        </fieldset>
    </form>
</div>
