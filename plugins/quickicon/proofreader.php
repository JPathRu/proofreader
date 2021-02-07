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

/**
 * Proofreader quickicon plugin
 *
 * @package     Proofreader
 * @subpackage  Plugins
 */
class plgQuickiconProofreader extends JPlugin
{
	/**
	 * Returns an icon definition for an icon which provides quick access to the Proofreader extension.
	 *
	 * @param  string $context The calling context
	 *
	 * @return array A list of icon definition associative arrays, consisting of the keys link, image, text and access.
	 */
	public function onGetIcons($context)
	{
		if ($context != $this->params->get('context', 'mod_quickicon')
			|| !JFactory::getUser()->authorise('core.manage', 'com_proofreader')
		)
		{
			return array();
		}

		$language = JFactory::getLanguage();
		$language->load('com_proofreader.sys', JPATH_ADMINISTRATOR, 'en-GB', true);
		$language->load('com_proofreader.sys', JPATH_ADMINISTRATOR, null, true);

		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$image = 'pencil';
		}
		else
		{
			$image = JURI::base() . 'components/com_proofreader/assets/images/icon-48-proofreader.png';
		}

		return array(
			array(
				'link'   => 'index.php?option=com_proofreader',
				'image'  => $image,
				'text'   => JText::_('COM_PROOFREADER'),
				'access' => array('core.manage', 'com_proofreader'),
				'id'     => 'plg_quickicon_proofreader'
			)
		);
	}
}
