<?php

class IWforums_Api_User extends Zikula_AbstractApi {

     /**
     * Gets all the forums created
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:	And array with the items information
     */
    public function getall($args) {
        $filter = FormUtil::getPassedValue('filter', isset($args['filter']) ? $args['filter'] : null, 'POST');
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : UserUtil::getVar('uid'), 'POST');
        $requestByCron = false;
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))) {
            // Security check
            if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
                return LogUtil::registerPermissionError();
            }
        } else
            $requestByCron = true;

        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_definition_column'];
        $sqlWhere = '';
        $groupsList = '';
        if (SecurityUtil::checkPermission('IWforums::', '::', ACCESS_ADMIN) && $filter != 1) {
            $where = '';
        } else {
            $uid = (!UserUtil::isLoggedIn() && !$requestByCron) ? '-1' : $uid;
            //get all the forums where the user can access because is group member or forum moderator
            if ($uid != '-1') {
                // get all user groups
                $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
                $groups = ModUtil::apiFunc('IWmain', 'user', 'getAllUserGroups', array('sv' => $sv,
                            'uid' => $uid));
                $sqlWhere = "$c[actiu]=1 AND (";
                foreach ($groups as $group) {
                    //create the select restrictive sql command
                    $groupsList .= "$c[grup] like '%$" . $group['gid'] . "|1$%' OR
                                    $c[grup] like '%$" . $group['gid'] . "|2$%' OR
                                    $c[grup] like '%$" . $group['gid'] . "|3$%' OR
                                    $c[grup] like '%$" . $group['gid'] . "|4$%' OR ";
                }
                $sqlWhere .= substr($groupsList, 0, -3);
            } else {  
                $sqlWhere = "$c[actiu]=1 AND ($c[grup] like '%$-1|1$%'";                
            }           
            $or = (trim(substr($groupsList, 0, -3)) === '') ? '' : " OR ";
            $sqlWhere .= ($uid != '-1') ? $or . "$c[mod] like '%$" . $uid . "$%')" : ')';
        }
        $where = $sqlWhere;
        $orderby = "$c[nom_forum]";
        // get the objects from the db
        $items = DBUtil::selectObjectArray('IWforums_definition', $where, $orderby, '-1', '-1', 'fid');
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        // Return the items
        return $items;
    }

    /**
     * Get a forum
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	Identity of the forum
     * @return:	And array with the forum information
     */
    public function get($args) {
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        $sv = FormUtil::getPassedValue('sv', isset($args['sv']) ? $args['sv'] : null, 'POST');

        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))) {
            // Security check
            if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
                return LogUtil::registerPermissionError();
            }
        }
        // Needed argument
        if (!isset($fid) || !is_numeric($fid)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $items = DBUtil::selectObjectByID('IWforums_definition', $fid, 'fid');
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        // Return the items
        return $items;
    }

    /**
     * Get the number of messages into a forum or topic and the number of them that the user haven't seen
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	Identity of the forum
     * @return:	And array with the information
     */
    public function compta_msg($args) {
        $fid = isset($args['fid']) ? $args['fid'] : null;
        $ftid = isset($args['ftid']) ? $args['ftid'] : 0;
        $tots = isset($args['tots']) ? $args['tots'] : 0;
        $uid = isset($args['uid']) ? $args['uid'] : UserUtil::getVar('uid');
        $sv = isset($args['sv']) ? $args['sv'] : null;
        $u = isset($args['u']) ? $args['u'] : null;
        
        if (!ModUtil::func('IWmain', 'user', 'checkSecurityValue', array('sv' => $sv))) {
            // Security check
            if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
                return LogUtil::registerPermissionError();
            }
        } else {
            $requestByCron = true;
        }
        $registres = array();
        if ($uid != UserUtil::getVar('uid') && !$requestByCron) {
            return $registres;
        }
        //check if user can access the forum
        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        if (ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid,
                    'uid' => $uid,
                    'sv' => $sv)) < 1) {
            return LogUtil::registerPermissionError();
        }
        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_msg_column'];
        $c = $pntable['IWforums_msg_column'];
        $userFilter = ($u > 0) ? " AND $c[usuari] = $u" : '';
        $temaFilter = ($tots == 1) ? '' : " AND $c[ftid]=$ftid";
        $where = "$c[fid]=$fid" . $temaFilter . $userFilter;
        $where1 = "$c[llegit] LIKE '%$" . $uid . "$%' AND $c[fid]=$fid" . $temaFilter . $userFilter;
        $where2 = "$c[marcat] LIKE '%$" . $uid . "$%' AND $c[fid]=$fid" . $temaFilter . $userFilter;
        $items = DBUtil::selectObjectArray('IWforums_msg', $where, '', '-1', '-1', 'fmid');
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        $nmsg = count($items);
        if ($u == null || $u == 0) {
            // get the number of topics
            $where = "$c[ftid]=$ftid AND $c[fid]=$fid AND $c[idparent]=0";
            $topics = DBUtil::selectObjectArray('IWforums_msg', $where, '', '-1', '-1', 'fmid');
            // error message and return
            if ($topics === false) {
                return LogUtil::registerError($this->__('Error! Could not load items.'));
            }
            $nparent = count($topics);
        } else
            $nparent = $nmsg;

        $uread = DBUtil::selectObjectArray('IWforums_msg', $where1, '', '-1', '-1', 'fmid');
        // error message and return
        if ($uread === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        $nmsgno = count($uread);
        $nollegits = $nmsg - $nmsgno;
        $checked = DBUtil::selectObjectArray('IWforums_msg', $where2, '', '-1', '-1', 'fmid');
        // error message and return
        if ($checked === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        $marcats = count($checked);
        $registres = array('nmsg' => $nmsg,
            'nollegits' => $nollegits,
            'marcats' => $marcats,
            'nparent' => $nparent);
        //print_r($registres);
        //Retornem la matriu plena de registres
        return $registres;
    }
    
    /**
     * Get the number of topics into a forum
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	Identity of the forum
     * @return:	The number of topics
     */
    public function compta_temes($args) {
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');

        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }

        // Needed argument
        if (!isset($fid) || !is_numeric($fid)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if ($access < 1) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }

        $pntable = DBUtil::getTables();

        $c = $pntable['IWforums_temes_column'];

        $where = "$c[fid]=$fid";
        $items = DBUtil::selectObjectArray('IWforums_temes', $where, '', '-1', '-1', 'ftid');

        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        return count($items);
    }

    public function getMessageInfo($args) {
        $mid = isset($args['fmid']) ? $args['fmid'] : null;
        //return $mid;
        return DBUtil::selectObjectByID('IWforums_msg',$mid, 'fmid');
    }
    /**
     * Get a topic
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	Identity of the forum and the topic
     * @return:	An array with the topic information
     */
    public function get_tema($args) {        
        
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        $ftid = FormUtil::getPassedValue('ftid', isset($args['ftid']) ? $args['ftid'] : null, 'POST');
        //$fid = isset($args['fid']) ? $args['fid'] : null;
        //$ftid =isset($args['ftid']) ? $args['ftid'] : null;     
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        $items = array();
        if ($ftid) {
            // Needed argument
            if (!isset($fid) || !is_numeric($fid) || !isset($ftid) || !is_numeric($ftid)) {
                return LogUtil::registerError("Function get_tema: ".$this->__('Error! Could not do what you wanted. Please check your input.'));
            }

            //check if user can access the forum
            $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
            if ($access < 1) {
                return LogUtil::registerError($this->__('You can\'t access the forum'));
            }

            $items = DBUtil::selectObjectByID('IWforums_temes', $ftid, 'ftid');

            // Check for an error with the database code, and if so set an appropriate
            // error message and return
            if ($items === false) {
                LogUtil::registerError($this->__('Error! Could not load items.'));
                //return false;
            }
        }
        // Return the items
        return $items;
    }

    /**
     * Get a message
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	Identity of the forum and the message
     * @return:	An array with the message information
     */
    public function get_msg($args) {
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'REQUEST');
        $fmid = FormUtil::getPassedValue('fmid', isset($args['fmid']) ? $args['fmid'] : null, 'POST');

        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }

        // Needed argument
        if (!isset($fid) || !is_numeric($fid) || !isset($fmid) || !is_numeric($fmid)) {
            return LogUtil::registerError("Function get_msg: ".$this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if ($access < 1) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }

        $items = DBUtil::selectObjectByID('IWforums_msg', $fmid, 'fmid');

        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }

        // Return the items
        return $items;
    }

    /**
     * Get all the topics in a forum
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	Identity of the forum
     * @return:	An array with all the topics into a forum
     */
    public function get_temes($args) {
        //$fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        //$ftid = FormUtil::getPassedValue('ftid', isset($args['ftid']) ? $args['ftid'] : null, 'POST');
        //$u = FormUtil::getPassedValue('u', isset($args['u']) ? $args['u'] : null, 'POST');
        //$forumtriat = FormUtil::getPassedValue('forumtriat', isset($args['forumtriat']) ? $args['forumtriat'] : null, 'POST');
        $fid = isset($args['fid']) ? $args['fid'] : null;
        $ftid = isset($args['ftid']) ? $args['ftid'] : null;
        $u = isset($args['u']) ? $args['u'] : null;
        $forumtriat = isset($args['forumtriat']) ? $args['forumtriat'] : null;

        if ($forumtriat != null) {
            $fid = $forumtriat;
        }
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        // Needed argument
        if (!isset($fid) || !is_numeric($fid)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if ($access < 1) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        //get forum information
        $forum = ModUtil::apiFunc('IWforums', 'user', 'get', array('fid' => $fid));
        if ($forum == false) {
            $view->assign('msg', $this->__('The forum upon which the ation had to be carried out hasn\'t been found'));
            return $view->fetch('IWforums_user_noacces.htm');
        }           
        
        $itemsArray = array();
        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_temes_column'];
        $where = "$c[fid]=$fid";
        if ($ftid) {
            $where .= " AND $c[ftid]=$ftid";
        }
        $orderBy = "$c[order] asc, $c[data] desc";
        $items = DBUtil::selectObjectArray('IWforums_temes', $where, $orderBy, '-1', '-1', 'ftid');
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        foreach ($items as $item) {
            if ($item['last_time'] == 0) {
                $last_post_exists = false;
                $lastdate = 0;
                $lasttime = 0;
            } else {
                $last_post_exists = true;
                $lastdate = date('d/m/y', $item['last_time']);
                $lasttime = date('H.i', $item['last_time']);
            }
                        
            $esborrable = (time() < $item['data'] + 60 * $forum['msgDelTime']  && $item['usuari'] == UserUtil::getVar('uid')) ? true : false;
            $editable   = (time() < $item['data'] + 60 * $forum['msgEditTime'] && $item['usuari'] == UserUtil::getVar('uid')) ? true : false;

            $n_msg = ModUtil::apiFunc('IWforums', 'user', 'compta_msg', 
                                array('ftid' => $item['ftid'],
                                      'fid' => $fid,
                                      'u' => $u));                       
            $n_msg_no_llegits = $n_msg['nollegits'];
            $marcats = $n_msg['marcats'];
            $n_msg = $n_msg['nmsg'];
            // Get user avatar
            $photo="";
            $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
            $photo = ModUtil::func('IWmain', 'user', 'getUserPicture', array('uname' => UserUtil::getVar('uname', $item['usuari']),'sv' => $sv));
            
            $itemsArray[] = array(
                'ftid' => $item['ftid'],
                'editable' => $editable,
                'esborrable' => $esborrable,
                'titol' => $item['titol'],
                'descriu' => $item['descriu'],
                'usuari' => $item['usuari'],
                'photo' => $photo,
                'data' => date('d/m/y', $item['data']),
                'hora' => date('H.i', $item['data']),
                'lastdate' => $lastdate,
                'lasttime' => $lasttime,
                'lastuser' => $item['last_user'],
                'last_post_exists' => $last_post_exists,
                'n_msg' => $n_msg,
                'n_msg_no_llegits' => $n_msg_no_llegits,
                'marcats' => $marcats,
            );            
        }                      
        return $itemsArray;
    }

    /**
     * Get the names of the attached files to a forum 
     * or topic 
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param: fid Identity of the forum
     * @param: ftid Identity of the topic (added in v 3.0.1)
     * @param: mode f (forum: default) or t (topic)
     * @return:	And array with the files names
     */
    public function get_adjunts($args) {
        //$fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        $fid  = isset($args['fid']) ? $args['fid'] : null;
        $ftid = isset($args['ftid']) ? $args['ftid'] : null;
        $mode = isset($args['mode']) ? $args['mode'] : 'f';
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }

        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_msg_column'];

        switch ($mode) {
            case 'f':
                if (!isset($fid) || !is_numeric($fid)) {
                    return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
                }
                $where = "$c[fid]=$fid";
                break;
            case 't':
                if (!isset($ftid) || !is_numeric($ftid)) {
                    return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
                }
                if (is_null($fid)) {
                    $topic = DBUtil::selectObjectByID('IWforums_temes', $ftid, 'ftid');
                    $fid = $topic['fid'];
                }
                $where = "$c[ftid]=$ftid";
                break;
        }
        // Needed argument fid or ftid                
        
        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if ($access < 4 ) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }

        $records = array();
        $items = DBUtil::selectObjectArray('IWforums_msg', $where, '');
        //Comprovem que la consulta hagi estat amb éxit
        if ($items === false) {
            return LogUtil::registerError($this->__('An error has occurred while reading records from the data base'));
        }

        foreach ($items as $item) {
            if ($item['adjunt'] != "") $records[] = array('adjunt' => $item['adjunt']);
        }

        //Retornem la matriu plena de registres
        return $records;
    }

    /**
     * Create a new topic in database
     * @author:	Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	Topic information
     * @return:	identity of the topic created if success and false otherwise
     */
    public function crear_tema($args) {
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        $descriu = FormUtil::getPassedValue('descriu', isset($args['descriu']) ? $args['descriu'] : '', 'POST');
        $titol = FormUtil::getPassedValue('titol', isset($args['titol']) ? $args['titol'] : null, 'POST');

        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }

        // Needed argument
        if (!isset($fid) || !is_numeric($fid) || !isset($titol)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if ($access < 3) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }

        $item = array('fid' => $fid,
            'titol' => $titol,
            'usuari' => UserUtil::getVar('uid'),
            'descriu' => $descriu,
            'data' => time());

        if (!DBUtil::insertObject($item, 'IWforums_temes', 'ftid')) {
            return LogUtil::registerError($this->__('Error! Creation attempt failed.'));
        }

        // Let any hooks know that we have created a new item
        ModUtil::callHooks('item', 'create', $item['ftid'], array('module' => 'IWforums'));

        return $item['ftid'];
    }

    /*
     * Update topic title and description
     */
    public function setTopic($args){
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        
        $fid    = $this->request->getPost()->get('fid', '');
        $ftid   = $this->request->getPost()->get('ftid', '');
        $titol  = $this->request->getPost()->get('titol', '');
        $descriu= $this->request->getPost()->get('descriu', '');
        
        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if ($access < 3) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        
        if (isset($ftid) && $ftid != 0) {
            $pntable = DBUtil::getTables();
            $c = $pntable['IWforums_temes_column'];
            $where = "$c[ftid]=$ftid AND $c[fid]=$fid";
            $item = array(
                        'fid' => $fid,
                        'ftid' => $ftid,
                        'titol' => $titol,
                        'descriu' => $descriu);
            if (!DBUTil::updateObject($item, 'IWforums_temes', $where)) {
                return LogUtil::registerError($this->__('Error! Update attempt failed.'));
            }
        }
       
        return $ftid;
    }
        
    /*
     * Update forum introduction vaalues
     */
    public function setForum($args){
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        
        $fid    = $this->request->getPost()->get('fid', '');
        $nom_forum   = $this->request->getPost()->get('nom_forum', '');
        $descriu  = $this->request->getPost()->get('descriu', '');
        $longDescriu= $this->request->getPost()->get('longDescriu', '');
        $observacions = $this->request->getPost()->get('observacions', '');
        
        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if ($access < 3) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        
        if (isset($fid) && $fid != 0) {
            $pntable = DBUtil::getTables();
            $c = $pntable['IWforums_definition_column'];
            $where = "$c[fid]=$fid";
            $item = array(
                        'fid' => $fid,
                        'nom_forum' => $nom_forum,
                        'descriu' => $descriu,
                        'longDescriu' => $longDescriu,
                        'observacions' => $observacions);
            if (!DBUTil::updateObject($item, 'IWforums_definition', $where)) {
                return LogUtil::registerError($this->__('Error! Update attempt failed.'));
            }
        }
       
        return $fid;
    }
    
    public function getall_msg($args) {
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        $ftid = FormUtil::getPassedValue('ftid', isset($args['ftid']) ? $args['ftid'] : null, 'POST');
        $tots = FormUtil::getPassedValue('tots', isset($args['tots']) ? $args['tots'] : null, 'POST');
        $usuari = FormUtil::getPassedValue('usuari', isset($args['usuari']) ? $args['usuari'] : null, 'POST');
        $idparent = FormUtil::getPassedValue('idparent', isset($args['idparent']) ? $args['idparent'] : null, 'POST');
        $inici = FormUtil::getPassedValue('inici', isset($args['inici']) ? $args['inici'] : null, 'POST');
        $indent = FormUtil::getPassedValue('indent', isset($args['indent']) ? $args['indent'] : null, 'POST');
        $rpp = FormUtil::getPassedValue('rpp', isset($args['rpp']) ? $args['rpp'] : null, 'POST');
        $oid = FormUtil::getPassedValue('oid', isset($args['oid']) ? $args['oid'] : null, 'POST');

        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        // Needed argument
        if (!isset($fid) || !is_numeric($fid)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if ($access < 1) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        $registres = array();

        $pntable = DBUtil::getTables();
        $t = $pntable['IWforums_msg'];
        $c = $pntable['IWforums_msg_column'];
        // Filtering: only show the messages sent by $usuari
        //$filter_user = (isset($usuari) && $usuari != null) ? $usuari : 0;
        $filter_user = ($usuari != null && $usuari > 0) ? " AND $c[usuari]=$usuari" : '';
        // Condition to select the topic
        $tema = (isset($tots) && $tots == 1) ? "" : " $c[ftid] = $ftid AND ";
        $inici = $inici - 1;
        $ordre = ($idparent == 0) ? "$c[lastdate] desc, $c[data] desc limit $inici, $rpp" : "$c[data] asc";
        $parent = (!isset($idparent)) ? '' : " AND $c[idparent]=$idparent";
        if ($filter_user != '') {
            $parent = '';
        }
        $where = $tema . "$c[fid]=$fid" . $parent . $filter_user;
        $ordreby = ($idparent == 0) ? "$c[onTop] desc, $c[lastdate] desc, $c[data] desc" : "$c[onTop] desc, $c[data] asc";
        $registre = DBUtil::selectObjectArray('IWforums_msg', $where, $ordreby, $inici, $rpp, 'fmid');

        //Recorrem els registres i els posem dins de la matriu
        foreach ($registre as $r) {
            // Set the id of the origen of the thread
            if ($idparent == 0)
                $oid = $r['fmid'];
            // Put the message in the array to be returned
            $indentValue = ($filter_user != '') ? 0 : $indent;
            //$clause = "$c[idparent]=$r[fmid]";
            //$hasReplies = DBUtil::selectField('IWforums_msg', 'fmid', $clause);            
            $registres[] = array('fmid' => $r['fmid'],
                'usuari' => $r['usuari'],
                'titol' => $r['titol'],
                'data' => $r['data'],
                'llegit' => $r['llegit'],
                'missatge' => $r['missatge'],
                'adjunt' => $r['adjunt'],
                'icon' => $r['icon'],
                'marcat' => $r['marcat'],
                'indent' => $indentValue,
                'oid' => $oid,
                'onTop' => $r['onTop'],
                //'canDelete' => !$hasReplies
            );
            if ($filter_user == '') {
                // Recursive call to get all the replies to a message
                $listmessages = ModUtil::apiFunc('IWforums', 'user', 'getall_msg', array('ftid' => $ftid,
                            'fid' => $fid,
                            'usuari' => $usuari,
                            'indent' => $indent + 30,
                            'idparent' => $r['fmid'],
                            'oid' => $oid,
                            'tots' => $tots));
                // Copy the replies to the all messages array
                foreach ($listmessages as $message) {
                    if ($filter_user != 0) { // Filtering
                        if ($filter_user == $message['usuari']) // Show only when the message is written by the selected user
                            $registres[] = array('fmid' => $message['fmid'],
                                'usuari' => $message['usuari'],
                                'titol' => $message['titol'],
                                'data' => $message['data'],
                                'llegit' => $message['llegit'],
                                'missatge' => $message['missatge'],
                                'adjunt' => $message['adjunt'],
                                'icon' => $message['icon'],
                                'marcat' => $message['marcat'],
                                'indent' => 0,
                                'oid' => $message['oid'],
                                'onTop' => $message['onTop'],
                                //'canDelete' => DBUtil::selectField('IWforums_msg', 'fmid', "$c[idparent]=$message[fmid]")
                            );
                    }else
                        $registres[] = array('fmid' => $message['fmid'],
                            'usuari' => $message['usuari'],
                            'titol' => $message['titol'],
                            'data' => $message['data'],
                            'llegit' => $message['llegit'],
                            'missatge' => $message['missatge'],
                            'adjunt' => $message['adjunt'],
                            'icon' => $message['icon'],
                            'marcat' => $message['marcat'],
                            'indent' => $message['indent'],
                            'oid' => $message['oid'],
                            'onTop' => $message['onTop'],
                            //'canDelete' => DBUtil::selectField('IWforums_msg', 'fmid', "$c[idparent]=$message[fmid]")
                        );
                }
            }
        }
        //Retornem la matriu plena de registres
        //echo '<pre>'.$r['fmid'].'===='.var_dump($registres).'<br></pre>';
        return $registres;
    }

    /*
      Funció que posa a l'usuari com que ha llegit el missatge
     */

    public function llegit($args) {
        //Avoid that unregistered user to be updated as reader
        if (!UserUtil::isLoggedIn()) {
            return true;
        }
        $fmid = FormUtil::getPassedValue('fmid', isset($args['fmid']) ? $args['fmid'] : null, 'POST');
        $llegit = FormUtil::getPassedValue('llegit', isset($args['llegit']) ? $args['llegit'] : null, 'POST');
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        //Comprovem que els valors han arribat
        if (!isset($fmid)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_msg_column'];
        $llegit = $llegit . '$' . UserUtil::getVar('uid') . '$';
        $where = "$c[fmid]=$fmid";
        $items = array('llegit' => $llegit);
        $registre = ModUtil::apiFunc('IWforums', 'user', 'get_msg', array('fmid' => $fmid));
        $lectors = explode('$$', substr($registre['llegit'], 2, -1));
        if ( !in_array((UserUtil::getVar('uid')), $lectors)) {
        if (!DBUTil::updateObject($items, 'IWforums_msg', $where)) {
            return LogUtil::registerError($this->__('The modification of the users who have read the message has failed'));
        }
        }

        //Informem que el procés s'ha acabat amb éxit
        return true;
    }

    public function getmessagesForDisplay($args) {
        $ftid = $this->request->getPost()->get('ftid', '');
        //$ftid = isset($args['ftid']) ? $args['ftid'] : null;
        //$fid = isset($args['fid']) ? $args['fid'] : null;
        $fid = $this->request->getPost()->get('fid', '');
        $u = isset($args['u']) ? $args['u'] : 0;
        $u = $this->request->getPost()->get('u', 0);
        //$inici = isset($args['inici']) ? $args['inici'] : null;
        $inici= $this->request->getPost()->get('inici', '');
        
        $listmessages = ModUtil::apiFunc('IWforums', 'user', 'getall_msg', array('ftid' => $ftid,
            'fid' => $fid,
            'usuari' => $u,
            'indent' => 0,
            'idparent' => 0,
            'inici' => $inici,
            'rpp' => 10));
        $messages = array();
        $hi_ha_missatges = false;
        // process the messages
        foreach ($listmessages as $message) {
            if (isset($message))
                $hi_ha_missatges = true;
            $imatge = (strpos($message['llegit'], '$' . UserUtil::getVar('uid') . '$') == 0) ? 'msgNo.gif' : 'msg.gif';
            $lectors = $message['llegit'];
            $llegit = (strpos($message['llegit'], '$' . UserUtil::getVar('uid') . '$') == 0) ? 0 : 1;
            $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
            $photo = ModUtil::func('IWmain', 'user', 'getUserPicture', array('uname' => UserUtil::getVar('uname', $message['usuari']),
                    'sv' => $sv));
            if (strpos($message['marcat'], '$' . UserUtil::getVar('uid') . '$') == 0) {
                $marcat = 'res.gif';
                $m = 1;
                $textmarca = $this->__('Check the message');
            } else {
                $marcat = 'marcat.gif';
                $m = 0;
                $textmarca = $this->__('Uncheck the message');
            }
            $boolMarcat = (strpos($message['marcat'], '$' . UserUtil::getVar('uid') . '$') == 0) ? false : true;
            $temps_esborrat = $registre['msgDelTime'];
            $temps_edicio = $registre['msgEditTime'];
            $esborrable = false;
            $editable = false;
            if (time() < $message['data'] + 60 * $temps_esborrat && $message['usuari'] == UserUtil::getVar('uid')) {
                $esborrable = true;
            }
            if (time() < $message['data'] + 60 * $temps_edicio && $message['usuari'] == UserUtil::getVar('uid')) {
                $editable = true;
            }
            $messages[] = array('fmid' => $message['fmid'],
                'imatge' => $imatge,
                'photo' => $photo,
                'llegit' => $llegit,
                'lectors' => $lectors,
                'title' => $message['titol'],
                'missatge' => $message['missatge'],
                'user' => $message['usuari'],
                'datetime' =>  strtolower(DateUtil::getDatetime($message['data'], 'datetimelong', true)),
                'date' => date('d/m/y', $message['data']),
                'time' => date('H.i', $message['data']),
                'adjunt' => $message['adjunt'],
                'icon' => $message['icon'],
                'marcat' => $marcat,
                'boolMarcat' => $boolMarcat,
                'm' => $m,
                'esborrable' => $esborrable,
                'editable' => $editable,
                'textmarca' => $textmarca,
                'indent' => $message['indent'],
                'oid' => $message['oid'],
                'onTop' => $message['onTop'],
                'canDelete' => !ModUtil::apiFunc($this->name, 'user', 'is_parent', array('fmid' => $message['fmid']))
            );
        }
        $result = array();
        $result['messages'] = $messages;
        $result['hi_ha_missatges'] = $hi_ha_missatges;
        return $result;
    }
    /*
      Delete a message in a forum
     */

    public function del_msg($args) {
        
        $fmid = FormUtil::getPassedValue('fmid', isset($args['fmid']) ? $args['fmid'] : null, 'POST');
        
        //$fmid = isset($args['fmid']) ? $args['fmid'] : null;
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        //check needed values
        if (!isset($fmid)) {
            return LogUtil::registerError("Function del_msg: ".$this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        //get message
        //$item = ModUtil::apiFunc('IWforums', 'user', 'get_msg', array('fmid' => $fmid));
        $item = ModUtil::apiFunc('IWforums', 'user', 'getMessageInfo', array('fmid' => $fmid));
        if ($item == false) {
            return LogUtil::registerError($this->__('No messages have been found'));
        }
        //get forum information
        $registre = ModUtil::apiFunc('IWforums', 'user', 'get', array('fid' => $item['fid']));
        if ($registre == false) {
            LogUtil::registerError($this->__('The forum upon which the action had to be carried out hasn\'t been found'));
            return System::redirect(ModUtil::url('IWforums', 'user', 'main'));
        }
        
        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $item['fid']));
        if ($access < 2) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        $moderator = ($access == 4) ? true : false;
        //Check if user can delete the message
        if (!$moderator && (time() > $item['data'] + 60 * $registre['msgDelTime'] || $item['usuari'] != UserUtil::getVar('uid'))) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }        
        //Delete the note content
        if (!DBUtil::deleteObjectByID('IWforums_msg', $fmid, 'fmid')) {
            return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
        }              
        //Update de last time and user in forum topic
        $updated = ModUtil::apiFunc('IWforums', 'user', 'updateLast', array('ftids' => array($item['ftid'])));
        
        $tema = ModUtil::apiFunc('IWforums', 'user', 'get_tema', array('ftid' => $item['ftid'], 'fid' => $fid));
        // If topic has no messages and topic was created by de current user then delete topic
        if (($tema['last_time']=="") && ($tema['usuari'] == UserUtil::getVar('uid'))) {
            if (ModUtil::apiFunc('IWforums', 'user', 'deltema', array('ftid' => $ftid, 'fid' => $fid, 'force' => true))){                    
                DBUtil::flushCache('IWforums_temes');        
                LogUtil::registerStatus($this->__('The empty topic has been deleted.'));                
            }    
        }        
        //success
        return true;
    }

    /*
      Moves a message and all its replies between topics and forums
     */

    public function mou($args) {
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        $fmid = FormUtil::getPassedValue('fmid', isset($args['fmid']) ? $args['fmid'] : null, 'POST');
        $noutema = FormUtil::getPassedValue('noutema', isset($args['noutema']) ? $args['noutema'] : null, 'POST');
        $nouforum = FormUtil::getPassedValue('nouforum', isset($args['nouforum']) ? $args['nouforum'] : null, 'POST');
        $ftid = FormUtil::getPassedValue('ftid', isset($args['ftid']) ? $args['ftid'] : null, 'POST');
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        //Comprovem que els valors han arribat
        if (!isset($fmid) || !isset($noutema)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if ($access < 4) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        //check if user can access the forum where the messages are going to be moved
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $noutema));
        if ($access < 4) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_msg_column'];
        // Update message clicked. Change idparent to 0
        $where = "$c[fmid]=$fmid";
        $items = array('fid' => $nouforum,
            'ftid' => $noutema,
            'idparent' => 0);
        if (!DBUTil::updateObject($items, 'IWforums_msg', $where)) {
            return LogUtil::registerError($this->__('The transfer of the message has failed'));
        }

        // Get the rest messages to move (the replies)
        $listmessages = ModUtil::apiFunc('IWforums', 'user', 'getall_msg', array('ftid' => $ftid,
                    'fid' => $fid,
                    'indent' => 0,
                    'idparent' => $fmid,
                    'oid' => 0,
                    'tots' => 1));
        // Update the replies
        foreach ($listmessages as $message) {
            $where = "$c[fmid]=$message[fmid]";
            $items = array('fid' => $nouforum,
                'ftid' => $noutema);
            if (!DBUTil::updateObject($items, 'IWforums_msg', $where)) {
                return LogUtil::registerError($this->__('The transfer of the message has failed'));
            }
        }

        //Update de last time and user in forum topic
        $updated = ModUtil::apiFunc('IWforums', 'user', 'updateLast', array('ftids' => array($ftid, $noutema)));
        //success
        return true;
    }

    /*
      Copy the message to another destiny: forum or topic
     */

    public function copy($args) {
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        $fmid = FormUtil::getPassedValue('fmid', isset($args['fmid']) ? $args['fmid'] : null, 'POST');
        $noutema = FormUtil::getPassedValue('noutema', isset($args['noutema']) ? $args['noutema'] : null, 'POST');
        $nouforum = FormUtil::getPassedValue('nouforum', isset($args['nouforum']) ? $args['nouforum'] : null, 'POST');
        $ftid = FormUtil::getPassedValue('ftid', isset($args['ftid']) ? $args['ftid'] : null, 'POST');
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        //Comprovem que els valors han arribat
        if (!isset($fmid) || !isset($noutema)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if ($access < 4) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        //check if user can access the forum where the messages are going to be moved
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $noutema));
        if ($access < 4) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        //get message
        $message = ModUtil::apiFunc('IWforums', 'user', 'get_msg', array('fmid' => $fmid));
        if ($message == false) {
            return LogUtil::registerError($this->__('No messages have been found'));
        }

        $item = array('fid' => $nouforum,
            'ftid' => $noutema,
            'titol' => $message['titol'],
            'usuari' => $message['usuari'],
            'missatge' => $message['missatge'],
            'llegit' => "$$" . UserUtil::getVar('uid') . "$",
            'data' => time(),
            'adjunt' => $message['adjunt'],
            'icon' => $message['icon'],
            'marcat' => '$',
            'idparent' => 0,
            'lastdate' => time(),
            'onTop' => $message['onTop'],
        );
        if (!DBUtil::insertObject($item, 'IWforums_msg', 'fmid')) {
            return LogUtil::registerError($this->__('Error! Creation attempt failed.'));
        }

        //Update de last time and user in forum topic
        $updated = ModUtil::apiFunc('IWforums', 'user', 'updateLast', array('ftids' => array($ftid)));
        //Retorna el id del nou registre que s'acaba d'introduir
        return $item['fmid'];
    }

    /*
        Funció que esborra un tema d'un fòrum
    */

    public function deltema($args) {
        
        //$ftid = FormUtil::getPassedValue('ftid', isset($args['ftid']) ? $args['ftid'] : null, 'POST');
        //$fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        $fid = $this->request->getPost()->get('fid', '');
        $ftid = $this->request->getPost()->get('ftid', '');
        //$ftid = isset($args['ftid']) ? $args['ftid'] : null;
        //$fid = isset($args['fid']) ? $args['fid'] : null;
        $force = isset($args['force']) ? $args['force'] : false;
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        // Arguments check
        if (!isset($ftid) || !isset($fid)) {
            return LogUtil::registerError("Function deltema: ".$this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        //Cridem la funcié get que retorna les dades
        $link = ModUtil::apiFunc('IWforums', 'user', 'get_tema', array('ftid' => $ftid,
                    'fid' => $fid));
        //Comprovem que el registre efectivament existeix i, per tant, es podrà esborrar
        if ($link == false) {
            return LogUtil::registerError($this->__('No messages have been found'));
        }
        //check if user can access the forum
        if (is_null($fid)) {
            $topic = DBUtil::selectObjectByID('IWforums_temes', $ftid, 'ftid');
            $fid = $topic['fid'];
        }
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if (($access < 4) && (!$force)) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        $pntable = DBUtil::getTables();
        $t = $pntable['IWforums_temes'];
        $c = $pntable['IWforums_temes_column'];
        $t2 = $pntable['IWforums_msg'];
        $c2 = $pntable['IWforums_msg_column'];
         
        
        //get messages files
        //$files = ModUtil::apiFunc('IWforums', 'user', 'get_adjunts', array('fid' => $fid));
        $files = ModUtil::apiFunc('IWforums', 'user', 'get_adjunts', array('ftid' => $ftid, 'mode' => 't'));

        //delete messages files
        foreach ($files as $file) {
            //if (false){
            $filePath = ModUtil::getVar('IWmain', 'documentRoot') . '/' . ModUtil::getVar('IWforums', 'urladjunts') . '/' . $file['adjunt'];
            if (file_exists($filePath))
                unlink($filePath);
            //}
        }
        // Messages deletion
        $where = "$c2[ftid]=$ftid";
        if (!DBUtil::deleteWhere('IWforums_msg', $where)) {
            return LogUtil::registerError($this->__('An error has occurred while deleting the message'));
        }
        // record deletion
        if (!DBUtil::deleteWhere('IWforums_temes', $where)) {
            return LogUtil::registerError($this->__('An error has occurred while deleting the message'));
        }

        //Retornem true ja que el procés ha finalitzat amb éxit
        return true;
    }

    
    /*
      Create a new msg
     */

    public function crear_msg($args) {
        $msg = FormUtil::getPassedValue('msg', isset($args['msg']) ? $args['msg'] : null, 'POST');
        $titol = FormUtil::getPassedValue('titol', isset($args['titol']) ? $args['titol'] : null, 'POST');
        $titolmsg = FormUtil::getPassedValue('titolmsg', isset($args['titolmsg']) ? $args['titolmsg'] : null, 'POST');
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        $ftid0 = FormUtil::getPassedValue('ftid0', isset($args['ftid0']) ? $args['ftid0'] : null, 'POST');
        $ftid = FormUtil::getPassedValue('ftid', isset($args['ftid']) ? $args['ftid'] : null, 'POST');
        $adjunt = FormUtil::getPassedValue('adjunt', isset($args['adjunt']) ? $args['adjunt'] : null, 'POST');
        $icon = FormUtil::getPassedValue('icon', isset($args['icon']) ? $args['icon'] : null, 'POST');
        $idparent = FormUtil::getPassedValue('idparent', isset($args['idparent']) ? $args['idparent'] : null, 'POST');
        $oid = FormUtil::getPassedValue('oid', isset($args['oid']) ? $args['oid'] : null, 'POST');
        $onTop = FormUtil::getPassedValue('onTop', isset($args['onTop']) ? $args['onTop'] : null, 'POST');

        if ($ftid0 != null)
            $ftid = $ftid0;
        if ($titolmsg != null)
            $titol = $titolmsg;
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if ($access < 2) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        //Comprova que el tÃ­tol del missatge i el contingut del mateix hagin arribat
        if (!isset($titol) || !isset($msg)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        if ($access > 4)
            $onTop = 0;

        $item = array('fid' => $fid,
            'ftid' => $ftid,
            'titol' => $titol,
            'usuari' => UserUtil::getVar('uid'),
            'missatge' => $msg,
            'llegit' => "$$" . UserUtil::getVar('uid') . "$",
            'data' => time(),
            'adjunt' => $adjunt,
            'icon' => $icon,
            'marcat' => '$',
            'idparent' => $idparent,
            'lastdate' => time(),
            'onTop' => $onTop,
        );
        if (!DBUtil::insertObject($item, 'IWforums_msg', 'fmid')) {
            return LogUtil::registerError($this->__('Error! Creation attempt failed.'));
        }
        if (isset($oid) && $oid != 0) {
            $pntable = DBUtil::getTables();
            $c = $pntable['IWforums_msg_column'];
            $where = "$c[fmid]=$oid";
            $items = array('lastdate' => time());
            if (!DBUTil::updateObject($items, 'IWforums_msg', $where)) {
                return LogUtil::registerError($this->__('Error! Update attempt failed.'));
            }
        }
        //Update de last time and user in forum topic
        $updated = ModUtil::apiFunc('IWforums', 'user', 'updateLast', array('ftids' => array($ftid)));
        //Retorna el id del nou registre que s'acaba d'introduir
        return $item['fmid'];
    }

    /*
     * update the last user and last time in topic table
     *
     */

    public function updateLast($args) {
        $ftids = FormUtil::getPassedValue('ftids', isset($args['ftids']) ? $args['ftids'] : null, 'POST');
        //get last message in topic
        $pntable = DBUtil::getTables();
        foreach ($ftids as $ftid) {
            $c = $pntable['IWforums_msg_column'];
            $where = "$c[ftid] = $ftid";
            $orderby = "$c[data] desc";
            // get the objects from the db
            $items = DBUtil::selectObjectArray('IWforums_msg', $where, $orderby, '0', '1', 'ftid');
            // Check for an error with the database code, and if so set an appropriate
            // error message and return
            if ($items === false) {
                return LogUtil::registerError($this->__('Error! Could not load items.'));
            }
            //update topic last time and user information
            $c = $pntable['IWforums_temes_column'];
            $where = "$c[ftid]=$ftid";
            $items = array('last_time' => $items[$ftid]['data'],
                'last_user' => $items[$ftid]['usuari']);
            if (!DBUTil::updateObject($items, 'IWforums_temes', $where)) {
                return LogUtil::registerError($this->__('Error! Update attempt failed.'));
            }
        }
        return true;
    }

    /*
      Funció que esborra el fitxer adjunt a un missatge
     */

    public function del_adjunt($args) {
        $fmid = FormUtil::getPassedValue('fmid', isset($args['fmid']) ? $args['fmid'] : null, 'POST');
        $fid = FormUtil::getPassedValue('fmid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        // Security check
        
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        //Comprova que el tÃ­tol del missatge i el contingut del mateix hagin arribat
        if (!isset($fmid)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        //Agafem les dades del missatge
        $missatge = ModUtil::apiFunc('IWforums', 'user', 'get_msg', array('fmid' => $fmid, 'fid'=> $fid));    

        if ($missatge == false) {
            return LogUtil::registerError($this->__('No messages have been found'));
        }
        //Carreguem la informació del fòrum
        $registre = ModUtil::apiFunc('IWforums', 'user', 'get', array('fid' => $missatge['fid']));
        if ($registre == false) {
            LogUtil::registerError($this->__('The forum upon which the action had to be carried out hasn\'t been found'));
            return System::redirect(ModUtil::url('IWforums', 'user', 'main'));
        }
        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $missatge['fid']));
        if ($access < 2) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        //Comprovem que l'usuari sigui moderador del férum i pugui editar el missatge
        $moderator = ($access == 4) ? true : false;
        if (!$moderator && (time() > $missatge['data'] + 60 * $registre['msgDelTime'] || $missatge['usuari'] != UserUtil::getVar('uid'))) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        //Esborrem el fitxer adjunt del servidor
        $esborrat = unlink(ModUtil::getVar('IWmain', 'documentRoot') . '/' . ModUtil::getVar('IWforums', 'urladjunts') . '/' . $missatge['adjunt']);
        if ($esborrat) {
            $pntable = DBUtil::getTables();
            $c = $pntable['IWforums_msg_column'];
            $where = "$c[fmid]=$fmid";
            $items = array('adjunt' => '');
            if (!DBUTil::updateObject($items, 'IWforums_msg', $where)) {
                return LogUtil::registerError($this->__('An error has occurred while editing the message'));
            }
        }
       
        //Retorna el id del nou registre que s'acaba d'introduir
        return $missatge;
    }

    /*
      Funcié que retorna una matriu amb la informacié de tots els usuaris que han participat en el férum
     */

    public function getremitents($args) {
        $tots = FormUtil::getPassedValue('tots', isset($args['tots']) ? $args['tots'] : null, 'POST');
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        $ftid = FormUtil::getPassedValue('ftid', isset($args['ftid']) ? $args['ftid'] : null, 'POST');
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        $pntable = DBUtil::getTables();
        //$t = $pntable['IWforums_msg'];
        $c = $pntable['IWforums_msg_column'];
        $tema = ($tots != null && $tots == 1) ? "" : ' ' . $c['ftid'] . '=' . $ftid . ' AND ';
        $where = $tema . " $c[fid]=$fid";
        $items = DBUtil::selectObjectArray('IWforums_msg', $where, '', -1, -1, 'usuari');

        //Comprovem que la consulta hagi estat amb éxit
        if ($items === false) {
            return LogUtil::registerError($this->__('An error has occurred while reading records from the data base'));
        }

        //Retornem la matriu plena de registres
        return $items;
    }

    /*
     * Marca tots els missatges visualitzats com a llegits
     */
    
    public function markMessagesAsReaded($args){
        $messages = FormUtil::getPassedValue('messages', isset($args['messages']) ? $args['messages'] : null, 'POST');
        // Marquem tots els missatges que es mostraran copm a llegits
        foreach ($messages as $message) {
            // set user as message reader
            if (strpos($message['lectors'], '$' . UserUtil::getVar('uid') . '$') == 0) {
                ModUtil::apiFunc('IWforums', 'user', 'llegit', 
                    array('fmid'   => $message['fmid'],
                          'llegit' => $message['lectors']));

                $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
                ModUtil::func('IWmain', 'user', 'userSetVar', 
                    array('module' => 'IWmain_block_news',
                          'name' => 'have_news',
                          'value' => 'fo',
                          'sv' => $sv));
            }
        }
    }
    /*
      Funcié que marca o treu la marca d'un missatge
     */

    public function marcat($args) {
        $marcat = FormUtil::getPassedValue('marcat', isset($args['marcat']) ? $args['marcat'] : null, 'POST');
        $fmid = FormUtil::getPassedValue('fmid', isset($args['fmid']) ? $args['fmid'] : null, 'POST');
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        //Comprovem que els valors han arribat
        if (!isset($fmid) || !isset($marcat)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_msg_column'];

        $where = "$c[fmid]=$fmid";
        $items = array('marcat' => $marcat);

        if (!DBUTil::updateObject($items, 'IWforums_msg', $where)) {
            return LogUtil::registerError($this->__('An error has occurred while checking/unchecking a message'));
        }

        return true;
    }

    /*
      Funcié que modifica un missatge
     */

    public function update_msg($args) {
        $msg = FormUtil::getPassedValue('msg', isset($args['msg']) ? $args['msg'] : null, 'POST');
        $titol = FormUtil::getPassedValue('titol', isset($args['titol']) ? $args['titol'] : null, 'POST');
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        $fmid = FormUtil::getPassedValue('fmid', isset($args['fmid']) ? $args['fmid'] : null, 'POST');
        $adjunt = FormUtil::getPassedValue('fadjunt', isset($args['fadjunt']) ? $args['fadjunt'] : null, 'POST');
        $icon = FormUtil::getPassedValue('icon', isset($args['icon']) ? $args['icon'] : null, 'POST');
        $idparent = FormUtil::getPassedValue('idparent', isset($args['idparent']) ? $args['idparent'] : null, 'POST');
        $onTop = FormUtil::getPassedValue('onTop', isset($args['onTop']) ? $args['onTop'] : null, 'POST');
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        //Carreguem la informació del fòrum
        $registre = ModUtil::apiFunc('IWforums', 'user', 'get', array('fid' => $fid));
        if ($registre == false) {
            LogUtil::registerError($this->__('The forum upon which the ation had to be carried out hasn\'t been found'));
            return System::redirect(ModUtil::url('IWforums', 'user', 'main'));
        }
        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if ($access < 2) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        if ($access > 4)
            $onTop = 0;
        //Agafem les dades del missatge
        $missatge = ModUtil::apiFunc('IWforums', 'user', 'get_msg', array('fmid' => $fmid));
        if ($missatge == false) {
            return LogUtil::registerError($this->__('No messages have been found'));
        }
        //Comprovem que l'usuari sigui moderador del férum i pugui editar el missatge
        $moderator = ($access == 4) ? true : false;
        if (!$moderator && (time() > $missatge['data'] + 60 * $registre['msgEditTime'] || $missatge['usuari'] != UserUtil::getVar('uid'))) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        //Comprova que el tétol del missatge i el contingut del mateix hagin arribat
        if (!isset($titol) || !isset($msg)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_msg_column'];
        $msg = str_replace("'", "&#039;", $msg);
        $titol = str_replace("'", "&#039;", $titol);
        $where = "$c[fmid]=$fmid";
        $items = array('titol' => $titol,
            'missatge' => $msg,
            'icon' => $icon,
            'adjunt' => $adjunt,
            'onTop' => $onTop,
            );

        if (!DBUTil::updateObject($items, 'IWforums_msg', $where)) {
            return LogUtil::registerError($this->__('Error! Update attempt failed.'));
        }

        return $fmid;
    }

    /*
      Funció que retorna si un missatge és pare o no ho és
     */

    public function is_parent($args) {        
        $fmid = isset($args['fmid']) ? $args['fmid'] : null;
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        //Comprovacié de seguretat. Si falla retorna una matriu buida
        $registres = array();
        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_msg_column'];
        $where = "$c[idparent]=$fmid";
        $number = DBUTil::selectObjectCount('IWforums_msg', $where);
        if ($number === false) {
            return LogUtil::registerError($this->__('An error has occurred while reading records from the data base'));
        }
        if ($number > 0) {
            return true;
        } else {
            return false;
        }
    }

    /*
      Gets all the unread messages in a forum topic
      @param $fid:		forum id
      @return:			Array with all the messages ordered
     */

    public function getall_msg_unread($args) {
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        $items = array();
        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_msg_column'];
        $where = "$c[fid]=$fid AND $c[llegit] NOT LIKE '%$" . UserUtil::getVar('uid') . "$%';";

        $items = DBUtil::selectObjectArray('IWforums_msg', $where, '');
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('An error has occurred while reading records from the data base'));
        }

        //Retornem la matriu plena de registres
        return $items;
    }

    /*
      Funció que modifica la posició d'un tema del fòrum
     */

    public function put_order($args) {
        $fid = isset($args['fid']) ? $args['fid'] : null;
        $ftid = isset($args['ftid']) ? $args['ftid'] : null;
        $ordre = isset($args['ordre']) ? $args['ordre'] : null;

        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        //Comprovem que els valors han arribat
        if ($ftid == null || $ordre == null) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));            
        }
        //Cridem la funcié get de l'API que ens retornaré les dades de l'entrada al mené
        $forum = ModUtil::apiFunc('IWforums', 'user', 'get', array('fid' => $fid));
        //Comprovem que la consulta anterior ha tornat amb resultats
        if ($forum == false) {
            return LogUtil::registerError($this->__('The forum upon which the ation had to be carried out hasn\'t been found'));
        }
        //check if user can access the forum
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid));
        if ($access < 4) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }
        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_temes_column'];

        $where = "$c[ftid]=$ftid";
        $items = array('order' => $ordre);

        if (!DBUTil::updateObject($items, 'IWforums_temes', $where)) {
            return LogUtil::registerError($this->__('Error! Update attempt failed.'));
        }
        return $ordre;
    }

    /**
     * Gets all the notes where that the user has flagged for a forum
     * @author:     Albert Pérez Monfort (aperezm@xtec.cat)
     * @param:	Identity of the forum where to search the messages
     * @return:	And array with the items information
     */
    public function getFlagged($args) {
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_msg_column'];
        $where = "$c[marcat] like '%$" . UserUtil::getVar('uid') . "$%' AND $c[fid]=$fid";
        $orderby = "$c[data] desc";
        // get the objects from the db
        $items = DBUtil::selectObjectArray('IWforums_msg', $where, $orderby, '-1', '-1', 'fmid');
        // Check for an error with the database code, and if so set an appropriate
        // error message and return
        if ($items === false) {
            return LogUtil::registerError($this->__('Error! Could not load items.'));
        }
        // Return the items
        return $items;
    }

    public function onTop($args) {
        $fmid = FormUtil::getPassedValue('fmid', isset($args['fmid']) ? $args['fmid'] : null, 'POST');
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }
        //check needed values
        if (!isset($fmid)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }
        //get message
        $item = ModUtil::apiFunc('IWforums', 'user', 'get_msg', array('fmid' => $fmid));
        if ($item == false) {
            return LogUtil::registerError($this->__('No messages have been found'));
        }
        //check if user can access the forum as moderator
        $access = ModUtil::func('IWforums', 'user', 'access', array('fid' => $item['fid']));
        if ($access < 4) {
            return LogUtil::registerError($this->__('You can\'t access the forum'));
        }

        $onTop = ($item['onTop'] == 0) ? 1 : 0;

        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_msg_column'];
        $where = "$c[fmid]=$fmid";

        $items = array('onTop' => $onTop);

        if (!DBUTil::updateObject($items, 'IWforums_msg', $where)) {
            return LogUtil::registerError($this->__('An error has occurred while setting a message as main message.'));
        }

        //Update de last time and user in forum topic
        $updated = ModUtil::apiFunc('IWforums', 'user', 'updateLast', array('ftids' => array($item['ftid'])));
        //success
        return true;
    }

    public function getlinks($args) {
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : 0, 'GETPOST');
        $ftid = FormUtil::getPassedValue('ftid', isset($args['ftid']) ? $args['ftid'] : 0, 'GETPOST');
        $fmid = FormUtil::getPassedValue('fmid', isset($args['fmid']) ? $args['fmid'] : 0, 'GETPOST');
        $inici = FormUtil::getPassedValue('inici', isset($args['inici']) ? $args['inici'] : 0, 'GETPOST');
        $oid = FormUtil::getPassedValue('oid', isset($args['oid']) ? $args['oid'] : 0, 'GETPOST');
        $u = FormUtil::getPassedValue('u', isset($args['u']) ? $args['u'] : 0, 'GETPOST');
        $m1 = FormUtil::getPassedValue('m1', isset($args['m1']) ? $args['m1'] : 0, 'POST');
        $m2 = FormUtil::getPassedValue('m2', isset($args['m2']) ? $args['m2'] : 0, 'POST');
        $m3 = FormUtil::getPassedValue('m3', isset($args['m3']) ? $args['m3'] : 0, 'POST');
        $m4 = FormUtil::getPassedValue('m4', isset($args['m4']) ? $args['m4'] : 0, 'POST');
        $m5 = FormUtil::getPassedValue('m5', isset($args['m5']) ? $args['m5'] : 0, 'POST');
        $m6 = FormUtil::getPassedValue('m6', isset($args['m6']) ? $args['m6'] : 0, 'POST');
        $m7 = FormUtil::getPassedValue('m7', isset($args['m7']) ? $args['m7'] : 0, 'POST');
        $m8 = FormUtil::getPassedValue('m8', isset($args['m8']) ? $args['m8'] : 0, 'POST');
        $m9 = FormUtil::getPassedValue('m9', isset($args['m9']) ? $args['m9'] : 0, 'POST');
        $m12 = FormUtil::getPassedValue('m12', isset($args['m12']) ? $args['m12'] : 0, 'POST');
        $m13 = FormUtil::getPassedValue('m13', isset($args['m13']) ? $args['m13'] : 0, 'POST');
        $access = ($fid > 0) ? ModUtil::func('IWforums', 'user', 'access', array('fid' => $fid)) : false;

        $message = array('marcat' => '');
        if ($fmid > 0) {
            //get message information
            $message = ModUtil::apiFunc('IWforums', 'user', 'get_msg', array('fmid' => $fmid));
            if ($message == false) {
                LogUtil::registerError($this->__('No messages have been found'));
                return System::redirect(ModUtil::url('IWforums', 'user', 'main'));
            }
        }
        $links = array();
        if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && $m5 == 1 && $access > 1) {
            $links[] = array('url' => ModUtil::url('IWforums', 'user', 'nou_msg', array('inici' => $inici, 'fid' => $fid, 'ftid' => $ftid, 'u' => $u, 'fmid' => $fmid, 'oid' => $oid)), 'text' => $this->__('Reply to the message'), 'id' => 'iwforums_nou_msg', 'class' => 'z-icon-es-new');
        }
        if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && $m6 == 1 && $access > 0 && UserUtil::isLoggedIn()) {
            $links[] = array('url' => ModUtil::url('IWforums', 'user', 'lectors', array('inici' => 0, 'fid' => $fid, 'ftid' => $ftid, 'u' => $u, 'fmid' => $fmid, 'oid' => $oid)), 'text' => $this->__('Who has read the message?'), 'id' => 'iwforums_lectors', 'class' => 'z-icon-es-info');
        }

        if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && $m1 == 1 && $access > 2 && $ftid == 0) {
            $links[] = array('url' => ModUtil::url('IWforums', 'user', 'nou_tema', array('fid' => $fid, 'u' => $u, 'inici' => $inici)), 'text' => $this->__('Create a new topic'), 'id' => 'iwforums_nou_tema', 'class' => 'z-icon-es-new');
        }
        if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && $m2 == 1 && $access > 1) {
            $links[] = array('url' => ModUtil::url('IWforums', 'user', 'nou_msg', array('fid' => $fid, 'u' => $u, 'inici' => $inici, 'ftid' => $ftid)), 'text' => $this->__('Send a new message'), 'id' => 'iwforums_nou_msg', 'class' => 'z-icon-es-new');
        }
        if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && $m3 == 1) {
            $links[] = array('url' => ModUtil::url('IWforums', 'user', 'main', array('inici' => $inici)), 'text' => $this->__('View the forum list'), 'id' => 'iwforums_main', 'class' => 'z-icon-es-view');
        }
        if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && ($m4 == 1 && $access > 0) || ($ftid != 0 && $access > 0)) {
            $links[] = array('url' => ModUtil::url('IWforums', 'user', 'forum', array('inici' => $inici, 'u' => $u, 'fid' => $fid)), 'text' => $this->__('Return to the list of topics and messages'), 'id' => 'iwforums_forum', 'class' => 'z-icon-es-view');
        }
        if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && $m7 == 1 && $access > 0 && $fmid != 0) {
            $links[] = array('url' => ModUtil::url('IWforums', 'user', 'llista_msg', array('inici' => $inici, 'u' => $u, 'fid' => $fid, 'ftid' => $ftid)), 'text' => $this->__('Return to the message list'), 'id' => 'iwforums_llista_msg', 'class' => 'z-icon-es-view');
        }
        if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && $m8 == 1 && $access > 0) {
            $links[] = array('url' => ModUtil::url('IWforums', 'user', 'msg', array('inici' => $inici, 'u' => $u, 'fid' => $fid, 'ftid' => $ftid, 'fmid' => $fmid, 'oid' => $oid)), 'text' => $this->__('Return to the message'), 'id' => 'iwforums_msg', 'class' => 'z-icon-es-mail');
        }
        if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && $m9 == 1 && $access > 0 && UserUtil::isLoggedIn()) {
            if ($ftid == 0) {
                $links[] = array('url' => ModUtil::url('IWforums', 'user', 'llegits', array('inici' => $inici, 'fid' => $fid)), 'text' => $this->__('Check all messages as read'), 'id' => 'iwforums_llegits', 'class' => 'z-icon-es-ok');
            } else {
                $links[] = array('url' => ModUtil::url('IWforums', 'user', 'llegits', array('inici' => $inici, 'fid' => $fid, 'ftid' => $ftid, 'u' => $u)), 'text' => $this->__('Check all messages as read'), 'id' => 'iwforums_llegits', 'class' => 'z-icon-es-ok');
            }
        }
        if (SecurityUtil::checkPermission('IWforums::', "::", ACCESS_READ) && ($m12 == 1 || $m13 == 1) && $access > 0 && UserUtil::isLoggedIn()) {
            $links[] = array('url' => "javascript:mark(" . $fid . "," . $fmid . ")", 'text' => $this->__('Check/uncheck the message'), 'id' => 'iwforums_mark', 'class' => 'z-icon-es-view');
        }
        return $links;
    }

    /**
     * Change user subscription to a forum
     * @author Josep Ferràndiz Farré (jferran6@xtec.cat)
     * @param fid forum id
     * @param action (1:add, -1:cancel)
     * @version 3.1.0 
     * @date 04/03/2015
     */
    public function setSubscriptionState($args) {
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : UserUtil::getVar('uid'), 'GET');  
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'GET');  
        $action = FormUtil::getPassedValue('action', isset($args['action']) ? $args['action'] : null, 'GET');  

        $uid = isset($args['uid']) ? $args['uid'] : UserUtil::getVar('uid');  
        $fid = isset($args['fid']) ? $args['fid'] : null;  
        $action = isset($args['action']) ? $args['action'] : null;  
        
        $result = false;
        if (true) {//(isset($fid) && isset($action) && is_numeric($fid) && is_numeric($action)) {
            $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
            $forum = ModUtil::apiFunc($this->name, 'user', 'get', array('fid' => $fid, 'sv' => $sv));
            switch ($action) {
                case IWforums_Constant::SUBSCRIBE:
                    // Check if forum allows subscription 
                    if ($forum['subscriptionMode'] == IWforums_Constant::VOLUNTARY){
                       // Add uid to subscribers field
                        if (strlen($forum['subscribers'])) 
                            $users = explode("$", $forum['subscribers']);
                        else {
                            $users = array();
                        }
                        // Verify uid isn't in subscribers list
                        if (!in_array($uid, $users)) {
                            // Add uid to subscriptors list
                            $users[] = $uid;
                            $subscribers = implode("$", $users);
                            // Update table
                            $pntable = DBUtil::getTables();
                            $c = $pntable['IWforums_definition_column'];
                            $where = "$c[fid]=$fid";
                            $item = array('subscribers' => $subscribers);
                            
                            $result =  (DBUTil::updateObject($item, 'IWforums_definition', $where)) ;
                        }
                    } elseif ($forum['subscriptionMode'] == IWforums_Constant::OPTIONAL){
                       // Remove uid from noSubscribers field 
                       if (strlen($forum['noSubscribers'])) 
                            $users = explode("$", $forum['noSubscribers']);
                        else {
                            $users = array();
                        }
                        // Verify uid isn't in subscribers list
                        if (in_array($uid, $users)) {
                            // Remove uid from noSubscriptors list
                            $remove[] = $uid;
                            //$nlist = array_diff($users, $remove);
                            $noSubscribers = implode("$", array_diff($users, $remove));
                            // Update table
                            $pntable = DBUtil::getTables();
                            $c = $pntable['IWforums_definition_column'];
                            $where = "$c[fid]=$fid";
                            $item = array('noSubscribers' => $noSubscribers);
                            
                            $result =  (DBUTil::updateObject($item, 'IWforums_definition', $where)) ;
                        }                   
                    }   
                    break;
                case IWforums_Constant::UNSUBSCRIBE:
                    // Check if forum allows unsubscription 
                    // Check if forum allows subscription 
                    if ($forum['subscriptionMode'] == IWforums_Constant::VOLUNTARY){
                       // Add uid to subscribers field
                        if (strlen($forum['subscribers'])) 
                            $users = explode("$", $forum['subscribers']);
                        else {
                            $users = array();
                        }
                        // Verify uid isn't in subscribers list
                        if (in_array($uid, $users)) {
                            // Remove uid from noSubscriptors list
                            $remove[] = $uid;
                            //$nlist = array_diff($users, $remove);
                            $subscribers = implode("$", array_diff($users, $remove));
                            // Update table
                            $pntable = DBUtil::getTables();
                            $c = $pntable['IWforums_definition_column'];
                            $where = "$c[fid]=$fid";
                            $item = array('subscribers' => $subscribers);
                            
                            $result =  (DBUTil::updateObject($item, 'IWforums_definition', $where)) ;
                        }                   
                    } elseif ($forum['subscriptionMode'] == IWforums_Constant::OPTIONAL){
                       // Remove uid from noSubscribers field 
                       if (strlen($forum['noSubscribers'])) 
                            $users = explode("$", $forum['noSubscribers']);
                        else {
                            $users = array();
                        }
                        // Verify uid isn't in subscribers list
                        if (!in_array($uid, $users)) {
                            // Add uid to subscriptors list
                            $users[] = $uid;
                            $noSubscribers = implode("$", $users);
                            // Update table
                            $pntable = DBUtil::getTables();
                            $c = $pntable['IWforums_definition_column'];
                            $where = "$c[fid]=$fid";
                            $item = array('noSubscribers' => $noSubscribers);
                            
                            $result =  (DBUTil::updateObject($item, 'IWforums_definition', $where)) ;
                        }                        
                    }   
                    break;
            }
        }
        return $result;        
    }
    
    /**
     * Get susbscription mode information
     * @author Josep Ferràndiz Farré (jferran6@xtec.cat)
     * @param $mode
     * @param action (1:add, -1:cancel)
     * @return array (val, type, explanation) val: forum subscription type (numeric): type: forum subscription type (text); explanation: subscription type text description
     * @version 3.1.0 
     * @date 04/03/2015
     */
    public function getSubscriptionModeText($mode){
        $modeText = array();
        switch ($mode) {
            case IWforums_Constant::NONE:
                $modeText['type'] = $this->__('Not allowed');  
                $modeText['explanation'] = $this->__('Nobody can be subscribed to this forum');  
                break;
            case IWforums_Constant::VOLUNTARY:
                $modeText['type'] = $this->__('Voluntary');  
                $modeText['explanation'] = $this->__('Users must subscribe to this forum and may unsubscribe');  
                break;
            case IWforums_Constant::OPTIONAL:
                $modeText['type'] = $this->__('Optional');
                $modeText['explanation'] = $this->__('All users are subscribed by default and may unsubscribe');  
                break;
            case IWforums_Constant::COMPULSORY: 
                $modeText['type'] = $this->__('Compulsory');
                $modeText['explanation'] = $this->__("All users are subscribed and can't unsubscribe");  
                break;                             
        }
        $modeText['val'] = $mode; 
        return $modeText;
    }
    
    /**
    * Get user subscriptions. If is passed fid parameter then only  is considered 
    * the specified forum, otherwise all forums. If uid is null then uses current user uid.
    * @author Josep Ferràndiz Farré (jferran6@xtec.cat)
    * 
    * @param fid forum id. If is set returns only information about specified forum. Otherwise all forums
    * @param uid user id
    * @return array (mode, action) mode: forum subscription mode or type; action: allowed subscription action for this forum 
    * @version 3.1.0 
    * @date 04/03/2015
    */    
 
    public function getUserSubscriptions($args){
        $uid = FormUtil::getPassedValue('uid', isset($args['uid']) ? $args['uid'] : UserUtil::getVar('uid'), 'GET');  
        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'GET');  
       
        $subscriptionInfo = array();
        if (isset($fid)) {
            // Get specific forum
            $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
            $forums[$fid] = ModUtil::apiFunc($this->name, 'user', 'get', array('fid' => $fid, 'sv' => $sv));
        } else {
            // get all forums
            $forums = ModUtil::apiFunc($this->name, 'user', 'getall' );
        }

       // $forums = ModUtil::apiFunc($this->name, 'user', 'getall' );
        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        foreach ($forums as $forum){            
            // For each forum get access level
            $access = ModUtil::func($this->name, 'user', 'access', array('fid' => $forum['fid'], 'uid' => $uid, 'sv'=> $sv));            

            if ($access > IWforums_Constant::NONE) {
                switch ($forum['subscriptionMode']) {
                    case IWforums_Constant::OPTIONAL:
                        // Subscribed by default. All users are subscribed if no have been canceled the subscription
                        // Get unsubscriptors list
                        if (strlen($forum['noSubscribers'])) {
                            $noSubsc = explode('$', $forum['noSubscribers']);
                        } else {
                            $noSubsc = array();
                        }
                        $subscriptionInfo[ $forum['fid']]['mode'] = IWforums_Constant::OPTIONAL;
                        $subscriptionInfo[ $forum['fid']]['action'] = in_array( $uid, $noSubsc) ? 'add' : 'cancel'; 
                        break;
                    case IWforums_Constant::VOLUNTARY:
                        // Unsubscribed by default.                        
                        // Get subscriptors list
                        if (strlen($forum['subscribers'])) {
                            $subsc = explode('$', $forum['subscribers']);
                        } else {
                            $subsc = array();
                        }
                        $subscriptionInfo[ $forum['fid']]['mode'] = IWforums_Constant::VOLUNTARY;
                        $subscriptionInfo[ $forum['fid']]['action'] = in_array( $uid ,$subsc) ? 'cancel' : 'add';
                        break;
                    case IWforums_Constant::COMPULSORY:
                        // Everybody with, at least, read access is subscribed
                        $subscriptionInfo[ $forum['fid']]['mode'] = IWforums_Constant::COMPULSORY;
                        $subscriptionInfo[ $forum['fid']]['action'] = 'none';
                        break;
                }
            }        
        }
        return $subscriptionInfo;
    }

    /**
     * Get all unreaded messages for all forums and all subscribers
     * @author Josep Ferràndiz Farré (jferran6@xtec.cat)
     * 
     * @param $dateTimeFrom timestamp date/time indicates starting period until now to collect unreaded messages 
     * @return array with messages unreaded per user, grouped by forum and topic 
     * 
     * @version 3.1.0 
     * @date 09/03/2015
     */
    public function getAllUnreadedMessages($dateTimeFrom) {
        $messages = array();
        if (!is_null($dateTimeFrom)) {
            // Get forums that allow subscription
            $pntable = DBUtil::getTables();
            $c = $pntable['IWforums_definition_column'];
            $where = "$c[actiu]=1 AND $c[subscriptionMode]>0";
            $orderby = "$c[nom_forum]";
            // get the forums that allow subscription
            $forums = DBUtil::selectObjectArray('IWforums_definition', $where, $orderby, '-1', '-1', 'fid');
            
            foreach ($forums as $forum){
                // Depenent del tipus de subscripció 
                switch ($forum['subscriptionMode']){
                    case IWforums_Constant::COMPULSORY:
                        // Everybody in readers groups
                        // Get forum groups
                        
                        $strGrups = $forum['grup'];
                        $groups = explode('$$', $strGrups);
                        $members = array();
                        foreach ($groups as $group){
                            // Get group members
                            $users = UserUtil::getUsersForGroup($group);
                            foreach ($users as $user){
                                // Avoid duplicated users
                                if (!in_array($user, $members)) $members[] = $user;
                            }
                        }
                        foreach ($members as $uid){
                            // Get the forum topics
                            $t = $pntable['IWforums_temes_column'];
                            $where = "$t[fid]=".$forum['fid'];
                            $topics = DBUtil::selectObjectArray('IWforums_temes', $where);
                            foreach ($topics as $topic){
                                // Get the topic messages
                                
                                $m = $pntable['IWforums_msg_column'];
                                $where = "$m[ftid]=".$topic['ftid']." AND $m[data] >= ".mkTime($dateTimeFrom);
                                $where .= " AND $m[llegit] NOT LIKE '%$".$uid."$%'";
                                $messages[] = DBUtil::selectObjectArray('IWforums_msg', $where, 'data');

                            }
                            
                            // Check if message is unreaded
                        }
                        // in $members are all the users subscribed
                        //array_key_exists()
                         
                        break;
                    case IWforums_Constant::VOLUNTARY:
                        // Only subscribers
                        $subscribers = explode('$', $forum['subscribers']);
                        break;
                    case IWforums_Constant::OPTIONAL:
                        // Everybody in readers groups execept unsubscribers
                        break;
                }
            }
        } 
        return $messages;
    }
}