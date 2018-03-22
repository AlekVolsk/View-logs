<?php defined('_JEXEC') or die;
/*
 * @package     com_vlogs
 * @copyright   Copyright (C) 2018 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

class VlogsViewItems extends JViewLegacy
{
	public $items;

	public function display($tpl = null)
	{
		
		$this->items = $this->get('Items');
		
		JToolBarHelper::title(JText::_('COM_VLOGS'), 'health');
		
		$custom_button_html = '<select id="view_select_files" style="margin-bottom:0">';
		foreach ($this->items as $item)
		{
			$custom_button_html .= '<option value="' . $item . '">' . $item . '</option>';
		}
		$custom_button_html .= '</select>';
		JToolBar::getInstance('toolbar')->appendButton('Custom', $custom_button_html, 'custom');
		
		$custom_button_html = '<button id="view_refresh_file" type="button" class="btn btn-small"><span class="icon-refresh"></span>' . JText::_('COM_VLOGS_REFRESH_BUTTON') . '</button>';
		JToolBar::getInstance('toolbar')->appendButton('Custom', $custom_button_html, 'custom');
		
		$custom_button_html = '<button id="view_download_file" type="button" class="btn btn-small"><span class="icon-download"></span>' . JText::_('COM_VLOGS_DOWNLOAD_BUTTON') . '</button>';
		JToolBar::getInstance('toolbar')->appendButton('Custom', $custom_button_html, 'custom');
		
		$custom_button_html = '<button id="view_download_bom_file" type="button" class="btn btn-small"><span class="icon-download"></span>' . JText::_('COM_VLOGS_DOWNLOAD_BOM_BUTTON') . '</button>';
		JToolBar::getInstance('toolbar')->appendButton('Custom', $custom_button_html, 'custom');
		
		$custom_button_html = '<button id="view_delete_file" type="button" class="btn btn-small"><span class="icon-delete"></span>' . JText::_('COM_VLOGS_DELETEFILE_BUTTON') . '</button>';
		JToolBar::getInstance('toolbar')->appendButton('Custom', $custom_button_html, 'custom');
		
		$custom_button_html = '<span style="display:inline-block;padding:0 15px;font-size:12px;line-height:25.5px;border:1px solid #d6e9c6;border-radius:3px;background-color:#dff0d8;color:#3c763d;">' . JText::_('COM_VLOGS_COUNT_ITEMS_VIEW') . ' <span id="view_count_items">0</span></span>';
		JToolBar::getInstance('toolbar')->appendButton('Custom', $custom_button_html, 'options');
		
		$canDo = JHelperContent::getActions('com_vlogs');
		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_vlogs');
		}
		
		parent::display( $tpl );
	}
}
