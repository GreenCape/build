<?php
namespace Celtic\Testing\Joomla;

class Joomla25_Admin_LoginPage extends Joomla25_Admin_Page
{
	public function isCurrent()
	{
		$form = $this->driver->byTag('form');
		$id   = $form->attribute('id');

		return $id == 'form-login';
	}

	public function login($username, $password)
	{
		$this->getElement("id:mod-login-username")->value($username);
		$this->getElement("id:mod-login-password")->value($password);
		$this->getElement("xpath://a[contains(., 'Log in')]")->click();

		return $this->driver->pageFactory_createFromType('Admin_CPanelPage');
	}
}