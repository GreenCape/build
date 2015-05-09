<?php

require_once "phing/Task.php";

class VersionMatchTask extends Task
{
	protected $version;
	protected $path;
	protected $pattern;
	protected $returnProperty = null;

	public function setVersion($version)
	{
		$this->version = $version;
	}

	public function setDir($path)
	{
		$this->path = $path;
	}

	public function setPattern($pattern)
	{
		$this->pattern = $pattern;
	}

	public function setReturnProperty($property)
	{
		$this->returnProperty = $property;
	}

	public function main()
	{
		$file = $this->findBestMatch($this->pattern, $this->path, $this->version);

		if ($this->returnProperty !== null)
		{
			$this->project->setProperty($this->returnProperty, $file);
		}
	}

	public function findBestMatch($pattern, $path, $version)
	{
		$bestVersion = '0';
		$bestFile    = null;
		foreach (glob("$path/*") as $filename)
		{
			if (preg_match("/{$pattern}/", $filename, $match))
			{
				if (version_compare($bestVersion, $match[1], '<') && version_compare($match[1], $version, '<='))
				{
					$bestVersion = $match[1];
					$bestFile    = $filename;
				}
			}
		}

		return $bestFile;
	}
}
