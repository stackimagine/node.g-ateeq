<?php


require_once 'modules/ModuleBuilder/parsers/views/SearchViewMetaDataParser.php';
require_once 'modules/ModuleBuilder/parsers/views/GridLayoutMetaDataParser.php';
require_once 'ModuleInstall/ModuleScanner.php';
require_once 'ModuleInstall/ModuleInstaller.php';

class si_removeSearchViewMetaDataParser extends SearchViewMetaDataParser
{
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

class si_removeGridLayoutMetaDataParser extends GridLayoutMetaDataParser
{
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

class si_removeModuleInstaller extends ModuleInstaller
{

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
					$parser = new si_removeGridLayoutMetaDataParser($view, $deployedModuleName);
					$parser->removeScript($script);
					$parser->handleSave(false);
				}
			}
		}
	}
}

function pre_uninstall()
{
	try {
		deleteSchedulerJobs();
		removeFieldsFromLayout();
		$GLOBALS['log']->fatal("SIEmailWriter pre uninstall script successful...");
	} catch (Exception $ex) {
		$GLOBALS['log']->fatal("Exception occurred in pre_uninstall of SI Email Writer: " . $ex->getMessage());
	}
}

function removeFieldsFromLayout()
{
	$installer_func = new si_removeModuleInstaller();
	$installer_func->removeFieldsFromLayout(['Accounts' => 'si_linkedin_profile']);
	$installer_func->removeFieldsFromLayout(['Accounts' => 'si_leads_contacted']);
	$installer_func->removeFieldsFromLayout(['Leads' => 'si_linkedin_profile']);
	$installer_func->removeFieldsFromLayout(['Leads' => 'si_email_body']);
	$installer_func->removeFieldsFromLayout(['Leads' => 'si_email_subject']);
	$installer_func->removeFieldsFromLayout(['Leads' => 'si_email_verified']);
	$installer_func->removeFieldsFromLayout(['Leads' => 'si_email_writer_leads_1_name']);
	$installer_func->removeFieldsFromLayout(['Leads' => 'si_company_linkedin_profile']);
	$installer_func->removeFieldsFromLayout(['Leads' => 'si_company_description']);


	$installer_func->removeScriptFromLayout(['Leads' => 'custom/modules/Leads/js/si_Email_Writer.js']);

	$search_func = new si_removeSearchViewMetaDataParser(MB_ADVANCEDSEARCH, "Leads");
	$search_func->removeFieldFromSearch('si_email_status', MB_ADVANCEDSEARCH);
	$search_func->removeFieldFromSearch('si_email_verified', MB_ADVANCEDSEARCH);
}

function deleteSchedulerJobs()
{
	$job_names = array('SIEmailWriter - Prepare Email', 'SIEmailWriter - Send Emails', 'SIEmailWriter - Sync Replies');
	foreach ($job_names as $job_name) {
		$scheduler = BeanFactory::getBean('Schedulers');
		$scheduler->retrieve_by_string_fields(array('name' => $job_name));
		if (!empty($scheduler->id)) {
			$scheduler->mark_deleted($scheduler->id);
			if (method_exists($scheduler, 'save')) {
				$scheduler->save();
			} else
				$GLOBALS['log']->fatal("SI Exception: Failed to save " . $scheduler->name . __FILE__ . ":" . __LINE__);
		}
	}
	return true;
}
