<?php

require_once('modules/UpgradeWizard/UpgradeRemoval.php');

function post_install()
{
    try {
        require_once('modules/Configurator/Configurator.php');
        require_once("modules/Administration/QuickRepairAndRebuild.php");
        $configurator = new Configurator();
        $si_config = array(
            'GOOGLE' => array(
                'APP_NAME' => 'SI Email Writer',
                'PROJECT_ID' => 'gmail-crm-stackimagine',
                'CLIENT_ID' => '240554306848-mmqvmrritrrhnbvs1qollunlgiosc5he.apps.googleusercontent.com',
                'CLIENT_SECRET' => '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',
                'REDIRECT_URI' => 'https://cdn-plugins.rolustech.com/gsync/redirect.php',
                'SCOPES' =>
                array(
                    0 => 'https://www.googleapis.com/auth/gmail.send',
                    1 => 'https://www.googleapis.com/auth/gmail.readonly',
                ),
            ),
        );
        //Load config
        $configurator->loadConfig();
        $configurator->config = array_merge($configurator->config, $si_config);
        //Save the new setting
        if (!array_key_exists('http_referer', $configurator->config)) {
            $configurator->config['http_referer'] = array();
            $configurator->config['http_referer']['list'] = array();
            $configurator->config['addAjaxBannedModules'] = array();
        }
        if (!in_array("si_Email_Writer", $configurator->config['addAjaxBannedModules'])) {
            $configurator->config['addAjaxBannedModules'][] = 'si_Email_Writer';
        }
        if (!in_array("Leads", $configurator->config['addAjaxBannedModules'])) {
            $configurator->config['addAjaxBannedModules'][] = 'Leads';
        }
        if (!in_array("https://cdn-plugins.stackimagine.com/si_email_writer/redirect.php", $configurator->config['http_referer'])) {
            $configurator->config['http_referer']['list'][] = 'https://cdn-plugins.stackimagine.com/si_email_writer/redirect.php';
        }
        if (!in_array("accounts.google.com", $configurator->config['http_referer'])) {
            $configurator->config['http_referer']['list'][] = 'accounts.google.com';
        }
        $configurator->handleOverride();
        repair_and_rebuild();

        if (createJOB('SIEmailWriter - Prepare Email', 'function::si_prepareFirstEmail', '*/5::*::*::*::*') === true) {
            $GLOBALS['log']->fatal('SIEmailWriter - Prepare First Email job created');
        }
        if (createJOB('SIEmailWriter - Prepare Followup Email', 'function::si_prepareFollowupEmails', '*/5::*::*::*::*') === true) {
            $GLOBALS['log']->fatal('SIEmailWriter - Prepare Followup Emails job created');
        }
        if (createJOB('SIEmailWriter - Mark Reply Received', 'function::si_markReplyReceived', '*/5::*::*::*::*') === true) {
            $GLOBALS['log']->fatal('SIEmailWriter - Mark Reply Received job created');
        }
        if (createJOB('SIEmailWriter - Send First Email', 'function::si_sendFirstEmail', '*::*::*::*::*') === true) {
            $GLOBALS['log']->fatal('SIEmailWriter - Send First Email created');
        }
        if (createJOB('SIEmailWriter - Send Folloup Email', 'function::si_sendFollowupEmail', '*::*::*::*::*') === true) {
            $GLOBALS['log']->fatal('SIEmailWriter - Send Followup Email created');
        }
        addFieldsToLayout();
        redirectToLicense();
        $GLOBALS['log']->fatal("SIEmailWriter installed successfully...");
    } catch (Exception $ex) {
        $GLOBALS['log']->fatal("SIEmailWriter Exception in " . __FILE__ . ":" . __LINE__ . ": " . $ex->getMessage());
    }
    $php_v = (int)PHP_VERSION;
    if ($php_v == 8) {
        replaceVendorFile();
    }
}

