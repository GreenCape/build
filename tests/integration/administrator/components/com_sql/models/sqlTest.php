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
 * @subpackage  IntegrationTests
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 */

use Celtic\Abstraction\VersionFactory;

// Run in backend (administrator)
$mainframe = \JFactory::getApplication('administrator');
$mainframe->initialise();

require_once JPATH_ADMINISTRATOR . '/components/com_sql/autoload.php';
if (!defined('JPATH_COMPONENT')) {
	define('JPATH_COMPONENT', JPATH_ADMINISTRATOR . '/components/com_sql');
}
require_once dirname(__DIR__) . '/tables/testtable.php';

class SqlModelSqlIntegrationTest extends PHPUnit_Framework_TestCase
{
	/** @var VersionFactory */
	private $factory;

	/** @var SqlModelSql */
	private $model = null;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->factory = new VersionFactory(JVERSION);
		$this->model = new SqlModelSql($this->factory);
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

	public function testGetPrefix()
	{
		$this->assertEquals('test_', $this->model->getPrefix());
	}

	public function dataIsTable()
	{
		return array(
			array('#__users', true),
			array('test_users', true),
			array('#__qwertz', false)
		);
	}

	/**
	 * @dataProvider dataIsTable
	 */
	public function testIsTable($table, $expected)
	{
		$this->assertEquals($expected, $this->model->isTable($table));
	}

	public function dataGetTableKey()
	{
		return array(
			'languages' => array('#__languages', 'lang_id'),
			'menu'      => array('#__menu', 'id'),
			'testmodel' => array('#__testmodel', 'id'),
		);
	}

	/**
	 * @dataProvider dataGetTableKey
	 */
	public function testGetTableKey($tablename, $key)
	{
		// The language table is not present in J1.5, so we have to mock it temporarily
		$languageTable = new TestTable('test_languages', 'lang_name VARCHAR(255), lang_id int UNIQUE');

		$table = new TestTable('#__testmodel');

		$this->assertEquals($key, $this->model->getTableKey($tablename));

		unset($table, $languageTable);
	}

	public function testGetDataWithEmptyTable()
	{
		$table = new TestTable('#__testmodel');

		$this->model->setQuery("SELECT * FROM #__testmodel");

		$expected = array();
		$expected[0] = new QueryResult("SELECT * FROM #__testmodel");
		$expected[0]->table = '#__testmodel';
		$expected[0]->rows = array();
		$expected[0]->total = 0;
		$expected[0]->key = null;
		$expected[0]->message = null;

		$result = $this->model->getQueryResults();
		$this->assertEquals($expected, $result);
		$this->assertTrue($result[0]->isSelect());
		$this->assertTrue($result[0]->hasData());

		unset($table);
	}

	public function testGetDataWithASingleQuery()
	{
		$table = new TestTable('#__testmodel');
		$table->execute("INSERT INTO #__testmodel (id, name) VALUES (1, 'row 1'), (2, 'row 2')");

		$this->model->setQuery("SELECT * FROM #__testmodel");

		$expected = array();
		$expected[0] = new QueryResult("SELECT * FROM #__testmodel");
		$expected[0]->table = '#__testmodel';
		$expected[0]->rows = array(
			array('id' => '1', 'name' => 'row 1'),
			array('id' => '2', 'name' => 'row 2'),
		);
		$expected[0]->total = 2;
		$expected[0]->key = 'id';
		$expected[0]->message = null;

		$result = $this->model->getQueryResults();
		$this->assertEquals($expected, $result);
		$this->assertTrue($result[0]->isSelect());
		$this->assertTrue($result[0]->hasData());

		unset($table);
	}

	public function testGetDataWithTwoQueries()
	{
		$table = new TestTable('#__testmodel');
		$table->execute("INSERT INTO #__testmodel (id, name) VALUES (1, 'row 1'), (2, 'row 2')");

		$this->model->setQuery("SELECT * FROM #__testmodel; SELECT '1;2' AS test;");

		$expected = array();
		$expected[0] = new QueryResult("SELECT * FROM #__testmodel");
		$expected[0]->table = '#__testmodel';
		$expected[0]->rows = array(
			array('id' => '1', 'name' => 'row 1'),
			array('id' => '2', 'name' => 'row 2')
		);
		$expected[0]->total = 2;
		$expected[0]->key = 'id';
		$expected[0]->message = null;

		$expected[1] = new QueryResult("SELECT '1;2' AS test");
		$expected[1]->table = '';
		$expected[1]->rows = array(
			array('test' => '1;2')
		);
		$expected[1]->total = 1;
		$expected[1]->key = 'test';
		$expected[1]->message = null;

		$result = $this->model->getQueryResults();
		$this->assertEquals($expected, $result);
		$this->assertTrue($result[0]->isSelect());
		$this->assertTrue($result[0]->hasData());
		$this->assertTrue($result[1]->isSelect());
		$this->assertTrue($result[1]->hasData());

		unset($table);
	}

