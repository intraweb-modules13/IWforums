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
        //success
        return true;
    }
}