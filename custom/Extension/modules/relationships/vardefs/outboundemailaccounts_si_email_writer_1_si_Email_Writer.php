<?php
// created: 2024-01-07 18:51:19
$dictionary["si_Email_Writer"]["fields"]["outboundemailaccounts_si_email_writer_1"] = array(
  'name' => 'outboundemailaccounts_si_email_writer_1',
  'type' => 'link',
  'relationship' => 'outboundemailaccounts_si_email_writer_1',
  'source' => 'non-db',
  'module' => 'OutboundEmailAccounts',
  'bean_name' => 'OutboundEmailAccounts',
  'vname' => 'LBL_OUTBOUNDEMAILACCOUNTS_SI_EMAIL_WRITER_1_FROM_OUTBOUNDEMAILACCOUNTS_TITLE',
  'id_name' => 'outboundemailaccounts_si_email_writer_1outboundemailaccounts_ida',
);
$dictionary["si_Email_Writer"]["fields"]["outboundemailaccounts_si_email_writer_1_name"] = array(
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
);
$dictionary["si_Email_Writer"]["fields"]["outboundemailaccounts_si_email_writer_1outboundemailaccounts_ida"] = array(
  'name' => 'outboundemailaccounts_si_email_writer_1outboundemailaccounts_ida',
  'type' => 'link',
  'relationship' => 'outboundemailaccounts_si_email_writer_1',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_OUTBOUNDEMAILACCOUNTS_SI_EMAIL_WRITER_1_FROM_SI_EMAIL_WRITER_TITLE',
);
