<?php
/**
 * Celtic Assertions
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
 * @subpackage  Assertions
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL2
 */

namespace Celtic\Assertions;

/**
 * Version dependent assertions for Joomla! versions before the platform era
 *
 * Don't use this class directly; it is used by Assert if needed.
 *
 * @package     Celtic
 * @subpackage  Assertions
 * @since       1.0
 */
class DefaultAssertions extends  Assertions
{
	/**
	 * Check for a valid session token
	 *
	 * @return  void
	 *
	 * @throws  InvalidTokenException if the token is not valid
	 * @see     Asset
	 */
	public function tokenIsValid()
	{
		/** @noinspection PhpDeprecationInspection */
		if (!\JRequest::checkToken())
		{
			throw new InvalidTokenException(\JText::_('INVALID_TOKEN'));
		}
	}

	/**
	 * Check if the current user is authorized to manage an asset
	 *
	 * @param   string  $asset    The name of the asset to be managed
	 * @param   string  $message  Error message (optional)
	 *
	 * @return  void
	 *
	 * @throws  NotAuthorizedException if the user is not allowed to manage the asset
	 * @see     Asset
	 */
	public function userCanManage($asset, $message = null)
	{
		if (empty($message))
		{
			$message = \JText::_('NOT AUTHORISED');
		}
		if (0)
		{
			throw new NotAuthorizedException($message);
		}
	}
}
