<?php
/**
 * Celtic Version Abstraction
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,USA.
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @package     Celtic
 * @subpackage  Abstraction
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 * @license     http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL2
 */

namespace Celtic\Assertions;

/**
 * Factory for version abstracting classes for Joomla! versions before the platform era
 *
 * Don't use this class directly; it is used by VersionFactory if needed.
 *
 * @package     Celtic
 * @subpackage  Assertions
 * @since       1.0
 */
class MockAssert extends Assertions
{
	public $tokenIsValid = false;

	public $userCanManage = true;

	/**
	 * Check for a valid session token
	 *
	 * Joomla! implements a simple technique which makes it more difficult for a cross-site request forgery
	 * attack (CSRF) to succeed. This involves adding a randomly-generated unique token to the form which is
	 * checked against a copy of the token held in the user's session. By checking that the submitted token
	 * matches the one contained in the stored session, it is possible to tie a rendered form to the request
	 * variables presented.
	 * The code will die if the token is omitted from the request, or the submitted token does not match the
	 * session token. If the token is correct but has expired, then JRequest::checkToken will automatically
	 * redirect to the site front page.
	 *
	 * @return  void
	 *
	 * @throws  AssertionException if the token is not valid
	 * @see     http://docs.joomla.org/Secure_coding_guidelines#Securing_forms
	 */
	public function tokenIsValid()
	{
		if (!$this->tokenIsValid)
		{
			throw new AssertionException;
		}
	}

	/**
	 * Check if the current user is authorized to manage an asset
	 *
	 * If the current user is not authorized to manage the asset in question, an exception
	 * is thrown.
	 *
	 * @param   string $asset    The name of the asset to be managed
	 * @param   string $message  Error message (optional)
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
			$message = \JText::_('JGLOBAL_AUTH_ACCESS_DENIED');
		}
		if (!$this->userCanManage)
		{
			throw new NotAuthorizedException($message);
		}
	}
}
