<?php
$application  = '@APPLICATION@';
$appDirectory = $application == 'site' ? '' : '/' . $application;
$cmsDirectory = '@CMS_ROOT@';

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTP_USER_AGENT'] = 'none';

if (version_compare(PHP_VERSION, '5.3.10', '<'))
{
	die('Your host needs to use PHP 5.3.10 or higher to run this version of Joomla!');
}

define('_JEXEC', 1);

if (file_exists($cmsDirectory . "$appDirectory/defines.php"))
{
	include_once $cmsDirectory . "$appDirectory/defines.php";
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', $cmsDirectory . $appDirectory);
	require_once JPATH_BASE . "/includes/defines.php";
}

require_once JPATH_BASE . "/includes/framework.php";

if ($application == 'administrator')
{
	require_once JPATH_BASE . "/includes/helper.php";
	require_once JPATH_BASE . "/includes/toolbar.php";
}
if ($application == 'site')
{
}

require_once $cmsDirectory . '/libraries/loader.php';

$app = JFactory::getApplication($application);

echo "\nBootstrap file for PHPUnit: " . __FILE__ . "\n";

