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

class SqlTableQueryIntegrationTest extends PHPUnit_Framework_TestCase
{
	/** @var SqlTableQuery */
	private $table = null;

	/** @var SqlQueriesTable */
	private $mockTable;

	/** @var VersionFactory */
	private $factory;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->factory   = new VersionFactory(JVERSION);
		$this->mockTable = new SqlQueriesTable($this->factory);
		$this->mockTable->insert(
			array(
				array(1, 'Show all queries', 'SELECT * FROM #__sql_queries'),
				array(2, 'Show all users', 'SELECT * FROM #__users'),
			)
		);
		$this->table = new SqlTableQuery($this->factory->getDbo());
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset($this->mockTable, $this->table);
	}

	public function testBindWithArray()
	{
		$data = array(
			'id'    => 3,
			'title' => 'Test Entry',
			'query' => 'SELECT * FROM #__sql_queries'
		);
		$this->table->bind($data);
		$this->assertEquals($data, $this->filterUnderscore(get_object_vars($this->table)));
	}

	public function testBindWithObject()
	{
		$data = new stdClass;
		$data->id    = 3;
		$data->title = 'Test Entry';
		$data->query = 'SELECT * FROM #__sql_queries';

		$this->table->bind($data);
		$this->assertEquals(get_object_vars($data), $this->filterUnderscore(get_object_vars($this->table)));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testBindWithBadData()
	{
		$this->table->bind('bad data');
	}

	/**
	 * @expectedException \UnexpectedValueException
	 */
	public function testCheckFailsWithEmptyTitle()
	{
		$data = new stdClass;
		$data->id    = 3;
		$data->title = '';
		$data->query = 'SELECT * FROM #__sql_queries';

		$this->table->bind($data);
		$this->table->check();
	}

	protected function filterUnderscore($unfilteredArray)
	{
		$filteredArray = array();
		foreach ($unfilteredArray as $key => $value)
		{
			if (is_string($key) && strlen($key) > 0 && $key[0] != '_')
			{
				$filteredArray[$key] = $value;
			}
		}
		return $filteredArray;
	}
}
