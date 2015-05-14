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
 * Class SqlModelSql
 *
 * @todo     Utilize an SQL parser, e.g. http://code.google.com/p/php-sql-parser/
 * @package  Celtic\SqlManager
 * @since    1.0.0
 */
class SqlModelSql extends SqlModel
{
	/** @var  string  A database table */
	protected $table = null;

	/** @var  string  An SQL query */
	protected $query = null;

	/** @var string */
	protected $key;

	/**
	 * Set the query
	 *
	 * @param   string  $query  An SQL query
	 *
	 * @return  void
	 */
	public function setQuery($query)
	{
		$this->query = $query;
	}

	/**
	 * Set the table
	 *
	 * @param   string  $table  A database table
	 *
	 * @return  void
	 */
	public function setTable($table)
	{
		$this->table = $table;
	}

	/**
	 * Set the key
	 *
	 * @param   string  $key  A database key
	 *
	 * @return  void
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}

	/**
	 * Get the results from the query/queries
	 *
	 * @startuml
	 * activate SqlModelSql
	 * ->> SqlModelSql: getQueryResults()
	 * SqlModelSql ->> SqlModelSql: splitSql()
	 * loop for each query
	 * SqlModelSql ->> SqlModelSql: executeQuery()
	 * activate SqlModelSql
	 * SqlModelSql <<- SqlModelSql: result
	 * deactivate SqlModelSql
	 * end
	 * <<- SqlModelSql: data
	 * deactivate SqlModelSql
	 * @enduml
	 *
	 * @return  QueryResult[]  List of query results
	 */
	public function getQueryResults()
	{
		$data = array();
		foreach ($this->splitSQL($this->query) as $query)
		{
			if (!empty($query))
			{
				$data[] = $this->executeQuery($query);
			}
		}

		return $data;
	}

	/**
	 * Get the current table prefix
	 *
	 * @startuml
	 * activate SqlModelSql
	 * ->> SqlModelSql: getPrefix()
	 * <<- SqlModelSql: prefix
	 * deactivate SqlModelSql
	 * @enduml
	 *
	 * @return   string  The database prefix
	 */
	public function getPrefix()
	{
		return $this->db->getPrefix();
	}

