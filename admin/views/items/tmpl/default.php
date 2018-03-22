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
		
		<div>
			<select id="view_select_files" name="view_select_files">
				<?php foreach ($this->items as $item) { ?>
				<option value="<?php echo $item; ?>"><?php echo $item; ?></option>
				<?php } ?>
			</select>
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
		$('#view_items_list').empty();
		$('#view_count_items').text('0');

		$.getJSON('index.php', {option:'com_vlogs', task:'getAjax', action:'List', filename:vfile}, function(response)
		{
			$('#view_items_list').html(response.message);
			$('#view_count_items').text(response.count);
		});
	}
	
	document.querySelector('#view_select_files').addEventListener('change', function(e)
	{
		getLog(e.target.value);
	});

	getLog($('#view_select_files option:selected').val());
});
</script>
