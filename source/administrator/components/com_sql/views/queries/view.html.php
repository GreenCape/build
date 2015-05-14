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

defined('_JEXEC') or die('Restricted access');

/**
 * Class SqlViewQueries
 *
 * @package  Celtic\SqlManager
 * @since    1.0.0
 */
class SqlViewQueries extends SqlView
{
	/** @var  SqlModelQueries  The main model */
	protected $model;

	/**
	 * Display
	 *
	 * @param   string  $tpl  An optional template
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$mainframe = \JFactory::getApplication();

		\JToolBarHelper::title(\JText::_('Sql') . ' - ' . \JText::_('COM_SQL_SAVED_QUERIES'), 'sql');
		\JToolBarHelper::editList();
		\JToolBarHelper::deleteList();

		// ACL
		if (version_compare(JVERSION, '1.6.0', 'ge') && \JFactory::getUser()->authorise('core.admin', 'com_sql'))
		{
			\JToolBarHelper::divider();
			\JToolBarHelper::preferences('com_sql', '550');
		}

		$this->mainframe = \JFactory::getApplication();

		$filter_order     = $mainframe->getUserStateFromRequest('com_sql.queries.filter_order', 'filter_order', 'title', 'string');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_sql.queries.filter_order_Dir', 'filter_order_Dir', '', 'word');
		$search           = $mainframe->getUserStateFromRequest('com_sql.queries.search', 'search', '', 'string');

		// Table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order']     = $filter_order;

		// Search filter
		$lists['search'] = $search;

		$this->lists      = $lists;
		$this->items      = $this->model->getData();
		$this->pagination = $this->model->getPagination();

		parent::display($tpl);
	}

	/**
	 * Edit
	 *
	 * @param   string  $tpl  An optional template
	 *
	 * @return  void
	 */
	public function edit($tpl = null)
	{
		// Toolbar
		\JToolBarHelper::title(\JText::_('Sql'), 'sql');
		\JToolBarHelper::save();
		\JToolBarHelper::cancel();

		parent::display($tpl);
	}
}
