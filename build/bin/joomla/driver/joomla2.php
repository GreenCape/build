<?php
/**
 * Celtic Joomla Command Line Interface
 *
 * Copyright (c) 2012-2013, Niels Braczek <nbraczek@bsds.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Niels Braczek nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Celtic\JoomlaCLI
 * @subpackage  Driver
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   2012-2013 Niels Braczek <nbraczek@bsds.de>
 * @license     http://opensource.org/licenses/AGPL-3.0 GNU AFFERO GENERAL PUBLIC LICENSE, Version 3 (AGPL-3.0)
 * @link        http://www.bsds.de/
 * @since       File available since Release 1.0.0
 */

namespace Celtic\JoomlaCLI;

/**
 * Version specific methods
 *
 * @package     Celtic\JoomlaCLI
 * @subpackage  Driver
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   2012-2013 Niels Braczek <nbraczek@bsds.de>
 * @license     http://opensource.org/licenses/AGPL-3.0 GNU AFFERO GENERAL PUBLIC LICENSE, Version 3 (AGPL-3.0)
 * @link        http://www.bsds.de/
 * @since       File available since Release 1.0.0
 */
class Joomla2Driver extends JoomlaDriver
{
	/**
	 * Setup the environment
	 *
	 * @param   string  $basePath     The root of the Joomla! application
	 * @param   string  $application  The application, eg., 'site' or 'administration'
	 *
	 * @return  void
	 */
	public function setupEnvironment($basePath, $application = 'site')
	{
		define('DS', DIRECTORY_SEPARATOR);

		parent::setupEnvironment($basePath, $application);

		jimport('joomla.application.component.helper');
	}

	/**
	 * Set a configuration value.
	 *
	 * @param   string  $key    The key
	 * @param   mixed   $value  The value
	 *
	 * @return  mixed  The value
	 */
	public function setCfg($key, $value)
	{
		return \JFactory::getConfig()->set($key, $value);
	}

	/**
	 * Gets a configuration value.
	 *
	 * @param   string  $key  The name of the value to get
	 *
	 * @return  mixed  The value
	 */
	public function getCfg($key)
	{
		return \JFactory::getConfig()->get($key);
	}

	/**
	 * @param $manifest
	 *
	 * @return array
	 */
	public function getExtensionInfo($manifest)
	{
		$data                = array();
		$data['type']        = (string) $manifest['type'];
		$data['extension']   = (string) $manifest->name;
		$data['name']        = \JText::_($manifest->name);
		$data['version']     = (string) $manifest->version;
		$data['description'] = \JText::_($manifest->description);

		return $data;
	}
}