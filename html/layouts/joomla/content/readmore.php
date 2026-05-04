<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$params = $displayData['params'];
$item   = $displayData['item'];

if (!$params->get('access-view')) {
    $label = Text::_('COM_CONTENT_REGISTER_TO_READ_MORE');
} elseif ($readmore = $item->alternative_readmore) {
    $label = $readmore;
    if ($params->get('show_readmore_title', 0)) {
        $label .= ' ' . HTMLHelper::_('string.truncate', $item->title, $params->get('readmore_limit'));
    }
} elseif ($params->get('show_readmore_title', 0)) {
    $label = Text::_('COM_CONTENT_READ_MORE') . ' ' . HTMLHelper::_('string.truncate', $item->title, $params->get('readmore_limit'));
} else {
    $label = Text::_('COM_CONTENT_READ_MORE');
}
?>
<p class="sf-readmore">
    <a class="sf-button sf-button--outline-dark" href="<?php echo $displayData['link']; ?>" itemprop="url">
        <?php echo $label; ?>
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </a>
</p>
