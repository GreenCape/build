<?php
namespace Celtic\Testing\Joomla;

class Joomla15_Admin_LoginPage extends Joomla15_Admin_Page
{
	public function isCurrent()
	{
		$form = $this->driver->byTag('form');
		$id   = $form->attribute('id');

		return $id == 'form-login';
	}

	public function login($username, $password)
	{
		$this->getElement("id:modlgn_username")->value($username);
		$this->getElement("id:modlgn_passwd")->value($password);
		$this->getElement("xpath://a[contains(., 'Login')]")->click();

		return $this->driver->pageFactory_createFromType('Admin_CPanelPage');
	}
}