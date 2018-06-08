<?php defined('_JEXEC') or die;
/*
 * @package     com_vlogs
 * @copyright   Copyright (C) 2018 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

class VlogsModelItems extends JModelList
{
	public function getItems()
	{
		$config = JFactory::getConfig();
		$log_path = str_replace('\\', '/', $config->get('log_path'));
		$items = glob($log_path . '/*.*');
		
		foreach ($items as $i => &$item)
		{
			$item = basename($item);
			if ($item == 'index.html')
			{
				unset($items[$i]);
			}
		}
		$items = array_values($items);
		
		$phpErrorLog = ini_get('error_log');
		if (file_exists($phpErrorLog))
		{
			$items[] = 'PHP error log';
		}
		
		return $items;
	}
}
