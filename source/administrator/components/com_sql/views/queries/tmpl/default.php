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

<h3>QueriesDefaultView</h3>
<form action="index.php?option=com_sql&amp;controller=queries" method="post" name="adminForm" id="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo \JText::_('Filter'); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>"
					   class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo \JText::_('Go'); ?></button>
				<button
					onclick="document.getElementById('search').value='';value='';this.form.submit();"><?php echo \JText::_('Reset'); ?></button>
			</td>
		</tr>
	</table>

	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th width="5">
					<?php echo \JText::_('#'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value=""
						   onclick="checkAll(<?php echo count($this->items); ?>);" />
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo \JHTML::_('grid.sort', \JText::_('ID'), 'id', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo \JText::_('COM_SQL_RUN'); ?>
				</th>
				<th width="15%">
					<?php echo \JHTML::_('grid.sort', \JText::_('COM_SQL_TITLE'), 'title', @$this->lists['order_Dir'], @$this->lists['order']); ?>
				</th>
				<th class="title">
					<?php echo \JText::_('COM_SQL_QUERY'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = $k = 0;
			foreach ($this->items as $row)
			{
				$edit_link = \JRoute::_('index.php?option=com_sql&view=queries&task=edit&cid[]=' . $row->id);
				$run_link  = \JRoute::_('index.php?option=com_sql&query=' . urlencode($row->query));

				$checked = \JHTML::_('grid.id', $i, $row->id);
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td>
						<?php echo $checked; ?>
					</td>
					<td align="center">
						<?php echo $row->id; ?>
					</td>
					<td align="center">
						<a href="<?php echo $run_link; ?>"><img
								src="components/com_sql/assets/images/icon-16-run.png" width="16px" height="16px"
								style="vertical-align:middle;" alt="<?php echo \JText::_('COM_SQL_RUN_QUERY'); ?>"
								title="<?php echo \JText::_('COM_SQL_RUN_QUERY'); ?>" /> </a>
					</td>
					<td>
						<a href="<?php echo $edit_link; ?>">
							<?php echo $row->title; ?>
						</a>
					</td>
					<td>
						<?php echo $row->query; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
				$i++;
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="option" value="com_sql" />
	<input type="hidden" name="view" value="queries" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

	<?php echo \JHTML::_('form.token'); ?>
</form>

<?php
// @codingStandardsIgnoreEnd
