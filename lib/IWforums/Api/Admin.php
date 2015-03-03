<?php

class IWforums_Api_Admin extends Zikula_AbstractApi {

    /**
     * Create a new forum in database
     * @author	Albert Pï¿œrez Monfort (aperezm@xtec.cat)
     * @param:	args   Array with the forum properties
     * @return	true if success
     */
    public function create($args) {
        $item = FormUtil::getPassedValue('item', isset($args['item']) ? $args['item'] : null, 'POST');
        
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', "::", ACCESS_ADMIN)) {
            throw new Zikula_Exception_Forbidden();
        }

        //Needed arguments
        if ((!isset($item['nom_forum']))) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        if (!DBUtil::insertObject($item, 'IWforums_definition', 'fid')) {
            return LogUtil::registerError($this->__('Error! Creation attempt failed.'));
        }

        // Let any hooks know that we have created a new item
        ModUtil::callHooks('item', 'create', $item['fid'],
                        array('module' => 'IWforums'));

        return $item['fid'];
    }

    /**
     * Update the forum information in the database
     * @author:   Albert Pï¿œrez Monfort (aperezm@xtec.cat)
     * @param:	forum identity and values
     * @return:	true if success and false otherwise
     */
    public function update($args) {

        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        $items = FormUtil::getPassedValue('items', isset($args['items']) ? $args['items'] : null, 'POST');
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', "::", ACCESS_ADMIN)) {
            throw new Zikula_Exception_Forbidden();
        }

        // Needed argument
        if (!isset($fid) || !is_numeric($fid)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        //Get form information
        $item = ModUtil::apiFunc('IWforums', 'user', 'get',
                        array('fid' => $fid));
        if ($item == false) {
            LogUtil::registerError($this->__("No valid forum"));
        }

        $pntable = DBUtil::getTables();
        $c = $pntable['IWforums_definition_column'];
        $where = "$c[fid] = $fid";

        if (!DBUTil::updateObject($items, 'IWforums_definition', $where)) {
            return LogUtil::registerError($this->__('Error! Update attempt failed.'));
        }

        return true;
    }

    /**
     * Delete the forum and post in database
     * @author:   Albert Pï¿œrez Monfort (aperezm@xtec.cat)
     * @param:	forum identity
     * @return:	true if success and false otherwise
     */
    public function delete($args) {

        $fid = FormUtil::getPassedValue('fid', isset($args['fid']) ? $args['fid'] : null, 'POST');
        // Security check
        if (!SecurityUtil::checkPermission('IWforums::', "::", ACCESS_ADMIN)) {
            throw new Zikula_Exception_Forbidden();
        }

        // Needed argument
        if (!isset($fid) || !is_numeric($fid)) {
            return LogUtil::registerError($this->__('Error! Could not do what you wanted. Please check your input.'));
        }

        //Get form information
        $item = ModUtil::apiFunc('IWforums', 'user', 'get',
                        array('fid' => $fid));
        if ($item == false) {
            LogUtil::registerError($this->__("No valid forum"));
        }

        $pntables = DBUtil::getTables();

        //Delete all the posts and attached files associated to the forum
        $adjunts = ModUtil::apiFunc('IWforums', 'user', 'get_adjunts',
                        array('fid' => $fid));

        $c = $pntables['IWforums_msg_column'];
        $where = "$c[fid]=$fid";
        if (!DBUtil::deleteWhere('IWforums_msg', $where)) {
            return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
        }

        //Delete all the topics associated to the forum
        $c = $pntables['IWforums_temes_column'];
        $where = "$c[fid]=$fid";
        if (!DBUtil::deleteWhere('IWforums_temes', $where)) {
            return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
        }

        //Delete the forum
        if (!DBUtil::deleteObjectByID('IWforums_definition', $fid, 'fid')) {
            return LogUtil::registerError($this->__('Error! Sorry! Deletion attempt failed.'));
        }

        return true;
    }

    public function getlinks($args) {
        $links = array();
        if (SecurityUtil::checkPermission('IWforms::', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => ModUtil::url('IWforums', 'admin', 'newItem'), 'text' => $this->__('Create a new forum'), 'id' => 'iwforums_newItem', 'class' => 'z-icon-es-new');
            $links[] = array('url' => ModUtil::url('IWforums', 'admin', 'main'), 'text' => $this->__('View the created forums'), 'id' => 'iwforums_main', 'class' => 'z-icon-es-view');
            $links[] = array('url' => ModUtil::url('IWforums', 'admin', 'conf'), 'text' => $this->__('Configure the module'), 'id' => 'iwforums_conf', 'class' => 'z-icon-es-config');
        }
        return $links;
    }
    
    /*
     * Set forum subscription mode
     */
    public function setSubscriptionMode($args){
        $fid  = $this->request->getPost()->get('fid', '');
        $mode = $this->request->getPost()->get('mode', '');
        
        if (isset($fid) && $fid != 0 && isset($mode)) {
            $pntable = DBUtil::getTables();
            $c = $pntable['IWforums_definition_column'];
            $where = "$c[fid]=$fid";
            $item = array(
                        'fid' => $fid,
                        'subscriptionMode' => $mode);
            if (!DBUTil::updateObject($item, 'IWforums_definition', $where)) {
                return LogUtil::registerError($this->__('Error! Update attempt failed.'));
            }
        }       
        return $ftid;
    }
}