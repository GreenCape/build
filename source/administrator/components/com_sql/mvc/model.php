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

use Celtic\Abstraction\Input;
use Celtic\Abstraction\VersionFactoryInterface;
use Celtic\Assertions\Assertions;

defined('_JEXEC') or die('Restricted access');

/**
 * Class SqlModel
 *
 * @package  Celtic\SqlManager
 *
 * @method   mixed getState(string $property, mixed $default = null)
 * @startuml
 * activate SqlModel
 * ->> SqlModel: getState()
 * group once
 *   SqlModel ->> SqlModel: populateState()
 * end
 * <<- SqlModel: state
 * deactivate SqlModel
 * @enduml
 *
 * @since    1.0.0
 */
class SqlModel extends AbstractedBaseModel
{
	/** @var \JDatabaseDriver|\JDatabase */
	protected $db;

	/** @var VersionFactoryInterface */
	protected $factory;

	/** @var  Assertions */
	protected $assert;

	/** @var  \JInput|Input */
	protected $input;

	/** @var \JApplication */
	protected $application;

	/**
	 * Constructor
	 *
	 * @param   VersionFactoryInterface  $factory  The abstraction factory
	 * @param   array                    $default  An array of configuration options (name, state, dbo, table_path, ignore_request).
	 */
	public function __construct(VersionFactoryInterface $factory, $default = array())
	{
		parent::__construct($default);

		$this->factory     = $factory;

		$this->application = $this->factory->getApplication();
		$this->assert      = $this->factory->getAssertions();
		$this->db          = $this->factory->getDbo();
	}

	/**
	 * Get an array of objects from the results of database query
	 *
	 * @param   string  $query       The query
	 * @param   int     $limitstart  Offset
	 * @param   int     $limit       The number of records
	 * @param   string  $key         The key column
	 *
	 * @return  array  An array of results
	 */
	protected function getList($query, $limitstart = 0, $limit = 0, $key = '')
	{
		$this->db->setQuery($query, $limitstart, $limit);

		return $this->db->loadObjectList($key);
	}

	/**
	 * Return a record count for the query
	 *
	 * @param   string  $query  The query
	 *
	 * @return  integer  Number of rows for query
	 */
	protected function getListCount($query)
	{
		$this->db->setQuery($query);
		$this->db->execute();

		return $this->db->getNumRows();
	}

	/**
	 * Set model state variables
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set or null.
	 *
	 * @startuml
	 * activate SqlModel
	 * ->> SqlModel: setState()
	 * <<-- SqlModel
	 * deactivate SqlModel
	 * @enduml
	 *
	 * @return  mixed  The previous value of the property or null if not set.
	 */
	public function setState($property, $value = null)
	{
		// Auto-populate the model state.
		$this->populateState();
		return parent::setState($property, $value);
	}
}