function addFieldsToLayout()
{

    require 'custom/include/ModuleInstaller/si_Email_WriterModuleInstaller.php';
    require 'custom/include/ModuleInstaller/si_Email_WriterSearchViewMetaDataParser.php';
    $installer_func = new si_Email_WriterModuleInstaller();
    $search_func = new si_Email_WriterSearchViewMetaDataParser(MB_ADVANCEDSEARCH, "Leads");
    //add gmail id field in users' editview
    $installer_func->removeFieldsFromLayout(['Users' => 'si_gmail_id']);
    $installer_func->addFieldsToLayout(['Users' => 'si_gmail_id']);
    $installer_func->removeFieldsFromLayout(['Accounts' => 'si_linkedin_profile']);
    $installer_func->addFieldsToLayout(['Accounts' => 'si_linkedin_profile']);
    // $installer_func->removeFieldsFromLayout(['Accounts' => 'si_leads_contacted']);
    // $installer_func->addFieldsToLayout(['Accounts' => 'si_leads_contacted']);
    $installer_func->removeFieldsFromLayout(['Leads' => 'si_linkedin_profile']);
    $installer_func->addFieldsToLayout(['Leads' => 'si_linkedin_profile']);
    $installer_func->removeFieldsFromLayout(['Leads' => 'si_company_linkedin_profile']);
    $installer_func->addFieldsToLayout(['Leads' => 'si_company_linkedin_profile']);
    $installer_func->removeFieldsFromLayout(['Leads' => 'si_company_description']);
    $installer_func->addFieldsToLayout(['Leads' => 'si_company_description']);
    $installer_func->removeFieldsFromLayout(['Leads' => 'si_email_body']);
    $installer_func->addFieldsToLayout(['Leads' => 'si_email_body']);
    $installer_func->removeFieldsFromLayout(['Leads' => 'si_email_subject']);
    $installer_func->addFieldsToLayout(['Leads' => 'si_email_subject']);
    $installer_func->removeFieldsFromLayout(['Leads' => 'si_email_status']);
    $installer_func->addFieldsToLayout(['Leads' => 'si_email_status']);
    $installer_func->removeFieldsFromLayout(['Leads' => 'si_email_verified']);
    $installer_func->addFieldsToLayout(['Leads' => 'si_email_verified']);
    $installer_func->removeFieldsFromLayout(['Leads' => 'si_email_writer_leads_1_name']);
    $installer_func->addFieldsToLayout(['Leads' => 'si_email_writer_leads_1_name']);
    $installer_func->removeScriptFromLayout(['Leads' => 'custom/modules/Leads/js/si_Email_Writer.js']);
    $installer_func->addScriptToLayout(['Leads' => 'custom/modules/Leads/js/si_Email_Writer.js']);
    $search_func->removeFieldFromSearch('si_email_status', MB_ADVANCEDSEARCH);
    $search_func->addFieldToSearch([
        'label' => 'LBL_SI_EMAIL_STATUS',
        'type' => 'enum',
        'default' => true,
        'studio' => 'visible',
        'width' => '10%',
        'name' => 'si_email_status',
    ], MB_ADVANCEDSEARCH);
    $search_func->removeFieldFromSearch('si_email_verified', MB_ADVANCEDSEARCH);
    $search_func->addFieldToSearch([
        'label' => 'LBL_SI_EMAIL_VERIFIED',
        'type' => 'enum',
        'default' => true,
        'studio' => 'visible',
        'width' => '10%',
        'name' => 'si_email_verified',
    ], MB_ADVANCEDSEARCH);
}
/**
 * This function replaces contents of File 'vendor/zf1/zend-xml/library/Zend/Xml/Security.php' with file 'custom/include/vendor_replace/Security.php'.
 * In file 'vendor/zf1/zend-xml/library/Zend/Xml/Security.php' on line 172 version_compare function is called with gte operator, php8 does not support
 * gte operator in version_compare function. That function will only be executed when php version is 8.
 */
function replaceVendorFile()
{
    if (file_exists(realpath('custom/include/vendor_replace/Security.php'))) {
        require_once('include/upload_file.php');
        $uploadFile = new UploadFile();
        $uploadFile->temp_file_location = 'custom/include/vendor_replace/Security.php';
        $file_contents = $uploadFile->get_file_contents();
        file_put_contents('vendor/zf1/zend-xml/library/Zend/Xml/Security.php', $file_contents);

        unlink(realpath('custom/include/vendor_replace/Security.php'));
        rmdir(realpath('custom/include/vendor_replace'));
    }
}

