<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
?>

<div class="sf-more-news">
    <h2>More news</h2>
    <ul>
        <?php foreach ($this->link_items as &$item) : ?>
            <li>
                <a href="<?php echo Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language)); ?>">
                    <?php echo $this->escape($item->title); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
