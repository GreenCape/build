<?php

require_once dirname(__DIR__) . '/autoload.php';

class SqlViewSystemTest extends Celtic\Testing\Joomla\SeleniumTestCase
{
	/** @var array  */
	public $menuMapExtension = array(
		'Components/Database Manager' => 'Components_DatabaseManager_SqlDefaultView',
		'Components/Database Manager/Run Query' => 'Components_DatabaseManager_SqlDefaultView',
		'Components/Database Manager/Saved Query' => 'Components_DatabaseManager_QueriesDefaultView',
	);

	public function testPageSetup()
	{
		/** @var \Celtic\Testing\Joomla\Joomla3_Admin_Page $page */
		$page = $this->loginToBackend();
		$page = $page->menu->select('Components/Database Manager/Run Query');

		$this->assertInstanceOf(
			'Components_DatabaseManager_SqlDefaultView',
			$page
		);

		$page->logout();
	}

	/**
	 * COM_SQL_RUN_QUERY="Run Query"
	 * COM_SQL_SAVE_QUERY="Save Query"
	 * COM_SQL_EXPORT_CSV="Export CSV"
	 * @depends testPageSetup
	 */
	public function testToolbarIsSetupCorrectly()
	{
		/** @var \Celtic\Testing\Joomla\Joomla3_Admin_Page $page */
		$page = $this->loginToBackend();
		$page = $page->menu->select('Components/Database Manager/Run Query');

		$this->assertTrue(
			$page->toolbar->itemExists("Run Query"),
			"Toolbar entry for 'Run Query' is missing."
		);
		$this->assertTrue(
			$page->toolbar->itemExists("Save Query"),
			"Toolbar entry for 'Save Query' is missing."
		);
		$this->assertTrue(
			$page->toolbar->itemExists("Export CSV"),
			"Toolbar entry for 'Export CSV' is missing."
		);

		$page->logout();
	}

	/**
	 * @depends testPageSetup
	 */
	public function testDisplay()
	{
		/** @var \Celtic\Testing\Joomla\Admin_Page $page */
		$page = $this->loginToBackend();
		/** @var Components_DatabaseManager_SqlDefaultView $page */
		$page = $page->menu->select('Components/Database Manager/Run Query');

		$query = "SELECT * FROM #__content ORDER BY hits DESC LIMIT 10";

		$queryInput = $page->queryInput();
		$queryInput->clear();
		$queryInput->value($query);
		$page = $page->toolbar->select("Run Query");

		$this->assertInstanceOf(
			'Components_DatabaseManager_SqlDefaultView',
			$page
		);

		$this->assertEquals(
			'Query Results',
			$page->getElement("css selector:#adminForm h1")->text(),
			"Missing headline"
		);
		$this->assertEquals(
			$query,
			$page->getElement("css selector:div.query-result code.sql")->text(),
			"Missing query as part of the result"
		);
		$this->assertEquals(
			'(10 records)',
			$page->getElement("css selector:div.query-result span.total")->text(),
			"Missing number of records"
		);

		$page->logout();
	}

	/**
	 * @depends testPageSetup
	 */
	public function testSaveQuery()
	{
		/** @var \Celtic\Testing\Joomla\Joomla3_Admin_Page $page */
		$page = $this->loginToBackend();
		/** @var Components_DatabaseManager_SqlDefaultView $page */
		$page = $page->menu->select('Components/Database Manager/Run Query');

		$title = "Top10 Articles";
		$query = "SELECT * FROM #__content ORDER BY hits DESC LIMIT 10";

		$queryInput = $page->queryInput();
		$queryInput->clear();
		$queryInput->value($query);
		/** @var Components_DatabaseManager_QueriesDefaultEditView $page */
		$page = $page->toolbar->select("Save Query");

		$this->assertInstanceOf(
			'Components_DatabaseManager_QueriesDefaultEditView',
			$page
		);

		$this->assertEquals(
			$query,
			$page->queryInput()->text(),
			"Missing pre-filled query as part of the form"
		);

		$page->titleInput()->value($title);
		$page = $page->toolbar->select("Save");

		$this->assertRegExp(
			'~success~i',
			$page->message()->text(),
			"Storing failed."
		);

		$page->logout();
	}
}
