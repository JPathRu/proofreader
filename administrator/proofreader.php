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

if (!JFactory::getUser()->authorise('core.manage', 'com_proofreader'))
{
	JFactory::getApplication()->redirect('index.php', JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::register('ProofreaderHelper', __DIR__ . '/helpers/proofreader.php');

$language = JFactory::getLanguage();
$language->load('com_proofreader', JPATH_ADMINISTRATOR, 'en-GB', true);
$language->load('com_proofreader', JPATH_ADMINISTRATOR, null, true);

$controller = JControllerLegacy::getInstance('Proofreader');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
