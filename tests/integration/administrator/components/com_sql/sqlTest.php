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
use Celtic\Assertions\MockAssert;

// Run in backend (administrator)
$mainframe = \JFactory::getApplication('administrator');
$mainframe->initialise();

require_once JPATH_ADMINISTRATOR . '/components/com_sql/autoload.php';
if (!defined('JPATH_COMPONENT')) {
	define('JPATH_COMPONENT', JPATH_ADMINISTRATOR . '/components/com_sql');
}

require_once dirname(JPATH_ROOT) . '/tests/autoload.php';

class SqlIntegrationTest extends PHPUnit_Framework_TestCase
{
	/** @var VersionFactory */
	private $factory = null;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->factory = new VersionFactory(JVERSION);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	/**
	 * @expectedException Celtic\Assertions\NotAuthorizedException
	 */
	public function testAccessIsDeniedWithoutPermission()
	{
		if ($this->factory->supportsAcl())
		{
			$_REQUEST['view'] = $_GET['view'] = null;
			ob_start();
			include JPATH_COMPONENT . '/sql.php';
			$result = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			$this->markTestSkipped('This version does not support ACL');
		}
	}

	public function testDefaultViewContainsQueryField()
	{
		$factory = $this->factory;

		$assertions = new MockAssert;
		$assertions->userCanManage = true;

		$factory->setAssertions($assertions);

		$_REQUEST['view'] = $_GET['view'] = null;
		ob_start();
		include JPATH_COMPONENT . '/sql.php';
		$result = ob_get_contents();
		ob_end_clean();

		$dom = new DOMDocument;
		$dom->loadHTML($result);

		$query = $dom->getElementById('query');
		$this->assertEquals('textarea', (string) $query->nodeName);
	}
}
