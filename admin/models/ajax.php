<?php defined('_JEXEC') or die;

class VlogsModelAjax extends JModelList
{
	private function printJson($message, $result = false, $custom = [])
	{
		if (empty($message)) {
			$message = '< empty message >';
		}

		$jsonData = ['result' => $result, 'message' => $message];

		foreach ($custom as $key => $value) {
			$jsonData[$key] = $value;
		}

		echo json_encode($jsonData);

		exit;
	}

	private function getPhpLog()
	{
		$a = [];

		if (($handle = fopen(ini_get('error_log'), 'r')) !== false) {
			while (($data = fgets($handle)) !== false) {
				$a[] = $data;
			}
			fclose($handle);
		}

		$a = array_reverse($a);

		return $a;
	}

	private function getCSV($file, $delimiter = ';')
	{
		$a = [];
		$slen = JComponentHelper::getParams('com_vlogs')->get('slen', 32768);

		if (($handle = fopen($file, 'r')) !== false) {
			while (($data = fgetcsv($handle, $slen, $delimiter)) !== false) {
				$a[] = $data;
			}
			fclose($handle);
		}

		return $a;
	}

	private function setCSV($file, $data, $delimiter = ';', $bom = false)
	{
		if (($handle = fopen($file, 'w')) !== false) {
			if ($bom) {
				fwrite($handle, "\xEF\xBB\xBF");
			}
			foreach ($data as $item) {
				fputcsv($handle, $item, $delimiter);
			}
			fclose($handle);
		}
	}

