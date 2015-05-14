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

namespace Celtic\Patterns;

/**
 * Class Decorator
 *
 * This class is a generic decorator.
 *
 * @package  Celtic\Patterns
 * @since    1.0.0
 */
abstract class Decorator
{
	/** @var object */
	protected $subject;

	/**
	 * Constructor
	 *
	 * @param   object  $subject  The subject to be decorated
	 */
	public function __construct($subject)
	{
		$this->subject = $subject;
	}

	/**
	 * Call a method on the subject
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
		$callback = array($this->subject, $method);

		if (!is_callable($callback))
		{
			$class = get_class($this->subject);
			throw new \BadMethodCallException("Unknown method {$class}::{$method}");
		}

		return call_user_func_array($callback, $args);
	}

	/**
	 * Get a value
	 *
	 * @param   string  $property  The name of the property
	 *
	 * @return  mixed
	 */
	public function __get($property)
	{
		return $this->subject->$property;
	}

	/**
	 * Set a value
	 *
	 * @param   string  $property  The name of the property
	 * @param   mixed   $value     The new value
	 *
	 * @return  mixed
	 */
	public function __set($property, $value)
	{
		return $this->subject->$property = $value;
	}
}
