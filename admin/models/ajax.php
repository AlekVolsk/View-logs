<?php defined('_JEXEC') or die;
/*
 * @package     com_vlogs
 * @copyright   Copyright (C) 2018 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

class VlogsModelAjax extends JModelList
{
	private function printJson($message, $result = false, $custom = [])
	{
		if (empty($message))
		{
			$message = '< empty message >';
		}
		
		$jsonData = ['result' => $result, 'message' => $message];
		
		foreach ($custom as $key => $value)
		{
			$jsonData[$key] = $value;
		}
		
		echo json_encode($jsonData);
		
		exit;
	}

	private function getCSV($file, $delimiter = ';')
	{
		$catalog = [];

		if (($handle = fopen($file, 'r')) !== false)
		{
			while (($data = fgetcsv($handle, 10000, $delimiter)) !== false)
			{
				$catalog[] = $data;
		    }
		    fclose($handle);
		}

		return $catalog;
	}

	public function List()
	{
		$log_path = str_replace('\\', '/', JFactory::getConfig()->get('log_path'));
		$file = filter_input(INPUT_GET, 'filename');
		
		$data = $this->getCSV($log_path . '/' . $file, '	');
		for ($i = 0; $i < 6; $i++)
		{
			if (count($data[$i]) < 4 || $data[$i][0][0] == '#')
			{
				unset($data[$i]);
			}
		}

		$data = array_reverse($data);
		$html = [];

		foreach ($data as $i => $item)
		{
			$date = new DateTime($item[0]);
			$timestamp = $date->format('U');
			
			$subitem = explode(' ', $item[1]);

			$html[] = '<tr class="row' . ($i % 2) . '">' .
				'<td class="nowrap">' . JHtml::_('date', $timestamp, 'd.m.Y H:i:s') . '</td>' .
				'<td>' . $subitem[0] . '</td>' .
				'<td>' . $subitem[1] . '</td>' .
				'<td>' . $item[2] . '</td>' .
				'<td>' . htmlspecialchars($item[3]) . '</td>' .
			'</tr>';
		}

		if (!$html)
		{
			$html[] = '<div class="alert">' . JText::_('COM_VLOGS_DATA_EMPTY') . '</div>';
		}

		$this->printJson(implode('', $html), true, ['count' => count($data)]);
	}
}
