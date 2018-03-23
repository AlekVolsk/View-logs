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
		$a = [];

		if (($handle = fopen($file, 'r')) !== false)
		{
			while (($data = fgetcsv($handle, 10000, $delimiter)) !== false)
			{
				$a[] = $data;
		    }
		    fclose($handle);
		}

		return $a;
	}

	private function setCSV($file, $data, $delimiter = ';', $bom = false)
	{
		if (($handle = fopen($file, 'w')) !== false)
		{
			if ($bom)
			{
				fwrite($handle, b"\xEF\xBB\xBF");
			}
			foreach ($data as $item)
			{
				fputcsv($handle, $item, $delimiter);
		    }
		    fclose($handle);
		}
	}

	private function file_force_download($file)
	{
		set_time_limit(0);
		if (file_exists($file))
		{
			if (ob_get_level())
			{
				ob_end_clean();
			}
			header('Content-Description: File Transfer');
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename=' . basename($file));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			return (bool)readfile($file);
		}
		else
		{
			return false;
		}
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
		$cnt = count($data);
		
		if ($cnt)
		{
			$html[] = '<table class="table table-striped"><thead><tr>';
			$html[] = '<th width="10%">' . JText::_('COM_VLOGS_COLUMN_DT') . '</th>';
			$html[] = '<th width="5%">' . JText::_('COM_VLOGS_COLUMN_PRIORITY') . '</th>';
			$html[] = '<th width="5%">' . JText::_('COM_VLOGS_COLUMN_IP') . '</th>';
			$html[] = '<th width="5%">' . JText::_('COM_VLOGS_COLUMN_CATEGORY') . '</th>';
			$html[] = '<th>' . JText::_('COM_VLOGS_COLUMN_MSG') . '</th>';
			$html[] = '</tr></thead><tbody>';

			foreach ($data as $i => $item)
			{
				$json = json_decode($item[3]);
				$json_result = json_last_error() === JSON_ERROR_NONE;
				
				$date = new DateTime($item[0]);
				$timestamp = $date->format('U');
				
				$subitem = explode(' ', $item[1]);

				$html[] = '<tr class="row' . ($i % 2) . '">' .
					'<td class="nowrap">' . JHtml::_('date', $timestamp, 'd.m.Y H:i:s') . '</td>' .
					'<td>' . trim($subitem[0]) . '</td>' .
					'<td>' . trim($subitem[1]) . '</td>' .
					'<td>' . $item[2] . '</td>' .
					'<td>' . ($json_result ? '<pre>' . $json . '</pre>' : htmlspecialchars($item[3])) . '</td>' .
				'</tr>';
			}

			$html[] = '</tbody></table>';

		}
		else
		{
			$html[] = '<div class="alert">' . JText::_('COM_VLOGS_DATA_EMPTY') . '</div>';
		}

		$this->printJson(implode('', $html), true, ['count' => $cnt]);
	}

	public function dwFile()
	{
		$log_path = str_replace('\\', '/', JFactory::getConfig()->get('log_path'));
		$file = filter_input(INPUT_GET, 'filename');
		$bom = (bool)filter_input(INPUT_GET, 'bom');
		
		$data = $this->getCSV($log_path . '/' . $file, '	');
		foreach ($data as $i => $item)
		{
			if (count($item) < 4 || $item[0][0] == '#')
			{
				unset($data[$i]);
			}
			else
			{
				$subitem = explode(' ', $item[1]);
				$data[$i][4] = $item[3];
				$data[$i][3] = $item[2];
				$data[$i][2] = trim($subitem[1]);
				$data[$i][1] = trim($subitem[0]);
			}
		}

		$data = array_reverse($data);
		
		$fpath = str_replace('\\', '/', JFactory::getConfig()->get('tmp_path'));
		$f = pathinfo($fpath . '/' . $file);
		$file = $fpath . '/' . $f['filename'] . '_' . JHtml::_('date', time(), 'Y-m-d-H-i-s') . '.csv';
		
		$this->setCSV($file, $data, $bom ? ';' : ',', $bom);
		$this->file_force_download($file);
		unlink($file);
		
		exit;
	}

	public function DelFile()
	{
		$log_path = str_replace('\\', '/', JFactory::getConfig()->get('log_path'));
		$file = filter_input(INPUT_GET, 'filename');
		
		$this->printJson('', unlink($log_path . '/' . $file));
	}
}
