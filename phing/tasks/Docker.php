<?php

include_once 'phing/BuildException.php';
include_once 'phing/Task.php';
include_once 'phing/tasks/system/condition/Condition.php';
require_once __DIR__ . '/traits/ReturnProperty.php';

class Docker extends Task
{
	private $container = '*';
	private $containerList = null;
	private $dir = null;
	private $state = null;

	private $supportedFileNames = array('docker-compose.yml', 'docker-compose.yaml', 'fig.yml', 'fig.yaml');
	private $configFile = null;

	use ReturnPropertyImplementation;

	/**
	 * @param string $state
	 */
	public function setState($state)
	{
		$this->state = strtolower($state);
	}

	/**
	 * @param string $dir
	 */
	public function setDir($dir)
	{
		$this->dir = $dir;
	}

	/**
	 * @param string $container
	 */
	public function setContainer($container)
	{
		$this->container = $container;
	}

	/**
	 * Run as a task.
	 *
	 * @throws BuildException if an error occurs.
	 */
	public function main()
	{
		$this->getContainerInfo();

		return call_user_func(
			array(
				$this,
				str_replace('-', '_', $this->getTaskName())
			),
			$this->filterContainers($this->containerList)
		);
	}

	/**
	 * Get a comma separated list of all containers matching the conditions, i.e.,
	 *   - name matches the pattern given in 'container'
	 *   - state equals the value given in 'state'
	 *
	 * @param array $containers Preselected list of containers
	 *
	 * @throws BuildException
	 */
	private function docker_list($containers)
	{
		$this->returnArray(array_keys($containers));
	}

	/**
	 * Get a comma separated list of all servers defined in the docker-compose (formerly called fig) configuration file
	 *
	 * @throws BuildException
	 */
	private function docker_def()
	{
		preg_match_all('~^(\w+):~sm', file_get_contents($this->dir . '/' . $this->configFile), $match);
		$this->returnArray($match[1]);
	}

	private function getContainerInfo()
	{
		$oldDir = getcwd();
		if (empty($this->dir))
		{
			$this->dir = $oldDir;
		}
		chdir($this->dir);

		$this->checkConfigurationFile();

		$replace   = array('?' => '.', '*' => '.*?');
		$container = str_replace(array_keys($replace), array_values($replace), $this->container);
		$this->log("Searching containers matching '{$this->container}'", Project::MSG_DEBUG);

		$this->containerList = array();
		foreach (explode("\n", `docker-compose ps`) as $line)
		{
			if (preg_match('~^(' . $container . ')\s+(.*?)\s+(\S+)\s+(\d.*)$~', $line, $match))
			{
				$this->containerList[$match[1]] = array(
					'name'    => $match[1],
					'command' => $match[2],
					'state'   => strtolower($match[3]),
					'ports'   => explode(', ', $match[4])
				);
			}
		}
		chdir($oldDir);
		$this->log(" - Found " . count($this->containerList) . " containers", Project::MSG_DEBUG);
	}

	private function checkConfigurationFile()
	{
		$this->configFile = null;
		foreach ($this->supportedFileNames as $filename)
		{
			if (file_exists($filename))
			{
				$this->configFile = $filename;
				break;
			}
		}
		if (empty($this->configFile))
		{
			throw new BuildException("Can't find a suitable configuration file. Are you in the right directory?\n\nSupported filenames: " . implode(', ', $$this->supportedFileNames));
		}
	}

	/**
	 * @param $availableContainers
	 *
	 * @return array
	 */
	private function filterContainers($availableContainers)
	{
		if ($this->state !== null)
		{
			$filteredContainers = array();
			foreach ($availableContainers as $container)
			{
				if ($container['state'] == $this->state)
				{
					$filteredContainers[$container['name']] = $container;
				}
			}
		}
		else
		{
			$filteredContainers = $availableContainers;
		}

		return $filteredContainers;
	}
}
