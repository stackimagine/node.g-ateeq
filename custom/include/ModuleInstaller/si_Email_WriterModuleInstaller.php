<?php
require_once 'ModuleInstall/ModuleScanner.php';
require_once 'ModuleInstall/ModuleInstaller.php';
require_once 'custom/include/ModuleInstaller/si_Email_WriterGridLayoutMetaDataParser.php';

define('DISABLED_PATH', 'Disabled');

class si_Email_WriterModuleInstaller extends ModuleInstaller
{

    function addFieldsToLayout($layoutAdditions)
    {

        $invalidModules = array(
            'emails',
            'kbdocuments'
        );
        foreach ($layoutAdditions as $deployedModuleName => $fieldName) {

            if (!in_array(strtolower($deployedModuleName), $invalidModules)) {

                foreach (array(MB_EDITVIEW, MB_DETAILVIEW) as $view) {

                    $GLOBALS['log']->debug(get_class($this) . ": adding $fieldName to $view layout for module $deployedModuleName");
                    $parser = new si_Email_WriterGridLayoutMetaDataParser($view, $deployedModuleName);
                    $parser->addField(array('name' => $fieldName));
                    $parser->handleSave(false);
                }
            }
        }
    }
    function addScriptToLayout($layoutAdditions)
    {

        $invalidModules = array(
            'emails',
            'kbdocuments'
        );
        foreach ($layoutAdditions as $deployedModuleName => $script) {

            if (!in_array(strtolower($deployedModuleName), $invalidModules)) {

                foreach (array(MB_EDITVIEW, MB_DETAILVIEW) as $view) {

                    $GLOBALS['log']->debug(get_class($this) . ": adding $script to $view layout for module $deployedModuleName");
                    $parser = new si_Email_WriterGridLayoutMetaDataParser($view, $deployedModuleName);
                    $parser->addScript(['file' => $script]);
                    $parser->handleSave(false);
                }
            }
        }
    }
    function removeScriptFromLayout($layoutAdditions)
    {

        $invalidModules = array(
            'emails',
            'kbdocuments'
        );
        foreach ($layoutAdditions as $deployedModuleName => $script) {

            if (!in_array(strtolower($deployedModuleName), $invalidModules)) {

                foreach (array(MB_EDITVIEW, MB_DETAILVIEW) as $view) {

                    $GLOBALS['log']->debug(get_class($this) . ": removing $script from $view layout for module $deployedModuleName");
                    $parser = new si_Email_WriterGridLayoutMetaDataParser($view, $deployedModuleName);
                    $parser->removeScript($script);
                    $parser->handleSave(false);
                }
            }
        }
    }
}
