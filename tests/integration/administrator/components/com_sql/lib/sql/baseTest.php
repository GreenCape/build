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
 * @package     Celtic\Sql
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

class QueryBuilderBaseIntegrationTest extends PHPUnit_Framework_TestCase
{
	/** @var VersionFactory */
	private $factory;

	/** @var \Celtic\Sql\QueryBuilder */
	private $queryBuilder = null;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->factory = new VersionFactory(JVERSION);
		$db = $this->factory->getDbo();
		$this->queryBuilder  = new \Celtic\Sql\QueryBuilder($db);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset($this->originalQuery, $this->queryBuilder);
	}

	private function normalize($string)
	{
		$replace = array(
			'( ' => '(',
			' )' => ')',
			', ' => ',',
		);
		return trim(
			str_replace(
				array_keys($replace),
				array_values($replace),
				preg_replace('~\s+~m', ' ', $string)
			)
		);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testStatementRejectsUnsupportedClauses()
	{
		$this->queryBuilder->delete()->set('id=1');
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testClauseRejectsUnsupportedClauses()
	{
		$clause = new \Celtic\Sql\FromClause(array());
		$clause->append(new \Celtic\Sql\SetClause());
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testCollectionRejectsUnsupportedClauses()
	{
		$clause = new \Celtic\Sql\ClauseCollection('Celtic\\Sql\\FromClause');
		$clause->append(new \Celtic\Sql\SetClause());
	}

	/**
	 * JDatabaseQuery does not support the table alias
	 */
	public function testSelectFromAliasedTable()
	{
		$this->assertEquals(
			'SELECT a.* FROM foo AS a',
			$this->normalize($this->queryBuilder->select('a.*')->from('foo', 'a'))
		);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testSelectFromSubqueryFailsWithoutAlias()
	{
		if ($this->factory->supportsSubqueries())
		{
			$subQueryB  = new \Celtic\Sql\QueryBuilder($this->factory->getDbo());
			$subQueryB->select('*')->from('b');

			$this->queryBuilder->select('a.*')->from($subQueryB);
		}
		else
		{
			$this->markTestSkipped('This version does not support subqueries');
		}
	}

	public function testWhereWithMultipleCalls()
	{
		$this->assertEquals(
			'SELECT a.* WHERE a.id=1 AND a.id=2 AND a.id=3',
			$this->normalize($this->queryBuilder->select('a.*')->where('a.id=1')->where('a.id=2')->where('a.id=3'))
		);
	}

	public function testWhereWithMultipleOrCalls()
	{
		$this->assertEquals(
			'SELECT a.* WHERE a.id=1 OR a.id=2 OR a.id=3',
			$this->normalize($this->queryBuilder->select('a.*')->where('a.id=1', 'or')->where('a.id=2', 'or')->where('a.id=3', 'or'))
		);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testWhereWithMixedCalls()
	{
		$this->assertEquals(
			'SELECT a.* WHERE a.id=1 OR a.id=2 OR a.id=3',
			$this->normalize($this->queryBuilder->select('a.*')->where('a.id=1', 'or')->where('a.id=2', 'and'))
		);
	}

	public function testHavingWithMultipleCalls()
	{
		$this->assertEquals(
			'SELECT a.* HAVING a.id=1 AND a.id=2 AND a.id=3',
			$this->normalize($this->queryBuilder->select('a.*')->having('a.id=1')->having('a.id=2')->having('a.id=3'))
		);
	}

	public function testHavingWithMultipleOrCalls()
	{
		$this->assertEquals(
			'SELECT a.* HAVING a.id=1 OR a.id=2 OR a.id=3',
			$this->normalize($this->queryBuilder->select('a.*')->having('a.id=1', 'or')->having('a.id=2', 'or')->having('a.id=3', 'or'))
		);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testHavingWithMixedCalls()
	{
		$this->assertEquals(
			'SELECT a.* HAVING a.id=1 OR a.id=2 OR a.id=3',
			$this->normalize($this->queryBuilder->select('a.*')->having('a.id=1', 'and')->having('a.id=2', 'or'))
		);
	}

	public function testLimit()
	{
		$this->assertEquals(
			'SELECT a.* FROM foo LIMIT 10,0',
			$this->normalize($this->queryBuilder->select('a.*')->from('foo')->limit(10, 0))
		);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testLimitCanOnlyBeSetOnce()
	{
			$this->queryBuilder->select('a.*')->from('foo')->limit(10, 0)->limit(20,10);
	}

	/**
	 * Delete Example 1 from the manual
	 *
	 * @see http://dev.mysql.com/doc/refman/5.5/en/delete.html
	 */
	public function testDeleteExample1()
	{
		$this->assertEquals(
			"DELETE FROM somelog WHERE user = 'jcole' ORDER BY timestamp_column LIMIT 1",
			$this->normalize(
				$this->queryBuilder
				->delete('somelog')
				->where("user = 'jcole'")
				->order('timestamp_column')
				->limit(1)
			)
		);
	}

	/**
	 * Delete Example 2 from the manual
	 *
	 * @see http://dev.mysql.com/doc/refman/5.5/en/delete.html
	 */
	public function disabled_testDeleteExample2()
	{
		$this->assertEquals(
			"DELETE t1, t2 FROM t1 INNER JOIN t2 INNER JOIN t3 WHERE t1.id=t2.id AND t2.id=t3.id;",
			$this->normalize(
				$this->queryBuilder
				->delete('t1')
				->delete('t2')
				->from('t1')
				->innerJoin('t2')
				->innerJoin('t3')
				->where("t1.id=t2.id", 'and')
				->where('t2.id=t3.id')
			)
		);
	}
}
