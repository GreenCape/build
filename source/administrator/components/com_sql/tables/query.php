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

defined('_JEXEC') or die('Restricted access');

/**
 * Class SqlTableQuery
 *
 * @package  Celtic\SqlManager
 * @since    1.0.0
 */
class SqlTableQuery extends \JTable
{
	/** @var int */
	public $id = 0;

	/** @var string */
	public $title = '';

	/** @var string */
	public $query = '';

	/**
	 * Constructor
	 *
	 * @param   \JDatabase  $db  The database connector
	 */
	public function __construct($db)
	{
		parent::__construct('#__sql_queries', 'id', $db);
	}

	/**
	 * Bind an associative array or object to the table instance
	 *
	 * @param   mixed  $data    An associative array or object to bind to the JTable instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  void
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function bind($data, $ignore = array())
	{
		if (!parent::bind($data, $ignore))
		{
			/** @noinspection PhpDeprecationInspection */
			throw new \InvalidArgumentException($this->getError());
		}
	}

	/**
	 * Perform sanity checks on the table instance properties
	 *
	 * @return  void
	 *
	 * @throws  \UnexpectedValueException
	 */
	public function check()
	{
		$errors = array();

		if (empty($this->title))
		{
			$errors[] = 'Title must not be empty';
		}

		if (!empty($errors))
		{
			throw new \UnexpectedValueException(implode("\n", $errors));
		}
	}

	/**
	 * Store a row in the database from the table instance properties
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return void
	 *
	 * @throws  \RuntimeException
	 */
	public function store($updateNulls = false)
	{
		// Must suppress warning due to J1.5 bug
		if (!@parent::store($updateNulls))
		{
			/** @noinspection PhpDeprecationInspection */
			throw new \RuntimeException($this->getError());
		}
	}

	/**
	 * Delete a row
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  void
	 *
	 * @throws  \UnexpectedValueException|\RuntimeException
	 */
	public function delete($pk = null)
	{
		if (!parent::delete($pk))
		{
			/** @noinspection PhpDeprecationInspection */
			throw new \RuntimeException($this->getError());
		}
	}
}
