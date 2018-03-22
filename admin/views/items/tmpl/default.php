<?php defined('_JEXEC') or die;
/*
 * @package     com_vlogs
 * @copyright   Copyright (C) 2018 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

JHtml::_('jquery.framework');
?>

<form action="<?php echo JRoute::_('index.php?option=com_vlogs&view=items'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		
		<div style="display:flex;margin-bottom:18px;">
			<select id="view_select_files" name="view_select_files">
				<?php foreach ($this->items as $item) { ?>
				<option value="<?php echo $item; ?>"><?php echo $item; ?></option>
				<?php } ?>
			</select>
			<button id="view_refresh_file" type="button" class="btn btn-success" style="margin-left:9px;height:28px"><?php echo JText::_('COM_VLOGS_REFRESH_BUTTON'); ?></button>
			<button id="view_delete_file" type="button" class="btn btn-danger" style="margin-left:9px;height:28px"><?php echo JText::_('COM_VLOGS_DELETEFILE_BUTTON'); ?></button>
		</div>
		
		<table class="table table-striped">
			<thead>
				<tr>
					<th width="10%"><?php echo JText::_('COM_VLOGS_COLUMN_DT'); ?></th>
					<th width="5%"><?php echo JText::_('COM_VLOGS_COLUMN_PRIORITY'); ?></th>
					<th width="5%"><?php echo JText::_('COM_VLOGS_COLUMN_IP'); ?></th>
					<th width="5%"><?php echo JText::_('COM_VLOGS_COLUMN_CATEGORY'); ?></th>
					<th><?php echo JText::_('COM_VLOGS_COLUMN_MSG'); ?></th>
				</tr>
			</thead>
			<tbody id="view_items_list"></tbody>
		</table>
		
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
		
	</div>
</form>
			
<script>
jQuery(document).ready(function($)
{
	getLog = function(vfile)
	{
		document.querySelector('#view_items_list').innerHTML = '';
		document.querySelector('#view_count_items').innerHTML = '0';

		$.getJSON('index.php', {option:'com_vlogs', task:'getAjax', action:'List', filename:vfile}, function(response)
		{
			document.querySelector('#view_items_list').innerHTML = response.message;
			document.querySelector('#view_count_items').innerHTML = response.count;
		});
	}
	
	document.querySelector('#view_refresh_file').addEventListener('click', function(e)
	{
		getLog(document.querySelector('#view_select_files').value);
	});
	
	document.querySelector('#view_delete_file').addEventListener('click', function(e)
	{
		$.getJSON('index.php', {option:'com_vlogs', task:'getAjax', action:'DelFile', filename:document.querySelector('#view_select_files').value}, function(response)
		{
			if (response.result)
			{
				var sel = document.querySelector('#view_select_files');
				sel.removeChild(sel.options[sel.selectedIndex]);
				getLog(sel.value);
			}
			else
			{
				alert('<?php echo JText::_('COM_VLOGS_DELETEFILE_ALERT'); ?>');
			}
		});
	});

	document.querySelector('#view_select_files').addEventListener('change', function(e)
	{
		getLog(e.target.value);
	});

	getLog(document.querySelector('#view_select_files').value);
});
</script>
