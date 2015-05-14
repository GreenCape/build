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
 * Class AbstractedBaseView
 *
 * @package  Celtic\SqlManager
 * @since    1.0.0
 */
class AbstractedBaseView
{
	public function __construct($config = array())
	{
	}

	public function display($tpl = null)
	{
	}

	public function escape($var)
	{
	}

	public function get($property, $default = null)
	{
	}

	public function getModel($name = null)
	{
	}

	public function getLayout()
	{
	}

	public function getLayoutTemplate()
	{
	}

	public function getName()
	{
	}

	public function setModel($model, $default = false)
	{
	}

	public function setLayout($layout)
	{
	}

	public function setLayoutExt($value)
	{
	}

	public function addTemplatePath($path)
	{
	}

	public function addHelperPath($path)
	{
	}

	public function loadTemplate($tpl = null)
	{
	}

	public function loadHelper($hlp = null)
	{
	}

	protected function _setPath($type, $path)
	{
	}

	protected function _addPath($type, $path)
	{
	}

	protected function _createFileName($type, $parts = array())
	{
	}
}
