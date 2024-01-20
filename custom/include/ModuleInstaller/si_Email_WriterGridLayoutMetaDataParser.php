<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require 'modules/ModuleBuilder/parsers/views/GridLayoutMetaDataParser.php';

class si_Email_WriterGridLayoutMetaDataParser extends GridLayoutMetaDataParser
{
    function addField($def, $panelID = FALSE)
    {
        // This function was updated in SuiteCRM 7.11 which resulted in failure to add fields (gmail id, calendar type) in the modules through post install.
        // This is the old function which works for us
        $backtrace_arr = debug_backtrace();
        if (count($this->_viewdefs['panels']) == 0) {
            $GLOBALS['log']->error(get_class($this) . "->addField(): _viewdefs empty for module {$this->_moduleName} and view {$this->_view}");
        }
        // if a panelID was not provided, use the first available panel in the list
        if (!$panelID) {
            $panels = array_keys($this->_viewdefs['panels']);
            $panelID = $panels[0];
        }
        if (isset($this->_viewdefs['panels'][$panelID])) {
            $panel      = $this->_viewdefs['panels'][$panelID];
            $lastrow    = count($panel) - 1; // index starts at 0
            $maxColumns = $this->getMaxColumns();
            $lastRowDef = $this->_viewdefs['panels'][$panelID][$lastrow];
            for ($column = 0; $column < $maxColumns; $column++) {
                if (!isset($lastRowDef[$column]) || (is_array($lastRowDef[$column]) && $lastRowDef[$column]['name'] == '(empty)') || (is_string($lastRowDef[$column]) && $lastRowDef[$column] == '(empty)')) {
                    break;
                }
            }
            // if we're on the last column of the last row, start a new row
            if ($column >= $maxColumns) {
                $lastrow++;
                $this->_viewdefs['panels'][$panelID][$lastrow] = array();
                $column                                        = 0;
            }
            $this->_viewdefs['panels'][$panelID][$lastrow][$column] = $def['name'];
            // now update the fielddefs
            if (isset($this->_fielddefs[$def['name']])) {
                $this->_fielddefs[$def['name']] = array_merge($this->_fielddefs[$def['name']], $def);
            } else {
                $this->_fielddefs[$def['name']] = $def;
            }
        }
        return true;
    }

    public function addScript($fileToInclude)
    {
        if (!isset($this->_viewdefs['templateMeta']['includes'])) {
            $this->_viewdefs['templateMeta']['includes'] = [];
        }
        $this->_viewdefs['templateMeta']['includes'][] = $fileToInclude;
        return true;
    }

    public function removeScript($fileToRemove)
    {
        if (!isset($this->_viewdefs['templateMeta']['includes'])) {
            return false;
        }

        foreach ($this->_viewdefs['templateMeta']['includes'] as $key => $include) {
            if (isset($include['file']) && $include['file'] === $fileToRemove) {
                unset($this->_viewdefs['templateMeta']['includes'][$key]);
                return true;
            }
        }
        return false;
    }
}
