<?php
namespace Celtic\Testing\Joomla;

class Joomla15_Admin_Toolbar extends Toolbar
{
	protected $pageMap = array(
		'default' => array('page' => 'Celtic\\Testing\\Joomla\\Joomla15_Admin_Page')
	);

	protected $itemFormat = "xpath://table[@class='toolbar']//a[contains(., '%s')]";
}
