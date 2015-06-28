#!/usr/bin/php
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
 * @subpackage  Core
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   2012-2013 Niels Braczek <nbraczek@bsds.de>
 * @license     http://opensource.org/licenses/AGPL-3.0 GNU AFFERO GENERAL PUBLIC LICENSE, Version 3 (AGPL-3.0)
 * @link        http://www.bsds.de/
 * @since       File available since Release 1.0.0
 */

namespace Celtic\JoomlaCLI;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

require_once __DIR__ . '/joomla/autoload.php';

/**
 * The main Joomla CLI application.
 *
 * @package     Celtic\JoomlaCLI
 * @subpackage  Core
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   2012-2013 Niels Braczek <nbraczek@bsds.de>
 * @license     http://opensource.org/licenses/AGPL-3.0 GNU AFFERO GENERAL PUBLIC LICENSE, Version 3 (AGPL-3.0)
 * @link        http://www.bsds.de/
 * @since       File available since Release 1.0.0
 */
class JoomlaCliApplication extends Application
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct('Joomla CLI', '1.0.0');
		$this->setCatchExceptions(false);
		$this->addPlugins(__DIR__ . '/joomla/commands');
	}

	/**
	 * Runs the current application.
	 *
	 * @param   InputInterface   $input   An InputInterface instance
	 * @param   OutputInterface  $output  An OutputInterface instance
	 *
	 * @return  integer  0 if everything went fine, or an error code
	 *
	 * @throws  \Exception on problems
	 */
	public function run(InputInterface $input = null, OutputInterface $output = null)
	{
		try
		{
			parent::run($input, $output);
		}
		catch (\Exception $e)
		{
			if (null === $output) {
				$output = new ConsoleOutput();
			}
			$message = array(
				$this->getLongVersion(),
				'',
				$e->getMessage(),
				''
			);
			$output->writeln($message);
		}
	}

	/**
	 * Dynamically add all commands from a path
	 *
	 * @param   string  $path  The directory with the plugins
	 *
	 * @return  void
	 */
	private function addPlugins($path)
	{
		foreach (glob($path . '/*.php') as $filename)
		{
			include_once $filename;
			$commandClass = __NAMESPACE__ . '\\' . ucfirst(basename($filename, '.php')) . 'Command';
			$command = new $commandClass;
			$this->add($command);
		}
	}
}

$console = new JoomlaCliApplication();
$console->run();