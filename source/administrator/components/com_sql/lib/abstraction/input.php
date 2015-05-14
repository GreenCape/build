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
 * @package    Celtic\Abstraction
 * @author     Niels Braczek <nbraczek@bsds.de>
 * @copyright  Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL2
 */

namespace Celtic\Abstraction;

defined('_JEXEC') or die('Restricted access');

/**
 * Class Input
 *
 * This class is an adapter to JRequest to unify access with the later JInput
 *
 * @package  Celtic\Abstraction
 * @method   static mixed  get()  get(string $name, int $default = null, string $filter = 'cmd')
 * @method   static integer  getInt()    getInt(string $name, int $default = null)
 * @method   static integer  getUint()   getUint(string $name, int $default = null)
 * @method   static float    getFloat()  getFloat(string $name, float $default = null)
 * @method   static boolean  getBool()   getBool(string $name, bool $default = null)
 * @method   static string   getWord()   getWord(string $name, string $default = null)
 * @method   static string   getAlnum()  getAlnum(string $name, string $default = null)
 * @method   static string   getCmd()    getCmd(string $name, string $default = null)
 * @method   static string   getBase64() getBase64(string $name, string $default = null)
 * @method   static string   getString() getString(string $name, string $default = null)
 * @method   static string   getHtml()   getHtml(string $name, string $default = null)
 * @method   static array    getArray()  getArray(string $name, array $default = null)
 * @since    1.0.0
 */
class Input
{
	/**
	 * Get a value from the input
	 *
	 * @param   string  $method  The method called
	 * @param   array   $args    The arguments for the method
	 *
	 * @return  mixed
	 *
	 * @throws  \BadMethodCallException on unknown method
	 */
	public function __call($method, $args)
	{
		if (!preg_match('~^get(.*)$~', $method, $match))
		{
			throw new \BadMethodCallException("Unknown method Input::{$method}");
		}

		return self::getInputValue(strtolower($match[1]), $args);
	}

	/**
	 * Get an input value
	 *
	 * @param   string  $type  The data type
	 * @param   array   $args  The arguments, @see __call
	 *
	 * @return mixed
	 */
	private static function getInputValue($type, $args)
	{
		$name    = array_shift($args);
		$default = array_shift($args);
		$filter  = array_shift($args);

		if ($type == 'var')
		{
			$type = $filter;
		}

		if (empty($type))
		{
			/** @noinspection PhpDeprecationInspection */
			return \JRequest::get($name);
		}
		else
		{
			/** @noinspection PhpDeprecationInspection */
			return \JRequest::getVar($name, \JRequest::getVar($name, $default, 'get', $type), 'post', $type);
		}
	}

	/**
	 * Set a value
	 *
	 * @param   string  $name   Name of the value to set.
	 * @param   mixed   $value  Value to assign to the input.
	 *
	 * @return  void
	 */
	public static function set($name, $value)
	{
		/** @noinspection PhpDeprecationInspection */
		\JRequest::setVar($name, $value);
	}
}
