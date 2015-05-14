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

use Celtic\Abstraction\DefaultDatabaseDecorator;

/**
 * Class QueryBuilder
 *
 * This class is a minimal query builder for Joomla 1.5.
 * The code is adapted from Joomla 3.1.
 *
 * @package  Celtic\Sql
 * @since    1.0.0
 */
class QueryBuilder
{
	/** @var  SqlStatement  The query element for a generic query (type = null */
	private $statement = null;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Add a single column, or array of columns to the SELECT clause of the query.
	 *
	 * @param   mixed  $columns  A string or an array of field names.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function select($columns)
	{
		if ($this->statement instanceof SqlStatement)
		{
			$this->statement->addClause(new SelectClause($columns));
		}
		else
		{
			$this->statement = new SelectStatement($columns);
		}

		return $this;
	}

	/**
	 * Add a table name to the INSERT clause of the query.
	 *
	 * @param   string  $table           The name of the table to insert data into.
	 * @param   bool    $incrementField  The name of the field to auto increment.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function insert($table, $incrementField=false)
	{
		$this->statement = new InsertStatement($table);
		$this->autoIncrementField = $incrementField;

		return $this;
	}

	/**
	 * Add a table name to the UPDATE clause of the query
	 *
	 * @param   string  $table  A table to update.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function update($table)
	{
		$this->statement = new UpdateStatement($table);

		return $this;
	}

	/**
	 * Create a DELETE statement
	 *
	 * Syntax:
	 *
	 *     $query->delete()->from($tbl_name)
	 *     [->where($where_condition)]
	 *     [->order(...)]
	 *     [->limit($row_count)]
	 *
	 * or, as a shortcut,
	 *
	 *     $query->delete($tbl_name)
	 *     [->where(where_condition)]
	 *     [->order(...)]
	 *     [->limit($row_count)]
	 *
	 * The DELETE statement deletes rows from $tbl_name.
	 * The WHERE clause, if given, specifies the conditions that identify which rows to delete.
	 * As always, the WHERE clause may be accompanied with one or more JOIN clauses.
	 * With no WHERE clause, all rows are deleted.
	 * If the ORDER BY clause is specified, the rows are deleted in the order that is specified.
	 * The LIMIT clause places a limit on the number of rows that can be deleted.
	 *
	 * @param   string  $table  The name of the table to delete from.,
	 *                          Alternatively, the table can be specified using the FROM clause.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining
	 */
	public function delete($table = null)
	{
		$this->statement = new DeleteStatement($table);

		return $this;
	}

