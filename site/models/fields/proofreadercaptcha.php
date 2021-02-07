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
 * CAPTCHA Form Field class for Proofreader based on JFormFieldCaptcha.
 *
 * @package     Proofreader
 * @see         JFormFieldCaptcha
 */
class JFormFieldProofreaderCaptcha extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var string
	 */
	protected $type = 'ProofreaderCaptcha';

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param  SimpleXMLElement $element The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param  mixed            $value   The form field value to validate.
	 * @param  string           $group   The field name group control value. This acts as as an array container for the field.
	 *                                   For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                   full field name would end up being "bar[foo]".
	 *
	 * @return boolean  True on success.
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$result = parent::setup($element, $value, $group);

		$plugin = $this->element['plugin']
			? (string) $this->element['plugin']
			: JFactory::getApplication()->getParams('com_proofreader')->get('captcha', JFactory::getConfig()->get('captcha'));

		if ($plugin === 0 || $plugin === '0' || $plugin === '' || $plugin === null)
		{
			$this->hidden = true;
		}
		else
		{
			// Force field to be required. There's no reason to have a captcha if it is not required.
			// Obs: Don't put required="required" in the xml file, you just need to have validate="captcha"
			$this->required = true;
			$class          = $this->element['class'];
			if (strpos($class, 'required') === false)
			{
				$this->element['class'] = $class . ' required';
			}
		}

		return $result;
	}

	/**
	 * Method to get the field input.
	 *
	 * @return string  The field input.
	 */
	protected function getInput()
	{
		$class     = $this->element['class'] ? (string) $this->element['class'] : '';
		$plugin    = $this->element['plugin']
			? (string) $this->element['plugin']
			: JFactory::getApplication()->getParams('com_proofreader')->get('captcha', JFactory::getConfig()->get('captcha'));
		$namespace = $this->element['namespace'] ? (string) $this->element['namespace'] : $this->form->getName();

		// Use 0 for none
		if ($plugin === 0 || $plugin === '0' || $plugin === '' || $plugin === null)
		{
			return '';
		}
		else
		{
			if (($captcha = JCaptcha::getInstance($plugin, array('namespace' => $namespace))) == null)
			{
				return '';
			}
		}

		return $captcha->display($this->name, $this->id, $class);
	}
}
