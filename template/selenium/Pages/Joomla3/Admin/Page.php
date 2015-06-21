<?php
namespace Celtic\Testing\Joomla;

class Joomla3_Admin_Page extends Admin_Page
{
	/** @var Joomla3_Admin_MainMenu */
	public $menu = null;

	protected $userMenuSelector      = 'css selector:nav.navbar ul.pull-right';
	protected $messageContainer      = "id:system-message-container";
	protected $headLineSelector      = "css selector:h1.page-title";

	public function __construct($driver)
	{
		parent::__construct($driver);
		$this->menu = new Joomla3_Admin_MainMenu($driver);
		$this->toolbar = new Joomla3_Admin_Toolbar($driver);
	}

	/**
	 * @return Joomla3_Admin_LoginPage
	 */
	public function logout()
	{
		$userMenu = $this->driver->getElement($this->userMenuSelector);
		$userMenu->byTag('a')->click();

		$userMenu->byLinkText('Logout')->click();

		return new Joomla3_Admin_LoginPage($this->driver);
	}
}