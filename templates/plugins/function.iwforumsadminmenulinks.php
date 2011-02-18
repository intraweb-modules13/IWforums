<?php
function smarty_function_iwforumsadminmenulinks($params, &$smarty)
{
	$dom = ZLanguage::getModuleDomain('IWforums');
	$tema = FormUtil::getPassedValue('tema');

	//Get the user permissions in forums
	$permisos = ModUtil::apiFunc('IWforums', 'user', 'permisos', array('uid' => UserUtil::getVar('uid')));

	// set some defaults
	if (!isset($params['start'])) {
		$params['start'] = '[';
	}
	if (!isset($params['end'])) {
		$params['end'] = ']';
	}
	if (!isset($params['seperator'])) {
		$params['seperator'] = '|';
	}
	if (!isset($params['class'])) {
		$params['class'] = 'z-menuitem-title';
	}

	$forumsadminmenulinks = "<span class=\"" . $params['class'] . "\">" . $params['start'] . " ";

	if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_ADMIN)) {
		$forumsadminmenulinks .= "<a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWforums', 'admin', 'newItem',array('m' => 'n'))) . "\">" . __('Create a new forum',$dom) . "</a> " . $params['seperator'];
	}

	if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_ADMIN)) {
		$forumsadminmenulinks .= " <a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWforums', 'admin', 'main')) . "\">" . __('View the created forums',$dom) . "</a> ";
	}

	if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_ADMIN)) {
		$forumsadminmenulinks .= $params['seperator'] . " <a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWforums', 'admin', 'conf')) . "\">" . __('Configure the module',$dom) . "</a> ";
	}

	$forumsadminmenulinks .= $params['end'] . "</span>\n";

	return $forumsadminmenulinks;
}
