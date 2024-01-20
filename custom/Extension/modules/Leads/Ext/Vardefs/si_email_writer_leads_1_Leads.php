<?php
// created: 2024-01-07 18:52:11
$dictionary["Lead"]["fields"]["si_email_writer_leads_1"] = array(
  'name' => 'si_email_writer_leads_1',
  'type' => 'link',
  'relationship' => 'si_email_writer_leads_1',
  'source' => 'non-db',
  'module' => 'si_Email_Writer',
  'bean_name' => 'si_Email_Writer',
  'vname' => 'LBL_SI_EMAIL_WRITER_LEADS_1_FROM_SI_EMAIL_WRITER_TITLE',
  'id_name' => 'si_email_writer_leads_1si_email_writer_ida',
);
$dictionary["Lead"]["fields"]["si_email_writer_leads_1_name"] = array(
  'name' => 'si_email_writer_leads_1_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_SI_EMAIL_WRITER_LEADS_1_FROM_SI_EMAIL_WRITER_TITLE',
  'save' => true,
  'id_name' => 'si_email_writer_leads_1si_email_writer_ida',
  'link' => 'si_email_writer_leads_1',
  'table' => 'si_Email_Writer',
  'module' => 'si_Email_Writer',
  'rname' => 'name',
);
$dictionary["Lead"]["fields"]["si_email_writer_leads_1si_email_writer_ida"] = array(
  'name' => 'si_email_writer_leads_1si_email_writer_ida',
  'type' => 'link',
  'relationship' => 'si_email_writer_leads_1',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_SI_EMAIL_WRITER_LEADS_1_FROM_LEADS_TITLE',
);
