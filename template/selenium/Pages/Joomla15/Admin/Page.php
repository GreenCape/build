<?php
namespace Celtic\Testing\Joomla;

class Joomla15_Admin_Page extends Admin_Page
{
	protected $userMenuSelector      = 'css selector:#header-box #module-status';
	protected $messageContainer      = "xpath://dl[@id='system-message']/dd/ul/li";
	protected $headLineSelector      = "css selector:#toolbar-box div.header";

	public function __construct($driver)
	{
		parent::__construct($driver);
		$this->menu = new Joomla15_Admin_MainMenu($driver);
		$this->toolbar = new Joomla15_Admin_Toolbar($driver);
	}

	/**
	 * @return Joomla15_Admin_LoginPage
	 */
	public function logout()
	{
		$userMenu = $this->driver->getElement($this->userMenuSelector, 1000);
		$userMenu->byCssSelector('.logout a')->click();

		return new Joomla15_Admin_LoginPage($this->driver);
	}
}