function createJOB($name, $job, $job_interval, $fields = [])
{
    $scheduler = BeanFactory::getBean('Schedulers');
    $scheduler->retrieve_by_string_fields(array('job' => $job, 'deleted' => '0'));
    //If there is no job by that name, create a new one
    //and set its job interval to the default interval
    if (empty($scheduler->id)) {
        $scheduler->job_interval = $job_interval;
        $scheduler->name = $name;
        $scheduler->job = $job;
        $scheduler->date_time_start = '2005-01-01 00:00:00';
        $scheduler->status = 'Inactive';
        $scheduler->catch_up = '1';
    }
    foreach ($fields as $field => $value) {
        $scheduler->$field = $value;
    }
    //Undelete the job
    $scheduler->mark_undeleted($scheduler->id);
    if (method_exists($scheduler, 'save')) {
        $scheduler->save();
        return true;
    } else
        $GLOBALS['log']->fatal("SIEmailWriter Exception: Failed to save " . $scheduler->name . __FILE__ . ":" . __LINE__);
    return false;
}

function repair_and_rebuild()
{
    global $mod_strings;
    // force developer mode for full vardef/dictionary refresh
    $backupDevMode = inDeveloperMode();
    $sugar_config['developerMode'] = true;
    // setup LBL_ALL_MODULES for full QRR
    $backupModStrings = isset($mod_strings) ? $mod_strings : null;
    require 'modules/Administration/language/en_us.lang.php';
    // perform full QRR - execute query mode
    require_once 'modules/Administration/QuickRepairAndRebuild.php';
    $repairer = new RepairAndClear();
    $repairer->repairAndClearAll(array('clearAll'), array($mod_strings['LBL_ALL_MODULES']), true, false);
    // reset altered flags to it's original state
    $sugar_config['developerMode'] = $backupDevMode;
    $mod_strings = $backupModStrings;
}

function redirectToLicense()
{
    global $db;
    if (!$db->tableExists('so_users')) {

        $fieldDefs = array(
            'id' => array(
                'name' => 'id',
                'vname' => 'LBL_ID',
                'type' => 'id',
                'required' => true,
                'reportable' => true,
            ),
            'deleted' => array(
                'name' => 'deleted',
                'vname' => 'LBL_DELETED',
                'type' => 'bool',
                'default' => '0',
                'reportable' => false,
                'comment' => 'Record deletion indicator',
            ),
            'shortname' => array(
                'name' => 'shortname',
                'vname' => 'LBL_SHORTNAME',
                'type' => 'varchar',
                'len' => 255,
            ),
            'user_id' => array(
                'name' => 'user_id',
                'rname' => 'user_name',
                'module' => 'Users',
                'id_name' => 'user_id',
                'vname' => 'LBL_USER_ID',
                'type' => 'relate',
                'isnull' => 'false',
                'dbType' => 'id',
                'reportable' => true,
                'massupdate' => false,
            ),
        );

        $indices = array(
            'id' => array(
                'name' => 'so_userspk',
                'type' => 'primary',
                'fields' => array(
                    0 => 'id',
                ),
            ),
            'shortname' => array(
                'name' => 'shortname',
                'type' => 'index',
                'fields' => array(
                    0 => 'shortname',
                ),
            ),
        );
        $db->createTableParams('so_users', $fieldDefs, $indices);
    }

    global $sugar_version;
    if (preg_match("/^6.*/", $sugar_version)) {
        echo "
            <script>
            document.location = 'index.php?module=si_Email_Writer&action=license';
            </script>";
    } else {
        echo "
            <script>
            var app = window.parent.SUGAR.App;
            window.parent.SUGAR.App.sync({callback: function(){
                app.router.navigate('#bwc/index.php?module=si_Email_Writer&action=license', {trigger:true});
            }});
            </script>";
    }
}