	private function file_force_download($file)
	{
		set_time_limit(0);
		if (file_exists($file)) {
			if (ob_get_level()) {
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
		} else {
			return false;
		}
	}

	protected function ListPHPEL()
	{
		$data = $this->getPhpLog();
		$cnt = 0;
		$html = [];

		if (!empty($data)) {
			$html[] = '<table class="com_vlogs table table-striped"><thead><tr>';
			$html[] = '<th width="10%">' . JText::_('COM_VLOGS_COLUMN_DT') . '</th>';
			$html[] = '<th width="10%">' . JText::_('COM_VLOGS_COLUMN_PRIORITY') . '</th>';
			$html[] = '<th width="80%">' . JText::_('COM_VLOGS_COLUMN_MSG') . '</th>';
			$html[] = '</tr></thead><tbody>';

			foreach ($data as $item) {
				if (empty($item)) {
					continue;
				}
				$tmp = explode('] ', $item);
				$date = substr($tmp[0], 1, strlen($tmp[0]) - 1);
				$date = explode(' ', $date);
				$date = new DateTime($date[0] . 'T' . $date[1], new DateTimeZone($date[2]));
				$date = date_format($date, 'Y-m-d H:i:s');
				[$type, $msg] = explode(':  ', $tmp[1]);
				$html[] = '<tr>';
				$html[] = '<td>' . $date . '</td>';
				switch ($type) {
					case 'PHP Error':
						$html[] = '<td class="text-error">' . $type . '</td>';
						break;
					case 'PHP Warning':
						$html[] = '<td class="text-warning">' . $type . '</td>';
						break;
					case 'PHP Notice':
						$html[] = '<td class="text-info">' . $type . '</td>';
						break;
					default:
						$html[] = '<td>' . $type . '</td>';
				}
				$html[] = '<td>' . $msg . '</td>';
				$html[] = '</tr>';
				$cnt++;
			}
			$html[] = '</tbody></table>';
		} else {
			$html[] = '<div class="alert">' . JText::_('COM_VLOGS_DATA_EMPTY') . '</div>';
		}

		$this->printJson(implode('', $html), true, ['count' => $cnt]);
	}

	public function List()
	{
		$log_path = str_replace('\\', '/', JFactory::getConfig()->get('log_path'));
		$file = filter_input(INPUT_GET, 'filename');
		if ($file === 'PHP error log') {
			$this->ListPHPEL();
		}

		$columns = '';
		$data = $this->getCSV($log_path . '/' . $file, '	');
		for ($i = 0; $i < 6; $i++) {
			if (count($data[$i]) < 4 || $data[$i][0][0] == '#') {
				if (strpos($data[$i][0], '#Fields:') !== false) {
					$columns = $data[$i];
				}
				unset($data[$i]);
			}
		}
		if ($columns) {
			$columns = explode(' ', implode(' ', $columns));
			unset($columns[0]);
			$columns = array_values($columns);
		}

		$data = array_reverse($data);
		$html = [];
		$cnt = count($data);

		if ($columns && $cnt) {
			$html[] = '<table class="com_vlogs table table-striped"><thead><tr>';

			foreach ($columns as $col) {
				switch ($col) {
					case 'datetime':
						$html[] = '<th width="10%">' . JText::_('COM_VLOGS_COLUMN_DT') . '</th>';
						break;
					case 'date':
						$html[] = '<th width="5%">' . JText::_('COM_VLOGS_COLUMN_DATE') . '</th>';
						break;
					case 'time':
						$html[] = '<th width="5%">' . JText::_('COM_VLOGS_COLUMN_TIME') . '</th>';
						break;
					case 'priority':
						$html[] = '<th width="5%">' . JText::_('COM_VLOGS_COLUMN_PRIORITY') . '</th>';
						break;
					case 'clientip':
						$html[] = '<th width="5%">' . JText::_('COM_VLOGS_COLUMN_IP') . '</th>';
						break;
					case 'category':
						$html[] = '<th width="5%">' . JText::_('COM_VLOGS_COLUMN_CATEGORY') . '</th>';
						break;
					case 'message':
						$cw = (count($columns) - 1) * 5;
						if (in_array('datetime', $columns)) $cw += 5;
						$html[] = '<th width="' . (100 - $cw) . '%">' . JText::_('COM_VLOGS_COLUMN_MSG') . '</th>';
						break;
					default:
						$html[] = '<th>' . $col . '</th>';
				}
			}

			$html[] = '</tr></thead><tbody>';

			foreach ($data as $i => $item) {
				if (count($item) == 1) {
					$item = explode(' ', $item[0]);
				}

				if (count($item) < count($columns)) {
					$ci = count($item) - 1;
					$msg = $item[$ci];
					unset($item[$ci]);
					$item = explode(' ', implode(' ', $item));
					$item[] = $msg;
					unset($msg);
				}

				$html[] = '<tr class="row' . ($i % 2) . '">';
				foreach ($item as $j => $dataitem) {
					switch (strtolower($columns[$j])) {
						case 'datetime':
							$date = new DateTime($dataitem);
							$dataitem = $date->format('U');
							$html[] = '<td class="nowrap">' . JHtml::_('date', $dataitem, 'Y-m-d H:i:s') . '</td>';
							break;
						case 'priority':
							switch (strtolower($dataitem)) {
								case 'emergency':
									$html[] = '<td class="text-error">' . $dataitem . '</td>';
									break;
								case 'alert':
									$html[] = '<td class="text-warning">' . $dataitem . '</td>';
									break;
								case 'critical':
									$html[] = '<td class="text-error">' . $dataitem . '</td>';
									break;
								case 'error':
									$html[] = '<td class="text-error">' . $dataitem . '</td>';
									break;
								case 'warning':
									$html[] = '<td class="text-warning">' . $dataitem . '</td>';
									break;
								case 'notice':
									$html[] = '<td class="text-info">' . $dataitem . '</td>';
									break;
								case 'info':
									$html[] = '<td class="text-info">' . $dataitem . '</td>';
									break;
								case 'debug':
									$html[] = '<td class="text-info">' . $dataitem . '</td>';
									break;
								default:
									$html[] = '<td>' . $dataitem . '</td>';
							}
							break;
						case 'message':
							$json = json_decode($dataitem, true);
							$json_result = json_last_error() === JSON_ERROR_NONE;
							$html[] = '<td>' . ($json_result ? '<p><a onclick="jQuery(this).parent().next(\'pre\').slideToggle(200);" style="cursor:pointer">' . JText::_('COM_VLOGS_COLUMN_MSG_JSON_TITLE') . '</a></p><pre style="display:none">' . print_r($json, true) . '</pre>' : htmlspecialchars($dataitem)) . '</td>';
							break;
						default:
							$html[] = '<td>' . $dataitem . '</td>';
					}
				}
				$html[] = '</tr>';
			}

			$html[] = '</tbody></table>';

		} else {
			$html[] = '<div class="alert">' . JText::_('COM_VLOGS_DATA_EMPTY') . '</div>';
		}

		$this->printJson(implode('', $html), true, ['count' => $cnt]);
	}

	public function dwFile()
	{
		$log_path = str_replace('\\', '/', JFactory::getConfig()->get('log_path'));
		$file = filter_input(INPUT_GET, 'filename');
		$bom = (bool)filter_input(INPUT_GET, 'bom');
		$fpath = str_replace('\\', '/', JFactory::getConfig()->get('tmp_path'));

		if ($file === 'PHP error log') {
			$data = [];
			$log = $this->getPhpLog();
			foreach ($log as $item) {
				if (empty($item)) {
					continue;
				}
				$tmp = explode('] ', $item);
				$date = substr($tmp[0], 1, strlen($tmp[0]) - 1);
				$date = explode(' ', $date);
				$date = new DateTime($date[0] . 'T' . $date[1], new DateTimeZone($date[2]));
				$date = date_format($date, 'Y-m-d H:i:s');
				[$type, $msg] = explode(':  ', $tmp[1]);
				$data[] = [$date, $type, trim($msg)];
			}

			$fileName = pathinfo(ini_get('error_log'))['filename'];
		} else {
			$data = $this->getCSV($log_path . '/' . $file, '	');
			foreach ($data as $i => $item) {
				if ($i < 6 && (count($item) < 4 || $item[0][0] == '#')) {
					unset($data[$i]);
				} else {
					if (count($item) == 1) {
						$item = explode(' ', $item[0]);
					} else {
						$ci = count($item) - 1;
						$msg = $item[$ci];
						unset($item[$ci]);
						$item = explode(' ', implode(' ', $item));
						$item[] = $msg;
						unset($msg);
					}

					$data[$i] = $item;
				}
			}

			$fileName = pathinfo($fpath . '/' . $file)['filename'];
		}

		$data = array_reverse($data);

		$file = $fpath . '/' . $fileName . '_' . JHtml::_('date', time(), 'Y-m-d-H-i-s') . '.csv';

		$this->setCSV($file, $data, $bom ? ';' : ',', $bom);
		$this->file_force_download($file);
		unlink($file);

		exit;
	}

	public function DelFile()
	{
		$log_path = str_replace('\\', '/', JFactory::getConfig()->get('log_path'));
		$file = filter_input(INPUT_GET, 'filename');

		if ($file !== 'PHP error log') {
			$result = unlink($log_path . '/' . $file);
			$this->printJson($result ? JText::sprintf('COM_VLOGS_DELETEFILE_SUCCESS', $file) : JText::_('COM_VLOGS_DELETEFILE_ALERT'), $result);
		} else {
			$this->printJson(JText::_('COM_VLOGS_NO_DELETE_PHP_LOG') . '   ' . $file, false);
		}
	}

	public function ArchiveFile()
	{
		$apath = JComponentHelper::getParams('com_vlogs')->get('apath', 'tmp');
		$delAfterArch = (int)JComponentHelper::getParams('com_vlogs')->get('delafterarch', 0);

		if (!$apath) {
			$this->printJson(JText::_('COM_VLOGS_ARCHIVEFILE_NO_FOLDER'), false);
		}

		$apath = str_replace('\\', '/', JPATH_ROOT . '/' . $apath);

		if (!is_dir($apath)) {
			$this->printJson(JText::_('COM_VLOGS_ARCHIVEFILE_NO_EXISTS_FOLDER'), false);
		}

		$log_path = str_replace('\\', '/', JFactory::getConfig()->get('log_path'));
		$file = filter_input(INPUT_GET, 'filename');

		if ($file !== 'PHP error log') {
			if (!extension_loaded('zip')) {
				$this->printJson(JText::_('COM_VLOGS_NO_PHPZIP'), false);
			}

			$zip = new ZipArchive();

			$archFile = pathinfo($log_path . DIRECTORY_SEPARATOR . $file, PATHINFO_FILENAME) . '__' . date('Y-m-d_h-i-s') . '.zip';
			$archPath = $apath . '/' . $archFile;

			if ($zip->open($archPath, ZIPARCHIVE::CREATE) !== true) {
				$this->printJson(JText::_('COM_VLOGS_ARCHIVEFILE_ERROR_CREATE'), false);
			} else {
				$zip->addFile($log_path . '/' . $file, $file);
				$zip->close();
			}

			$resultDel = 0;
			if ($delAfterArch) {
				$resultDel = unlink($log_path . '/' . $file);
			}

			$this->printJson(
				JText::sprintf('COM_VLOGS_ARCHIVEFILE_ALERT_' . (int)($delAfterArch && $resultDel), $file, str_replace(str_replace('\\', '/', JPATH_ROOT), '', $archPath)), 
				true, 
				['del' => (int)($delAfterArch && $resultDel)]
			);
		} else {
			$this->printJson(JText::_('COM_VLOGS_NO_ARCHIVE_PHP_LOG') . '   ' . $file, false);
		}
	}
}
