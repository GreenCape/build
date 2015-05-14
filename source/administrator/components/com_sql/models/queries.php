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

defined('_JEXEC') or die('Restricted access');

/**
 * Class SqlModelQueries
 *
 * @package  Celtic\SqlManager
 * @since    1.0.0
 */
class SqlModelQueries extends SqlModel
{
	/** @var array  */
	protected $data = null;

	/** @var int  */
	protected $total = null;

	/** @var \JPagination */
	protected $pagination = null;

	/**
	 * Constructor
	 *
	 * @param   VersionFactoryInterface  $factory  The abstraction factory
	 * @param   array                    $config   An array of configuration options (name, state, dbo, table_path, ignore_request).
	 *
	 * @startuml
	 * -->> SqlModelQueries: «create»
	 * activate SqlModelQueries
	 * SqlModelQueries ->> SqlModelQueries: buildQuery()
	 * <<-- SqlModelQueries
	 * deactivate SqlModelQueries
	 * @enduml
	 */
	public function __construct(VersionFactoryInterface $factory, $config = array())
	{
		parent::__construct($factory, $config);
	}

	/**
	 * Auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * @startuml
	 * activate SqlModelQueries
	 * activate Application
	 * ->> SqlModelQueries: populateState()
	 * SqlModelQueries ->> Application: getUserStateFromRequest()
	 * note over Application: Multiple\ncalls
	 * SqlModelQueries <<- Application: state
	 * <<-- SqlModelQueries
	 * deactivate Application
	 * deactivate SqlModelQueries
	 * @enduml
	 *
	 * @return  void
	 */
	protected function populateState()
	{
		static $hasRun = false;

		if (!$hasRun) {
			$hasRun = true;

			$context    = 'com_sql.queries.';
			$limit      = $this->application->getUserStateFromRequest($context . 'limit', 'limit', $this->application->getCfg('list_limit'), 'int');
			$limitstart = $this->application->getUserStateFromRequest($context . 'limitstart', 'limitstart', 0, 'int');

			$this->setState('limit',      $limit);
			$this->setState('limitstart', $limit > 0 ? (floor($limitstart / $limit) * $limit) : 0);
			$this->setState('search',     $this->application->getUserStateFromRequest($context . 'search', 'search', '', 'string'));
			$this->setState('order',      $this->application->getUserStateFromRequest($context . 'filter_order', 'filter_order', 'title', 'string'));
			$this->setState('orderDir',   $this->application->getUserStateFromRequest($context . 'filter_order_Dir', 'filter_order_Dir', '', 'word'));
		}
	}

	/**
	 * Get the data
	 *
	 * @startuml
	 * activate SqlModelQueries
	 * ->> SqlModelQueries: getData()
	 * group once
	 *   SqlModelQueries ->> SqlModelQueries: getList()
	 * end
	 * <<- SqlModelQueries: data
	 * deactivate SqlModelQueries
	 * @enduml
	 *
	 * @return  array  An array of results indexed by id
	 */
	public function getData()
	{
		if (empty($this->data))
		{
			$query = $this->buildQuery();
			$this->data = $this->getList(
				$query,
				$this->getState('limitstart'),
				$this->getState('limit'),
				'id'
			);
			$this->total = $this->getListCount($query);
		}

		return $this->data;
	}

	/**
	 * Get total number of records
	 *
	 * @startuml
	 * activate SqlModelQueries
	 * ->> SqlModelQueries: getTotal()
	 * SqlModelQueries ->> SqlModelQueries: getListCount()
	 * <<-- SqlModelQueries: total
	 * deactivate SqlModelQueries
	 * @enduml
	 *
	 * @return  int
	 */
	public function getTotal()
	{
		if (empty($this->total))
		{
			$this->getData();
		}

		return $this->total;
	}

	/**
	 * Get the pagination object
	 *
	 * @startuml
	 * activate SqlModelQueries
	 * ->> SqlModelQueries: getPagination()
	 * SqlModelQueries -->> Pagination: «create»
	 * activate Pagination
	 * SqlModelQueries <<-- Pagination
	 * <<- SqlModelQueries: pagination
	 * deactivate Pagination
	 * deactivate SqlModelQueries
	 * @enduml
	 *
	 * @return  \JPagination
	 */
	public function getPagination()
	{
		if (empty($this->pagination))
		{
			jimport('joomla.html.pagination');
			$this->pagination = new \JPagination(
				$this->getTotal(),
				$this->getState('limitstart'),
				$this->getState('limit')
			);
		}

		return $this->pagination;
	}

	/**
	 * Build the query
	 *
	 * @return  string
	 */
	protected function buildQuery()
	{
		$query = $this->db->getQuery(true);
		$query->select('*')->from('#__sql_queries');

		$search = strtolower($this->getState('search'));
		if (!empty($search))
		{
			$query->where('LOWER(title) LIKE ' . $this->db->quote('%' . $this->db->escape($search, true) . '%', false));
		}

		$order = $this->getState('order');
		if (!empty($order))
		{
			$query->order($order . ' ' . $this->getState('orderDir'));
		}

		return $query;
	}

