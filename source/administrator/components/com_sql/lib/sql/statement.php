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

namespace Celtic\Sql;

/**
 * SQL Statement Class
 *
 * @package  Celtic\Sql
 * @since    1.0.0
 */
abstract class SqlStatement
{
	/**
	 * Add a clause to the statement
	 *
	 * @param   SqlClause  $clause  The clause
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException
	 */
	public function addClause(SqlClause $clause)
	{
		$class    = get_class($clause);
		$property = explode('\\', $class);
		$property = strtolower(preg_replace('~Clause$~', '', array_pop($property)));
		if (!property_exists($this, $property))
		{
			throw new \RuntimeException(get_class($this) . ' does not support the ' . $property . ' clause');
		}
		if ($this->{$property} instanceof $class || $this->{$property} instanceof ClauseCollection)
		{
			$this->{$property}->append($clause);
		}
		else
		{
			$this->{$property} = $clause;
		}
	}

	/**
	 * Transform this into its string representation
	 *
	 * @return  string
	 */
	abstract public function __toString();
}
