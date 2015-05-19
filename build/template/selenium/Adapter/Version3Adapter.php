<?php
/**
 * @package        Joomla.FunctionalTest
 * @copyright      Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Celtic\Testing\Joomla;

use \PHPUnit_Extensions_Selenium2TestCase_Element as WebElement;

class Version3Adapter extends AbstractAdapter
{
	/** @var string The class prefix for this family */
	protected $classPrefix = 'Joomla3';
}
