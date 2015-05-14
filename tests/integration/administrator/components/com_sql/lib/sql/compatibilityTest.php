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

class QueryBuilderCompatibilityIntegrationTest extends PHPUnit_Framework_TestCase
{
	/** @var VersionFactory */
	private $factory;

	/** @var \JDatabaseQuery */
	private $originalQuery = null;

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
		$this->originalQuery = $db->getQuery(true);
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

	public function testDelete()
	{
		$setup = function($query)
		{
			return $query->delete();
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testDeleteWithTable()
	{
		$setup = function($query)
		{
			return $query->delete('foo');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testDeleteWithFrom()
	{
		$setup = function($query)
		{
			return $query->delete()->from('foo');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testInsert()
	{
		$setup = function($query)
		{
			return $query->insert('foo');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testInsertWithSingleRecord()
	{
		$setup = function($query)
		{
			return $query->insert('foo')->columns('id, name')->values("1, 'row 1'");
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testInsertWithValuesCalledTwice()
	{
		$setup = function($query)
		{
			return $query->insert('foo')->values("1, 'row 1'")->values("2, 'row 2'");
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testInsertWithTwoRecordsByArray()
	{
		$setup = function($query)
		{
			return $query->insert('foo')->values(array("1, 'row 1'", "2, 'row 2'"));
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testInsertWithSet()
	{
		$setup = function($query)
		{
			return $query->insert('foo')->set("id=1, name='row 1");
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testUpdate()
	{
		$setup = function($query)
		{
			return $query->update('foo');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testUpdateWithSet()
	{
		$setup = function($query)
		{
			return $query->update('foo')->set("id=1, name='row 1");
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testWhere()
	{
		$setup = function($query)
		{
			return $query->update('foo')->where("id=1");
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testWhereWithSingleOr()
	{
		$setup = function($query)
		{
			return $query->update('foo')->where("id=1", 'or');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testWhereWithAnd()
	{
		$setup = function($query)
		{
			return $query->update('foo')->where(array("id=1", "name='row 1'"), 'and');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testWhereWithOr()
	{
		$setup = function($query)
		{
			return $query->update('foo')->where(array("id=1", "name='row 1'"), 'or');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testWhereCalledTwice()
	{
		$setup = function($query)
		{
			return $query->update('foo')->where("id=1")->where("name='row 1'");
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testSelectFromUnaliasedTable()
	{
		$setup = function($query)
		{
			return $query->select('a.*')->from('foo');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testSelectFromSubquery()
	{
		if ($this->factory->supportsSubqueries())
		{
			$db = $this->factory->getDbo();
			$subQueryA = $db->getQuery(true);
			$subQueryA->select('*')->from('b');

			$subQueryB  = new \Celtic\Sql\QueryBuilder($db);
			$subQueryB->select('*')->from('b');

			$this->assertEquals(
				// JDatabaseQuery is in-consequent with quoting. Should be either always or never.
				$this->normalize($this->originalQuery->select('a.*')->from($subQueryA, 'a')),
				$this->normalize($this->queryBuilder->select('a.*')->from($subQueryB, '`a`'))
			);
		}
		else
		{
			$this->markTestSkipped('This version does not support subqueries');
		}
	}

	public function testSelectCalledTwice()
	{
		$setup = function($query)
		{
			return $query->select('a.*')->select('b.id');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testSelectWithArray()
	{
		$setup = function($query)
		{
			return $query->select(array('a.*', 'b.id'));
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testJoin()
	{
		$setup = function($query)
		{
			return $query->select('a.*')->join('INNER', 'b ON b.id = a.id');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testJoinTwoLeft()
	{
		$setup = function($query)
		{
			return $query->select('a.*')->leftJoin('b ON b.id = a.id')->leftJoin('c ON c.id = b.id');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testJoinInner()
	{
		$setup = function($query)
		{
			return $query->select('a.*')->innerJoin('b ON b.id = a.id')->innerJoin('c ON c.id = b.id');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testJoinOuter()
	{
		$setup = function($query)
		{
			return $query->select('a.*')->outerJoin('b ON b.id = a.id')->outerJoin('c ON c.id = b.id');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testJoinRight()
	{
		$setup = function($query)
		{
			return $query->select('a.*')->rightJoin('b ON b.id = a.id')->rightJoin('c ON c.id = b.id');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testGroup()
	{
		$setup = function($query)
		{
			return $query->select('a.*')->group('id');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testHaving()
	{
		$setup = function($query)
		{
			return $query->select('a.*')->group('id')->having('COUNT(id) > 5');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testHavingCalledTwice()
	{
		$setup = function($query)
		{
			return $query->select('a.*')->group('id')->having('COUNT(id) > 5')->having('MAX(id) > 5');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testOrderCalledTwice()
	{
		$setup = function($query)
		{
			return $query->select('a.*')->order('foo')->order('bar');
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}

	public function testOrderCalledWithArray()
	{
		$setup = function($query)
		{
			return $query->select('a.*')->order(array('foo','bar'));
		};
		$this->assertEquals(
			$this->normalize($setup($this->originalQuery)),
			$this->normalize($setup($this->queryBuilder))
		);
	}
}
