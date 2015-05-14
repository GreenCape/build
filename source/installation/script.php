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
 * Class com_SqlInstallerScript
 *
 * @package  Celtic\SqlManager
 * @since    1.0.0
 */
class Com_SqlInstallerScript
{
	/**
	 * Postflight
	 *
	 * @return  void
	 */
	public function postflight()
	{
		\JFactory::getLanguage()->load('com_sql');

		$results = array(
			'com_sql' => '',
		);
		$this->displayResultScreen($results);
	}

	/**
	 * Display the results of the installation
	 *
	 * @param   array  $results  The results of the sub-packages
	 *
	 * @return  void
	 */
	private function displayResultScreen($results)
	{
		// @codingStandardsIgnoreStart
		?>
		<div class="install">
			<?php
			$this->displayHeader();
			$this->displayResults($results);
			?>
		</div>
		<?php
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Display the result header
	 *
	 * @return  void
	 */
	private function displayHeader()
	{
		// @codingStandardsIgnoreStart
		?>
		<a href="http://www.bsds.de" style="float:right;">
			<img src="components/com_sql/assets/images/celtic.png" alt="Celtic  - because reliability matters"/>
		</a>
		<img src="components/com_sql/assets/images/logo.png" alt="<?php echo \JText::_('COM_SQL'); ?>" style="float:left;margin:0 2em 1em 0;"/>
		<div style="margin-left: 160px;">
			<h1><?php echo \JText::_('COM_SQL'); ?></h1>
			<p><?php echo \JText::_('COM_SQL_DESCRIPTION'); ?></p>
			<ul>
				<li><a href="index.php?option=com_sql"><?php echo \JText::_('COM_SQL_INSTALLATION_START_NOW'); ?></a></li>
			</ul>
		</div>
		<?php
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Display the results
	 *
	 * @param   array  $results  The results of the sub-packages
	 *
	 * @return  void
	 */
	private function displayResults($results)
	{
		// @codingStandardsIgnoreStart
		?>
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th class="title"><?php echo \JText::_('COM_SQL_INSTALLATION_EXTENSION'); ?></th>
					<th width="30%"><?php echo \JText::_('COM_SQL_INSTALLATION_STATUS'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="2"><small>(C) 2013 BSDS Braczek Software- und DatenSysteme. All rights reserved.</small></td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($results as $extension => $message) : ?>
					<tr class="row0">
						<td class="key">
							<?php echo \JText::_($extension); ?>
						</td>
						<td>
							<?php if (empty($message)) : ?>
								<?php echo \JText::_('COM_SQL_INSTALLATION_STATUS_SUCCESS'); ?>
							<?php else : ?>
								<?php echo $message; ?>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		// @codingStandardsIgnoreEnd
	}
}

/**
 * Stub for Joomla! 1.5
 *
 * @return  void
 */
function com_install()
{
	$installer = new Com_SqlInstallerScript;
	$installer->postflight();
}
