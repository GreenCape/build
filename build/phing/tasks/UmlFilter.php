<?php

require_once "phing/Task.php";
require_once __DIR__ . '/traits/FileSet.php';

class UmlFilter extends Task
{
	private $file;
	private $dir;
	private $skin;
	private $jar;
	private $includeRef = true;

	use FileSetImplementation;

	public function setFile($file)
	{
		$this->file = $file;
	}

	public function setDir($dir)
	{
		$this->dir = $dir;
	}

	public function setSkin($skin)
	{
		$this->skin = $skin;
	}

	/**
	 * @param mixed $jar
	 */
	public function setJar($jar)
	{
		$this->jar = $jar;
	}

	public function main()
	{
		if (empty($this->jar))
		{
			throw new BuildException("Please provide location of plantuml.jar");
		}

		if (count($this->fileSets) == 0 && count($this->fileLists) == 0)
		{
			throw new BuildException("Need either nested fileset or nested filelist to iterate through");
		}

		$aggregate = array();
		foreach ($this->getFileSetFiles() as $file)
		{
			$code = file_get_contents($file);

			foreach ($this->generateDiagramSource($code) as $level => $fragments)
			{
				$aggregate[$level] = array_merge((array) $aggregate[$level], $fragments);
			}
		}
		foreach ($aggregate as $level => $fragments)
		{
			$filename = $this->dir . '/package-' . $level . '.puml';
			$lines = array_filter(explode("\n", implode("\n", $fragments)), function($line) {
				return $line[0] != '!';
			});
			$uml = implode("\n", array_unique($lines)) . "\n";
			file_put_contents($filename, "@startuml\n!include skin.puml\n{$uml}@enduml\n");
		}

		$this->log("Rendering ...");
		`java -jar '{$this->jar}' -tsvg '{$this->dir}/*.puml'`;
		$this->log("... done.");
	}

	/**
	 * @param $matches
	 */
	private function generateDiagramSource($code)
	{
		$identifier = '([\S]+)';

		$namespace = '';
		if (preg_match('~namespace\s+(.*?);~', $code, $match))
		{
			$namespace = trim(str_replace('\\', '.', $match[1]), '.') . '.';
		}

		$declaration = '(abstract\s+class|interface|trait|class)\s+' . $identifier;
		$extends     = '\s+extends\s+' . $identifier;
		$implements  = '\s+implements\s+' . $identifier . '(:?\s*,\s*' . $identifier . ')*';
		$pattern     = "~{$declaration}(:?{$extends})?(:?{$implements})?\s*\{~";
		if (!preg_match_all($pattern, $code, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER))
		{
			return array();
		}
		$classes = array();
		for ($i = 0, $n = count($matches); $i < $n; $i++)
		{
			if (isset($matches[$i + 1]))
			{
				$classes[$i] = substr($code, $matches[$i][0][1], $matches[$i + 1][0][1] - $matches[$i][0][1]);
			}
			else
			{
				$classes[$i] = substr($code, $matches[$i][0][1]);
			}
		}
		$aggregate = array('global' => '');
		$currLevel = '';
		$parts = explode('.', $namespace);
		while (!empty($parts))
		{
			$currLevel = trim($currLevel . '.' . array_shift($parts), '.');
			$aggregate[$currLevel] = '';
		}
		foreach ($matches as $i => $match)
		{
			$uml          = '';
			$currentClass = $namespace . $match[2][0];
			$filename     = $this->dir . '/class-' . $currentClass . '.puml';
			$uml .= "{$match[1][0]} {$currentClass}\n";
			if (!empty($match[4][0]))
			{
				$uml .= $this->handleReference($namespace, $currentClass, '<|--', $match[4][0]);
			}
			if (!empty($match[6][0]))
			{
				$uml .= $this->handleReference($namespace, $currentClass, '<|..', $match[6][0]);
			}
			file_put_contents($filename, "@startuml\n!include skin.puml\n{$uml}@enduml\n");
			foreach ($aggregate as $level => $levelCode)
			{
				$aggregate[$level][] = $uml
;			}
			$this->log("Generated class diagram for {$currentClass}");

			$this->handleMethods($currentClass, $classes[$i]);
		}
		return $aggregate;
	}

	/**
	 * @param $namespace
	 * @param $currentClass
	 * @param $op
	 * @param $referencedClass
	 *
	 * @return string
	 */
	private function handleReference($namespace, $currentClass, $op, $referencedClass)
	{
		$ref = str_replace('\\', '.', $referencedClass);
		$ref = $ref[0] == '.' ? substr($ref, 1) : $namespace . $ref;
		$res = "{$ref} {$op} {$currentClass}\n";
		$res .= $this->includeReferencedClass($ref);

		return $res;
	}

	/**
	 * @param $className
	 *
	 * @return array
	 */
	private function includeReferencedClass($className)
	{
		$pUmlCode = '';

		if ($this->includeRef)
		{
			$refFile  = "{$this->dir}/class-{$className}.puml";
			$pUmlCode = "!include {$refFile}\n";
			touch($refFile);
		}

		return $pUmlCode;
	}

	/**
	 * @param $currentClass
	 * @param $classCode
	 */
	private function handleMethods($currentClass, $classCode)
	{
		$pumlCode = "@startuml\n(.*?)@enduml";
		$access   = "(private|protected|public)";
		$method   = "{$access}?\s+function\s+(\S+)\s*\(";
		$pattern  = "~{$pumlCode}.*?{$method}~sm";
		$matches = array();
		if (!preg_match_all($pattern, $classCode, $matches, PREG_SET_ORDER))
		{
			return;
		}
		foreach ($matches as $match)
		{
			$methodName = $currentClass . '.' . $match[3];
			$filename   = $this->dir . '/seq-' . $methodName . '.puml';
			$lines      = preg_split("~\s+\*\s+~", $match[1]);
			$lines      = implode("\n", $lines);
			file_put_contents($filename, "@startuml\n!include skin.puml\n{$lines}@enduml\n");
			$this->log("Extracted diagram for {$methodName}()");
		}

		return;
	}
}
