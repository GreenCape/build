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
 * @package    Celtic\Abstraction
 * @author     Niels Braczek <nbraczek@bsds.de>
 * @copyright  Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL2
 */

namespace Celtic\Abstraction;

use Celtic\Assertions\Assertions;

/**
 * Class VersionFactory
 *
 * @package  Celtic\Abstraction
 * @since    1.0
 */
class VersionFactory implements VersionFactoryInterface
{
	/** @var  VersionFactoryInterface */
	private $version;

	/** @var Assertions */
	private $assertions = null;

	/**
	 * Constructor
	 *
	 * @param   string  $version  The current version, usually represented by JVERSION
	 *
	 * @throws  VersionException
	 */
	public function __construct($version)
	{
		switch (true)
		{
			case version_compare($version, '3', 'ge'):
				$this->version = new PlatformTwelveFactory;
				break;

			case version_compare($version, '1.6', 'ge'):
				$this->version = new PlatformElevenFactory;
				break;

			case version_compare($version, '1.5', 'ge'):
				$this->version = new DefaultFactory;
				break;

			default:
				throw new VersionException("Version $version is not supported");
				break;
		}
	}

	/**
	 * Get an application object.
	 *
	 * @param   mixed   $id      A client identifier or name.
	 * @param   array   $config  An optional associative array of configuration settings.
	 * @param   string  $prefix  Application prefix
	 *
	 * @return  \JApplication
	 */
	public function getApplication($id = null, array $config = array(), $prefix = 'J')
	{
		return $this->version->getApplication($id, $config, $prefix);
	}

	/**
	 * Get version dependent assertions
	 *
	 * @return  Assertions
	 */
	public function getAssertions()
	{
		if (!is_null($this->assertions))
		{
			return $this->assertions;
		}
		return $this->version->getAssertions();
	}

	/**
	 * Set version dependent assertions
	 *
	 * @param   Assertions  $assertions  An assertions class
	 *
	 * @return  Assertions
	 */
	public function setAssertions(Assertions $assertions)
	{
		$this->assertions = $assertions;
	}

	/**
	 * Get the database object
	 *
	 * @return  \JDatabase
	 */
	public function getDbo()
	{
		return $this->version->getDbo();
	}

	/**
	 * Get the document
	 *
	 * @return  \JDocument
	 */
	public function getDocument()
	{
		return $this->version->getDocument();
	}

	/**
	 * Get an Input object
	 *
	 * @return  mixed
	 */
	public function getInput()
	{
		return $this->version->getInput();
	}

	/**
	 * Check if the current version supports subqueries
	 *
	 * @return bool
	 */
	public function supportsSubqueries()
	{
		return $this->version->supportsSubqueries();
	}

	/**
	 * Check if the current version supports ACL
	 *
	 * @return bool
	 */
	public function supportsAcl()
	{
		return $this->version->supportsAcl();
	}
}
