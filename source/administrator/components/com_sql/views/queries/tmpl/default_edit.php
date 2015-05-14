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

$this->document->addStyleSheet('components/com_sql/assets/css/style.css');

// @codingStandardsIgnoreStart
?>

<h3>QueriesDefaultEditView</h3>
<form action="index.php?option=com_sql&amp;controller=queries&amp;task=save&cid[]=<?php echo $this->row->id; ?>"
	  method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
		<legend><?php echo \JText::_('COM_SQL_QUERY_DETAILS'); ?></legend>
		<table class="admintable">
			<tr>
				<td width="20%" class="key">
					<label for="name">
						<?php echo \JText::_('COM_SQL_TITLE'); ?>
					</label>
				</td>
				<td width="80%">
					<input class="inputbox" type="text" id="title" name="title" size="50"
						   value="<?php echo $this->row->title; ?>" />
				</td>
			</tr>
			<tr>
				<td width="20%" class="key">
					<label for="name">
						<?php echo \JText::_('COM_SQL_QUERY'); ?>
					</label>
				</td>
				<td width="80%">
					<textarea class="text_area" id="query" name="query"
							  style="width:100%;height:70px;"><?php echo $this->row->query; ?></textarea>
				</td>
			</tr>
			</tr>
		</table>
	</fieldset>
	<input type="hidden" name="option" value="com_sql" />
	<input type="hidden" name="view" value="queries" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<?php echo \JHTML::_('form.token'); ?>
</form>

<?php
// @codingStandardsIgnoreEnd
