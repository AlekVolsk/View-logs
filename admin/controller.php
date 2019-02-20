<?php defined('_JEXEC') or die;

class VlogsController extends JControllerLegacy
{
	function display($cachable = false, $urlparams = [])
	{
		$this->default_view = 'items';
		parent::display($cachable, $urlparams);
		return $this;
	}

	public function getAjax()
	{
		$model = $this->getModel('ajax');
		$action = filter_input(INPUT_GET, 'action');
		$reflection = new ReflectionClass($model);
		$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
		$methodList = array();
		foreach ($methods as $method) {
			$methodList[] = $method->name;
		}
		if (in_array($action, $methodList)) {
			$model->$action();
		}
		exit;
	}
}
