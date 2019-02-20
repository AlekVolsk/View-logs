<?php defined('_JEXEC') or die;

?>

<form action="<?php echo JRoute::_('index.php?option=com_vlogs&view=items'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
		
		<div id="view_items_list"></div>
		
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
		
	</div>
</form>

<style>
.com_vlogs pre {box-sizing:border-box;max-width:100%;width:100%;}
</style>			

<script>
document.addEventListener('DOMContentLoaded', function()
{
	var
		request = new XMLHttpRequest(),
		formData = new FormData(),
		response = false;
	
	Joomla.JText.load({info:"<?php echo JText::_('MESSAGE'); ?>",error:"<?php echo JText::_('ERROR'); ?>"});
	
	getLog = function(vfile)
	{
		Joomla.removeMessages();

		document.querySelector('#view_items_list').innerHTML = '';
		document.querySelector('#view_count_items').innerHTML = '0';

		request.open('POST', location.protocol + '//' + location.host + location.pathname + '?option=com_vlogs&task=getAjax&action=List&filename=' + vfile);
		request.send(new URLSearchParams(formData));

		request.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				try {
					response = JSON.parse(this.response);
					document.querySelector('#view_items_list').innerHTML = response.message;
					document.querySelector('#view_count_items').innerHTML = response.count;
				} catch (e) {
					Joomla.renderMessages({'error':[this.response]});
					response = false;
				}
			}
		};
	}
	
	delLog = function(vfile)
	{
		Joomla.removeMessages();

		request.open('POST', location.protocol + '//' + location.host + location.pathname + '?option=com_vlogs&task=getAjax&action=DelFile&filename=' + vfile);
		request.send(new URLSearchParams(formData));

		request.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				try {
					response = JSON.parse(this.response);
					if (response.result)
					{
						var sel = document.querySelector('#view_select_files');
						sel.removeChild(sel.options[sel.selectedIndex]);
						getLog(sel.value);
						Joomla.renderMessages({'info':[response.message]});
					}
					else
					{
						Joomla.renderMessages({'error':[response.message]});
					}
				} catch (e) {
					Joomla.renderMessages({'error':[this.response]});
					response = false;
				}
			}
		};
	}
	
	archLog = function(vfile)
	{
		Joomla.removeMessages();

		request.open('POST', location.protocol + '//' + location.host + location.pathname + '?option=com_vlogs&task=getAjax&action=ArchiveFile&filename=' + vfile);
		request.send(new URLSearchParams(formData));

		request.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				try {
					response = JSON.parse(this.response);
					if (response.result)
					{
						if (response.del) {
							var sel = document.querySelector('#view_select_files');
							sel.removeChild(sel.options[sel.selectedIndex]);
							getLog(sel.value);
						}
						Joomla.renderMessages({'info':[response.message]});
					}
					else
					{
						Joomla.renderMessages({'error':[response.message]});
					}
				} catch (e) {
					Joomla.renderMessages({'error':[this.response]});
					response = false;
				}
			}
		};
	}

	document.querySelector('#view_select_files').addEventListener('change', function(e)
	{
		getLog(e.target.value);
	});
	
	document.querySelector('#view_refresh_file').addEventListener('click', function(e)
	{
		getLog(document.querySelector('#view_select_files').value);
	});
	
	document.querySelector('#view_download_file').addEventListener('click', function(e)
	{
		Joomla.removeMessages();
		document.location.href = 'index.php?option=com_vlogs&task=getAjax&action=dwFile&bom=0&filename=' + document.querySelector('#view_select_files').value;
	});
	
	document.querySelector('#view_download_bom_file').addEventListener('click', function(e)
	{
		Joomla.removeMessages();
		document.location.href = 'index.php?option=com_vlogs&task=getAjax&action=dwFile&bom=1&filename=' + document.querySelector('#view_select_files').value;
	});
	
	document.querySelector('#view_delete_file').addEventListener('click', function(e)
	{
		delLog(document.querySelector('#view_select_files').value);
	});
	
	document.querySelector('#view_archive_file').addEventListener('click', function(e)
	{
		archLog(document.querySelector('#view_select_files').value);
	});

	getLog(document.querySelector('#view_select_files').value);
});
</script>
