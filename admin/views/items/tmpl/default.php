<?php defined('_JEXEC') or die; ?>

<form action="<?php echo JRoute::_('index.php?option=com_vlogs&view=items'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">

		<div id="view_items_list"></div>

		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>

<?php

JFactory::getDocument()->addStyleDeclaration(".com_vlogs pre {box-sizing:border-box;margin-top:10px;padding:10px;max-width:100%;width:100%;border:1px solid #ddd;background-color:#fefefe;} .com_vlogs .table td, .com_vlogs .table th {vertical-align:top;}");

JFactory::getDocument()->addScriptDeclaration("
document.addEventListener('DOMContentLoaded', function()
{
	var
		request = new XMLHttpRequest(),
		formData = new FormData(),
		response = false,
		sel = document.querySelector('#view_select_files');
	
	Joomla.JText.load({info:\"" . JText::_('MESSAGE') . "\",error:\"" . JText::_('ERROR') . "\"});
	
	getLog = function(vfile)
	{
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
					console.log(response);
					Joomla.renderMessages({'error':[this.response]});
					response = false;
				}
			}
		};
	}
	
	delLog = function(vfile)
	{
		request.open('POST', location.protocol + '//' + location.host + location.pathname + '?option=com_vlogs&task=getAjax&action=DelFile&filename=' + vfile);
		request.send(new URLSearchParams(formData));

		request.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				try {
					response = JSON.parse(this.response);
					if (response.result) {
						sel.removeChild(sel.options[sel.selectedIndex]);
						getLog(sel.value);
						Joomla.renderMessages({'info':[response.message]});
					} else {
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
		request.open('POST', location.protocol + '//' + location.host + location.pathname + '?option=com_vlogs&task=getAjax&action=ArchiveFile&filename=' + vfile);
		request.send(new URLSearchParams(formData));

		request.onreadystatechange = function () {
			if (this.readyState === 4 && this.status === 200) {
				try {
					response = JSON.parse(this.response);
					if (response.result) {
						if (response.del) {
							sel.removeChild(sel.options[sel.selectedIndex]);
							getLog(sel.value);
						}
						Joomla.renderMessages({'info':[response.message]});
					} else {
						Joomla.renderMessages({'error':[response.message]});
					}
				} catch (e) {
					Joomla.renderMessages({'error':[this.response]});
					response = false;
				}
			}
		};
	}

	sel.addEventListener('change', function(e)
	{
		getLog(e.target.value);
	});
	
	document.querySelector('#view_refresh_file').addEventListener('click', function(e)
	{
		getLog(sel.value);
	});
	
	var dbtn = document.querySelector('#view_download_file');
	if (dbtn) {
		dbtn.addEventListener('click', function(e)
		{
			document.location.href = 'index.php?option=com_vlogs&task=getAjax&action=dwFile&bom=0&filename=' + sel.value;
		});
	}
	
	var dbbtn = document.querySelector('#view_download_bom_file');
	if (dbbtn) {
		dbbtn.addEventListener('click', function(e)
		{
			document.location.href = 'index.php?option=com_vlogs&task=getAjax&action=dwFile&bom=1&filename=' + sel.value;
		});
	}

	rbtn = document.querySelector('#view_delete_file');
	if (rbtn) {
		rbtn.addEventListener('click', function(e)
		{
			delLog(sel.value);
		});
	}

	abtn = document.querySelector('#view_archive_file');
	if (abtn) {
		abtn.addEventListener('click', function(e)
		{
			archLog(sel.value);
		});
	}

	getLog(sel.value);
});
");
