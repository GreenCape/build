<?php
class Bootstrap1_5 extends BootstrapBase
{
	protected function getDefines()
	{
		parent::getDefines();
		define('DS', DIRECTORY_SEPARATOR);
	}

	protected function getLoader()
	{
		require_once JPATH_LIBRARIES . '/loader.php';
		spl_autoload_register(function ($class)
		{
			return JLoader::load($class);
		});
	}

	protected function initApp()
	{
		$mainframe = JFactory::getApplication($this->application);
		$mainframe->initialise();
	}
}

$bootstrap = new Bootstrap1_5;
$bootstrap->init(__FILE__);
