<?php
/**
 * Celtic Database - SQL Database manager for Joomla!
 *
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
 * @package     Celtic\SqlManager
 * @subpackage  IntegrationTests
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 */

use Celtic\Abstraction\VersionFactory;

class TestTable
{
	private $table;
	private $db;

	public function __construct($tablename, $schema = null)
	{
		$this->factory = new VersionFactory(JVERSION);
		$this->table   = $tablename;
		$this->db      = $this->factory->getDbo();
		$this->addTestTable($schema);
	}

	public function __destruct()
	{
		$this->removeTestTable();
	}

	private function addTestTable($schema)
	{
		if (empty($schema))
		{
			$schema = "id int UNIQUE, name varchar(255)";
		}
		$this->execute("CREATE TEMPORARY TABLE IF NOT EXISTS {$this->table} ( {$schema} )");
		$this->execute("TRUNCATE {$this->table}");
	}

	private function removeTestTable()
	{
		try
		{
			$this->execute("DROP TEMPORARY TABLE IF EXISTS {$this->table}");
		}
		catch (\Exception $e)
		{
			/*
			 * Connection might have been closed, but
			 * temporary table are moved automatically then,
			 * so it is ok.
			 */
		}
	}

	public function execute($query)
	{
		$this->db->setQuery($query);
		$this->db->execute();
	}

	public function select($query = '')
	{
		$this->db->setQuery("SELECT * FROM {$this->table} {$query}");
		$this->db->execute();
		return $this->db->loadAssocList('id');
	}
}
