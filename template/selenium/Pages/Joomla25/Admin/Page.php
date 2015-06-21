<?php
namespace Celtic\Testing\Joomla;

class Joomla25_Admin_Page extends Admin_Page
{
	protected $userMenuSelector      = 'css selector:#header-box #module-status';
	protected $messageContainer      = "id:system-message-container";
	protected $headLineSelector      = "css selector:div.page-title h2";

	public function __construct($driver)
	{
		parent::__construct($driver);
		$this->menu = new Joomla25_Admin_MainMenu($driver);
		$this->toolbar = new Joomla25_Admin_Toolbar($driver);
	}

	/**
	 * @return Joomla25_Admin_LoginPage
	 */
	public function logout()
	{
		$userMenu = $this->driver->getElement($this->userMenuSelector);
		$userMenu->byCssSelector('.logout a')->click();

		return new Joomla25_Admin_LoginPage($this->driver);
	}
}