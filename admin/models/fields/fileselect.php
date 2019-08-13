<?php defined('JPATH_PLATFORM') or die;

/*
Usage:

<field 
	name
	label
	class
	type="fileselect" 
	folder="foldername"       // default 'images'
	folderonly="true|false"   // show directories only (val 'true') or directories && files (val 'false'), default 'false'
	showroot="true|false"     // show root directori no tree (val 'true'), default 'false'
/>

*/

JFormHelper::loadFieldClass('list');

class JFormFieldFileselect extends JFormField
{

	public $type = 'fileselect';

	protected $uid;

	protected function showdir(
		$dir,
		$folderOnly = false,
		$showRoot = false,
		$level = 0,  // do not use!!!
		$ef = ''     // do not use!!!
	) {
		$html = '';
		if ((int) $level == 0) {
			$dir = realpath($dir);
			$ef = ($showRoot ? realpath($dir . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR : $dir . DIRECTORY_SEPARATOR);
		}
		if (!file_exists($dir))
			return '';

		if ($showRoot && (int) $level == 0) {
			$html = '<ul id="' . $this->uid . '" class="av-folderlist level-0' . '">';
			$subdir = $this->showdir($dir, $folderOnly, $showRoot, $level + 1, $ef);
			$name = substr(strrchr($dir, DIRECTORY_SEPARATOR), 1);
			$html .= '<li class="av-folderlist-item av-folderlist-dir">' . ($subdir ? '<span class="av-folderlist-tree"></span>' : '') . '<span class="av-folderlist-label" path="' . $name . '">' . $name . '</span>' . $subdir . '</li>';
			$html .= '</ul>';
		} else {
			$list = scandir($dir);
			if (is_array($list)) {
				$list = array_diff($list, array('.', '..'));
				if ($list) {
					$folders = array();
					$files = array();

					foreach ($list as $name) {
						if (is_dir($dir . DIRECTORY_SEPARATOR . $name)) {
							$folders[] = $name;
						} else {
							$files[] = $name;
						}
					}

					if (!($folderOnly && !$folders) || !(!$folders || !$files)) {
						$html .= '<ul' . ((int) $level == 0 ? ' id="' . $this->uid . '"' : '') . ' class="' . ((int) $level == 0 ? 'av-folderlist ' : '') . 'level-' . (int) $level . '">';
					}

					sort($folders);
					sort($files);

					foreach ($folders as $name) {
						$fpath = $dir . DIRECTORY_SEPARATOR . $name;
						$subdir = $this->showdir($fpath, $folderOnly, $showRoot, $level + 1, $ef);
						$fpath = str_replace('\\', '/', str_replace($ef, '', $fpath));
						$html .= '<li class="av-folderlist-item av-folderlist-dir">' . ($subdir ? '<span class="av-folderlist-tree"></span>' : '') . '<span class="av-folderlist-label" path="' . $fpath . '">' . $name . '</span>' . $subdir . '</li>';
					}

					if (!$folderOnly)
						foreach ($files as $name) {
							$fpath = $dir . DIRECTORY_SEPARATOR . $name;
							$fpath = str_replace('\\', '/', str_replace($ef, '', $fpath));
							$ext = substr(strrchr($name, '.'), 1);
							$html .= '<li class="av-folderlist-item av-folderlist-file' . ($ext ? ' av-folderlist-file-' . $ext : '') . '"><span class="av-folderlist-label" path="' . $fpath . '">' . $name . '</span></li>';
						}

					if (!($folderOnly && !$folders) || !(!$folders || !$files)) {
						$html .= '</ul>';
					}

					unset($folders, $files, $fpath, $ext);
				}
			}
		}

		return $html;
	}

	protected function getInput()
	{
		// include css 
		$path = str_replace('\\', '/', str_replace(JPATH_ROOT . DIRECTORY_SEPARATOR, '', __DIR__));
		JHtml::_('stylesheet', $path . DIRECTORY_SEPARATOR . 'fileselect.css');

		// get attributes
		$folder = $this->getAttribute('folder');
		$folder = ($folder && file_exists(realpath(JPATH_ROOT . DIRECTORY_SEPARATOR . $folder)) ? $folder : 'images');

		$folderOnly = $this->getAttribute('folderonly');
		$folderOnly = ($folderOnly && (strtolower($folderOnly) === 'true' || strtolower($folderOnly) === 'folderonly') ? true : false);

		$showRoot = $this->getAttribute('showroot');
		$showRoot = ($showRoot && (strtolower($showRoot) === 'true' || strtolower($showRoot) === 'showroot') ? true : false);

		// get uniq id
		$this->uid = uniqid('avfl');

		// make html
		$html = '<div class="field-wrapper">';

		// input
		$html .= '<div class="btn-group input-append" style="position:relative;">';
		$html .= '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ($this->class ? ' class="' . $this->class . '"' : '') . ' value="' . $this->value . '" placeholder="' . JText::_('Select file or folder') . '" readonly ' . ($this->required ? 'required' : '') . '/>';
		if (!$this->readonly)
			$html .= '<button id="' . $this->uid . 'btn" class="btn btn-primary" type="button"><span class="icon-folder"></span></button>';

		// modal
		$html .= '<div id="' . $this->uid . 'modal" class="av-modal">';
		$html .= $this->showdir(JPATH_ROOT . DIRECTORY_SEPARATOR . $folder, $folderOnly, $showRoot);
		$html .= '</div>';
		$html .= '</div>';

		$html .= "<script>
			document.addEventListener('DOMContentLoaded', function() {
				
				document.getElementById('" . $this->uid . "btn').addEventListener('click', function(e) {
					document.getElementById('" . $this->uid . "modal').classList.toggle('open');
				});

				document.querySelectorAll('#" . $this->uid . " .av-folderlist-tree').forEach(function(el) {
					el.addEventListener('click', function(e) {
						e.target.parentNode.classList.toggle('open');
					});
				});
				
				document.querySelectorAll('#" . $this->uid . " .av-folderlist-label').forEach(function(el) {
					el.addEventListener('click', function(e) {
						var
							target = e.target;
							list = e.target.closest('.av-folderlist');
						
						list.querySelectorAll('.av-folderlist-label').forEach(function(elm) {
							elm.classList.remove('selected');
						});

						document.getElementById('" . $this->id . "').value = target.getAttribute('path');
						target.classList.add('selected');
						if (!target.parentNode.classList.contains('open')) {
							target.parentNode.classList.add('open');
						}
					});
				});

			});
		</script>";

		$html .= '</div>';

		return $html;
	}
}
