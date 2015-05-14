<?php
/**
 * Celtic Database - SQL Database manager for Joomla!
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,USA.
 *
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @package    Celtic\SqlManager
 * @author     Niels Braczek <nbraczek@bsds.de>
 * @copyright  Copyright (C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL
 */

use Celtic\Abstraction\VersionFactory;

defined('_JEXEC') or die('Restricted access');

require_once __DIR__ . '/autoload.php';

/**
 * @startuml
 *
 * activate sql.php
 *
 * sql.php -->> VersionFactory: «create»
 * activate VersionFactory
 *
 * sql.php ->> VersionFactory: getAssertions()
 * VersionFactory -->> Assertions: «create»
 * activate Assertions
 * sql.php <<- VersionFactory: assert
 *
 * sql.php ->> Assertions: userCanManage()
 * note over Assertions: Return on success,\n Exception otherwise
 * sql.php <<-- Assertions
 *
 * sql.php ->> VersionFactory: getInput()
 * VersionFactory -->> Input: «create»
 * activate Input
 * sql.php <<- VersionFactory: input
 *
 * sql.php ->> Input: get*()
 * sql.php <<- Input: controller, task
 *
 * note over SqlController: One of\nSqlController\nSqlControllerQueries\nSqlControllerSql\ndepending on $controller
 * sql.php -->> SqlController: «create»
 * activate SqlController
 *
 * sql.php ->> SqlController: execute(task)
 * sql.php <<-- SqlController
 *
 * sql.php ->> SqlController: redirect()
 *
 * deactivate sql.php
 * @enduml
 */

if (empty($factory))
{
	$factory = new VersionFactory(JVERSION);
}
$factory->getAssertions()->userCanManage('com_sql');
require_once JPATH_COMPONENT . '/toolbar.php';

$input = $factory->getInput();
$className = 'SqlController' . ucfirst($input->getWord('view', 'Sql'));

/** @var SqlController $controller */
$controller = new $className($factory);
$controller->execute($input->getCmd('task'));
$controller->redirect();