	/**
	 * Delete a record from a table
	 *
	 * @param   string  $table  The name of a table
	 * @param   string  $key    The key column
	 * @param   int     $id     The id
	 *
	 * @startuml
	 * activate SqlModelSql
	 * activate Database
	 * ->> SqlModelSql: delete()
	 * SqlModelSql ->> Database: getQuery()
	 * Database -->> Query: «create»
	 * activate Query
	 * Database <<-- Query
	 * SqlModelSql <<- Database: query
	 * SqlModelSql ->> Query: delete()
	 * SqlModelSql <<-- Query
	 * SqlModelSql ->> Query: where()
	 * SqlModelSql <<-- Query
	 * SqlModelSql ->> Database: setQuery()
	 * SqlModelSql <<-- Database
	 * SqlModelSql ->> Database: execute()
	 * <<-- Database
	 * deactivate Query
	 * deactivate Database
	 * deactivate SqlModelSql
	 * @enduml
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException
	 */
	public function delete($table, $key, $id)
	{
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName($table));
		$query->where($this->db->quoteName($key) . '=' . $this->db->quote($id));
		$this->db->setQuery($query);
		$this->db->execute();
	}

	/**
	 * exportToCsv
	 *
	 * @param   string  $query            An SQL query
	 * @param   string  $textDelimiter    The text delimiter, defaults to '"'
	 * @param   string  $fieldSeparator   The field separator, defaults to ','
	 * @param   string  $recordSeparator  The record separator, defaults to '\n'
	 *
	 * @return  string  The CSV data
	 */
	public function exportToCsv(
		$query,
		$textDelimiter = '"',
		$fieldSeparator = ',',
		$recordSeparator = "\n")
	{
		$this->db->setQuery($query);
		$rows = $this->db->loadAssocList();

		if (empty($rows))
		{
			return '';
		}

		$lines = array();
		$titles = array_keys($rows[0]);
		$lines[] = implode(
			$fieldSeparator,
			$this->escape(
				$titles,
				$textDelimiter,
				$fieldSeparator,
				$recordSeparator
			)
		);

		foreach ($rows as $row)
		{
			$lines[] = implode(
				$fieldSeparator,
				$this->escape(
					$row,
					$textDelimiter,
					$fieldSeparator,
					$recordSeparator
				)
			);
		}

		return implode($recordSeparator, $lines) . $recordSeparator;
	}

	/**
	 * Escape a single data row for CSV
	 *
	 * @param   array   $row              A single data (or title) row
	 * @param   string  $textDelimiter    The text delimiter, defaults to '"'
	 * @param   string  $fieldSeparator   The field separator, defaults to ','
	 * @param   string  $recordSeparator  The record separator, defaults to '\n'
	 *
	 * @return mixed
	 */
	private function escape(
		$row,
		$textDelimiter,
		$fieldSeparator,
		$recordSeparator)
	{
		foreach ($row as $key => $value)
		{
			$value = str_replace($textDelimiter, $textDelimiter . $textDelimiter, $value);
			if (strpos($value, $fieldSeparator) !== false || strpos($value, $recordSeparator) !== false)
			{
				$value = $textDelimiter . $value . $textDelimiter;
			}
			$row[$key] = $value;
		}
		return $row;
	}

	/**
	 * Extract the main table name from an SQL query
	 *
	 * @param   string  $query  The SQL query
	 *
	 * @return  string  The table name, empty string if no table determined
	 */
	public function getTableNameFromQuery($query)
	{
		$table = '';
		if (preg_match('~\s+FROM\s+(\S+)~i', $query, $match))
		{
			$table = $match[1];
		}

		return $table;
	}

	/**
	 * splitSql
	 *
	 * @param   string  $sql  A ';'-separated list of SQL queries
	 *
	 * @return  array  The separated queries
	 */
	public function splitSQL($sql)
	{
		$sql = preg_replace("/\n#[^\n]*\n/", "\n", trim($sql, " \t\n\r\0\x0B;"));

		$buffer    = array();
		$ret       = array();
		$in_string = false;

		for ($i = 0; $i < strlen($sql); $i++)
		{
			if ($sql[$i] == ";" && !$in_string)
			{
				$ret[] = substr($sql, 0, $i);
				$sql   = substr($sql, $i + 1);
				$i     = 0;
			}

			if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\")
			{
				$in_string = false;
			}
			elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\"))
			{
				$in_string = $sql[$i];
			}

			if (isset($buffer[1]))
			{
				$buffer[0] = $buffer[1];
			}

			$buffer[1] = $sql[$i];
		}

		if (!empty($sql))
		{
			$ret[] = $sql;
		}

		return $ret;
	}

	/**
	 * Check if a table is present in the database
	 *
	 * @param   string  $table  A table name
	 *
	 * @startuml
	 * activate SqlModelSql
	 * ->> SqlModelSql: isTable()
	 * SqlModelSql ->> SqlModelSql: getTableList()
	 * activate SqlModelSql
	 * SqlModelSql <<- SqlModelSql: tables
	 * deactivate SqlModelSql
	 * <<- SqlModelSql: result
	 * deactivate SqlModelSql
	 * @enduml
	 *
	 * @return  bool  True if table exists
	 */
	public function isTable($table)
	{
		return in_array(
			str_replace("#__", $this->getPrefix(), $table),
			$this->getTableList()
		);
	}

	/**
	 * Get a unique key column for the table
	 *
	 * @param   string  $table  A table name
	 *
	 * @startuml
	 * activate SqlModelSql
	 * activate Database
	 * ->> SqlModelSql: getTableKey()
	 * SqlModelSql ->> Database: getTableKeys()
	 * SqlModelSql <<- Database: keys
	 * <<- SqlModelSql: key
	 * deactivate Database
	 * deactivate SqlModelSql
	 * @enduml
	 *
	 * @return  string  The name of the first unique column, if any
	 */
	public function getTableKey($table)
	{
		$key = null;
		foreach ($this->db->getTableKeys($table) as $index)
		{
			if ($index->Key_name == 'PRIMARY')
			{
				$key = $index->Column_name;
				break;
			}
			if ($index->Non_unique == 0 && empty($key))
			{
				$key = $index->Column_name;
			}
		}

		return $key;
	}

	/**
	 * Execute an SQL query
	 *
	 * @param   string  $query  An SQL query
	 *
	 * @startuml
	 * activate SqlModelSql
	 * ->> SqlModelSql: executeQuery()
	 * alt replace prefix?
	 * SqlModelSql ->> SqlModelSql: replacePrefixInConfiguration()
	 * activate SqlModelSql
	 * SqlModelSql <<- SqlModelSql: result
	 * deactivate SqlModelSql
	 * else otherwise
	 * SqlModelSql ->> SqlModelSql: executeDatabaseQuery()
	 * activate SqlModelSql
	 * SqlModelSql <<- SqlModelSql: result
	 * deactivate SqlModelSql
	 * end
	 * <<- SqlModelSql: result
	 * deactivate SqlModelSql
	 * @enduml
	 *
	 * @return  QueryResult
	 */
	protected function executeQuery($query)
	{
		$query = trim($query);

		return $this->executeDatabaseQuery($query);
	}

	/**
	 * Get the list of tables in the database
	 *
	 * @startuml
	 * activate SqlModelSql
	 * activate Database
	 * ->> SqlModelSql: getTableList()
	 * SqlModelSql ->> Database: getTableList()
	 * SqlModelSql <<- Database: tables
	 * <<- SqlModelSql: tables
	 * deactivate Database
	 * deactivate SqlModelSql
	 * @enduml
	 *
	 * @return  array
	 */
	public function getTableList()
	{
		static $tables = null;

		if (is_null($tables))
		{
			$tables = $this->db->getTableList();
		}

		return $tables;
	}

	/**
	 * Replace the prefix in the configuration
	 *
	 * @param   string  $query  The 'REPLACE PREFIX' query
	 *
	 * @return  QueryResult
	 */
	/* Not part of the primary milestone
	protected function replacePrefixInConfiguration($query)
	{
		$this->_replacePrefix($this->db, $this->db, $query);
		$result          = new QueryResult($query);
		$result->message = 'Replaced prefix';

		return $result;
	}
	*/

	/**
	 * Execute a query in the database
	 *
	 * @param   string  $query  The SQL query
	 *
	 * @startuml
	 * activate SqlModelSql
	 * participant QueryResult
	 * activate Database
	 * ->> SqlModelSql: executeDatabaseQuery()
	 * SqlModelSql ->> Database: setQuery()
	 * SqlModelSql <<-- Database
	 * SqlModelSql -->> QueryResult: «create»
	 * activate QueryResult
	 * alt is SELECT
	 * SqlModelSql ->> Database: loadAssocList()
	 * SqlModelSql <<- Database: rows
	 * SqlModelSql ->> Database: getNumRows()
	 * SqlModelSql <<- Database: count
	 * SqlModelSql -->> QueryResult: set results
	 * else otherwise
	 * SqlModelSql ->> Database: setQuery()
	 * SqlModelSql <<-- Database
	 * SqlModelSql ->> Database: getAffectedRows()
	 * SqlModelSql <<- Database: count
	 * SqlModelSql -->> QueryResult: set results
	 * end
	 * <<- SqlModelSql: result
	 * deactivate QueryResult
	 * deactivate Database
	 * deactivate SqlModelSql
	 * @enduml
	 *
	 * @return  QueryResult
	 */
	protected function executeDatabaseQuery($query)
	{
		$result        = new QueryResult($query);
		$result->table = $this->getTableNameFromQuery($query);
		$this->db->setQuery($query);
		try
		{
			if ($result->hasData())
			{
				$result->rows  = $this->db->loadAssocList();
				$result->total = count($result->rows);
				$result->key   = ($this->isTable($result->table)) ? $this->getTableKey($result->table) : '';
				if (!empty($result->rows))
				{
					reset($result->rows[0]);
					$result->key = empty($result->key) ? key($result->rows[0]) : $result->key;
				}
			}
			else
			{
				$this->db->execute();
				$result->total = $this->db->getAffectedRows();
			}
		}
		catch (\Exception $e)
		{
			$result->message = $e->getMessage();
		}

		return $result;
	}

	/**
	 * Insert a record into a table
	 *
	 * @param   string  $table   The name of the table
	 * @param   array   $fields  Key/value pairs to store in table
	 *
	 * @startuml
	 * activate SqlModelSql
	 * activate Database
	 * ->> SqlModelSql: insert()
	 * SqlModelSql ->> SqlModelSql: buildInsertQuery()
	 * SqlModelSql ->> Database: setQuery()
	 * SqlModelSql <<-- Database
	 * SqlModelSql ->> Database: execute()
	 * <<-- Database
	 * deactivate Database
	 * deactivate SqlModelSql
	 * @enduml
	 *
	 * @return  void
	 *
	 * @throws  \Celtic\Assertions\AssertionException|\RuntimeException
	 */
	public function insert($table, array $fields)
	{
		$this->assert->that(!empty($table));
		$this->assert->that(!empty($fields));

		$this->db->setQuery($this->buildInsertQuery($table, $fields));
		$this->db->execute();
	}

	/**
	 * Update a record
	 *
	 * @param   string  $table   The name of the table
	 * @param   array   $fields  The data record
	 * @param   string  $key     The key column
	 *
	 * @startuml
	 * activate SqlModelSql
	 * activate Database
	 * ->> SqlModelSql: update()
	 * SqlModelSql ->> SqlModelSql: buildUpdateQuery()
	 * SqlModelSql ->> Database: setQuery()
	 * SqlModelSql <<-- Database
	 * SqlModelSql ->> Database: execute()
	 * <<-- Database
	 * deactivate Database
	 * deactivate SqlModelSql
	 * @enduml
	 *
	 * @return  void
	 *
	 * @throws  \Celtic\Assertions\AssertionException|\RuntimeException
	 */
	public function update($table, array $fields, $key)
	{
		$this->assert->that(!empty($table));
		$this->assert->that(!empty($fields));

		$this->db->setQuery($this->buildUpdateQuery($table, $fields, $key));
		$this->db->execute();
	}

	/**
	 * Build an INSERT query
	 *
	 * @param   string  $table   The name of the table
	 * @param   array   $fields  Key/value pairs to store in table
	 *
	 * @return  \JDatabaseQuery
	 */
	protected function buildInsertQuery($table, array $fields)
	{
		$query = $this->db->getQuery(true);
		$query->insert($this->db->quoteName($table));
		$columns = array();
		foreach (array_keys($fields) as $column)
		{
			$columns[] = $this->db->quoteName($column);
		}
		$values = array();
		foreach (array_values($fields) as $value)
		{
			$values[] = $this->db->quote($value);
		}
		$query->columns($columns)->values(implode(',', $values));

		return $query;
	}

	/**
	 * Build an UPDATE query
	 *
	 * @param   string  $table   The name of the table
	 * @param   array   $fields  Key/value pairs to store in table
	 * @param   string  $key     The key column
	 *
	 * @return  \JDatabaseQuery
	 */
	protected function buildUpdateQuery($table, $fields, $key)
	{
		$query = $this->db->getQuery(true);
		$query->update($this->db->quoteName($table));
		foreach ($fields as $column => $value)
		{
			$query->set($this->db->quoteName($column) . '=' . $this->db->quote($value));
		}
		$query->where($this->db->quoteName($key) . '=' . $this->db->quote($fields[$key]));

		return $query;
	}

	/**
	 * Get the data
	 *
	 * @param   string  $query  An SQL query
	 * @param   string  $key    The name of the key column
	 *
	 * @startuml
	 * activate SqlModelSql
	 * activate Database
	 * ->> SqlModelSql: getData()
	 * SqlModelSql ->> Database: setQuery()
	 * SqlModelSql <<-- Database
	 * SqlModelSql ->> Database: execute()
	 * SqlModelSql <<-- Database
	 * SqlModelSql ->> Database: loadAssoc()
	 * SqlModelSql <<-- Database: data
	 * <<- SqlModelSql: data
	 * deactivate Database
	 * deactivate SqlModelSql
	 * @enduml
	 *
	 * @return  array
	 */
	public function getData($query, $key)
	{
		if (empty($query) || empty($key))
		{
			return array();
		}
		$this->db->setQuery($query);

		return $this->db->loadAssoc();
	}

	/**
	 * Get the fields of a database table
	 *
	 * The result is an array of objects with the properties
	 * - Field
	 * - Type
	 * - Collation
	 * - Null
	 * - Key
	 * - Default
	 * - Extra
	 * - Privileges
	 * - Comment
	 * for each column of the specified table, indexed by column name..
	 *
	 * @param   string  $table  A table name
	 *
	 * @startuml
	 * activate SqlModelSql
	 * activate Database
	 * ->> SqlModelSql: getFields()
	 * SqlModelSql ->> Database: getTableColumns()
	 * SqlModelSql <<-- Database
	 * <<-- SqlModelSql
	 * deactivate Database
	 * deactivate SqlModelSql
	 * @enduml
	 *
	 * @return  array
	 */
	public function getFields($table)
	{
		if (empty($table))
		{
			return array();
		}
		return $this->db->getTableColumns($table, false);
	}
}
