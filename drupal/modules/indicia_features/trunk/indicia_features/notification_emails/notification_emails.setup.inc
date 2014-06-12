<?php

/**
 * Return an associative array defining the list of required profile fields.
 */
function _notification_emails_profile_fields() {
  $fields = array();
  $opts = array("None\nHourly\nDaily\nWeekly");
  $explain = 'Frequency you will receive emails containing details of ';
  $fields['profile_comment_notification_email_frequency'] = array(
      'title' => 'Comment notification email frequency',
      'explanation' => $explain . 'comments on your records or records that you\'ve commented on',
      'category' => 'Notification emails',
      'type' => 'selection',
      'weight' => 1,
      'required' => 0,
      'register' => 0,
      'visibility' => 2,
      'autocomplete' => 0,
      'options' => $opts,
      'page' => ''
  );
  $fields['profile_verification_notification_email_frequency'] = array(
      'title' => 'Verification notification email frequency',
      'explanation' => $explain . 'verification of your records',
      'category' => 'Notification emails',
      'type' => 'selection',
      'weight' => 2,
      'required' => 0,
      'register' => 0,
      'visibility' => 2,
      'autocomplete' => 0,
      'options' => $opts,
      'page' => ''
  );
  $fields['profile_species_alert_notification_email_frequency'] = array(
      'title' => 'Species alerts notification email frequency',
      'explanation' => $explain . 'any species record alerts you have registered to receive',
      'category' => 'Notification emails',
      'type' => 'selection',
      'weight' => 3,
      'required' => 0,
      'register' => 0,
      'visibility' => 2,
      'autocomplete' => 0,
      'options' => $opts,
      'page' => ''
  );
  $fields['profile_verifier_task_notification_email_frequency'] = array(
      'title' => 'Verifier task notification email frequency',
      'explanation' => $explain . 'records you need to verify (only applicable if you are a verifier)',
      'category' => 'Notification emails',
      'type' => 'selection',
      'weight' => 4,
      'required' => 0,
      'register' => 0,
      'visibility' => 2,
      'autocomplete' => 0,
      'options' => $opts,
      'page' => ''
  );
  $fields['profile_milestone_notification_email_frequency'] = array(
      'title' => 'Milestone notification email frequency',
      'explanation' => $explain . 'awards and milestones you\'ve reached',
      'category' => 'Notification emails',
      'type' => 'selection',
      'weight' => 6,
      'required' => 0,
      'register' => 0,
      'visibility' => 2,
      'autocomplete' => 0,
      'options' => $opts,
      'page' => ''
  );
  return $fields;
}