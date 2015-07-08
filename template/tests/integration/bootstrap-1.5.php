<?php
require_once '/vendor/autoload.php';

class Bootstrap15 extends BootstrapBase
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

$bootstrap = new Bootstrap15;
$bootstrap->init(__FILE__);
