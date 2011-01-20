<?php
class IWforums_Installer extends Zikula_Installer {
    /**
     * Initialise the IWforums module creating module tables and module vars
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function Install() {
        // Checks if module IWmain is installed. If not returns error
        $modid = ModUtil::getIdFromName('IWmain');
        $modinfo = ModUtil::getInfo($modid);

        if ($modinfo['state'] != 3) {
            return LogUtil::registerError($this->__('Module IWmain is required. You have to install the IWmain module previously to install it.'));
        }

        // Check if the version needed is correct
        $versionNeeded = '3.0.0';
        if (!ModUtil::func('IWmain', 'admin', 'checkVersion',
                            array('version' => $versionNeeded))) {
            return false;
        }

        // Create module tables
        if (!DBUtil::createTable('IWforums_definition')) return false;
        if (!DBUtil::createTable('IWforums_temes')) return false;
        if (!DBUtil::createTable('IWforums_msg')) return false;

        //Create indexes
        $tables = DBUtil::getTables();
        $c = $tables['IWforums_msg_column'];
        if (!DBUtil::createIndex($c['idparent'], 'IWforums_msg', 'idparent')) return false;
        if (!DBUtil::createIndex($c['ftid'], 'IWforums_msg', 'ftid')) return false;
        if (!DBUtil::createIndex($c['fid'], 'IWforums_msg', 'fid')) return false;

        $c = $tables['IWforums_temes_column'];
        if (!DBUtil::createIndex($c['fid'], 'IWforums_temes', 'fid')) return false;

        //Create module vars
        ModUtil::setVar('IWforums', 'urladjunts', 'forums');
        ModUtil::setVar('IWforums', 'avatarsVisible', '1');

        //Initialation successfull
        return true;
    }

    /**
     * Delete the IWforums module
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function  uninstall() {
        // Delete module table
        DBUtil::dropTable('IWforums_definition');
        DBUtil::dropTable('IWforums_temes');
        DBUtil::dropTable('IWforums_msg');

        //Delete module vars
        ModUtil::delVar('IWforums', 'urladjunts');
        ModUtil::delVar('IWforums', 'avatarsVisible');

        //success
        return true;
    }

    /**
     * Update the IWforums module
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function upgrade($oldversion) {

        if (!DBUtil::changeTable('IWforums_temes'))
            return false;
        if (!DBUtil::changeTable('IWforums_msg'))
            return false;

        if ($oldversion < 1.1) {
            $tables = DBUtil::getTables();
            $c = $tables['IWforums_temes_column'];

            //get all the forums defined
            $items = DBUtil::selectObjectArray('IWforums_definition', '', '', '-1', '-1', 'fid');

            //for each forum get the topics
            foreach ($items as $item) {
                $where = "$c[fid]=$item[fid]";
                $items1 = DBUtil::selectObjectArray('IWforums_temes', $where, '', '-1', '-1', 'ftid');
                foreach ($items1 as $item1) {
                    //get last message in topic
                    $c = $tables['IWforums_msg_column'];

                    $where = "$c[ftid] = $item1[ftid]";

                    $orderby = "$c[data] desc";

                    // get the objects from the db
                    $items2 = DBUtil::selectObjectArray('IWforums_msg', $where, $orderby, '0', '1', 'ftid');

                    //update topic last time and user information
                    $c = $tables['IWforums_temes_column'];

                    $where = "$c[ftid]=$item1[ftid]";

                    $itemsUpdate = array('last_time' => $items2[$item1[ftid]]['data'],
                        'last_user' => $items2[$item1[ftid]]['usuari']);

                    if (!DBUTil::updateObject($itemsUpdate, 'IWforums_temes', $where)) {
                        return LogUtil::registerError($this->__('Error! Update attempt failed.'));
                    }
                }
            }
        }

        if ($oldversion < 1.2) {
            //Create indexes
            $tables = DBUtil::getTables();
            $c = $tables['IWforums_msg_column'];
            if (!DBUtil::createIndex($c['idparent'], 'IWforums_msg', 'idparent'))
                return false;
            if (!DBUtil::createIndex($c['ftid'], 'IWforums_msg', 'ftid'))
                return false;
            if (!DBUtil::createIndex($c['fid'], 'IWforums_msg', 'fid'))
                return false;

            $c = $tables['IWforums_temes_column'];
            if (!DBUtil::createIndex($c['fid'], 'IWforums_temes', 'fid'))
                return false;
        }

        //success
        return true;
    }
}