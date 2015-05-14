<?php
/**
 * Celtic Version Abstraction
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,USA.
 *
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @package     Celtic
 * @subpackage  Abstraction
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL2
 */

namespace Celtic\Abstraction;

use Celtic\Assertions\Assertions;
use Celtic\Assertions\PlatformTwelveAssertions;

/**
 * Factory for version abstracting classes for Joomla! versions based on platform 12.2 or newer
 *
 * Don't use this class directly; it is used by VersionFactory if needed.
 *
 * @package     Celtic
 * @subpackage  Abstraction
 * @since       1.0
 */
class PlatformTwelveFactory extends PlatformElevenFactory implements VersionFactoryInterface
{
	/** @var bool */
	public $supportsSubqueries = true;

	/**
	 * Get version dependent assertions
	 *
	 * @return  Assertions
	 */
	public function getAssertions()
	{
		return new PlatformTwelveAssertions;
	}

	/**
	 * Get an Input object
	 *
	 * @return  \JInput
	 */
	public function getInput()
	{
		return \JFactory::getApplication()->input;
	}
}