	/**
	 * Get the query data
	 *
	 * @param   string  $query  An SQL query
	 * @param   int     $id     An id
	 *
	 * @startuml
	 * activate SqlModelQueries
	 * ->> SqlModelQueries: getQueryData()
	 * alt empty query
	 *   SqlModelQueries ->> SqlModelQueries: getQueryDataById()
	 * else otherwise
	 *   SqlModelQueries ->> SqlModelQueries: createQueryData()
	 * end
	 * <<- SqlModelQueries: data
	 * deactivate SqlModelQueries
	 * @enduml
	 *
	 * @return  SqlTableQuery
	 */
	public function getQueryData($query = null, $id = 0)
	{
		if (empty($query))
		{
			$data = $this->getQueryDataById($id);
		}
		else
		{
			$data = $this->createQueryData($query);
		}

		return $data;
	}

	/**
	 * Save the query
	 *
	 * @param   array  $post  The data
	 *
	 * @startuml
	 * activate SqlModelQueries
	 * ->> SqlModelQueries: saveQuery()
	 * SqlModelQueries -->> SqlTableQuery: «create»
	 * activate SqlTableQuery
	 * SqlModelQueries <<-- SqlTableQuery
	 * SqlModelQueries ->> SqlTableQuery: bind()
	 * SqlModelQueries <<-- SqlTableQuery
	 * SqlModelQueries ->> SqlTableQuery: check()
	 * SqlModelQueries <<-- SqlTableQuery
	 * SqlModelQueries ->> SqlTableQuery: store()
	 * SqlModelQueries <<-- SqlTableQuery
	 * <<-- SqlModelQueries
	 * deactivate SqlTableQuery
	 * deactivate SqlModelQueries
	 * @enduml
	 *
	 * @return  void
	 *
	 * @throws  \UnexpectedValueException|\RuntimeException
	 */
	public function saveQuery($post)
	{
		$row = new SqlTableQuery($this->db);

		$row->bind($post);
		$row->check();
		$row->store();
	}

	/**
	 * Delete records
	 *
	 * @param   array  $cid  Ids of the disposable records
	 *
	 * @startuml
	 * activate SqlModelQueries
	 * ->> SqlModelQueries: delete()
	 * SqlModelQueries -->> SqlTableQuery: «create»
	 * activate SqlTableQuery
	 * SqlModelQueries <<-- SqlTableQuery
	 * loop for each id
	 * SqlModelQueries ->> SqlTableQuery: delete()
	 * SqlModelQueries <<-- SqlTableQuery
	 * end
	 * note over SqlModelQueries: Exception\non errors
	 * <<-- SqlModelQueries
	 * deactivate SqlTableQuery
	 * deactivate SqlModelQueries
	 * @enduml
	 *
	 * @return  void
	 *
	 * @throws \RuntimeException
	 */
	public function delete(array $cid)
	{
		$table  = new SqlTableQuery($this->db);
		$errors = array();

		foreach ((array) $cid as $id)
		{
			try
			{
				$this->assert->that(intval($id) > 0);
				$table->delete((int) $id);
			}
			catch (\RuntimeException $e)
			{
				$errors[] = $e->getMessage();
			}
		}

		if (!empty($errors))
		{
			throw new \RuntimeException(implode("\n", $errors));
		}
	}

	/**
	 * Get the query data by id
	 *
	 * @param   int  $id  An id
	 *
	 * @startuml
	 * activate SqlModelQueries
	 * ->> SqlModelQueries: getQueryDataById()
	 * SqlModelQueries -->> SqlTableQuery: «create»
	 * activate SqlTableQuery
	 * SqlModelQueries <<-- SqlTableQuery
	 * SqlModelQueries ->> SqlTableQuery: load()
	 * SqlModelQueries <<-- SqlTableQuery
	 * <<- SqlModelQueries: data
	 * deactivate SqlTableQuery
	 * deactivate SqlModelQueries
	 * @enduml
	 *
	 * @return  SqlTableQuery
	 */
	protected function getQueryDataById($id)
	{
		$data = new SqlTableQuery($this->db);
		$data->load($id);

		return $data;
	}

	/**
	 * Create new query data
	 *
	 * @param   string  $query  An SQL query
	 *
	 * @startuml
	 * activate SqlModelQueries
	 * ->> SqlModelQueries: createQueryData()
	 * SqlModelQueries -->> SqlTableQuery: «create»
	 * activate SqlTableQuery
	 * SqlModelQueries <<-- SqlTableQuery
	 * <<- SqlModelQueries: data
	 * deactivate SqlTableQuery
	 * deactivate SqlModelQueries
	 * @enduml
	 *
	 * @return  SqlTableQuery
	 */
	protected function createQueryData($query)
	{
		$data        = new SqlTableQuery($this->db);
		$data->id    = 0;
		$data->title = '';
		$data->query = $query;

		return $data;
	}
}
