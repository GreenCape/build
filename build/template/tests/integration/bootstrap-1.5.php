<?php
$application  = '@APPLICATION@';
$appDirectory = $application == 'site' ? '' : '/' . $application;
$cmsDirectory = '@CMS_ROOT@';

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTP_USER_AGENT'] = 'none';

define('_JEXEC', 1);
define('JPATH_BASE', $cmsDirectory . $appDirectory);
define('DS', DIRECTORY_SEPARATOR);

require_once JPATH_BASE . "/includes/defines.php";

require_once JPATH_LIBRARIES . '/loader.php';
spl_autoload_register(function ($class)
{
	return JLoader::load($class);
});

require_once JPATH_BASE . '/includes/framework.php';

if ($application == 'administrator')
{
	require_once JPATH_BASE . '/includes/helper.php';
	require_once JPATH_BASE . '/includes/toolbar.php';
}
if ($application == 'site')
{
}

$mainframe = JFactory::getApplication($application);
$mainframe->initialise();

echo "\nBootstrap file for PHPUnit: " . __FILE__ . "\n";