	public function testGetDataWithDeleteAndSelect()
	{
		$table = new TestTable('#__testmodel');
		$table->execute("INSERT INTO #__testmodel (id, name) VALUES (1, 'row 1'), (2, 'row 2')");

		$this->model->setQuery("DELETE FROM #__testmodel WHERE id=1; SELECT * FROM #__testmodel;");

		$expected = array();
		$expected[0] = new QueryResult("DELETE FROM #__testmodel WHERE id=1");
		$expected[0]->table = '#__testmodel';
		$expected[0]->rows = array();
		$expected[0]->total = 1;
		$expected[0]->key = null;
		$expected[0]->message = null;

		$expected[1] = new QueryResult("SELECT * FROM #__testmodel");
		$expected[1]->table = '#__testmodel';
		$expected[1]->rows = array(
			array('id' => '2', 'name' => 'row 2')
		);
		$expected[1]->total = 1;
		$expected[1]->key = 'id';
		$expected[1]->message = null;

		$result = $this->model->getQueryResults();
		$this->assertEquals($expected, $result);
		$this->assertFalse($result[0]->isSelect());
		$this->assertFalse($result[0]->hasData());
		$this->assertTrue($result[1]->isSelect());
		$this->assertTrue($result[1]->hasData());

		unset($table);
	}

	public function testDelete()
	{
		$table = new TestTable('#__testmodel');
		$table->execute("INSERT INTO #__testmodel (id, name) VALUES (1, 'row 1'), (2, 'row 2')");

		$this->model->delete('#__testmodel', 'id', 1);

		$expected = array(
			'2' => array(
				'id' => '2',
				'name' => 'row 2'
			)
		);
		$this->assertEquals($expected, $table->select('#__testmodel'));

		unset($table);
	}

	public function testGetDataReturnsOnSyntaxError()
	{
		$this->model->setQuery("ILLEGAL SYNTAX");

		$expected = array();
		$expected[0] = new QueryResult("ILLEGAL SYNTAX");
		$expected[0]->table = '';
		$expected[0]->rows = array();
		$expected[0]->total = 0;
		$expected[0]->key = null;
		$expected[0]->message = null;

		$result = $this->model->getQueryResults();
		$this->assertRegExp('~you have an error in your sql syntax~i', $result[0]->message);
		$this->assertFalse($result[0]->isSelect());
		$this->assertFalse($result[0]->hasData());
	}

	public function testExportToCsvWithDefaultSettings()
	{
		$table = new TestTable('#__testmodel');
		$table->execute("INSERT INTO #__testmodel (id, name) VALUES (1, 'row \"1\"'), (2, 'row,2')");

		$query = "SELECT * FROM #__testmodel";

		$expected = "id,name\n1,row \"\"1\"\"\n2,\"row,2\"\n";

		$result = $this->model->exportToCsv($query);

		$this->assertEquals($expected, $result);

		unset($table);
	}

	public function testExportToCsvWithEmptyData()
	{
		$table = new TestTable('#__testmodel');

		$query = "SELECT * FROM #__testmodel";

		$this->assertEquals("", $this->model->exportToCsv($query));

		unset($table);
	}

	public function testInsert()
	{
		$table = new TestTable('#__testmodel');
		$this->model->insert(
			'#__testmodel',
			array('id' => '1', 'name' => 'row 1')
		);
		$rows = $table->select();
		$this->assertEquals('row 1', $rows[1]['name']);
		unset($table);
	}

	public function testUpdate()
	{
		$table = new TestTable('#__testmodel');
		$table->execute("INSERT INTO #__testmodel (id, name) VALUES (1, 'row 1'), (2, 'row 2')");
		$this->model->update(
			'#__testmodel',
			array('id' => '1', 'name' => 'new name'),
			'id'
		);
		$rows = $table->select();
		$this->assertEquals('new name', $rows[1]['name']);
		$this->assertEquals('row 2',    $rows[2]['name']);
		unset($table);
	}

	public function testGetData()
	{
		$table = new TestTable('#__testmodel');
		$table->execute("INSERT INTO #__testmodel (id, name) VALUES (1, 'row 1'), (2, 'row 2')");
		$data = $this->model->getData('SELECT * FROM #__testmodel', 'id');
		$this->assertEquals(1, $data['id']);
		$this->assertEquals('row 1', $data['name']);
		unset($table);
	}

	public function testGetFields()
	{
		$table = new TestTable('#__testmodel');
		$table->execute("INSERT INTO #__testmodel (id, name) VALUES (1, 'row 1'), (2, 'row 2')");
		$data = $this->model->getFields('#__testmodel');
		$this->assertEquals('id', $data['id']->Field);
		$this->assertRegExp('~^int~i', $data['id']->Type);
		$this->assertEquals('name', $data['name']->Field);
		$this->assertRegExp('~varchar~i', $data['name']->Type);
		unset($table);
	}

	public function testGetFieldsWithEmptyTableName()
	{
		$data = $this->model->getFields('');
		$this->assertEquals(array(), $data);
	}
}
