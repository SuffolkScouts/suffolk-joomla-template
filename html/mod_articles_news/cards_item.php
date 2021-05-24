<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_news
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<?php 

	$itemHeader = "";
	$imageURL = $item->imageSrc;

	if(!empty($imageURL))
	{
		$itemHeader = '<img src="'.$item->imageSrc.'" class="card-img-top" alt="'.$item->imageAlt.'">';
	}
	else
	{
		$itemHeader = '<div class="card-header bg-info align-middle"><svg class="bi bi-scout-fleur bi-light" width="150" height="150" viewBox="0 0 20 20"><path d="M5.38,9.11a6.13,6.13,0,0,1,.91,2.43h1a7,7,0,0,0-1.07-3,3.6,3.6,0,0,0-3-1.86h0A2.93,2.93,0,0,0,.91,7.81,3,3,0,0,0,.3,10.39l1-.21a2.1,2.1,0,0,1,.42-1.76A1.92,1.92,0,0,1,3.2,7.7h0A2.73,2.73,0,0,1,5.38,9.11Z"/><path d="M12.57,16a4.71,4.71,0,0,1-.79-1.78h-1a5.55,5.55,0,0,0,.82,2.1A4.18,4.18,0,0,1,10,17.77h0a4.17,4.17,0,0,1-1.59-1.45,5.55,5.55,0,0,0,.82-2.1h-1A4.71,4.71,0,0,1,7.44,16l-.17.23.12.26A5,5,0,0,0,9.8,18.77l.2.09h0l.2-.09a5,5,0,0,0,2.41-2.27l.12-.26Z"/><path d="M14.62,9.11a6.13,6.13,0,0,0-.91,2.43h-1a7,7,0,0,1,1.07-3,3.6,3.6,0,0,1,3-1.86h0a2.93,2.93,0,0,1,2.28,1.09,3,3,0,0,1,.61,2.59l-1-.21a2.1,2.1,0,0,0-.42-1.76A1.92,1.92,0,0,0,16.8,7.7h0A2.73,2.73,0,0,0,14.62,9.11Z"/><path d="M8.27,11.54h1a9.86,9.86,0,0,0-1-3A6.35,6.35,0,0,1,7.52,6,3.38,3.38,0,0,1,9,3.39c.16-.12.61-.46,1-.83.41.37.86.71,1,.83A3.38,3.38,0,0,1,12.48,6a6.36,6.36,0,0,1-.77,2.54,9.85,9.85,0,0,0-1,3h1a9.27,9.27,0,0,1,.88-2.61A7.06,7.06,0,0,0,13.46,6a4.37,4.37,0,0,0-1.86-3.37,11.42,11.42,0,0,1-1.25-1.07L10,1.15l-.36.39A11.41,11.41,0,0,1,8.39,2.61,4.37,4.37,0,0,0,6.54,6a7.06,7.06,0,0,0,.86,2.95A9.27,9.27,0,0,1,8.27,11.54Z"/><rect x="4.35" y="12.41" width="11.3" height="0.94"/></svg></div>';
	}

?>

<div class="col mb-4">
    <div class="newflash-item card border-0">
		<a href="<?php echo $item->link; ?>"><?php echo($itemHeader); ?></a>
		<!-- <a href="<?php echo $item->link; ?>"><img src="<?php echo $item->imageSrc; ?>" class="card-img-top" alt="<?php echo $item->imageAlt; ?>"></a> -->
      	<div class="card-body bg-light">
	  		<a href="<?php echo $item->link; ?>" class="text-body text-decoration-none"><h5 class="card-title"><?php echo $item->title; ?></h5></a>
        	<p class="card-text"><?php echo $item->introtext; ?></p>
      </div>
    </div>
</div>
