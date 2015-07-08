<?php
require_once '/vendor/autoload.php';

class Bootstrap3 extends BootstrapBase
{
	public function init($file)
	{
		$this->assertPhpVersion('5.3.10');
		parent::init($file);
	}

	protected function initApp()
	{
		JFactory::getApplication($this->application);
	}
}

$bootstrap = new Bootstrap3;
$bootstrap->init(__FILE__);

