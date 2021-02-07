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

if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.application.component.controlleradmin');
}

class ProofreaderControllerTypos extends JControllerAdmin
{
	protected $text_prefix = 'COM_PROOFREADER_TYPOS';

	/**
	 * Method to display a view.
	 *
	 * @param  boolean $cachable  If true, the view output will be cached
	 * @param  array   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return JController  This object to support chaining.
	 */
	function display($cachable = false, $urlparams = array())
	{
		$this->input->set('view', 'typos');

		parent::display($cachable, $urlparams);
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param  string $name   The model name. Optional.
	 * @param  string $prefix The class prefix. Optional.
	 * @param  array  $config Configuration array for model. Optional.
	 *
	 * @return object  The model.
	 */
	public function getModel($name = 'Typo', $prefix = 'ProofreaderModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}