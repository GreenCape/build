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
 * @package     Celtic\Sql
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 */

use Celtic\Abstraction\VersionFactory;

// Run in backend (administrator)
$mainframe = \JFactory::getApplication('administrator');
$mainframe->initialise();

require_once JPATH_ADMINISTRATOR . '/components/com_sql/autoload.php';
if (!defined('JPATH_COMPONENT')) {
	define('JPATH_COMPONENT', JPATH_ADMINISTRATOR . '/components/com_sql');
}

class QueryBuilderRealLifeIntegrationTest extends PHPUnit_Framework_TestCase
{
	/** @var VersionFactory */
	private $factory;

	/** @var \JDatabaseQuery */
	private $originalQuery = null;

	/** @var \Celtic\Sql\QueryBuilder */
	private $queryBuilder = null;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->factory = new VersionFactory(JVERSION);
		$db = $this->factory->getDbo();
		$this->originalQuery = $db->getQuery(true);
		$this->queryBuilder  = new \Celtic\Sql\QueryBuilder($db);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset($this->originalQuery, $this->queryBuilder);
	}

	private function normalize($string)
	{
		$replace = array(
			'( ' => '(',
			' )' => ')',
			', ' => ',',
		);
		return trim(
			str_replace(
				array_keys($replace),
				array_values($replace),
				preg_replace('~\s+~m', ' ', $string)
			)
		);
	}

	public function testRealLiveQueryFromArticleModel()
	{
		$setup = function($query, $subQuery)
		{
			$query->select(
				'item.select', 'a.id, a.asset_id, a.title, a.alias, a.introtext, a.fulltext, ' .
				'CASE WHEN badcats.id is null THEN a.state ELSE 0 END AS state, ' .
				'a.catid, a.created, a.created_by, a.created_by_alias, ' .
				'CASE WHEN a.modified = \'0000-00-00 00:00:00\' THEN a.created ELSE a.modified END as modified, ' .
				'a.modified_by, a.checked_out, a.checked_out_time, a.publish_up, a.publish_down, ' .
				'a.images, a.urls, a.attribs, a.version, a.ordering, ' .
				'a.metakey, a.metadesc, a.access, a.hits, a.metadata, a.featured, a.language, a.xreference'
			);
			$query->from('#__content AS a');

			$query->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access')
				->join('LEFT', '#__categories AS c on c.id = a.catid');

			// Join on user table.
			$query->select('u.name AS author')
				->join('LEFT', '#__users AS u on u.id = a.created_by');

			// Join on contact table
			$subQuery->select('contact.user_id, MAX(contact.id) AS id, contact.language')
				->from('#__contact_details AS contact')
				->where('contact.published = 1')
				->group('contact.user_id, contact.language');

			$query->select('contact.id as contactid')
				->join('LEFT', '(' . $subQuery . ') AS contact ON contact.user_id = a.created_by');

			// Filter by language
			$query->where('a.language in (\'en-US\',\'*\')')
				->where('(contact.language in (\'en-US\',\'*\') OR contact.language IS NULL)');

			// Join over the categories to get parent category titles
			$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias')
				->join('LEFT', '#__categories as parent ON parent.id = c.parent_id');

			// Join on voting table
			$query->select('ROUND(v.rating_sum / v.rating_count, 0) AS rating, v.rating_count as rating_count')
				->join('LEFT', '#__content_rating AS v ON a.id = v.content_id')
				->where('a.id = 1');

			// Filter by start and end dates.
			$query->where('(a.publish_up = \'0000-00-00 00:00:00\' OR a.publish_up <= \'2013-01-01 00:00:00\')')
			->where('(a.publish_down = \'0000-00-00 00:00:00\' OR a.publish_down >= \'2013-01-01 00:00:00\')');

			// Join to check for category published state in parent categories up the tree
			// If all categories are published, badcats.id will be null, and we just use the article state
			$subQuery = ' (SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ';
			$subQuery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
			$subQuery .= 'WHERE parent.extension = \'com_content\'';
			$subQuery .= ' AND parent.published <= 0 GROUP BY cat.id)';
			$query->join('LEFT OUTER', $subQuery . ' AS badcats ON badcats.id = c.id');

			// Filter by published state.
			$query->where('(a.state = 1 OR a.state =-1)');
		};

		$db = $this->factory->getDbo();
		$subQueryA = $db->getQuery(true);

		$subQueryB  = new \Celtic\Sql\QueryBuilder($db);

		$this->assertEquals(
			$this->normalize($setup($this->originalQuery, $subQueryA)),
			$this->normalize($setup($this->queryBuilder, $subQueryB))
		);
	}
}
