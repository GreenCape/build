<?php

use PHPUnit_Extensions_Selenium2TestCase_Element as Element;

require_once dirname(__DIR__) . '/autoload.php';

class InstallationSystemTest extends Celtic\Testing\Joomla\SeleniumTestCase
{
	public function testSetupIsWorking()
	{
		$page = $this->loginToBackend('superadmin', 'test');

		$this->assertRegExp('/Admin_CPanelPage$/', get_class($page));

		$page->logout();
	}

	/**
	 * @depends testSetupIsWorking
	 */
	public function testInstallation()
	{
		$packageUrl = 'http://localhost/~nibra/com_sql-1.0.0.zip';

		/** @var \Celtic\Testing\Joomla\Joomla3_Admin_ExtensionManager_InstallPage $page */
		$page = $this->loginToBackend('superadmin', 'test')->menu->select('Extension Manager');
		$page = $page->installFromUrl($packageUrl);

		$output = $page->output();

		$this->assertEquals(
			'Celtic Database Manager',
			$output->byTag('h1')->text()
		);

		$link = $output->byXPath("//a[contains(@href, 'bsds.de')]");

		$this->assertStringEndsWith(
			'celtic.png',
			$link->byTag('img')->attribute('src'),
			"Celtic logo is missing (or not linked to bsds.de)."
		);

		$page->logout();
	}

	/**
	 * @depends testInstallation
	 */
	public function testAdminMenuIsPresent()
	{
		$page = $this->loginToBackend('superadmin', 'test');

		$this->debug("Checking Components/Database Manager\n");
		$this->assertTrue(
			$page->menu->itemExists('Components/Database Manager'),
			'Main menu entry is missing.'
		);

		$this->debug("Checking Components/Database Manager/Run Query\n");
		$this->assertTrue(
			$page->menu->itemExists('Components/Database Manager/Run Query'),
			'Sub menu entry for sql view is missing.'
		);

		$this->debug("Checking Components/Database Manager/Saved Queries\n");
		$this->assertTrue(
			$page->menu->itemExists('Components/Database Manager/Saved Queries'),
			'Sub menu entry for queries view is missing.'
		);

		$page->logout();
	}
}
