<?php defined( '_JEXEC' ) or die;
/*
 * @package     com_vlogs
 * @copyright   Copyright (C) 2018 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

class VlogsController extends JControllerLegacy
{
	function display( $cachable = false, $urlparams = [] )
	{
		$this->default_view = 'items';
		parent::display( $cachable, $urlparams );
		return $this;
	}

	public function getAjax()
	{
		$input = JFactory::getApplication()->input;
		$model = $this->getModel('ajax');
		$action = $input->getCmd('action');
		$reflection = new ReflectionClass($model);
		$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
		$methodList = array();
		foreach ($methods as $method)
		{
			$methodList[] = $method->name;
		}
		if (in_array($action, $methodList))
		{
			$model->$action();
		}
		exit;
	}
}
