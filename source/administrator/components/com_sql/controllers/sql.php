<?php
/**
 * Celtic Database - SQL Database manager for Joomla!
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,USA.
 *
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @package    Celtic\SqlManager
 * @author     Niels Braczek <nbraczek@bsds.de>
 * @copyright  Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL
 */

use Celtic\Abstraction\VersionFactoryInterface;
use Celtic\Assertions\AssertionException;

defined('_JEXEC') or die('Restricted access');

/**
 * SQL Controller - handles generic SQL for any table
 *
 * @package  Celtic\SqlManager
 * @since    1.0.0
 */
class SqlControllerSql extends SqlController
{
	/** @var SqlModelSql */
	protected $model;

	/** @var string  */
	protected $command;

	/** @var string  */
	protected $table;

	/** @var string  */
	protected $query;

	/** @var string  */
	protected $key;

	/** @var int  */
	protected $id;

	/** @var int  */
	protected $limit;

	/** @var string  */
	protected $file;

	/** @var array */
	protected $fields;

	/**
	 * Constructor
	 *
	 * @param   VersionFactoryInterface  $factory  The abstraction factory
	 * @param   array                    $config   An optional associative array of configuration settings.
	 *
	 * @startuml
	 * -->> SqlControllerSql: «create»
	 * activate SqlControllerSql
	 * SqlControllerSql -->> SqlModelSql: «create»
	 * activate SqlModelSql
	 * SqlControllerSql <<- SqlModelSql: model
	 * <<-- SqlControllerSql
	 * deactivate SqlModelSql
	 * deactivate SqlControllerSql
	 * @enduml
	 */
	public function __construct(VersionFactoryInterface $factory, $config = array())
	{
		parent::__construct($factory, $config);
		$this->setParameters();

		$this->model = new SqlModelSql($factory);
		$this->model->setTable($this->table);
		$this->model->setQuery($this->query);
		$this->model->setKey($this->key);
	}

	/**
	 * Display
	 *
	 * @startuml
	 * activate SqlControllerSql
	 * ->> SqlControllerSql: display()
	 * SqlControllerSql -->> SqlViewSql: «create»
	 * activate SqlViewSql
	 * SqlControllerSql <<- SqlViewSql: view
	 * SqlControllerSql ->> SqlViewSql: display()
	 * <<- SqlViewSql
	 * deactivate SqlViewSql
	 * deactivate SqlControllerSql
	 * @enduml
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	 */
	public function display()
	{
		$view = new SqlViewSql(
			$this->factory,
			$this->model,
			array(
				'layout' => 'default'
			)
		);
		$view->document = $this->factory->getDocument();
		$view->command  = $this->command;
		$view->query    = $this->query;
		$view->table    = $this->table;
		$view->limit    = $this->limit;
		$view->data     = $this->model->getQueryResults();

		$view->display();
	}

	/**
	 * Export to CSV
	 *
	 * @startuml
	 * activate SqlControllerSql
	 * activate SqlModelSql
	 * ->> SqlControllerSql: csv()
	 * note over SqlControllerSql: assert token is valid
	 * note over SqlControllerSql: send headers
	 * SqlControllerSql ->> SqlModelSql: exportToCsv()
	 * SqlControllerSql <<- SqlModelSql: data
	 * note over SqlControllerSql: echo data
	 * <<-- SqlControllerSql: exit()
	 * deactivate SqlModelSql
	 * deactivate SqlControllerSql
	 * @enduml
	 *
	 * @return  void
	 */
	public function csv()
	{
		$this->assert->tokenIsValid();

		ob_end_clean();

		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Accept-Ranges: bytes');
		header('Content-Disposition: attachment; filename=' . basename($this->file) . ';');
		header('Content-Type: text/csv; charset="utf-8"');
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Pragma: no-cache');

		echo $this->model->exportToCsv($this->query);

		$this->factory->getApplication()->close();
	}

	/**
	 * Delete a query
	 *
	 * @startuml
	 * activate SqlControllerSql
	 * activate SqlModelSql
	 * ->> SqlControllerSql: delete()
	 * note over SqlControllerSql: assert token is valid
	 * SqlControllerSql ->> SqlModelSql: delete()
	 * note over SqlModelSql: Exception\non error
	 * SqlControllerSql <<-- SqlModelSql
	 * SqlControllerSql ->> SqlControllerSql: setRedirect()
	 * <<-- SqlControllerSql
	 * deactivate SqlModelSql
	 * deactivate SqlControllerSql
	 * @enduml
	 *
	 * @return  void
	 *
	 * @throws  AssertionException
	 */
	public function delete()
	{
		$this->assert->tokenIsValid();

		try
		{
			$this->model->delete($this->table, $this->key, $this->id);
			$message = \JText::_('COM_SQL_DELETE_TRUE');
			$msgType = null;
		}
		catch (\RuntimeException $e)
		{
			$message = \JText::_('COM_SQL_DELETE_FALSE');
			$msgType = 'error';
		}

		$this->setRedirect(
			'index.php?option=com_sql&view=sql' . $this->helper->buildQueryVars($this->table, $this->query),
			$message,
			$msgType
		);
	}

	/**
	 * Store a query
	 *
	 * @startuml
	 * activate SqlControllerSql
	 * ->> SqlControllerSql: saveQuery()
	 * SqlControllerSql ->> SqlControllerSql: setRedirect()
	 * <<-- SqlControllerSql
	 * deactivate SqlControllerSql
	 * @enduml
	 *
	 * @return  void
	 *
	 * @throws  AssertionException
	 */
	public function saveQuery()
	{
		$this->assert->tokenIsValid();

		$this->setRedirect(
			'index.php?option=com_sql&view=queries&task=edit&query=' . urlencode($this->query)
		);
	}

