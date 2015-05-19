<?php
namespace Celtic\Testing\Joomla;

class Joomla3_Admin_Toolbar extends Toolbar
{
	protected $pageMap = array(
		'default' => array('page' => 'Celtic\\Testing\\Joomla\\Joomla3_Admin_Page')
	);

	protected $itemFormat = "xpath://div[@id='toolbar']//button[contains(., '%s')]";
}
