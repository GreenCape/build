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
 * @package    Celtic\Abstraction
 * @author     Niels Braczek <nbraczek@bsds.de>
 * @copyright  Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL2
 */

namespace Celtic\Abstraction;

use Celtic\Sql\QueryBuilder;
use Celtic\Patterns\Decorator;

defined('_JEXEC') or die('Restricted access');

/**
 * Class DefaultDatabaseDecorator
 *
 * This class is an adapter to JDatabase to unify access with the later versions
 *
 * @package  Celtic\Abstraction
 *
 * @method   array|mixed loadObjectList($key = '', $class = 'stdClass')
 * @method   DefaultDatabaseDecorator setQuery(\string $query, $offset = 0, $limit = 0)
 *
 * @since    1.0.0
 */
class DefaultDatabaseDecorator extends Decorator
{
	/**
	 * Escape a string for usage in an SQL statement.
	 *
	 * This method was renamed in newer versions.
	 *
	 * @param   string  $text   The string to be escaped.
	 * @param   bool    $extra  Optional parameter to provide extra escaping.
	 *
	 * @return  string  The escaped string.
	 */
	public function escape($text, $extra = false)
	{
		return $this->subject->getEscaped($text, $extra);
	}

	/**
	 * Execute the SQL statement
	 *
	 * This method was renamed in newer versions.
	 * The error handling did not use exceptions (consequently).
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @throws  \RuntimeException
	 */
	public function execute()
	{
		$debug = $this->subject->_debug;
		$this->subject->_debug = 0;
		$result = $this->subject->query();
		$this->subject->_debug = $debug;
		if (!$result)
		{
			throw new \RuntimeException($this->subject->getErrorMsg(), $this->subject->getErrorNum());
		}
		return $result;
	}

	/**
	 * Retrieve field information about the given table
	 *
	 * This method was renamed in newer versions.
	 * The signature has changed.
	 *
	 * @param   string   $table     The name of the database table.
	 * @param   boolean  $typeOnly  True (default) to only return field types.
	 *
	 * @return  array  An array of fields by table.
	 *
	 * @throws  \RuntimeException
	 */
	public function getTableColumns($table, $typeOnly = true)
	{
		$fields = $this->subject->getTableFields(array($table), $typeOnly);
		return $fields[$table];
	}

	/**
	 * Get the details list of keys for a table
	 *
	 * @param   string  $table  The name of the table.
	 *
	 * @return  array  An array of the column specification for the table.
	 *
	 * @throws  \RuntimeException
	 */
	public function getTableKeys($table)
	{
		$this->setQuery('SHOW KEYS FROM ' . $this->quoteName($table));
		$keys = $this->loadObjectList();
		if (is_null($keys))
		{
			throw new \RuntimeException('Unable to retrieve key info for table ' . $table);
		}

		return $keys;
	}

	/**
	 * Get the current query object or a new QueryBuilder object
	 *
	 * @param   bool  $new  False to return the current query object, True to return a new QueryBuilder object.
	 *
	 * @return  QueryBuilder|mixed  The current query object or a new object extending the JDatabaseQuery class.
	 *
	 * @throws  \RuntimeException
	 */
	public function getQuery($new = false)
	{
		if ($new)
		{
			return new QueryBuilder($this);
		}
		else
		{
			return $this->subject->getQuery();
		}
	}

	/**
	 * Wrap an SQL statement identifier name such as column, table or database names in quotes to prevent injection
	 * risks and reserved word conflicts.
	 *
	 * @param   string|array  $name  The identifier name to wrap in quotes, or an array of identifier names to wrap in quotes.
	 *                               Each type supports dot-notation name.
	 * @param   string|array  $as    The AS query part associated to $name. It can be string or array, in latter case it has to be
	 *                               same length of $name; if is null there will not be any AS part for string or array element.
	 *
	 * @return  string|array  The quote wrapped name, same type of $name.
	 */
	public function quoteName($name, $as = null)
	{
		if (is_array($name))
		{
			if (is_null($as))
			{
				$as = array_fill(0, count($name), null);
			}
			$fin = array();
			foreach (array_combine($name, $as) as $identifier => $alias)
			{
				$fin[] = $this->quoteName($identifier, $alias);
			}

			return $fin;
		}

		$quotedName = implode('.', array_map(array($this->subject, 'nameQuote'), explode('.', $name)));

		if (!is_null($as))
		{
			$quotedName .= ' AS ' . $this->subject->nameQuote($as);
		}

		return $quotedName;
	}

	/**
	 * Insert a row into a table based on an object's properties
	 *
	 * This proxy is needed, because Joomla requires $object to be a reference
	 *
	 * @param   string  $table    The name of the table
	 * @param   object  $object   An object whose properties match table fields
	 * @param   string  $keyName  The name of the primary key. If provided the object property is updated.
	 *
	 * @return  bool
	 */
	public function insertObject($table, $object, $keyName = null)
	{
		$errorReporting = error_reporting(0);
		$ret = $this->subject->insertObject($table, $object, $keyName);
		error_reporting($errorReporting);
		return $ret;
	}

	/**
	 * Update a row in a table based on an object's properties
	 *
	 * This proxy is needed, because Joomla requires $object to be a reference
	 *
	 * @param   string  $table        The name of the table
	 * @param   object  $object       An object whose properties match table fields
	 * @param   string  $keyName      The name of the primary key. If provided the object property is updated.
	 * @param   bool    $updateNulls  Whether or not NULLs have to considered a value
	 *
	 * @return  bool
	 */
	public function updateObject($table, $object, $keyName = null, $updateNulls = true)
	{
		$errorReporting = error_reporting(0);
		$ret = $this->subject->updateObject($table, $object, $keyName, $updateNulls);
		error_reporting($errorReporting);
		return $ret;
	}
}
