<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Contact\Administrator\Helper\ContactHelper;
use Joomla\Component\Contact\Site\Helper\RouteHelper;

/** @var \Joomla\Component\Contact\Site\View\Category\HtmlView $this */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('com_contact.contacts-list')
    ->useScript('core');

$canDo   = ContactHelper::getActions('com_contact', 'category', $this->category->id);
$canEdit = $canDo->get('core.edit');
$userId  = $this->getCurrentUser()->id;

$showEditColumn = false;
if ($canEdit) {
    $showEditColumn = true;
} elseif ($canDo->get('core.edit.own') && !empty($this->items)) {
    foreach ($this->items as $item) {
        if ((int) $item->created_by === (int) $userId) {
            $showEditColumn = true;
            break;
        }
    }
}

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$buildDetails = function ($item): array {
    $details = [];

    if ($this->params->get('show_position_headings') && !empty($item->con_position)) {
        $details[] = ['label' => Text::_('COM_CONTACT_POSITION'), 'value' => $item->con_position];
    }

    if ($this->params->get('show_telephone_headings') && !empty($item->telephone)) {
        $details[] = ['label' => Text::_('COM_CONTACT_TELEPHONE'), 'value' => Text::sprintf('COM_CONTACT_TELEPHONE_NUMBER', $item->telephone)];
    }

    if ($this->params->get('show_mobile_headings') && !empty($item->mobile)) {
        $details[] = ['label' => Text::_('COM_CONTACT_MOBILE'), 'value' => Text::sprintf('COM_CONTACT_MOBILE_NUMBER', $item->mobile)];
    }

    if ($this->params->get('show_fax_headings') && !empty($item->fax)) {
        $details[] = ['label' => Text::_('COM_CONTACT_FAX'), 'value' => Text::sprintf('COM_CONTACT_FAX_NUMBER', $item->fax)];
    }

    if ($this->params->get('show_email_headings') && !empty($item->email_to)) {
        $details[] = ['label' => Text::_('JGLOBAL_EMAIL'), 'value' => HTMLHelper::_('email.cloak', $item->email_to)];
    }

    $location = [];
    if ($this->params->get('show_suburb_headings') && !empty($item->suburb)) {
        $location[] = $item->suburb;
    }
    if ($this->params->get('show_state_headings') && !empty($item->state)) {
        $location[] = $item->state;
    }
    if ($this->params->get('show_country_headings') && !empty($item->country)) {
        $location[] = $item->country;
    }

    if ($location) {
        $details[] = ['label' => Text::_('COM_CONTACT_ADDRESS'), 'value' => implode(', ', $location)];
    }

    return $details;
};
?>
<div class="com-contact-category__items sf-contact-list">
    <form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
        <?php if ($this->params->get('filter_field')) : ?>
            <div class="sf-contact-list__toolbar">
                <label class="filter-search-lbl visually-hidden" for="filter-search">
                    <?php echo Text::_('COM_CONTACT_FILTER_SEARCH_DESC'); ?>
                </label>
                <input
                    type="text"
                    name="filter-search"
                    id="filter-search"
                    value="<?php echo $this->escape($this->state->get('list.filter')); ?>"
                    class="inputbox"
                    onchange="document.adminForm.submit();"
                    placeholder="<?php echo Text::_('COM_CONTACT_FILTER_SEARCH_DESC'); ?>"
                >
                <button type="submit" name="filter_submit" class="sf-button sf-button--yellow"><?php echo Text::_('JGLOBAL_FILTER_BUTTON'); ?></button>
                <button type="reset" name="filter-clear-button" class="sf-button sf-button--outline-dark"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>
        <?php endif; ?>

        <?php if ($this->params->get('show_pagination_limit')) : ?>
            <div class="sf-contact-list__limit">
                <label for="limit" class="visually-hidden">
                    <?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?>
                </label>
                <?php echo $this->pagination->getLimitBox(); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($this->items)) : ?>
            <?php if ($this->params->get('show_no_contacts', 1)) : ?>
                <div class="sf-alert sf-alert--info">
                    <?php echo Text::_('COM_CONTACT_NO_CONTACTS'); ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="sf-contact-list__sort">
                <?php echo HTMLHelper::_('grid.sort', 'COM_CONTACT_FIELD_NAME_LABEL', 'a.name', $listDirn, $listOrder, null, 'asc', '', 'adminForm'); ?>
            </div>

            <div class="sf-contact-list__grid" id="contactList">
                <?php foreach ($this->items as $i => $item) : ?>
                    <?php
                    $contactUrl = Route::_(RouteHelper::getContactRoute($item->slug, $item->catid, $item->language));
                    $details    = $buildDetails($item);
                    $canEditItem = $canEdit || ($canDo->get('core.edit.own') && (int) $item->created_by === (int) $userId);
                    ?>
                    <article class="sf-contact-card<?php echo $item->published == 0 ? ' is-unpublished' : ''; ?>">
                        <div class="sf-contact-card__main">
                            <?php if ($this->params->get('show_image_heading') && !empty($item->image)) : ?>
                                <a class="sf-contact-card__image" href="<?php echo $contactUrl; ?>" aria-hidden="true" tabindex="-1">
                                    <?php echo LayoutHelper::render(
                                        'joomla.html.image',
                                        [
                                            'src'   => $item->image,
                                            'alt'   => '',
                                            'class' => 'contact-thumbnail',
                                        ]
                                    ); ?>
                                </a>
                            <?php endif; ?>

                            <div class="sf-contact-card__content">
                                <h2 class="sf-contact-card__title">
                                    <a href="<?php echo $contactUrl; ?>"><?php echo $this->escape($item->name); ?></a>
                                </h2>

                                <?php if ($item->published == 0) : ?>
                                    <span class="sf-contact-card__badge"><?php echo Text::_('JUNPUBLISHED'); ?></span>
                                <?php endif; ?>
                                <?php if ($item->publish_up && strtotime($item->publish_up) > strtotime(Factory::getDate())) : ?>
                                    <span class="sf-contact-card__badge"><?php echo Text::_('JNOTPUBLISHEDYET'); ?></span>
                                <?php endif; ?>
                                <?php if (!is_null($item->publish_down) && strtotime($item->publish_down) < strtotime(Factory::getDate())) : ?>
                                    <span class="sf-contact-card__badge"><?php echo Text::_('JEXPIRED'); ?></span>
                                <?php endif; ?>
                                <?php if ($item->published == -2) : ?>
                                    <span class="sf-contact-card__badge"><?php echo Text::_('JTRASHED'); ?></span>
                                <?php endif; ?>

                                <?php echo $item->event->afterDisplayTitle; ?>
                                <?php echo $item->event->beforeDisplayContent; ?>

                                <?php if ($details) : ?>
                                    <dl class="sf-contact-card__details">
                                        <?php foreach ($details as $detail) : ?>
                                            <div>
                                                <dt><?php echo $this->escape($detail['label']); ?></dt>
                                                <dd><?php echo $detail['value']; ?></dd>
                                            </div>
                                        <?php endforeach; ?>
                                    </dl>
                                <?php endif; ?>

                                <?php echo $item->event->afterDisplayContent; ?>
                            </div>
                        </div>

                        <div class="sf-contact-card__actions">
                            <a class="sf-contact-card__link" href="<?php echo $contactUrl; ?>">
                                <?php echo Text::_('COM_CONTACT_DETAILS'); ?>
                            </a>
                            <?php if ($showEditColumn && $canEditItem) : ?>
                                <?php echo HTMLHelper::_('contacticon.edit', $item, $this->params); ?>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($canDo->get('core.create')) : ?>
            <?php echo HTMLHelper::_('contacticon.create', $this->category, $this->category->params); ?>
        <?php endif; ?>

        <?php if ($this->params->get('show_pagination', 2)) : ?>
            <div class="sf-contact-list__pagination">
                <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                    <p class="sf-contact-list__counter">
                        <?php echo $this->pagination->getPagesCounter(); ?>
                    </p>
                <?php endif; ?>

                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        <?php endif; ?>

        <div>
            <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->state->get('list.ordering')); ?>">
            <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->state->get('list.direction')); ?>">
        </div>
    </form>
</div>
