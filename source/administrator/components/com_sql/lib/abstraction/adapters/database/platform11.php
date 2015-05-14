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

use Celtic\Patterns\Decorator;

defined('_JEXEC') or die('Restricted access');

/**
 * Class PlatformElevenDatabaseDecorator
 *
 * This class is an adapter to JDatabase to unify access with the later versions
 *
 * @package  Celtic\Abstraction
 * @since    1.0.0
 */
class PlatformElevenDatabaseDecorator extends Decorator
{
	/**
	 * Execute the SQL statement
	 *
	 * The error handling did not use exceptions (consequently).
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @throws  \RuntimeException|\JDatabaseException
	 */
	public function execute()
	{
		$debug = $this->subject->setDebug(false);
		$result = $this->subject->execute();
		$this->subject->setDebug($debug);
		if (!$result)
		{
			throw new \RuntimeException($this->subject->getErrorMsg(), $this->subject->getErrorNum());
		}
		return $result;
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
