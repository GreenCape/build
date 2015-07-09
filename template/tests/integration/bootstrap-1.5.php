<?php
class Bootstrap
{
	protected $application;
	protected $appDirectory;
	protected $cmsDirectory;

	public function __construct()
	{
		$this->application  = '@APPLICATION@';
		$this->appDirectory = $this->application == 'site' ? '' : '/' . $this->application;
		$this->cmsDirectory = '@CMS_ROOT@';
	}

	protected function initApp()
	{
		$mainframe = JFactory::getApplication($this->application);
		$mainframe->initialise();
	}

	public function init($file)
	{
		$this->setServerHttpVars();
		$this->getDefines();
		$this->getLoader();
		$this->getFramework();
		$this->getHelpers();

		$this->initApp();

		echo "\nBootstrap file for PHPUnit: " . $file . "\n";
	}

	protected function assertPhpVersion($version)
	{
		if (version_compare(PHP_VERSION, $version, '<'))
		{
			throw new ErrorException("Your host needs to use PHP $version or higher to run this version of Joomla!");
		}
	}

	protected function setServerHttpVars()
	{
		$_SERVER['HTTP_HOST']       = 'localhost';
		$_SERVER['HTTP_USER_AGENT'] = 'none';
	}

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

	protected function getFramework()
	{
		require_once JPATH_BASE . "/includes/framework.php";
	}

	protected function getHelpers()
	{
		if ($this->application == 'administrator')
		{
			require_once JPATH_BASE . "/includes/helper.php";
			require_once JPATH_BASE . "/includes/toolbar.php";
		}
		if ($this->application == 'site')
		{
		}
	}
}

$bootstrap = new Bootstrap;
$bootstrap->init(__FILE__);
