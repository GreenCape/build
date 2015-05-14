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

class SqlViewSqlIntegrationTest extends PHPUnit_Framework_TestCase
{
	/** @var SqlModelSql */
	private $model = null;

	/** @var SqlViewSql */
	private $view = null;

	/** @var VersionFactory */
	private $factory = null;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->factory = new VersionFactory(JVERSION);
		$this->model   = new SqlModelSql($this->factory);
		$this->view    = new SqlViewSql(
			$this->factory,
			$this->model,
			array(
				'layout' => 'default'
			)
		);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset($this->model, $this->view);
	}

	public function testThemesSetup()
	{
		$this->assertRegExp('~/administrator/templates$~', JPATH_THEMES);
	}

	public function testDisplayWithoutDataOutputContainsForm()
	{
		$this->view->document = $this->factory->getDocument();

		ob_start();
		$this->view->display();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertRegExp('~<form.*?</form>~sim', $result, 'Output does not contain a form');
	}

	public function testDisplayWithDataOutputContainsForm()
	{
		$table = new TestTable('#__testmodel');
		$table->execute("INSERT INTO #__testmodel (id, name) VALUES (1, 'row 1'), (2, 'row 2')");

		$this->model->setQuery("SELECT * FROM #__testmodel");

		$this->view->document = $this->factory->getDocument();
		$this->view->command  = "SELECT * FROM";

		ob_start();
		$this->view->display();
		$result = ob_get_contents();
		ob_end_clean();

		$this->assertRegExp('~<form.*?</form>~sim', $result, 'Output does not contain a form');
	}
}
