<?php
namespace Celtic\Testing\Joomla;

use PHPUnit_Extensions_Selenium2TestCase_Element as Element;

abstract class Menu
{
	/** @var  AbstractAdapter */
	protected $driver;

	/**
	 * Map menu levels to retrieval information
	 *
	 * Format of each entry is
	 * array(
	 *     'locator' => 'method:value',
	 *     'click'   => false
	 * )
	 *
	 * Set 'click' to true, if a mouse click is needed to open the next level.
	 *
	 * @var array
	 */
	protected $levelMap = array();

	/**
	 * Map menu paths to page classes
	 *
	 * Format of each entry is
	 * 'abstract menu path' => array(
	 *     'menu' => 'actual corresponding menu path',
	 *     'page' => 'Fully\\Qualified\\Class'
	 * )
	 *
	 * @var array
	 */
	protected $pageMap = array();

	protected $itemFormat = 'link text:%s';

	public function __construct(AbstractAdapter $driver)
	{
		$this->driver = $driver;
	}

	/**
	 * @param $menuItem
	 *
	 * @return Element
	 */
	public function item($menuItem)
	{
		$this->debug($menuItem);
		if (isset($this->pageMap[$menuItem]))
		{
			$menuItem = $this->pageMap[$menuItem]['menu'];
			$this->debug(" => " . $menuItem);
		}
		$this->debug("\n");

		// Close all menus
		$item = $this->driver->getElement("tag name:body");
		$item->click();

		$menuPath = explode('/', $menuItem);
		$lastLevel = count($menuPath) - 1;
		foreach ($menuPath as $level => $label)
		{
			$menu = $this->locateChild($item, $this->levelMap[$level]['locator']);
			$this->debug("$level: " . str_replace("\n", '|', $menu->text()) . "\n");
			$item = $this->locateChild($menu, sprintf($this->itemFormat, urldecode($label)));
			$this->driver->moveto($item);
			if ($level == $lastLevel)
			{
				return $item;
			}
			if ($this->levelMap[$level]['click'])
			{
				// Open submenu
				$item->click();
			}
		}
		return $item;
	}

	/**
	 * @param $menuItem
	 *
	 * @return bool
	 */
	public function itemExists($menuItem)
	{
		try
		{
			$this->item($menuItem);
			return true;
		}
		catch (\PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e)
		{
			$this->debug($e->getMessage());
			$this->debug($e->getTraceAsString());
			return false;
		}
	}

	/**
	 * @param $menuItem
	 *
	 * @return Page
	 */
	public function select($menuItem)
	{
		$pageClass = $this->getPageClass($menuItem);

		$element = $this->item($menuItem)->click();
		#$this->debug("Clicking element $menuItem (" . $element->getId() . ")\n");
		#$element->click();

		return $this->driver->pageFactory_create($pageClass);
	}

	public function add($menuItem, $pageClass)
	{
		$this->pageMap[$menuItem] = array(
			'menu' => $menuItem,
			'page' => $pageClass
		);
	}

	public function remove($menuItem)
	{
		unset($this->pageMap[$menuItem]);
	}

	protected function debug($message)
	{
		$this->driver->debug($message);
	}

	/**
	 * @param   string  $menuItem
	 *
	 * @return  string
	 */
	protected function getPageClass($menuItem)
	{
		if (!isset($this->pageMap[$menuItem]))
		{
			$menuItem = 'default';
		}
		if (isset($this->pageMap[$menuItem]))
		{
			$pageClass = $this->pageMap[$menuItem]['page'];
		}
		else
		{
			$pageClass = strtr($menuItem, array(' ' => '', '/' => '_'));
		}

		return $pageClass;
	}

	/**
	 * @param Element $parent
	 * @param string $locator
	 *
	 * @return Element
	 */
	protected function locateChild($parent, $locator, $timeout = null)
	{
		$this->debug("Locating $locator\n");
		$timeout = max(10000, (int) $timeout);
		$driver = $this->driver;
		list($method, $value) = explode(':', $locator, 2);

		$callback = function() use ($driver, $parent, $method, $value) {
			try
			{
				$element = $parent->element($driver->using($method)->value($value));
				$driver->debug("Got element $method:$value (" . $element->getId() . ")\n");
				return $element;
			}
			catch (\PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e)
			{
				$driver->debug("Waiting for $method:$value to appear\n");
				return null;
			}
		};
		$element = $this->driver->waitUntil($callback, $timeout);
		if (!is_object($element))
		{
			$this->debug("Element is " . var_export($element, true) . "\n");
			$seconds = $timeout / 1000;
			throw new \PHPUnit_Framework_AssertionFailedError("Timed out after $seconds seconds");
		}

		return $element;
	}
}