	/**
	 * Add a table to the FROM clause of the query
	 *
	 * @param   mixed   $tables         A string or array of table names.
	 * @param   string  $subQueryAlias  Alias used when $tables is a JDatabaseQuery.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function from($tables, $subQueryAlias = null)
	{
		$this->statement->addClause(new FromClause($tables, $subQueryAlias));

		return $this;
	}

	/**
	 * Add a JOIN clause to the query.
	 *
	 * Usage:
	 * $query->join('INNER', 'b ON b.id = a.id);
	 *
	 * @param   string  $type        The type of join. This string is prepended to the JOIN keyword.
	 * @param   string  $conditions  A string or array of conditions.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function join($type, $conditions)
	{
		$this->statement->addClause(new JoinClause($type, $conditions));

		return $this;
	}

	/**
	 * Add an INNER JOIN clause to the query.
	 *
	 * Usage:
	 * $query->innerJoin('b ON b.id = a.id')->innerJoin('c ON c.id = b.id');
	 *
	 * @param   string  $condition  The join condition.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function innerJoin($condition)
	{
		$this->join('INNER', $condition);

		return $this;
	}

	/**
	 * Add an OUTER JOIN clause to the query.
	 *
	 * Usage:
	 * $query->outerJoin('b ON b.id = a.id')->outerJoin('c ON c.id = b.id');
	 *
	 * @param   string  $condition  The join condition.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function outerJoin($condition)
	{
		$this->join('OUTER', $condition);

		return $this;
	}

	/**
	 * Add a LEFT JOIN clause to the query.
	 *
	 * Usage:
	 * $query->leftJoin('b ON b.id = a.id')->leftJoin('c ON c.id = b.id');
	 *
	 * @param   string  $condition  The join condition.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function leftJoin($condition)
	{
		$this->join('LEFT', $condition);

		return $this;
	}

	/**
	 * Add a RIGHT JOIN clause to the query.
	 * Usage:
	 * $query->rightJoin('b ON b.id = a.id')->rightJoin('c ON c.id = b.id');
	 *
	 * @param   string  $condition  The join condition.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function rightJoin($condition)
	{
		$this->join('RIGHT', $condition);

		return $this;
	}

	/**
	 * Add a single condition string, or an array of strings to the SET clause of the query
	 *
	 * @param   string  $assignment  The assignment
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function set($assignment)
	{
		$this->statement->addClause(new SetClause($assignment));

		return $this;
	}

	/**
	 * Add a single condition, or an array of conditions to the WHERE clause of the query
	 *
	 * @param   mixed   $conditions  A string or array of where conditions.
	 * @param   string  $glue        The glue by which to join the conditions. Defaults to AND.
	 *                               Note that the glue is set on first use and cannot be changed.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function where($conditions, $glue = 'AND')
	{
		$this->statement->addClause(new WhereClause($conditions, $glue));

		return $this;
	}

	/**
	 * Add a grouping column to the GROUP clause of the query.
	 *
	 * Usage:
	 * $query->group('id');
	 *
	 * @param   mixed  $columns  A string or array of ordering columns.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function group($columns)
	{
		$this->statement->addClause(new GroupClause($columns));

		return $this;
	}

	/**
	 * A conditions to the HAVING clause of the query.
	 *
	 * Usage:
	 * $query->group('id')->having('COUNT(id) > 5');
	 *
	 * @param   mixed   $conditions  A string or array of columns.
	 * @param   string  $glue        The glue by which to join the conditions. Defaults to AND.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function having($conditions, $glue = 'AND')
	{
		$this->statement->addClause(new HavingClause($conditions, $glue));

		return $this;
	}

	/**
	 * Add a ordering column to the ORDER clause of the query.
	 *
	 * Usage:
	 * $query->order('foo')->order('bar');
	 * $query->order(array('foo','bar'));
	 *
	 * @param   mixed  $columns  A string or array of ordering columns.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function order($columns)
	{
		$this->statement->addClause(new OrderClause($columns));

		return $this;
	}

	/**
	 * Add a column, or array of column names that would be used for an INSERT INTO statement.
	 *
	 * @param   mixed  $columns  A column name, or array of column names.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function columns($columns)
	{
		$this->statement->addClause(new ColumnsClause($columns));

		return $this;
	}

	/**
	 * Add a tuple, or array of tuples that would be used as values for an INSERT INTO statement.
	 *
	 * Usage:
	 * $query->values('1,2,3')->values('4,5,6');
	 * $query->values(array('1,2,3', '4,5,6'));
	 *
	 * @param   string  $values  A single tuple, or array of tuples.
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function values($values)
	{
		$this->statement->addClause(new ValuesClause($values));

		return $this;
	}

	/**
	 * Add a LIMIT clause
	 *
	 * Usage:
	 * $query->limit(10,0)
	 *
	 * @param   int  $limit  The number of records
	 * @param   int  $start  The 0-based index of the first record
	 *
	 * @return  QueryBuilder  Returns this object to allow chaining.
	 */
	public function limit($limit, $start = null)
	{
		$this->statement->addClause(new LimitClause($limit, $start));

		return $this;
	}

	/**
	 * Transform this into its string representation
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return (string) $this->statement;
	}
}
