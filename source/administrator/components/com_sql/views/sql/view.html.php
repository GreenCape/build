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
 * Class SqlViewSql
 *
 * @package  Celtic\SqlManager
 * @since    1.0
 */
class SqlViewSql extends SqlView
{
	/** @var  \JDocument */
	public $document;

	/** @var  string */
	public $command;

	/** @var  string */
	public $query;

	/** @var  string */
	public $table;

	/** @var  int */
	public $limit;

	/** @var  array */
	public $data;

	/** @var  string */
	public $key;

	/** @var  string */
	public $id;

	/**
	 * Display
	 *
	 * @param   string  $tpl  An optional template
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->addToolbar();

		echo $this->loadTemplate($tpl);
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
		\JToolBarHelper::title(\JText::_('COM_SQL') . ': ' . $this->table . ' [' . $this->key . ' = ' . $this->id . ']', 'sql');
		\JToolBarHelper::apply();
		\JToolBarHelper::save();
		\JToolBarHelper::divider();
		\JToolBarHelper::cancel();

		$row = $this->model->getData($this->query, $this->key);

		$this->row          = $row;

		$this->fields = $this->model->getFields($this->table);

		parent::display($tpl);
	}

	/**
	 * Render a single result set
	 *
	 * @param   QueryResult  $result  Query result
	 *
	 * @return  string  HTML table
	 */
	public function getTableHtml($result)
	{
		$body = '<div class="query-result"><code class="sql">' . $result->query . '</code> ';
		$body .= '<span class="total">' . $this->plural('COM_SQL_RESULT_COUNT', $result->total) . '</span> ';

		if (!empty($result->rows))
		{
			$body .= '<table class="adminlist table table-striped">';

			$body .= $this->getTableHeadHtml($result);
			$body .= $this->getTableBodyHtml($result);

			$body .= '</table>';
		}

		return $body . '</div>';
	}

	/**
	 * Pluralize
	 *
	 * @param   string  $term   The base term (translation identifier)
	 * @param   int     $count  The number
	 *
	 * @todo    Move this to the version abstraction library
	 *
	 * @return  string
	 */
	private function plural($term, $count)
	{
		if (is_callable('\\JText::plural'))
		{
			return \JText::plural($term, $count);
		}
		else
		{
			return \JText::sprintf($term, $count);
		}
	}

	/**
	 * Render the action links
	 *
	 * @param   QueryResult  $result  Query result
	 * @param   int          $id      The primary key value
	 *
	 * @return  string
	 */
	private function renderActions($result, $id)
	{
		$body = '';
		$body .= '<td align=center nowrap>';
		$body .= '<a href="index.php'
				. '?option=com_sql'
				. '&task=edit'
				. '&table=' . urlencode($result->table)
				. '&query=' . urlencode($result->query)
				. '&key=' . $result->key
				. '&id=' . $id
				. '">';
		$body .= '<img border="0" '
				. 'src="components/com_sql/assets/images/icon-16-edit.png" '
				. 'alt="' . \JText::_('COM_SQL_EDIT') . '" '
				. 'title="' . \JText::_('COM_SQL_EDIT') . '" '
				. '/>';
		$body .= '</a>';
		$body .= '&nbsp;';
		$body .= '<a href="#" onclick="if (confirm(\'Are you sure you want to delete this record?\')) {this.href=\'index.php'
				. '?option=com_sql'
				. '&view=sql'
				. '&task=delete'
				. '&table=' . urlencode($result->table)
				. '&query=' . urlencode($result->query)
				. '&key=' . $result->key
				. '&id=' . $id
				. '\'};">';
		$body .= '<img border="0" '
				. 'src="components/com_sql/assets/images/icon-16-delete.png" '
				. 'alt="' . \JText::_('COM_SQL_DELETE') . '" '
				. 'title="' . \JText::_('COM_SQL_DELETE') . '" '
				. '/>';
		$body .= '</a>';
		$body .= '</td>';

		return $body;
	}

	/**
	 * Add the toolbar
	 *
	 * @todo Refactor Toolbar setup to be version agnostic
	 *
	 * @return  void
	 */
	protected function addToolbar()
	{
		\JToolBarHelper::title(\JText::_('COM_SQL') . ' - ' . \JText::_('COM_SQL_RUN_QUERY'), 'sql');

		\JToolBarHelper::custom('display', 'run.png', 'run.png', \JText::_('COM_SQL_RUN_QUERY'), false);
		\JToolBarHelper::divider();
		\JToolBarHelper::custom('savequery', 'save.png', 'save.png', \JText::_('COM_SQL_SAVE_QUERY'), false);
		\JToolBarHelper::divider();
		\JToolBarHelper::custom('csv', 'export.png', 'export.png', \JText::_('COM_SQL_EXPORT_CSV'), false);

		// ACL
		if (version_compare(JVERSION, '1.6.0', 'ge') && \JFactory::getUser()->authorise('core.admin', 'com_sql'))
		{
			\JToolBarHelper::divider();
			\JToolBarHelper::preferences('com_sql', '550');
		}
	}

	/**
	 * Get the table headings for a result set
	 *
	 * @param   QueryResult  $result  The result set
	 *
	 * @return  string  The headings
	 */
	protected function getTableHeadHtml($result)
	{
		$html = '<thead>';
		$html .= "<tr>";

		// Display table header
		if ($result->isSelect())
		{
			$html .= '<th>' . \JText::_('COM_SQL_ACTION') . '</th>';
		}

		foreach (array_keys($result->rows[0]) as $var)
		{
			if ($this->isValidColumnName($var))
			{
				$html .= '<th>' . $var . "</th>";
			}
		}

		$html .= "</tr>";
		$html .= "</thead>";

		return $html;
	}

	/**
	 * Get the table body for a result set
	 *
	 * @param   QueryResult  $result  The result set
	 *
	 * @return  string  The body
	 */
	protected function getTableBodyHtml($result)
	{
		$html = '<tbody>';

		// Display table rows
		$k = 0;
		foreach ($result->rows as $row)
		{
			$html .= '<tr class="row' . $k . '">';

			$format = '<pre>%s</pre>';
			if ($result->isSelect())
			{
				$html .= $this->renderActions($result, $row[$result->key]);
				$format = '<div class="limited">%s</div>';
			}

			foreach ($row as $var => $val)
			{
				if ($this->isValidColumnName($var))
				{
					$html .= '<td>' . sprintf($format, htmlspecialchars($val)) . "</td>\n";
				}
			}

			$html .= "</tr>";
			$k = 1 - $k;
		}

		$html .= "</tbody>";

		return $html;
	}

	/**
	 * Check if a column name is valid
	 *
	 * @param   string  $name  The column name
	 *
	 * @return  bool
	 */
	private function isValidColumnName($name)
	{
		return preg_match("/[a-zA-Z]+/", $name);
	}
}
