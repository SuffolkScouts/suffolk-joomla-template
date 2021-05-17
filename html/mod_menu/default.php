<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$id = '';

if ($tagId = $params->get('tag_id', ''))
{
	$id = ' id="' . $tagId . '"';
}

?>
<ul class="navbar-nav <?php echo $class_sfx; ?>"<?php echo $id; ?>>

<?php 

$depth = 0;

foreach ($list as $i => &$item)
{
	
	$class = 'item-' . $item->id;

	if ($item->id == $default_id)
	{
		$class .= ' default';
	}

	if ($item->id == $active_id || ($item->type === 'alias' && $item->params->get('aliasoptions') == $active_id))
	{
		$class .= ' current';
	}

	if (in_array($item->id, $path))
	{
		$class .= ' active';
	}
	elseif ($item->type === 'alias')
	{
		$aliasToId = $item->params->get('aliasoptions');

		if (count($path) > 0 && $aliasToId == $path[count($path) - 1])
		{
			$class .= ' active';
		}
		elseif (in_array($aliasToId, $path))
		{
			$class .= ' alias-parent-active';
		}
	}

	if ($item->type === 'separator')
	{
		$class .= ' divider';
	}

	if ($item->deeper)
	{
		$class .= ' deeper';
		$class .= " dropdown";
		$item->anchor_css .= " dropdown-toggle";
		$item->data = 'data-toggle="dropdown"';
	}

	if($depth == 0)
	{
		$class .= ' nav-item';
		$item->anchor_css .= " nav-link";
	}
	else
	{
		if ($item->deeper)
		{
			$class .= " dropdown-submenu";
		}
	
		$item->anchor_css .= " dropdown-item";
	}


	if ($item->parent)
	{
		$class .= ' parent';
	}

	echo '<li class="' . $class . '">';

	switch ($item->type) :
		case 'separator':
		case 'component':
		case 'heading':
		case 'url':
			require JModuleHelper::getLayoutPath('mod_menu', 'default_' . $item->type);
			break;

		default:
			require JModuleHelper::getLayoutPath('mod_menu', 'default_url');
			break;
	endswitch;

	// The next item is deeper.
	if ($item->deeper)
	{
		//Increase menu depth
		$depth++;
		
		$itemID = "item-".$item->id;
		echo '<ul class="dropdown-menu" aria-labelledby="'.$itemID.'">';
	}
	// The next item is shallower.
	elseif ($item->shallower)
	{
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);

		//Increase menu depth
		$depth--;
	}
	// The next item is on the same level.
	else
	{
		echo '</li>';
	}
}
?></ul>
