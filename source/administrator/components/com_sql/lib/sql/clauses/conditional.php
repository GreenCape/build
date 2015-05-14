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
 * Conditional Clause Class
 *
 * @package  Celtic\Sql
 * @since    1.0.0
 */
abstract class ConditionalClause extends SqlClause
{
	/** @var string  The Glue Operator */
	protected $glue;

	/**
	 * Constructor
	 *
	 * @param   string  $data  Initial data
	 * @param   string  $glue  The conjunction, defaults to 'AND'
	 */
	public function __construct($data, $glue = 'AND')
	{
		$this->glue = strtoupper($glue);
		$this->data = (array) $data;
	}

	/**
	 * Append a clause
	 *
	 * @param   ConditionalClause  $clause  The clause to append
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException
	 */
	public function append(ConditionalClause $clause)
	{
		if ($clause->getConjunction() != $this->glue)
		{
			throw new \RuntimeException('Mixed conjunctions are not supported');
		}
		parent::append($clause);
	}

	/**
	 * Get the Glue Operator
	 *
	 * @return  string  The Glue Operator
	 */
	public function getConjunction()
	{
		return $this->glue;
	}
}
