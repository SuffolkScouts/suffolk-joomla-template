<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$msgList = $displayData['msgList'];

$alertClass = [
    'error'   => 'sf-alert--error',
    'warning' => 'sf-alert--warning',
    'notice'  => 'sf-alert--info',
    'message' => 'sf-alert--success',
];
?>
<div id="system-message-container" aria-live="polite">
    <?php if (is_array($msgList) && !empty($msgList)) : ?>
        <div id="system-message">
            <?php foreach ($msgList as $type => $msgs) : ?>
                <?php if (!empty($msgs)) : ?>
                    <div class="sf-alert <?php echo $alertClass[$type] ?? 'sf-alert--info'; ?>" role="alert">
                        <button class="sf-alert__close" type="button" aria-label="<?php echo Text::_('JCLOSE'); ?>" onclick="this.parentElement.remove()">&#x2715;</button>
                        <strong class="sf-alert__heading"><?php echo Text::_($type); ?></strong>
                        <?php foreach ($msgs as $msg) : ?>
                            <div class="sf-alert__body"><?php echo $msg; ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
