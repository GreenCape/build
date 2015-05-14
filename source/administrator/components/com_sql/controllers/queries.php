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
 * Query Controller - handles stored queries
 *
 * @package  Celtic\SqlManager
 * @since    1.0.0
 */
class SqlControllerQueries extends SqlController
{
	/** @var SqlModelQueries */
	protected $model;

	/** @var string  */
	protected $query;

	/** @var string  */
	protected $title;

	/** @var int  */
	protected $id;

	/**
	 * Constructor
	 *
	 * @param   VersionFactoryInterface  $factory  The abstraction factory
	 * @param   array                    $config   An optional associative array of configuration settings.
	 *
	 * @startuml
	 * -->> SqlControllerQueries: «create»
	 * activate SqlControllerQueries
	 * SqlControllerQueries -->> SqlModelQueries: «create»
	 * activate SqlModelQueries
	 * SqlControllerQueries <<- SqlModelQueries: model
	 * <<-- SqlControllerQueries
	 * deactivate SqlModelQueries
	 * deactivate SqlControllerQueries
	 * @enduml
	 */
	public function __construct(VersionFactoryInterface $factory, $config = array())
	{
		parent::__construct($factory, $config);
		$this->setParameters();

		$this->model = new SqlModelQueries($factory);
	}

	/**
	 * Display
	 *
	 * @startuml
	 * activate SqlControllerQueries
	 * ->> SqlControllerQueries: display()
	 * SqlControllerQueries -->> SqlViewQueries: «create»
	 * activate SqlViewQueries
	 * SqlControllerQueries <<- SqlViewQueries: view
	 * SqlControllerQueries ->> SqlViewQueries: display()
	 * <<- SqlViewQueries
	 * deactivate SqlViewQueries
	 * deactivate SqlControllerQueries
	 * @enduml
	 *
	 * @return  void
	 */
	public function display()
	{
		$view = new SqlViewQueries(
			$this->factory,
			$this->model,
			array(
				'layout' => 'default'
			)
		);

		$view->document = $this->factory->getDocument();

		$view->display();
	}

	/**
	 * Display the query form
	 *
	 * @startuml
	 * activate SqlControllerQueries
	 * ->> SqlControllerQueries: edit()
	 * SqlControllerQueries -->> SqlViewQueries: «create»
	 * activate SqlViewQueries
	 * SqlControllerQueries <<- SqlViewQueries: view
	 * SqlControllerQueries ->> SqlViewQueries: display()
	 * <<- SqlViewQueries
	 * deactivate SqlViewQueries
	 * deactivate SqlControllerQueries
	 * @enduml
	 *
	 * @return  void
	 */
	public function edit()
	{
		$this->input->set('hidemainmenu', 1);

		$view = new SqlViewQueries(
			$this->factory,
			$this->model,
			array(
				'layout' => 'default'
			)
		);

		$cid   = $this->input->getVar('cid', array(0), 'method', 'array');
		$view->row = $this->getModel()->getQueryData($this->query, $cid[0]);

		$view->document = $this->factory->getDocument();

		$view->edit('edit');
	}

	/**
	 * Store a query
	 *
	 * @startuml
	 * activate SqlControllerQueries
	 * activate SqlModelQueries
	 * ->> SqlControllerQueries: save()
	 * note over SqlControllerQueries: assert token is valid
	 * SqlControllerQueries ->> SqlModelQueries: saveQuery()
	 * note over SqlModelQueries: Exception\non error
	 * SqlControllerQueries <<-- SqlModelQueries
	 * SqlControllerQueries ->> SqlControllerQueries: setRedirect()
	 * <<-- SqlControllerQueries
	 * deactivate SqlModelQueries
	 * deactivate SqlControllerQueries
	 * @enduml
	 *
	 * @return  void
	 *
	 * @throws  AssertionException
	 */
	public function save()
	{
		$this->assert->tokenIsValid();

		$data = array(
			'id'    => $this->id,
			'title' => $this->title,
			'query' => $this->query
		);

		try
		{
			$this->model->saveQuery($data);
			$msg = \JText::_('COM_SQL_SAVE_TRUE');
		}
		catch (\Exception $e)
		{
			$msg = \JText::_('COM_SQL_SAVE_FALSE') . ' ' . $e->getMessage();
		}

		$this->setRedirect(
			'index.php?option=com_sql&view=queries',
			$msg
		);
	}

	/**
	 * Cancel editing
	 *
	 * @startuml
	 * activate SqlControllerQueries
	 * ->> SqlControllerQueries: cancel()
	 * SqlControllerQueries ->> SqlControllerQueries: setRedirect()
	 * <<-- SqlControllerQueries
	 * deactivate SqlControllerQueries
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
			'index.php?option=com_sql&view=queries'
		);
	}

	/**
	 * Remove a query
	 *
	 * @startuml
	 * activate SqlControllerQueries
	 * activate SqlModelQueries
	 * ->> SqlControllerQueries: remove()
	 * note over SqlControllerQueries: assert token is valid
	 * SqlControllerQueries ->> SqlModelQueries: delete()
	 * note over SqlModelQueries: Exception\non error
	 * SqlControllerQueries <<-- SqlModelQueries
	 * SqlControllerQueries ->> SqlControllerQueries: setRedirect()
	 * <<-- SqlControllerQueries
	 * deactivate SqlModelQueries
	 * deactivate SqlControllerQueries
	 * @enduml
	 *
	 * @return  void
	 *
	 * @throws  AssertionException
	 */
	public function remove()
	{
		$this->assert->tokenIsValid();

		$cid = $this->input->getArray('cid', array());

		try
		{
			$this->model->delete($cid);
			$message = \JTEXT::_('COM_SQL_QUERY_DELETED');
			$msgType = null;
		}
		catch (\Exception $e)
		{
			$message = $e->getMessage();
			$msgType = 'error';
		}

		$this->setRedirect(
			'index.php?option=com_sql&view=queries',
			$message,
			$msgType
		);
	}

	/**
	 * Set the internal properties according to input parameters
	 *
	 * @return  void
	 */
	protected function setParameters()
	{
		$this->query = $this->input->getString('query');
		$this->title = $this->input->getString('title');
		$this->id    = $this->input->getInt('id');
	}
}
