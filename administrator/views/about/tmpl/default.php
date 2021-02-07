<?php
/**
 * Proofreader
 *
 * @package     Proofreader
 * @author      Sergey M. Litvinov (smart@joomlatune.com)
 * @copyright   Copyright (C) 2013-2015 by Sergey M. Litvinov. All rights reserved.
 * @copyright   Copyright (C) 2005-2007 by Alexandr Balashov. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$jedUrl = 'http://proofreaders.joomla.org/proofreaders/proofreader/authoring-a-content/proofreader';
if (is_dir(JPATH_SITE . '/language/ru-RU') || is_dir(JPATH_ADMINISTRATOR . '/language/ru-RU'))
{
	$homepageUrl = 'http://www.joomlatune.ru';
	$supportUrl  = 'http://joomlaforum.ru/index.php/board,150.0.html';
}
else
{
	$homepageUrl = "http://www.joomlatune.com";
	$supportUrl  = "http://www.joomlatune.com/forum/";
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_proofreader&view=about'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif; ?>
		<div class="row-fluid">
			<div id="proofreader-about">
				<div>
					<span class="proofreader-name">Proofreader</span>
					<span class="proofreader-version"><?php echo $this->version['version']; ?></span>
					<span class="proofreader-date">[<?php echo $this->version['date']; ?>]</span>
				</div>
				<div class="proofreader-copyright">
					&copy; 2005-<?php echo date('Y'); ?> <a href="<?php echo $homepageUrl; ?>">JoomlaTune Team</a>. <?php echo JText::_('COM_PROOFREADER_ABOUT_COPYRIGHT'); ?>
				</div>
				<div class="proofreader-description">
					<?php echo JText::_('COM_PROOFREADER_XML_DESCRIPTION'); ?>
				</div>
				<div class="proofreader-jed">
					<?php echo JText::sprintf('COM_PROOFREADER_ABOUT_JED_PROMO', $jedUrl); ?>
				</div>
				<div class="proofreader-license">
					<?php echo JText::_('COM_PROOFREADER_ABOUT_LICENSE'); ?>
				</div>
				<div class="proofreader-donate">
					<?php echo $this->donate; ?>
				</div>
			</div>
		</div>
	</div>
</form>