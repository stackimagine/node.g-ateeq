<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once 'modules/ModuleBuilder/parsers/views/SearchViewMetaDataParser.php';

class si_Email_WriterSearchViewMetaDataParser extends SearchViewMetaDataParser
{
    public function addFieldToSearch($fieldDef, $type = MB_ADVANCEDSEARCH)
    {
        if (!isset($fieldDef['name'])) {
            throw new Exception("Invalid field definition. 'name' is a required property.");
        }

        // Add the field to the advanced search layout
        $fieldKey = strtolower($fieldDef['name']);
        $this->_saved['layout'][self::$variableMap[$type]][$fieldKey] = $fieldDef;

        // Ensure the field is included in the search view
        $this->_viewdefs[$fieldKey] = $fieldDef;

        // Save the modified layout
        $this->handleSave(false);
    }

    public function removeFieldFromSearch($fieldName, $type = MB_ADVANCEDSEARCH)
    {
        // Ensure the field name is provided
        if (empty($fieldName)) {
            throw new Exception("Invalid field name. Field name is required.");
        }

        // Convert field name to lowercase
        $fieldKey = strtolower($fieldName);

        // Remove the field from the advanced search layout
        if (isset($this->_saved['layout'][self::$variableMap[$type]][$fieldKey])) {
            unset($this->_saved['layout'][self::$variableMap[$type]][$fieldKey]);
        }

        // Remove the field from the search view
        if (isset($this->_viewdefs[$fieldKey])) {
            unset($this->_viewdefs[$fieldKey]);
        }

        // Save the modified layout
        $this->handleSave(false);
    }
}