	/**
	 * Edit
	 *
	 * @startuml
	 * activate SqlControllerSql
	 * ->> SqlControllerSql: edit()
	 * SqlControllerSql -->> SqlViewSql: «create»
	 * activate SqlViewSql
	 * SqlControllerSql <<- SqlViewSql: view
	 * SqlControllerSql ->> SqlViewSql: edit()
	 * <<- SqlViewSql
	 * deactivate SqlViewSql
	 * deactivate SqlControllerSql
	 * @enduml
	 *
	 * @return  void
	 */
	public function edit()
	{
		$view = new SqlViewSql(
			$this->factory,
			$this->model,
			array(
				'layout' => 'default'
			)
		);

		$view->document = $this->factory->getDocument();
		$view->table    = $this->table;
		$view->key      = $this->key;
		$view->id       = $this->id;
		$view->query    = $this->query;

		$view->display();
	}

	/**
	 * Add a query
	 *
	 * @startuml
	 * activate SqlControllerSql
	 * activate SqlModelSql
	 * ->> SqlControllerSql: add()
	 * note over SqlControllerSql: assert token is valid
	 * SqlControllerSql ->> SqlModelSql: insert()
	 * note over SqlModelSql: Exception\non error
	 * SqlControllerSql <<-- SqlModelSql
	 * SqlControllerSql ->> SqlControllerSql: setRedirect()
	 * <<-- SqlControllerSql
	 * deactivate SqlModelSql
	 * deactivate SqlControllerSql
	 * @enduml
	 *
	 * @return  void
	 *
	 * @throws  AssertionException
	 */
	public function add()
	{
		$this->assert->tokenIsValid();

		try
		{
			$this->model->insert($this->table, $this->fields);
			$msg = "Yes";
		}
		catch (\Exception $e)
		{
			$msg = "No " . $e->getMessage();
		}

		$this->setRedirect(
			'index.php?option=com_sql&view=sql' . $this->helper->buildQueryVars($this->table, $this->query),
			$msg
		);
	}

	/**
	 * Store a query and return to the form
	 *
	 * @startuml
	 * activate SqlControllerSql
	 * activate SqlModelSql
	 * ->> SqlControllerSql: apply()
	 * note over SqlControllerSql: assert token is valid
	 * SqlControllerSql ->> SqlModelSql: update()
	 * note over SqlModelSql: Exception\non error
	 * SqlControllerSql <<-- SqlModelSql
	 * SqlControllerSql ->> SqlControllerSql: setRedirect()
	 * <<-- SqlControllerSql
	 * deactivate SqlModelSql
	 * deactivate SqlControllerSql
	 * @enduml
	 *
	 * @return  void
	 *
	 * @throws  AssertionException
	 */
	public function apply()
	{
		$this->assert->tokenIsValid();

		try
		{
			$this->model->update($this->table, $this->fields, $this->key);
			$msg = \JText::_('COM_SQL_SAVE_TRUE');
		}
		catch (\Exception $e)
		{
			$msg = \JText::_('COM_SQL_SAVE_FALSE');
		}

		$this->setRedirect(
			'index.php?option=com_sql&view=sql&task=edit' . $this->helper->buildQueryVars($this->table, $this->query, $this->key, $this->id),
			$msg
		);
	}

	/**
	 * Store a query
	 *
	 * @startuml
	 * activate SqlControllerSql
	 * activate SqlModelSql
	 * ->> SqlControllerSql: save()
	 * note over SqlControllerSql: assert token is valid
	 * SqlControllerSql ->> SqlModelSql: update()
	 * note over SqlModelSql: Exception\non error
	 * SqlControllerSql <<-- SqlModelSql
	 * SqlControllerSql ->> SqlControllerSql: setRedirect()
	 * <<-- SqlControllerSql
	 * deactivate SqlModelSql
	 * deactivate SqlControllerSql
	 * @enduml
	 *
	 * @return  void
	 *
	 * @throws  AssertionException
	 */
	public function save()
	{
		$this->assert->tokenIsValid();

		try
		{
			$this->model->update($this->table, $this->fields, $this->key);
			$msg = \JText::_('COM_SQL_SAVE_TRUE');
		}
		catch (\Exception $e)
		{
			$msg = \JText::_('COM_SQL_SAVE_FALSE');
		}

		$this->setRedirect(
			'index.php?option=com_sql&view=sql' . $this->helper->buildQueryVars($this->table, $this->query),
			$msg
		);
	}

	/**
	 * Cancel editing
	 *
	 * @startuml
	 * activate SqlControllerSql
	 * ->> SqlControllerSql: cancel()
	 * SqlControllerSql ->> SqlControllerSql: setRedirect()
	 * <<-- SqlControllerSql
	 * deactivate SqlControllerSql
	 * @enduml
	 *
	 * @return  void
	 *
	 * @throws  AssertionException
	 */
	public function cancel()
	{
		$this->assert->tokenIsValid();

		$this->setRedirect(
			'index.php?option=com_sql&view=sql' . $this->helper->buildQueryVars($this->table, $this->query)
		);
	}

	/**
	 * Set the internal properties according to input parameters
	 *
	 * @return  void
	 */
	public function setParameters()
	{
		$this->command = $this->input->getString('command');
		$this->table   = $this->input->getString('table');
		$this->query   = $this->input->getString('query');
		$this->key     = $this->input->getCmd('table');
		$this->id      = $this->input->getInt('id');
		$this->limit   = max(0, $this->input->getInt('limit', 10));
		$this->file    = $this->input->getString('file', 'export_' . $this->table . '.csv');
		$this->fields  = $this->input->get('fields', array(), 'array');
	}
}
