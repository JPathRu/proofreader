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

jimport('joomla.application.component.controller');
require_once(JPATH_SITE . '/components/com_proofreader/helpers/form.php');

/**
 * Typo JSON controller for Proofreader.
 *
 * @package     Proofreader
 */
class ProofreaderControllerTypo extends JControllerLegacy
{
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param  string $name   The model name. Optional.
	 * @param  string $prefix The class prefix. Optional.
	 * @param  array  $config Configuration array for model. Optional.
	 *
	 * @return JModelForm The model.
	 */
	public function getModel($name = 'Typo', $prefix = 'ProofreaderModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Method to get Proofreader's form.
	 *
	 * @return void
	 */
	public function form()
	{
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

		$app   = JFactory::getApplication();
		$url   = (string) $app->input->get('page_url', '', 'raw');
		$title = (string) $app->input->get('page_title', '', 'string');

		$response = $this->getFormResponse($url, $title);

		header('Content-Type: application/json');
		echo json_encode($response);
		JFactory::getApplication()->close();
	}

	/**
	 * Method to save a typo.
	 *
	 * @return void
	 */
	public function submit()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$data = JFactory::getApplication()->input->get('proofreader', array(), 'array');

		$model  = $this->getModel();
		$form   = $model->getForm();
		$return = $model->validate($form, $data);

		if ($return === false)
		{
			$response = $this->getErrorResponse($this->getModelErrorMessages($model));
		}
		else
		{
			if (!$model->save($return))
			{
				$response = $this->getErrorResponse($this->getModelErrorMessages($model));
			}
			else
			{
				$response = $this->getFormResponse($return['page_url'], $return['page_title']);
			}
		}

		header('Content-Type: application/json');
		echo json_encode($response);
		JFactory::getApplication()->close();
	}

	/**
	 * Returns an object with error information
	 *
	 * @param array $messages Error messages
	 *
	 * @return object
	 */
	private function getErrorResponse($messages)
	{
		$response           = new StdClass;
		$response->error    = true;
		$response->messages = $messages;

		return $response;
	}

	/**
	 * Returns an object with Proofreader's form code and used scripts
	 *
	 * @param  string $url   The page link
	 * @param  string $title The page title
	 *
	 * @return object
	 */
	private function getFormResponse($url, $title)
	{
		$response        = new StdClass;
		$response->error = false;
		$response->form  = ProofreaderFormHelper::getForm($url, $title);

		$params = JComponentHelper::getParams('com_proofreader', true);
		if ($params->get('captcha') !== '' && $params->get('captcha') !== '0' && $params->get('dynamic_form_load', 0) == 1)
		{
			$scripts           = ProofreaderFormHelper::getFormScripts();
			$response->scripts = $scripts['scripts'];
			$response->script  = $scripts['script'];
		}
		else
		{
			$response->scripts = array();
			$response->script  = '';
		}

		return $response;
	}

	/**
	 * Gets errors from model and returns them as array
	 *
	 * @param JModelForm $model
	 *
	 * @return array
	 */
	private function getModelErrorMessages($model)
	{
		$messages = array();
		$errors   = $model->getErrors();
		for ($i = 0, $n = count($errors); $i < $n; $i++)
		{
			if ($errors[$i] instanceof Exception)
			{
				$messages[] = $errors[$i]->getMessage();
			}
			else
			{
				$messages[] = $errors[$i];
			}
		}

		return $messages;
	}
}
