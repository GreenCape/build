<?php
use Celtic\Testing\Joomla\PageDecorator;

/**
 * Class Components_DatabaseManager_SqlDefaultView
 */
class Components_DatabaseManager_QueriesDefaultEditView extends PageDecorator
{
	public function __construct(\Celtic\Testing\Joomla\AbstractAdapter $driver)
	{
		$this->driver = $driver;
		parent::__construct($driver->pageFactory_createFromType('Admin_Page'));

		$this->toolbar->add('Save', 'Components_DatabaseManager_QueriesDefaultView');
		$this->toolbar->add('Cancel', 'Components_DatabaseManager_QueriesDefaultView');
	}

	/**
	 * Check whether the current page matches this class
	 *
	 * @return  bool
	 */
	public function isCurrent()
	{
		return preg_match('~Celtic Database Manager.*?Run Query~', $this->headLine()->text());
	}

	public function queryInput()
	{
		return $this->getElement("id:query");
	}

	public function titleInput()
	{
		return $this->getElement("id:title");
	}
}
