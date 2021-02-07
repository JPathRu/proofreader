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
 * Helper to render a JLayout object (supports Joomla 2.5)
 *
 * @package     Proofreader
 */
class ProofreaderLayoutHelper
{
	/**
	 * Method to render the layout.
	 *
	 * @param   string $layout      Name of the layout to render
	 * @param   array  $displayData Object which properties are used inside the layout file to build displayed output
	 * @param   string $basePath    Base path to use when loading layout files
	 *
	 * @return  string
	 */
	public static function render($layout, $displayData, $basePath)
	{
		$result = '';
		if (is_dir($basePath))
		{
			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				$result = JLayoutHelper::render($layout, $displayData, $basePath);
			}
			else
			{
				ob_start();
				require $basePath . '/' . $layout . '.php';
				$result = ob_get_contents();
				ob_get_clean();
			}
		}

		return $result;
	}
}