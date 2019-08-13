<?php defined('_JEXEC') or die;
/*
 * @package     com_vlogs
 * @copyright   Copyright (C) 2019 Aleksey A. Morozov (AlekVolsk). All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

if (!JFactory::getUser()->authorise('core.manage', 'com_vlogs')) {
	return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = JControllerLegacy::getInstance('vlogs');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
