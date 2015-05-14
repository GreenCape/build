<?php
/**
 * Celtic Database - SQL Database manager for Joomla!
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,USA.
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @package     Celtic\SqlManager
 * @subpackage  UnitTests
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 */

use Celtic\Abstraction\MockFactory;

class SqlModelSqlUnitTest extends PHPUnit_Framework_TestCase
{
	/** @var MockFactory */
	private $factory;

	/** @var SqlModelSql */
	private $model = null;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->factory = new MockFactory();
		$this->model   = new SqlModelSql($this->factory);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset($this->model);
	}

	public function dataGetTableNameFromQuery()
	{
		return array(
			'short query'         => array("SELECT * FROM mytable",               'mytable'),
			'long query'          => array("SELECT * FROM mytable ORDER BY id",   'mytable'),
			'short query escaped' => array("SELECT * FROM `mytable`",             '`mytable`'),
			'long query escaped'  => array("SELECT * FROM `mytable` ORDER BY id", '`mytable`'),
			'short query with LF' => array("SELECT *\nFROM mytable",              'mytable'),
			'long query with LF'  => array("SELECT *\nFROM mytable\nORDER BY id", 'mytable'),
			'non-query'           => array("This is not an SQL query",            ''),
		);
	}

	/**
	 * @dataProvider dataGetTableNameFromQuery
	 * @param $query
	 * @param $table
	 */
	public function testGetTableNameFromQuery($query, $table)
	{
		$this->assertEquals($table, $this->model->getTableNameFromQuery($query));
	}

	public function dataSplitSql()
	{
		return array(
			'empty query' => array(
				"",
				array()
			),
			'single query' => array(
				"SELECT * FROM my_table",
				array(
					"SELECT * FROM my_table"
				)
			),
			'terminated query' => array(
				"SELECT * FROM my_table;",
				array(
					"SELECT * FROM my_table"
				)
			),
			'query w/semicolon in data' => array(
				"SELECT 'one;two' AS value",
				array(
					"SELECT 'one;two' AS value"
				)
			),
			'two empty queries' => array(
				";;",
				array()
			),
			'two queries' => array(
				"SELECT * FROM my_table;SELECT * FROM another_table",
				array(
					"SELECT * FROM my_table",
					"SELECT * FROM another_table"
				)
			),
		);
	}

	/**
	 * @dataProvider dataSplitSql
	 */
	public function testSplitSql($sql, $expected)
	{
		$this->assertEquals($expected, $this->model->splitSQL($sql));
	}

	/**
	 * @expectedException \Celtic\Assertions\AssertionException
	 */
	public function testInsertFailsOnMissingTableName()
	{
		$this->model->insert(
			'',
			array('id' => '1', 'name' => 'row 1')
		);
	}

	/**
	 * @expectedException \Celtic\Assertions\AssertionException
	 */
	public function testInsertFailsOnEmptyData()
	{
		$this->model->insert(
			'#__testmodel',
			array()
		);
	}

	/**
	 * @expectedException \Celtic\Assertions\AssertionException
	 */
	public function testUpdateFailsOnMissingTableName()
	{
		$this->model->update(
			'',
			array('id' => '1', 'name' => 'row 1'),
			'id'
		);
	}

	/**
	 * @expectedException \Celtic\Assertions\AssertionException
	 */
	public function testUpdateFailsOnEmptyData()
	{
		$this->model->update(
			'#__testmodel',
			array(),
			'id'
		);
	}

	public function dataGetDataWithMissingArguments()
	{
		return array(
			'no query' => array(null, 'id'),
			'no id'    => array('query', null),
			'nothing'  => array(null, null)
		);
	}

	/**
	 * @dataProvider dataGetDataWithMissingArguments
	 */
	public function testGetDataReturnsEmptyArrayOnMissingArguments($query, $id)
	{
		$this->assertEquals(array(), $this->model->getData($query, $id));
	}
}
