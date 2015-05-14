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

$helper = new SqlHelper(new VersionFactory(JVERSION));

$this->document->addStyleSheet('components/com_sql/assets/css/style.css');

// @codingStandardsIgnoreStart
?>
<h3>EditDefaultView</h3>
<form id="adminForm" name="adminForm" action="index.php?option=com_sql" method="post">
	<table class="adminlist table table-striped">
		<?php
		$k = 0;
		foreach ($this->fields as $field => $fieldInfo)
		{
			$type = $fieldInfo->Type;
			?>
			<tr valign="top" class="row<?php echo $k; ?>">
				<td width="20%" class="key">
					<label>
						<?php echo $field; ?>:
						<?php echo $this->key == $field ? "<strong>[PK]</strong>" : ""; ?>
						<span class="datatype"><?php echo $type; ?></span>
					</label>
				</td>
				<td width="80%">
					<?php
					if (isset($this->row[$field]))
					{
						$value = $this->row[$field];
					}
					else
					{
						$value = $fieldInfo->Default;
						if ($value == 'NULL')
						{
							$value = '';
						}
					}
					echo $helper->renderHtml($field, $type, $value);
					?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
	</table>

	<input type="hidden" name="option" value="com_sql" />
	<input type="hidden" name="view" value="edit" />
	<input type="hidden" name="task" value="">
	<input type="hidden" name="id" value="<?php echo $this->id; ?>">
	<input type="hidden" name="key" value="<?php echo $this->key; ?>">
	<input type="hidden" name="query" value="<?php echo $this->query; ?>">
	<input type="hidden" name="table" value="<?php echo $this->table; ?>">

	<?php echo \JHTML::_('form.token'); ?>
</form>

<?php
// @codingStandardsIgnoreEnd
