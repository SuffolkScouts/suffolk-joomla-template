<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\String\PunycodeHelper;

$icon = $this->params->get('contact_icons') == 0;

/**
 * Marker_class: Class based on the selection of text, none, or icons
 * jicon-text, jicon-none, jicon-icon
 */
?>
<dl class="contact-address dl-horizontal" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
	<?php if (
		($this->params->get('address_check') > 0) &&
		($this->item->address || $this->item->suburb || $this->item->state || $this->item->country || $this->item->postcode)
	): ?>
		<div class="row">
			<?php if ($this->params->get('address_check') > 0): ?>
				<dt class="col-sm-3 text-sm-right">
					<span class="<?php echo $this->params->get('marker_class'); ?> fa fa-home">
						<?php //echo $this->params->get('marker_address'); ?>
					</span>
				</dt>
			<?php endif; ?>

			<?php if ($this->item->address && $this->params->get('show_street_address')): ?>
				<dd class="col-sm-9">
					<span class="contact-street" itemprop="streetAddress">
						<?php echo nl2br($this->item->address, false); ?>
					</span>
				</dd>
			<?php endif; ?>

			<?php if ($this->item->suburb && $this->params->get('show_suburb')): ?>
				<dd class="col-sm-9 ml-auto">
					<span class="contact-suburb" itemprop="addressLocality">
						<?php echo $this->item->suburb; ?>
					</span>
				</dd>
			<?php endif; ?>
			<?php if ($this->item->state && $this->params->get('show_state')): ?>
				<dd class="col-sm-9 ml-auto">
					<span class="contact-state" itemprop="addressRegion">
						<?php echo $this->item->state; ?>
					</span>
				</dd>
			<?php endif; ?>
			<?php if ($this->item->postcode && $this->params->get('show_postcode')): ?>
				<dd class="col-sm-9 ml-auto">
					<span class="contact-postcode" itemprop="postalCode">
						<?php echo $this->item->postcode; ?>
					</span>
				</dd>
			<?php endif; ?>
			<?php if ($this->item->country && $this->params->get('show_country')): ?>
				<dd class="col-sm-9 ml-auto">
					<span class="contact-country" itemprop="addressCountry">
						<?php echo $this->item->country; ?>
					</span>
				</dd>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ($this->item->email_to && $this->params->get('show_email')): ?>
        <div class="row">
			<dt class="col-sm-3 text-sm-right">
				<?php if ($icon && !$this->params->get('marker_email')): ?>
					<span class="icon-envelope" aria-hidden="true"></span><span class="visually-hidden">
						<?php echo Text::_('COM_CONTACT_EMAIL_LABEL'); ?>
					</span>
				<?php else: ?>
					<span class="<?php echo $this->params->get('marker_class'); ?>">
						<?php echo $this->params->get('marker_email'); ?>
					</span>
				<?php endif; ?>
			</dt>
			<dd class="col-sm-9">
				<span class="contact-emailto">
					<?php echo $this->item->email_to; ?>
				</span>
			</dd>
		</div>
    <?php endif; ?>

	<?php if ($this->item->telephone && $this->params->get('show_telephone')): ?>
		<div class="row">
			<dt class="col-sm-3 text-sm-right">
				<?php if ($icon && !$this->params->get('marker_telephone')): ?>
					<span class="icon-phone" aria-hidden="true"></span><span class="visually-hidden">
						<?php echo Text::_('COM_CONTACT_TELEPHONE'); ?>
					</span>
				<?php else: ?>
					<span class="<?php echo $this->params->get('marker_class'); ?>">
						<?php echo $this->params->get('marker_telephone'); ?>
					</span>
				<?php endif; ?>
			</dt>
			<dd class="col-sm-9">
				<span class="contact-telephone" itemprop="telephone">
					<?php echo $this->item->telephone; ?>
				</span>
			</dd>
		</div>
    <?php endif; ?>

    <?php if ($this->item->mobile && $this->params->get('show_mobile')): ?>
        <dt>
            <?php if ($icon && !$this->params->get('marker_mobile')): ?>
                <span class="icon-mobile" aria-hidden="true"></span><span class="visually-hidden">
                    <?php echo Text::_('COM_CONTACT_MOBILE'); ?>
                </span>
            <?php else: ?>
                <span class="<?php echo $this->params->get('marker_class'); ?>">
                    <?php echo $this->params->get('marker_mobile'); ?>
                </span>
            <?php endif; ?>
        </dt>
        <dd>
            <span class="contact-mobile" itemprop="telephone">
                <?php echo $this->item->mobile; ?>
            </span>
        </dd>
    <?php endif; ?>

	<?php if ($this->item->webpage && $this->params->get('show_webpage')): ?>
		<div class="row">
			<dt class="col-sm-3 text-sm-right">
				<?php if ($icon && !$this->params->get('marker_webpage')): ?>
					<span class="icon-home" aria-hidden="true"></span><span class="visually-hidden">
						<?php echo Text::_('COM_CONTACT_WEBPAGE'); ?>
					</span>
				<?php else: ?>
					<span class="<?php echo $this->params->get('marker_class'); ?>">
						<?php echo $this->params->get('marker_webpage'); ?>
					</span>
				<?php endif; ?>
			</dt>
			<dd class="col-sm-9">
				<span class="contact-webpage">
					<a href="<?php echo $this->item->webpage; ?>" target="_blank" rel="noopener noreferrer" itemprop="url">
						<?php echo PunycodeHelper::urlToUTF8($this->item->webpage); ?></a>
				</span>
			</dd>
		</div>
    <?php endif; ?>
</dl>