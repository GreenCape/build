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

use Celtic\Abstraction\VersionFactoryInterface;

defined('_JEXEC') or die('Restricted access');

/**
 * Class SqlView
 *
 * @package  Celtic\SqlManager
 * @since    1.0.0
 */
class SqlView extends AbstractedBaseView
{
	/** @var  SqlModelSql  The main model*/
	protected $model;

	/** @var  VersionFactoryInterface  The abstraction factory */
	protected $factory;

	/**
	 * Constructor
	 *
	 * @param   VersionFactoryInterface  $factory  The abstraction factory
	 * @param   SqlModel                 $model    The default model
	 * @param   array                    $config   A configuration hash
	 */
	public function __construct(VersionFactoryInterface $factory, SqlModel $model, array $config = array())
	{
		parent::__construct($config);

		$this->factory = $factory;
		$this->model   = $model;
	}
}
