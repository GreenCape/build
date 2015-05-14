<?php
/**
 * Celtic Database - SQL Database manager for Joomla!
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
 * @package    Celtic\SqlManager
 * @author     Niels Braczek <nbraczek@bsds.de>
 * @copyright  Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL
 */

use Celtic\Abstraction\Input;
use Celtic\Abstraction\VersionFactoryInterface;
use Celtic\Assertions\Assertions;

defined('_JEXEC') or die('Restricted access');

/**
 * Class SqlController
 *
 * @package  Celtic\SqlManager
 * @since    1.0.0
 */
class SqlController extends AbstractedBaseController
{
	/** @var VersionFactoryInterface */
	protected $factory;

	/** @var  Assertions */
	protected $assert;

	/** @var  \JInput|Input */
	protected $input;

	/** @var \JDatabaseDriver */
	protected $db;

	/** @var SqlModel */
	protected $model;

	/** @var SqlHelper */
	protected $helper;

	/**
	 * Constructor
	 *
	 * @param   VersionFactoryInterface  $factory  The abstraction factory
	 * @param   array                    $default  An optional associative array of configuration settings.
	 */
	public function __construct(VersionFactoryInterface $factory, $default = array())
	{
		parent::__construct($default);

		$this->factory = $factory;

		$this->assert  = $this->factory->getAssertions();
		$this->input   = $this->factory->getInput();
		$this->db      = $this->factory->getDbo();

		$this->helper  = new SqlHelper($factory);

		$this->task    = $this->input->getCmd('task');
	}

	/**
	 * Get the model object
	 *
	 * @return  SqlModel
	 */
	public function getModel()
	{
		return $this->model;
	}
}
