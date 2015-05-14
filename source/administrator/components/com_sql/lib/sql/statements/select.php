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
 * Select Statement Class
 *
 * @package  Celtic\Sql
 * @since    1.0.0
 */
class SelectStatement extends SqlStatement
{
	/** @var SelectClause */
	protected $select;

	/** @var FromClause */
	protected $from;

	/** @var ClauseCollection */
	protected $join;

	/** @var WhereClause */
	protected $where;

	/** @var GroupClause */
	protected $group;

	/** @var HavingClause */
	protected $having;

	/** @var OrderClause */
	protected $order;

	/** @var LimitClause */
	protected $limit;

	/**
	 * Constructor
	 *
	 * @param   mixed  $columns  A string or an array of field names.
	 */
	public function __construct($columns)
	{
		$this->select = new SelectClause($columns);
		$this->from = new NullClause;
		$this->join = new ClauseCollection('Celtic\\Sql\\JoinClause');
		$this->where = new NullClause;
		$this->group = new NullClause;
		$this->having = new NullClause;
		$this->order = new NullClause;
		$this->limit = new NullClause;
	}

	/**
	 * Transform this into its string representation
	 *
	 * @return  string
	 */
	public function __toString()
	{
		$sql = 'SELECT ' . $this->select . $this->from . $this->join . $this->where . $this->group . $this->having . $this->order . $this->limit;

		return $sql;
	}
}
