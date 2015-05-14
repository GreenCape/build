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

class SqlControllerSqlUnitTest extends PHPUnit_Framework_TestCase
{
	/** @var SqlControllerSql */
	private $controller = null;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$version              = new MockFactory();
		$version->input       = $this->getMock('Input', array('get', 'getInt', 'getCmd', 'getString'));
		$this->controller     = new SqlControllerSql($version);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset($this->controller);
	}

	/**
	 * Test if the controller could be instantiated
	 */
	public function testControllerIsInstantiated()
	{
		$this->assertInstanceOf('SqlControllerSql', $this->controller);
	}

	/**
	 * Test if the controller has the proper model
	 */
	public function testControllerHasProperModel()
	{
		$this->assertInstanceOf('SqlModelSql', $this->controller->getModel());
	}
}