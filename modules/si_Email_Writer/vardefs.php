<?php

/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2018 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

$dictionary['si_Email_Writer'] = [
  'table' => 'si_Email_Writer',
  'audited' => true,
  'inline_edit' => true,
  'duplicate_merge' => true,
  'fields' => [
    'large_language_model' => [
      'required' => false,
      'name' => 'large_language_model',
      'vname' => 'LBL_LARGE_LANGUAGE_MODEL',
      'type' => 'enum',
      'massupdate' => 0,
      'default' => 'gpt-3.5-turbo',
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'inline_edit' => true,
      'reportable' => true,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'len' => 100,
      'size' => '20',
      'options' => 'large_language_model_list',
      'studio' => 'visible',
      'dependency' => false,
    ],
    'api_key' => [
      'required' => false,
      'name' => 'api_key',
      'vname' => 'LBL_LLM_API_KEY',
      'type' => 'varchar',
      'massupdate' => 0,
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'inline_edit' => true,
      'reportable' => true,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'len' => '255',
      'size' => '20',
    ],
    'followup_prompt' => [
      'required' => false,
      'name' => 'followup_prompt',
      'vname' => 'LBL_FOLLOWUP_PROMPT',
      'type' => 'text',
      'massupdate' => 0,
      'no_default' => false,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => false,
      'inline_edit' => true,
      'reportable' => true,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'size' => '20',
      'studio' => 'visible',
      'rows' => '4',
      'cols' => '20',
    ],
    'timezone' => [
      'name' => 'timezone',
      'vname' => 'LBL_TIMEZONE',
      'type' => 'enum',
      'options' => 'timezone_dom',
      'len' => 50,
      'comment' => 'Timezone of most of your target leads',
      'merge_filter' => 'enabled',
      'default' => 'America/Los_Angeles',
      'studio' => 'visible',
    ],
    'require_approval' => [
      'name' => 'require_approval',
      'vname' => 'LBL_REQUIRE_APPROVAL',
      'type' => 'enum',
      'options' => 'require_approval_dom',
      'len' => 50,
      'comment' => 'Require approval on each email before sending',
      'merge_filter' => 'enabled',
      'default' => 'Yes',
      'studio' => 'visible',
    ],
    'followup_require_approval' => [
      'name' => 'followup_require_approval',
      'vname' => 'LBL_FOLLOWUP_REQUIRE_APPROVAL',
      'type' => 'enum',
      'options' => 'require_approval_dom',
      'len' => 50,
      'comment' => 'Require approval on each followup email before sending',
      'merge_filter' => 'enabled',
      'default' => 'Yes',
      'studio' => 'visible',
    ],
    'campaign_days' => [
      'name' => 'campaign_days',
      'vname' => 'LBL_CAMPAIGN_DAYS',
      'type' => 'multienum',
      'options' => 'campaign_days_list',
      'len' => 50,
      'comment' => 'On which days to run the campaign',
      'merge_filter' => 'enabled',
      'default' => '^Monday^,^Tuesday^,^Wednesday^,^Thursday^,^Friday^',
      'studio' => 'visible',
      'isMultiSelect' => true,
    ],
    'email_frequency' => [
      'required' => false,
      'name' => 'email_frequency',
      'vname' => 'LBL_EMAIL_FREQUENCY',
      'type' => 'int',
      'default' => 1,
      'no_default' => false,
      'comments' => 'Emails per 10 minutes',
      'help' => 'Emails per 10 minutes',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'audited' => false,
      'reportable' => true,
      'unified_search' => false,
      'merge_filter' => 'disabled',
      'len' => '255',
      'size' => '20',
    ],
    'start_time' => [
      'name' => 'start_time',
      'vname' => 'LBL_START_TIME',
      'default' => '07:00:00',
      'type' => 'time',
      'audited' => true,
      'required' => false,
      'reportable' => false,
    ],
    'end_time' => [
      'name' => 'end_time',
      'vname' => 'LBL_END_TIME',
      'default' => '19:00:00',
      'type' => 'time',
      'audited' => true,
      'required' => false,
      'reportable' => false,
    ],
    'outboundemailaccounts_si_email_writer_1' => [
      'name' => 'outboundemailaccounts_si_email_writer_1',
      'type' => 'link',
      'relationship' => 'outboundemailaccounts_si_email_writer_1',
      'source' => 'non-db',
      'module' => 'OutboundEmailAccounts',
      'bean_name' => 'OutboundEmailAccounts',
      'vname' => 'LBL_OUTBOUNDEMAILACCOUNTS_SI_EMAIL_WRITER_1_FROM_OUTBOUNDEMAILACCOUNTS_TITLE',
      'id_name' => 'outboundemailaccounts_si_email_writer_1outboundemailaccounts_ida',
    ],
    'outboundemailaccounts_si_email_writer_1_name' => [
      'name' => 'outboundemailaccounts_si_email_writer_1_name',
      'type' => 'relate',
      'source' => 'non-db',
      'vname' => 'LBL_OUTBOUNDEMAILACCOUNTS_SI_EMAIL_WRITER_1_FROM_OUTBOUNDEMAILACCOUNTS_TITLE',
      'save' => true,
      'id_name' => 'outboundemailaccounts_si_email_writer_1outboundemailaccounts_ida',
      'link' => 'outboundemailaccounts_si_email_writer_1',
      'table' => 'outbound_email',
      'module' => 'OutboundEmailAccounts',
      'rname' => 'name',
    ],
    'outboundemailaccounts_si_email_writer_1outboundemailaccounts_ida' => [
      'name' => 'outboundemailaccounts_si_email_writer_1outboundemailaccounts_ida',
      'type' => 'link',
      'relationship' => 'outboundemailaccounts_si_email_writer_1',
      'source' => 'non-db',
      'reportable' => false,
      'side' => 'right',
      'vname' => 'LBL_OUTBOUNDEMAILACCOUNTS_SI_EMAIL_WRITER_1_FROM_SI_EMAIL_WRITER_TITLE',
    ],
    "si_email_writer_leads_1" => [
      'name' => 'si_email_writer_leads_1',
      'type' => 'link',
      'relationship' => 'si_email_writer_leads_1',
      'source' => 'non-db',
      'module' => 'Leads',
      'bean_name' => 'Lead',
      'side' => 'right',
      'vname' => 'LBL_SI_EMAIL_WRITER_LEADS_1_FROM_LEADS_TITLE',
    ]
  ],
  'relationships' => [],
  'optimistic_locking' => true,
  'unified_search' => true,
];

if (!class_exists('VardefManager')) {
  require_once('include/SugarObjects/VardefManager.php');
}
VardefManager::createVardef('si_Email_Writer', 'si_Email_Writer', array('basic', 'assignable', 'security_groups'));
