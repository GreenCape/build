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

class SqlHelperUnitTest extends PHPUnit_Framework_TestCase
{
	/** @var MockFactory */
	private $factory;

	/** @var SqlHelper */
	private $helper;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->factory = new MockFactory;
		$this->helper = new SqlHelper($this->factory);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	public function dataRenderHtmlInput()
	{
		return array(
			/*
			 * SQL types as returned by SHOW COLUMNS
			 * HTML control element
			 * HTML control element type
			 */
			'char(7)'             => array('char(7)',             'text'),
			'char(32)'            => array('char(32)',            'text'),
			'datetime'            => array('datetime',            'text'),
			'int(10) unsigned'    => array('int(10) unsigned',    'text'),
			'int(11)'             => array('int(11)',             'text'),
			'int(11) unsigned'    => array('int(11) unsigned',    'text'),
			'tinyint(1)'          => array('tinyint(1)',          'text'),
			'tinyint(1) unsigned' => array('tinyint(1) unsigned', 'text'),
			'tinyint(3)'          => array('tinyint(3)',          'text'),
			'tinyint(4)'          => array('tinyint(4)',          'text'),
			'varchar(50)'         => array('varchar(50)',         'text'),
			'varchar(100)'        => array('varchar(100)',        'text'),
			// Internal types
			'hidden'              => array('hidden',              'hidden'),
			'disabled'            => array('disabled',            'text'),
		);
	}

	/**
	 * @dataProvider dataRenderHtmlInput
	 */
	public function testRenderHtmlInput($sqlType, $elementType)
	{
		$html = $this->helper->renderHtml('name', $sqlType, 'value of the field');
		$node = $this->createDomNodeFromString($html);

		// Is it the right tag?
		$this->assertEquals('input', (string) $node->nodeName);

		// Is it the right type?
		$this->assertEquals($elementType, (string) $node->attributes->getNamedItem('type')->nodeValue);

		// Does it have the right name?
		$this->assertEquals('fields[name]', (string) $node->attributes->getNamedItem('name')->nodeValue);

		// Does it have the right value?
		$this->assertEquals('value of the field', $node->attributes->getNamedItem('value')->nodeValue);
	}

	public function dataRenderHtmlTextarea()
	{
		return array(
			/*
			 * SQL types as returned by SHOW COLUMNS
			 */
			'mediumtext'    => array('mediumtext'),
			'text'          => array('text'),
			'varchar(200)'  => array('varchar(200)'),
			'varchar(255)'  => array('varchar(255)'),
			'varchar(1024)' => array('varchar(1024)'),
			'varchar(2048)' => array('varchar(2048)'),
			'varchar(5120)' => array('varchar(5120)'),
		);
	}

	/**
	 * @dataProvider dataRenderHtmlTextarea
	 */
	public function testRenderHtmlTextarea($sqlType)
	{
		$html = $this->helper->renderHtml('name', $sqlType, 'value of the field');
		$node = $this->createDomNodeFromString($html);

		// Is it the right tag?
		$this->assertEquals('textarea', (string) $node->nodeName);

		// Does it have the right name?
		$this->assertEquals('fields[name]', (string) $node->attributes->getNamedItem('name')->nodeValue);

		// Does it have the right value?
		$this->assertEquals('value of the field', (string) $node->nodeValue);
	}

	/**
	 * @param   string  $string  An HTML element
	 *
	 * @return  DOMNode
	 */
	private function createDomNodeFromString($string)
	{
		$dom = new DOMDocument();
		$dom->loadXML($string);
		return $dom->firstChild;
	}

	public function dataIsActiveSubMenu()
	{
		return array(
			// current_view, checked_view, expected
			'edit-sql'        => array('edit',    'sql',     false),
			'edit-queries'    => array('edit',    'queries', false),
			'sql-sql'         => array('sql',     'sql',     true),
			'sql-queries'     => array('sql',     'queries', false),
			'queries-sql'     => array('queries', 'sql',     false),
			'queries-queries' => array('queries', 'queries', true),
		);
	}

	/**
	 * @dataProvider dataIsActiveSubMenu
	 */
	public function testIsActiveSubMenu($current, $checked, $expected)
	{
		$input = $this->getMock('Celtic\\Abstraction\\Input', array('getCmd'));
		$input->expects($this->once())
			->method('getCmd')
			->with(
				$this->equalTo('view'),
				$this->equalTo('sql')
			)
			->will($this->returnValue($current))
		;
		$this->factory->input = $input;
		$this->assertEquals($expected, $this->helper->isActiveSubMenu($checked));
	}

	public function dataBuildQueryVars()
	{
		return array(
			array('my_table', 'SELECT * FROM my_table', null, null, '&table=my_table&query=SELECT+%2A+FROM+my_table'),
			array('my_table', 'SELECT * FROM my_table', null, 1,    '&table=my_table&query=SELECT+%2A+FROM+my_table'),
			array('my_table', 'SELECT * FROM my_table', 'id', 1,    '&table=my_table&query=SELECT+%2A+FROM+my_table&key=id&id=1'),
		);
	}

	/**
	 * @dataProvider dataBuildQueryVars
	 */
	public function testBuildQueryVars($table, $query, $key, $id, $expected)
	{
		$this->assertEquals($expected, $this->helper->buildQueryVars($table, $query, $key, $id));
	}
}
