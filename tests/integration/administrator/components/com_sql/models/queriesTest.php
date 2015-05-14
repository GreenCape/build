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
require_once dirname(__DIR__) . '/tables/queries.php';

class SqlModelQueriesIntegrationTest extends PHPUnit_Framework_TestCase
{
	/** @var SqlModelQueries */
	private $model = null;

	/** @var SqlQueriesTable */
	private $table;

	/** @var VersionFactory */
	private $factory;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->factory = new VersionFactory(JVERSION);
		$this->model = new SqlModelQueries($this->factory);
		$this->table = new SqlQueriesTable($this->factory);
		$this->table->insert(
			array(
				array(1, 'Show all queries', 'SELECT * FROM #__sql_queries'),
				array(2, 'Show all users', 'SELECT * FROM #__users'),
			)
		);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset($this->model, $this->table);
	}

	public function testGetData()
	{
		$expected = array (
			1 => (object) array (
				'id' => '1',
				'title' => 'Show all queries',
				'query' => 'SELECT * FROM #__sql_queries',
			),
			2 => (object) array (
				'id' => '2',
				'title' => 'Show all users',
				'query' => 'SELECT * FROM #__users',
			),
		);

		$this->assertEquals($expected, $this->model->getData());
		$this->assertEquals(count($expected), $this->model->getTotal());
	}

	public function testGetTotal()
	{
		$expected = array (
			1 => (object) array (
				'id' => '1',
				'title' => 'Show all queries',
				'query' => 'SELECT * FROM #__sql_queries',
			),
			2 => (object) array (
				'id' => '2',
				'title' => 'Show all users',
				'query' => 'SELECT * FROM #__users',
			),
		);

		$this->assertEquals(count($expected), $this->model->getTotal());
		$this->assertEquals($expected, $this->model->getData());
	}

	public function testGetPagination()
	{
		$this->assertInstanceOf('JPagination', $this->model->getPagination());
	}

	public function testDeleteOneRecord()
	{
		$this->model->delete(array(1));

		$expected = array(
			2 => array (
				'id' => '2',
				'title' => 'Show all users',
				'query' => 'SELECT * FROM #__users',
			),
		);

		$this->assertEquals($expected, $this->table->fetch("SELECT * FROM #__sql_queries"));
	}

	public function testDeleteMultipleRecords()
	{
		$this->model->delete(array(1, 2));

		$this->assertEquals(array(), $this->table->fetch("SELECT * FROM #__sql_queries"));
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testDeleteFailsOnInvalidKey()
	{
		$this->model->delete(array('foo'));
	}

	public function testInternalStateCanBeSetAndRetrieved()
	{
		$this->model->setState('any_state', 'value');

		$this->assertEquals('value', $this->model->getState('any_state'));
	}

	public function testInternalSearchStateCanBeSetAndRetrieved()
	{
		$this->model->setState('search', 'value');

		$this->assertEquals('value', $this->model->getState('search'));
	}

	public function testUndefinedStateReturnsNull()
	{
		$this->assertEquals(null, $this->model->getState('undefined_state'));
	}

	/**
	 * Joomla 1.5 does not support default values for getState()
	 */
	public function disabled_testUndefinedStateReturnsDefaultIfSet()
	{
		$this->assertEquals('default', $this->model->getState('undefined_state', 'default'));
	}

	public function dataSearchTerms()
	{
		return array(
			'case independent' => array('show', array('Show all queries', 'Show all users')),
			'partial string'   => array('user', array('Show all users')),
			'complete string'  => array('Show all users', array('Show all users')),
		);
	}

	/**
	 * @dataProvider dataSearchTerms
	 */
	public function testSearchTermIsUtilized($searchTerm, $expected)
	{
		$this->model->setState('search', $searchTerm);

		$data = $this->model->getData();
		$this->assertEquals(count($expected), $this->model->getTotal());
		foreach ($data as $record) {
			$this->assertTrue(in_array($record->title, $expected));
		}
	}
}
