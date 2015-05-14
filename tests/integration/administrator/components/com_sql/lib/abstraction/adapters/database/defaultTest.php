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

use Celtic\Abstraction\DefaultDatabaseDecorator;
use Celtic\Abstraction\VersionFactory;

// Run in backend (administrator)
$mainframe = \JFactory::getApplication('administrator');
$mainframe->initialise();

require_once JPATH_ADMINISTRATOR . '/components/com_sql/autoload.php';
if (!defined('JPATH_COMPONENT')) {
	define('JPATH_COMPONENT', JPATH_ADMINISTRATOR . '/components/com_sql');
}

class DefaultDatabaseDecoratorIntegrationTest extends PHPUnit_Framework_TestCase
{
	/** @var VersionFactory */
	private $factory;

	/** @var DefaultDatabaseDecorator|\JDatabase */
	private $subject;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->factory = new VersionFactory(JVERSION);
		$this->subject = $this->factory->getDbo();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset($this->subject, $this->factory);
	}

	public function disabled_testClass()
	{
		$this->assertEquals(
			JVERSION,
			get_class($this->subject)
		);
	}

	public function dataQuoteName()
	{
		return array(
			array(
				'name'     => 'a',
				'as'       => null,
				'expected' => '`a`'
			),
			array(
				'name'     => 'foo',
				'as'       => 'f',
				'expected' => '`foo` AS `f`'
			),
			array(
				'name'     => 't1.a',
				'as'       => null,
				'expected' => '`t1`.`a`'
			),
			array(
				'name'     => 't1.foo',
				'as'       => 'f',
				'expected' => '`t1`.`foo` AS `f`'
			),
			array(
				'name'     => array('foo', 'bar'),
				'as'       => null,
				'expected' => array('`foo`', '`bar`')
			),
			array(
				'name'     => array('foo', 'bar'),
				'as'       => array('f', 'b'),
				'expected' => array('`foo` AS `f`', '`bar` AS `b`')
			),
		);
	}

	/**
	 * @dataProvider dataQuoteName
	 *
	 * @param $name
	 * @param $as
	 * @param $expected
	 */
	public function testQuoteName($name, $as, $expected)
	{
		$this->assertEquals(
			$expected,
			$this->subject->quoteName($name, $as)
		);
	}
}
