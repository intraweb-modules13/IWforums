<?php
function smarty_function_iwforumsusermenulinks($params, &$smarty) {
    $dom = ZLanguage::getModuleDomain('IWforums');
    // check the access level for the user for the forum
    $access = (isset($params['fid'])) ? ModUtil::func('IWforums', 'user', 'access',
                                                       array('fid' => $params['fid'])) : false;

    // set some defaults
    if (!isset($params['start'])) $params['start'] = '[';
    if (!isset($params['end'])) $params['end'] = ']';
    if (!isset($params['seperator'])) $params['seperator'] = '| ';
    if (!isset($params['class'])) $params['class'] = 'z-menuitem-title';
    $message = array('marcat' => '');
    if (isset($params['fmid']) && $params['fmid'] > 0) {
        //get message information
        $message = ModUtil::apiFunc('IWforums', 'user', 'get_msg',
                                     array('fmid' => $params['fmid']));
        if ($message == false) {
            LogUtil::registerError(__('No messages have been found', $dom));
            return System::redirect(ModUtil::url('IWforums', 'user', 'main'));
        }
    }

    $forumsusermenulinks = "<span class=\"" . $params['class'] . "\">" . $params['start'] . " ";

    if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && isset($params['m5']) && $params['m5'] == 1 && $access > 1) {
        $forumsusermenulinks .= "<a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWforums', 'user', 'nou_msg',
                                                                                            array('inici' => $params['inici'],
                                                                                                  'fid' => $params['fid'],
                                                                                                  'ftid' => $params['ftid'],
                                                                                                  'u' => $params['u'],
                                                                                                  'fmid' => $params['fmid'],
                                                                                                  'oid' => $params['oid']))) . "\">" . __('Reply to the message', $dom) . "</a> " . $params['seperator'];
    }

    if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && isset($params['m6']) && $params['m6'] == 1 && $access > 0 && UserUtil::isLoggedIn()) {
        $forumsusermenulinks .= "<a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWforums', 'user', 'lectors',
                                                                                            array('inici' => 0,
                                                                                                  'fid' => $params['fid'],
                                                                                                  'ftid' => $params['ftid'],
                                                                                                  'u' => $params['u'],
                                                                                                  'fmid' => $params['fmid'],
                                                                                                  'oid' => $params['oid']))) . "\">" . __('Who has read the message?', $dom) . "</a> " . $params['seperator'];
    }

    if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && isset($params['m1']) && $params['m1'] == 1 && $access > 2 && $params['ftid'] == null) {
        $forumsusermenulinks .= "<a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWforums', 'user', 'nou_tema',
                                                                                            array('fid' => $params['fid'],
                                                                                                  'u' => $params['u'],
                                                                                                  'inici' => $params['inici']))) . "\">" . __('Create a new topic', $dom) . "</a> " . $params['seperator'];
    }

    if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && isset($params['m2']) && $params['m2'] == 1 && $access > 1) {
        $forumsusermenulinks .= "<a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWforums', 'user', 'nou_msg',
                                                                                            array('fid' => $params['fid'],
                                                                                                  'inici' => $params['inici'],
                                                                                                  'u' => $params['u'],
                                                                                                  'ftid' => $params['ftid']))) . "\">" . __('Send a new message', $dom) . "</a> " . $params['seperator'];
    }

    if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && isset($params['m3']) && $params['m3'] == 1) {
        $forumsusermenulinks .= "<a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWforums', 'user', 'main',
                                                                                            array('inici' => $params['inici']))) . "\">" . __('View the forum list', $dom) . "</a> " . $params['seperator'];
    }

    if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && (isset($params['m4']) && $params['m4'] == 1 && $access > 0) || ($params['ftid'] != null && $access > 0)) {
        $forumsusermenulinks .= "<a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWforums', 'user', 'forum',
                                                                                            array('inici' => $params['inici'],
                                                                                                  'u' => $params['u'],
                                                                                                  'fid' => $params['fid']))) . "\">" . __('Return to the list of topics and messages', $dom) . "</a> " . $params['seperator'];
    }

    if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && isset($params['m7']) && $params['m7'] == 1 && $access > 0) {
        if ($params['fmid'] != 0) {
            $forumsusermenulinks .= "<a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWforums', 'user', 'llista_msg',
                                                                                                array('inici' => $params['inici'],
                                                                                                      'fid' => $params['fid'],
                                                                                                      'u' => $params['u'],
                                                                                                      'ftid' => $params['ftid']))) . "\">" . __('Return to the message list', $dom) . "</a> " . $params['seperator'];
        }
    }

    if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && isset($params['m8']) && $params['m8'] == 1 && $access > 0) {
        $forumsusermenulinks .= "<a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWforums', 'user', 'msg',
                                                                                            array('inici' => $params['inici'],
                                                                                                  'fid' => $params['fid'],
                                                                                                  'ftid' => $params['ftid'],
                                                                                                  'u' => $params['u'],
                                                                                                  'fmid' => $params['fmid'],
                                                                                                  'oid' => $params['oid']))) . "\">" . __('Return to the message', $dom) . "</a> " . $params['seperator'];
    }

    if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && isset($params['m9']) && $params['m9'] == 1 && $access > 0 && UserUtil::isLoggedIn()) {
        if ($params['ftid'] == null) {
            $forumsusermenulinks .= "<a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWforums', 'user', 'llegits',
                                                                                                array('inici' => $params['inici'],
                                                                                                      'fid' => $params['fid']))) . "\">" . __('Check all messages as read', $dom) . "</a> " . $params['seperator'];
        } else {
            $forumsusermenulinks .= "<a href=\"" . DataUtil::formatForDisplayHTML(ModUtil::url('IWforums', 'user', 'llegits',
                                                                                                array('inici' => $params['inici'],
                                                                                                      'ftid' => $params['ftid'],
                                                                                                      'u' => $params['u'],
                                                                                                      'fid' => $params['fid']))) . "\">" . __('Check all messages as read', $dom) . "</a> " . $params['seperator'];
        }
    }
    if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && isset($params['m12']) && $params['m12'] == 1 && $access > 0 && UserUtil::isLoggedIn() && strpos($message['marcat'], '$' . UserUtil::getVar('uid') . '$') == 0) {
        $forumsusermenulinks .= "<span style=\"cursor: pointer;\" id=\"markText" . $params['fmid'] . "\"><a onclick=\"javascript:mark(" . $params['fid'] . "," . $params['fmid'] . ")\">" . __('Check the message', $dom) . "</a></span> " . $params['seperator'];
    }

    if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && isset($params['m13']) && $params['m13'] == 1 && $access > 0 && UserUtil::isLoggedIn() && strpos($message['marcat'], '$' . UserUtil::getVar('uid') . '$') != 0) {
        $forumsusermenulinks .= "<span style=\"cursor: pointer;\" id=\"markText" . $params['fmid'] . "\"><a onclick=\"javascript:mark(" . $params['fid'] . "," . $params['fmid'] . ")\">" . __('Uncheck the message', $dom) . "</a></span> " . $params['seperator'];
    }

    $forumsusermenulinks = substr($forumsusermenulinks, 0, -2);

    $forumsusermenulinks .= $params['end'] . "</span>\n";

    return $forumsusermenulinks;
}