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
	jimport('joomla.application.component.modellist');
}

class ProofreaderModelTypos extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param  array $config An optional associative array of configuration settings.
	 *
	 * @see JModelLegacy
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'p.id',
				'typo_text', 'p.typo_text',
				'page_language', 'p.page_language',
				'page_title', 'p.page_title',
				'author', 'p.created_by_name',
				'typo_comment', 'p.typo_comment',
				'created', 'p.created',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param  string $type   The table type to instantiate. [optional]
	 * @param  string $prefix A prefix for the table class name. [optional]
	 * @param  array  $config Configuration array for model. [optional]
	 *
	 * @return JTable  A JTable object
	 */
	public function getTable($type = 'Typo', $prefix = 'ProofreaderTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 */
	protected function getListQuery()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select("p.*");
		$query->from($db->quoteName('#__proofreader_typos') . ' AS p');

		// Join over the users
		$query->select('u.name AS author');
		$query->join('LEFT', $db->quoteName('#__users') . ' AS u ON u.id = p.created_by');

		// Filter by author
		$author_id = $this->getState('filter.author_id');
		if ($author_id != '')
		{
			$query->where('p.created_by = ' . (int) $author_id);
		}

		// Filter by language
		$language = $this->getState('filter.language');
		if ($language != '' && $language != '*')
		{
			$query->where('p.page_language = ' . $db->Quote($db->escape($language)));
		}

		// Filter by search in typo, replacement or comment
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('(p.typo_text LIKE ' . $search . ' OR p.typo_comment LIKE ' . $search . ')');
		}

		$ordering  = $this->state->get('list.ordering', 'p.created');
		$direction = $this->state->get('list.direction', 'asc');
		$query->order($db->escape($ordering . ' ' . $direction));

		return $query;
	}

	/**
	 * Build a list of authors
	 *
	 * @return JDatabaseQuery
	 */
	public function getAuthors()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('u.id AS value, u.name AS text')
			->from('#__users AS u')
			->join('INNER', '#__proofreader_typos AS t ON t.created_by = u.id')
			->group('u.id, u.name')
			->order('u.name');

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @param  string $ordering  An optional ordering field.
	 * @param  string $direction An optional direction (asc|desc).
	 *
	 * @return void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$author_id = $app->getUserStateFromRequest($this->context . '.filter.author_id', 'filter_author_id', '');
		$this->setState('filter.author_id', $author_id);

		$language = $app->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		parent::populateState('p.created', 'asc');
	}
}