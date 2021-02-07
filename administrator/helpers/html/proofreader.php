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
 * Proofreader HTML class.
 *
 * @package     Proofreader
 */
abstract class JHtmlProofreader
{
	/**
	 * Array containing information for loaded methods
	 *
	 * @var array
	 */
	protected static $loaded = array();

	/**
	 * Adds stylesheets for Proofreader's backend
	 */
	public static function stylesheet()
	{
		if (empty(self::$loaded[__METHOD__]))
		{
			$assetsPath = JURI::root(true) . '/administrator/components/com_proofreader/assets/css';

			$document = JFactory::getDocument();
			if (JFactory::getLanguage()->isRTL())
			{
				$document->addStylesheet($assetsPath . '/style_rtl.css', 'text/css', null);
			} else {
				$document->addStylesheet($assetsPath . '/style.css', 'text/css', null);
			}

			if (version_compare(JVERSION, '3.0', 'lt'))
			{
				$document->addStylesheet($assetsPath . '/legacy.css', 'text/css', null);
			}


			self::$loaded[__METHOD__] = true;
		}

		return;
	}
}