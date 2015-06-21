<?php
namespace Celtic\Testing\Joomla;

class Joomla15_Admin_MainMenu extends Menu
{
	protected $levelMap = array(
		array(
			'locator' => 'css selector:#header-box #menu',
			'click' => false
		),
		array(
			'locator' => 'xpath:parent::li/ul',
			'click' => false
		),
		array(
			'locator' => 'xpath:following-sibling::ul',
			'click' => false
		),
	);

	protected $pageMap = array(
		'Extension Manager' => array(
			'menu' => 'Extensions/Install%2FUninstall',
			'page' => 'Celtic\\Testing\\Joomla\\Joomla15_Admin_ExtensionManager_InstallPage',
		),
		'Control Panel' => array(
			'menu' => 'Site/Control Panel',
			'page' => 'Celtic\\Testing\\Joomla\\Joomla15_Admin_CPanelPage',
		),
		'default' => array('page' => 'Celtic\\Testing\\Joomla\\Joomla15_Admin_Page')
	);
}
