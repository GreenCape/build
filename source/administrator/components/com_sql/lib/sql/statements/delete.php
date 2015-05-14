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
 * DELETE SQL Statement
 *
 * For the single-table syntax, the DELETE statement deletes rows from the table specified in the FROM clause.
 * The WHERE clause, if given, specifies the conditions that identify which rows to delete.
 * With no WHERE clause, all rows are deleted.
 * If the ORDER BY clause is specified, the rows are deleted in the order that is specified.
 * The LIMIT clause places a limit on the number of rows that can be deleted.
 *
 * @package  Celtic\Sql
 * @since    1.0.0
 */
class DeleteStatement extends SqlStatement
{
	/** @var FromClause */
	protected $from;

	/** @var ClauseCollection */
	protected $join;

	/** @var WhereClause */
	protected $where;

	/** @var OrderClause */
	protected $order;

	/** @var LimitClause */
	protected $limit;

	/**
	 * Constructor
	 *
	 * @param   string  $table  The name of a table
	 */
	public function __construct($table = null)
	{
		$this->from  = new NullClause;
		$this->join  = new ClauseCollection('Celtic\\Sql\\JoinClause');
		$this->where = new NullClause;
		$this->order = new NullClause;
		$this->limit = new NullClause;

		if (!empty($table))
		{
			$this->addClause(new FromClause($table));
		}
	}

	/**
	 * Transform this into its string representation
	 *
	 * @return  string
	 */
	public function __toString()
	{
		$sql = 'DELETE ' . $this->from . $this->join . $this->where . $this->order . $this->limit;

		return $sql;
	}
}
