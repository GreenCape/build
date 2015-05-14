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

namespace Celtic\Abstraction;

use Celtic\Assertions\MockAssert;

/**
 * Factory for version abstracting classes for Joomla! versions before the platform era
 *
 * Don't use this class directly; it is used by VersionFactory if needed.
 *
 * @package     Celtic
 * @subpackage  Abstraction
 * @since       1.0
 */
class MockFactory implements VersionFactoryInterface
{
	/** @var \JApplication */
	public $application = null;

	/** @var MockAssert */
	public $assertions = null;

	/** @var \JDocument */
	public $document = null;

	/** @var \JDatabase */
	public $dbo = null;

	/** @var Input */
	public $input = null;

	/** @var bool */
	public $supportsSubqueries = false;

	/** @var bool */
	public $supportsAcl = true;

	public function __construct()
	{
		$this->assertions = new MockAssert();
	}

	/**
	 * Get the application object
	 *
	 * @param   mixed  $id      A client identifier or name.
	 * @param   array  $config  An optional associative array of configuration settings.
	 * @param   string $prefix  Application prefix
	 *
	 * @return  \JApplication
	 */
	public function getApplication($id = null, array $config = array(), $prefix = 'J')
	{
		return $this->application;
	}

	/**
	 * Get version dependent assertions
	 *
	 * @return  MockAssert
	 */
	public function getAssertions()
	{
		return $this->assertions;
	}

	/**
	 * Get the document
	 *
	 * @return  \JDocument
	 */
	public function getDocument()
	{
		return $this->document;
	}

	/**
	 * Get the database object
	 *
	 * @return  \JDatabase
	 */
	public function getDbo()
	{
		return $this->dbo;
	}

	/**
	 * Get an Input object
	 *
	 * @return  Input
	 */
	public function getInput()
	{
		return $this->input;
	}

	/**
	 * Check if the current version supports subqueries
	 *
	 * @return bool
	 */
	public function supportsSubqueries()
	{
		return $this->supportsSubqueries;
	}

	/**
	 * Check if the current version supports ACL
	 *
	 * @return bool
	 */
	public function supportsAcl()
	{
		return $this->supportsAcl;
	}}
