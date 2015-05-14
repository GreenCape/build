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

use Celtic\Abstraction\VersionFactoryInterface;

class SqlQueriesTable
{
	private $db;

	public function __construct(VersionFactoryInterface $factory)
	{
		$this->db = $factory->getDbo();
		$this->create();
	}

	public function __destruct()
	{
		$this->drop();
	}

	private function create()
	{
		$this->execute(
			"CREATE TEMPORARY TABLE IF NOT EXISTS `test_sql_queries` (
				`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`title` VARCHAR(255) NOT NULL DEFAULT '',
				`query` TEXT NOT NULL DEFAULT '',
				PRIMARY KEY(`id`)
			) DEFAULT CHARSET = utf8"
		);
		$this->execute("TRUNCATE test_sql_queries");
	}

	private function drop()
	{
		$this->execute("DROP TEMPORARY TABLE IF EXISTS test_sql_queries");
	}

	public function execute($query)
	{
		$this->db->setQuery($query);
		$this->db->execute();
	}

	public function fetch($query = '')
	{
		$this->db->setQuery($query);
		return $this->db->loadAssocList('id');
	}

	public function insert($records)
	{
		$values = array();
		foreach ($records as $record)
		{
			$values[] = implode(
				', ',
				array(
					(int) $record[0],
					$this->db->quote($record[1]),
					$this->db->quote($record[2]),
				)
			);
		}
		$this->execute(
			"INSERT INTO `test_sql_queries` (`id`, `title`, `query`) VALUES\n("
			. implode("),\n(", $values)
			. ")"
		);
	}
}
