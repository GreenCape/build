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

$script = <<<SCRIPT
	function changeQuery() {
		limit = 'LIMIT ' + document.getElementById('limit').value;
		sel = document.getElementById('command').value;

		if (sel != 'SELECT * FROM') {
			limit = '';
		}

		table = '';

		if (sel == 'SELECT * FROM table_name PROCEDURE ANALYSE()') {
			table = document.getElementById('table').value;
			document.getElementById('query').value = 'SELECT * FROM ' + table + ' PROCEDURE ANALYSE()';
			return;
		}

		if (sel == 'SELECT * FROM' ||
			sel == 'SHOW KEYS FROM' ||
			sel == 'SHOW FIELDS FROM' ||
			sel == 'REPAIR TABLE' ||
			sel == 'OPTIMIZE TABLE' ||
			sel == 'CHECK TABLE' ||
			sel == 'SHOW FULL COLUMNS FROM' ||
			sel == 'SHOW INDEX FROM' ||
			sel == 'SHOW CREATE TABLE' ||
			sel == 'ANALYZE TABLE') {
			table = ' ' + document.getElementById('table').value + ' ' + limit;
		}

		document.getElementById('query').value = sel + table;
	}
	window.addEvent('domready', function() {
		if (document.getElementById('query').value == '') {
			changeQuery();
		}
	});
SCRIPT;

$this->document->addStyleSheet('components/com_sql/assets/css/style.css');
$this->document->addScriptDeclaration($script);

// @codingStandardsIgnoreStart
?>

<h3>SqlDefaultView</h3>
<form id="adminForm" name="adminForm" action="index.php?option=com_sql" method="post">
	<div id="sql-query">
		<div>
			<label for="command"><?php echo \JText::_('COM_SQL_COMMAND') . ': '; ?></label>
			<?php
			$options = array();
			$options[] = \JHtml::_('select.option', "SELECT * FROM", "SELECT *");
			$options[] = \JHtml::_('select.option', "SHOW DATABASES", "SHOW DATABASES");
			$options[] = \JHtml::_('select.option', "SHOW TABLES", "SHOW TABLES");
			$options[] = \JHtml::_('select.option', "SHOW FULL COLUMNS FROM", "SHOW COLUMNS");
			$options[] = \JHtml::_('select.option', "SHOW INDEX FROM", "SHOW INDEX");
			$options[] = \JHtml::_('select.option', "SHOW TABLE STATUS", "SHOW TABLE STATUS");
			$options[] = \JHtml::_('select.option', "SHOW STATUS", "SHOW STATUS");
			$options[] = \JHtml::_('select.option', "SHOW VARIABLES", "SHOW VARIABLES");
			$options[] = \JHtml::_('select.option', "SHOW FULL PROCESSLIST", "SHOW PROCESSLIST");
			$options[] = \JHtml::_('select.option', "SHOW GRANTS", "SHOW GRANTS");
			$options[] = \JHtml::_('select.option', "SHOW CREATE TABLE", "SHOW CREATE TABLE");
			$options[] = \JHtml::_('select.option', "SHOW MASTER STATUS", "SHOW MASTER STATUS");
			$options[] = \JHtml::_('select.option', "SHOW MASTER LOGS", "SHOW MASTER LOGS");
			$options[] = \JHtml::_('select.option', "SHOW SLAVE STATUS", "SHOW SLAVE STATUS");
			$options[] = \JHtml::_('select.option', "SHOW KEYS FROM", "SHOW KEYS");
			$options[] = \JHtml::_('select.option', "SHOW FIELDS FROM", "SHOW FIELDS");
			$options[] = \JHtml::_('select.option', "REPAIR TABLE", "REPAIR TABLE");
			$options[] = \JHtml::_('select.option', "OPTIMIZE TABLE", "OPTIMIZE TABLE");
			$options[] = \JHtml::_('select.option', "CHECK TABLE", "CHECK TABLE");
			$options[] = \JHtml::_('select.option', "SELECT * FROM table_name PROCEDURE ANALYSE()", "SELECT * FROM ... PROCEDURE ANALYSE()");
			$options[] = \JHtml::_('select.option', "ANALYZE TABLE", "ANALYZE TABLE");
			echo \JHtml::_(
				'select.genericlist',
				$options,
				'command',
				array(
					 'class'    => 'textarea',
					 'onchange' => 'changeQuery();',
				),
				'value',
				'text',
				$this->command
			);
			?>

			<label for="table"><?php echo \JText::_('COM_SQL_TABLE') . ': '; ?></label>
			<?php
			$options = array();
			foreach ($this->model->getTableList() as $table)
			{
				$options[] = \JHtml::_('select.option', $table);
			}
			echo \JHtml::_(
				'select.genericlist',
				$options,
				'table',
				array(
					 'class'    => 'textarea',
					 'onchange' => 'changeQuery();',
				),
				'value',
				'text',
				$this->table
			);
			?>

			<label for="limit"><?php echo \JText::_('COM_SQL_RECORDS') . ': '; ?></label>
			<input class="text_area" type="text" size="3" id="limit" name="limit" value="<?php echo $this->limit; ?>" onchange="changeQuery();">
		</div>
		<div>
			<label for="query"><?php echo \JText::_('COM_SQL_QUERY') . ': '; ?></label>
			<textarea class="text_area" id="query" name="query"
					  style="width:100%;height:70px;"><?php echo $this->query; ?></textarea>
		</div>
	</div>

	<?php if (!empty($this->data)) : ?>
	<h1><?php echo \JText::_('COM_SQL_RESULTS'); ?></h1>
	<?php foreach ($this->data as $result) : ?>
		<?php echo $this->getTableHtml($result); ?>
	<?php endforeach; ?>
	<?php endif; ?>

	<input type="hidden" name="option" value="com_sql" />
	<input type="hidden" name="view" value="sql" />
	<input type="hidden" name="task" value="">

	<?php echo \JHTML::_('form.token'); ?>
</form>

<?php
// @codingStandardsIgnoreEnd
