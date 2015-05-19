<?php
namespace Celtic\Testing\Joomla;

class Joomla25_Admin_Toolbar extends Toolbar
{
	protected $pageMap = array(
		'default' => array('page' => 'Celtic\\Testing\\Joomla\\Joomla25_Admin_Page')
	);

	protected $itemFormat = "xpath://div[@id='toolbar']//a[contains(., '%s')]";
}
