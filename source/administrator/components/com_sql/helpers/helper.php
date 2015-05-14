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
 * Class SqlHelper
 *
 * @package  Celtic\SqlManager
 * @since    1.0.0
 */
class SqlHelper
{
	/** @var VersionFactoryInterface  */
	private $factory;

	/** @var array */
	private $typeMap = array(
		'hidden' => 'hidden',
		'disabled' => 'disabled',
		'char' => 'text07',
		'nchar' => 'text07',
		'varchar' => 'text40',
		'nvarchar' => 'text40',
		'tinyblob' => 'textarea',
		'tinytext' => 'textarea',
		'blob' => 'textarea',
		'text' => 'textarea',
		'mediumblob' => 'textarea-big',
		'mediumtext' => 'textarea-big',
		'longblob' => 'textarea-big',
		'longtext' => 'textarea-big',
		'bit' => 'checkbox',
		'bool' => 'checkbox',
		'tinyint' => 'text15',
		'smallint' => 'text15',
		'mediumint' => 'text15',
		'integer' => 'text15',
		'int' => 'text15',
		'bigint' => 'text15',
		'datetime' => 'text15',
		'time' => 'text15',
		'real' => 'text15',
		'float' => 'text15',
		'decimal' => 'text15',
		'numeric' => 'text15',
		'double' => 'text15',
		'double precision' => 'text15',
	);

	/** @var array */
	private $controls = array(
		'hidden' => '<input type="hidden" name="fields[%1$s]" value="%2$s"/>',
		'disabled' => '<input type="text" name="fields[%1$s]" value="%2$s" disabled="disabled"/>',
		'text07' => '<input type="text" name="fields[%1$s]"  style="width:7%;" value="%2$s"/>',
		'text15' => '<input type="text" name="fields[%1$s]"  style="width:15%;" value="%2$s"/>',
		'text40' => '<input type="text" name="fields[%1$s]"  style="width:40%;" value="%2$s"/>',
		'textarea' => '<textarea name="fields[%1$s]" style="width:70%;">%2$s</textarea>',
		'textarea-big' => '<textarea name="fields[%1$s]" style="width:70%; height:150px;">%2$s</textarea>',
		'checkbox' => '<input type="checkbox"  name="fields[%1$s]" value="%2$s"/>',
	);

	/**
	 * Constructor
	 *
	 * @param   VersionFactoryInterface  $factory  The version abstraction
	 */
	public function __construct(VersionFactoryInterface $factory)
	{
		$this->factory = $factory;
	}

	/**
	 * Render HTML input control
	 *
	 * @param   string  $name   The name of the field
	 * @param   string  $type   The data type
	 * @param   mixed   $value  The current value
	 *
	 * @return  string
	 */
	public function renderHtml($name, $type, $value)
	{
		$ret = '';
		preg_match('~^([^(]+)(?:\((\d+)\))?~', $type, $match);
		$length = isset($match[2]) ? $match[2] : '';
		$type   = $this->adjustSqlTypeByLength(trim($match[1]), $length);
		if (isset($this->typeMap[$type]))
		{
			$controlType = $this->adjustControlTypeByLength($this->typeMap[$type], $length);
			$ret         = sprintf($this->controls[$controlType], $name, $value);
		}

		return $ret;
	}

	/**
	 * Adjust the SQL type according to the length
	 *
	 * @param   string  $type    The SQL data type
	 * @param   int     $length  The field length
	 *
	 * @return  string
	 */
	private function adjustSqlTypeByLength($type, $length)
	{
		if ($length > 511)
		{
			$type = 'longtext';
		}
		elseif ($length > 127)
		{
			$type = 'text';
		}

		return $type;
	}

	/**
	 * Adjust the control type according to the length
	 *
	 * @param   string  $type    The SQL data type
	 * @param   int     $length  The field length
	 *
	 * @return  string
	 */
	private function adjustControlTypeByLength($type, $length)
	{
		if (!empty($length) && preg_match('~^text(\d+)$~', $type))
		{
			if ($length < 8)
			{
				$type = 'text07';
			}
			elseif ($length < 16)
			{
				$type = 'text15';
			}
			else
			{
				$type = 'text40';
			}
		}

		return $type;
	}

	/**
	 * Check if src is current submenu
	 *
	 * @param   string  $src  Controller type to check
	 *
	 * @return  bool
	 */
	public function isActiveSubMenu($src)
	{
		$view = $this->factory->getInput()->getCmd('view', 'sql');

		return ($src == $view);
	}

	/**
	 * Build query variables for table and query
	 *
	 * @param   string  $table  The table name
	 * @param   string  $query  The query
	 * @param   string  $key    The key column
	 * @param   int     $id     The id
	 *
	 * @return  string
	 */
	public function buildQueryVars($table, $query, $key = null, $id = null)
	{
		$vars = '&table=' . urlencode($table) . '&query=' . urlencode($query);
		if (!is_null($key))
		{
			$vars .= '&key=' . urlencode($key) . '&id=' . urlencode($id);
		}

		return $vars;
	}
}
