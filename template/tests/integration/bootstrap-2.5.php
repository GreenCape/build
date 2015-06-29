<?php
class Bootstrap25 extends BootstrapBase
{
	protected function getDefines()
	{
		parent::getDefines();
		define('DS', DIRECTORY_SEPARATOR);
	}

	protected function initApp()
	{
		$app = JFactory::getApplication($this->application);
		$app->initialise();
	}
}

$bootstrap = new Bootstrap25;
$bootstrap->init(__FILE__);
