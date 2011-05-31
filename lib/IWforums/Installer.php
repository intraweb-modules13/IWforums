<?php

class IWforums_Installer extends Zikula_AbstractInstaller {

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
        if (!ModUtil::func('IWmain', 'admin', 'checkVersion', array('version' => $versionNeeded))) {
            return false;
        }

        // Create module tables
        if (!DBUtil::createTable('IWforums_definition'))
            return false;
        if (!DBUtil::createTable('IWforums_temes'))
            return false;
        if (!DBUtil::createTable('IWforums_msg'))
            return false;

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

        //Create module vars
        $this->setVar('urladjunts', 'forums')
                ->setVar('avatarsVisible', '1');

        //Initialation successfull
        return true;
    }

    /**
     * Delete the IWforums module
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function uninstall() {
        // Delete module table
        DBUtil::dropTable('IWforums_definition');
        DBUtil::dropTable('IWforums_temes');
        DBUtil::dropTable('IWforums_msg');

        //Delete module vars
        $this->delVar('urladjunts')
                ->delVar('avatarsVisible');
        //success
        return true;
    }

    /**
     * Update the IWforums module
     * @author Albert Pérez Monfort (aperezm@xtec.cat)
     * @author Jaume Fernàndez Valiente (jfern343@xtec.cat)
     * @return bool true if successful, false otherwise
     */
    public function upgrade($oldversion) {

        $prefix = $GLOBALS['ZConfig']['System']['prefix'];

        //Rename tables
        if (!DBUtil::renameTable('iw_forums_def', 'IWforums_definition'))
            return false;

        if (!DBUtil::renameTable('iw_forums_msg', 'IWforums_msg'))
            return false;

        if (!DBUtil::renameTable('iw_forums_temes', 'IWforums_temes'))
            return false;
        
        // Update module_vars table

        //Update the name (keeps old var value)
        $c = "UPDATE {$prefix}_module_vars SET z_modname = 'IWforums' WHERE z_bkey = 'iw_forums'";
        if (!DBUtil::executeSQL($c)) {
            return false;
        }

        //Array de noms
        $oldVarsNames = DBUtil::selectFieldArray("module_vars", 'name', "`z_modname` = 'iw_forums'", '', false, '');

        $newVarsNames = Array('urladjunts', 'avatarsVisible');

        $newVars = Array('urladjunts' => 'forums',
            'avatarsVisible' => '1');

        // Delete unneeded vars
        $del = array_diff($oldVarsNames, $newVarsNames);
        foreach ($del as $i) {
            $this->delVar($i);
        }

        // Add new vars
        $add = array_diff($newVarsNames, $oldVarsNames);
        foreach ($add as $i) {
            $this->setVar($i, $newVars[$i]);
        }    
        
        return true;
    }

}