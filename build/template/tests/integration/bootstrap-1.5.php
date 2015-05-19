<?php
$application  = '@APPLICATION@';
$appDirectory = $application == 'site' ? '' : '/' . $application;
$cmsDirectory = '@CMS_ROOT@';

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTP_USER_AGENT'] = 'none';

define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);

if (file_exists($cmsDirectory . "$appDirectory/defines.php"))
{
	include_once $cmsDirectory . "$appDirectory/defines.php";
}

if (!defined('JPATH_BASE'))
{
	define('JPATH_BASE', $cmsDirectory . $appDirectory);
	require_once JPATH_BASE . "/includes/defines.php";
}

require_once JPATH_BASE . '/includes/framework.php';

if ($application == 'administrator')
{
	require_once JPATH_BASE . '/includes/helper.php';
	require_once JPATH_BASE . '/includes/toolbar.php';
}
if ($application == 'site')
{
}

require_once $cmsDirectory . '/libraries/loader.php';

$mainframe = JFactory::getApplication($application);

$mainframe->initialise();

echo "\nBootstrap file for PHPUnit: " . __FILE__ . "\n";
