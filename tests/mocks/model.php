<?php
/**
 * Celtic Database - SQL Database manager for Joomla!
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
 * @package    Celtic\SqlManager
 * @author     Niels Braczek <nbraczek@bsds.de>
 * @copyright  Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL
 */

/**
 * Class AbstractedBaseModel
 *
 * @see      \JModelLegacy
 * @package  Celtic\SqlManager
 * @since    1.0.0
 */
class AbstractedBaseModel
{
	public function __construct($config = array())
	{
	}

	public static function addIncludePath($path = '', $prefix = '')
	{
	}

	public static function addTablePath($path)
	{
	}

	protected static function _createFileName($type, $parts = array())
	{
	}

	public static function getInstance($type, $prefix = '', $config = array())
	{
	}

	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
	}

	protected function _getListCount($query)
	{
	}

	protected function _createTable($name, $prefix = 'Table', $config = array())
	{
	}

	public function getDbo()
	{
	}

	public function getName()
	{
	}

	public function getState($property = null, $default = null)
	{
	}

	public function getTable($name = '', $prefix = 'Table', $options = array())
	{
	}

	protected function populateState()
	{
	}

	public function setDbo($db)
	{
	}

	public function setState($property, $value = null)
	{
	}

	protected function cleanCache($group = null, $client_id = 0)
	{
	}
}
