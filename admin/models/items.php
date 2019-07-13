<?php defined('_JEXEC') or die;

class VlogsModelItems extends JModelList
{
	public function getItems()
	{
		$config = JFactory::getConfig();
		$log_path = str_replace('\\', '/', $config->get('log_path'));
		$items = glob($log_path . '/*.*');

		foreach ($items as $i => &$item) {
			$item = basename($item);
			if ($item == 'index.html') {
				unset($items[$i]);
			}
		}
		$items = array_values($items);

		$phpErrorLog = ini_get('error_log');
		if ($phpErrorLog && file_exists($phpErrorLog)) {
			$items[] = 'PHP error log';
		}

		return $items;
	}
}
