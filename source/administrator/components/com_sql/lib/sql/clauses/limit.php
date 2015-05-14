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
 * Limit Clause Class
 *
 * @package  Celtic\Sql
 * @since    1.0.0
 */
class LimitClause extends SqlClause
{
	/** @var string  The number of records */
	private $limit;

	/** @var string  The index of the first record */
	private $start;

	/**
	 * Constructor
	 *
	 * @param   string  $limit  The number of records
	 * @param   string  $start  The index of the first record
	 */
	public function __construct($limit, $start = null)
	{
		$this->limit = $limit;
		$this->start = $start;
	}

	/**
	 * Append a clause
	 *
	 * @return  void
	 *
	 * @throws \RuntimeException
	 */
	public function append()
	{
		throw new \RuntimeException('Limit can be set only once');
	}

	/**
	 * Transform this into its string representation
	 *
	 * @return  string
	 */
	public function __toString()
	{
		$sql = ' LIMIT ' . $this->limit;
		if (!is_null($this->start))
		{
			$sql .= ', ' . $this->start;
		}

		return $sql;
	}
}
