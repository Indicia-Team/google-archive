<?php
/**
 * Indicia, the OPAL Online Recording Toolkit.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 *
 * @package	Verification Check
 * @subpackage Plugins
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL
 * @link 	http://code.google.com/p/indicia/
 */

// @todo Update to use occdelta
/**
 * Hook into the task scheduler. Runs a query to find all comments and verification status updates that need
 * to be notified back to the recorder of a record. 
 */
function notify_verifications_and_comments_scheduled_task($last_run_date) { 
  if (!$last_run_date)
    // first run, so get all records changed in last day. Query will automatically gradually pick up the rest.
    $last_run_date=date('Y-m-d', time()-60*60*24*50);  
  try {
    $db = new Database();
    $notifications = postgreSQL::selectVerificationAndCommentNotifications($last_run_date, $db);
    foreach ($notifications as $notification) {
      $vd = array($notification->date_start, $notification->date_end, $notification->date_type);
      $date = vague_date::vague_date_to_string($vd);
      if (empty($notification->comment)) {
        switch ($notification->record_status) {
          case 'V': 
            $action='verified'; 
            break;
          case 'R': 
            $action='rejected'; 
            break;
          case 'D': 
            $action='marked dubious'; 
            break;
          case 'S': 
            $action='emailed for checking'; 
            break;
        }
        $comment = 'The record of '.$notification->taxon.' at '.$notification->public_entered_sref." on $date was $action.";
      } else {
        if ($notification->auto_generated==='t' && substr($notification->generated_by, 0, 12)==='data_cleaner' && $notification->record_owner==='t') {          
          $comment = 'An automated check using the <a target="_blank" href="http://www.nbn.org.uk/Tools-Resources/Recording-Resources/NBN-Record-Cleaner.aspx" target="_blank">'.
              'NBN Record Cleaner</a> rules has highlighted your record of '.$notification->taxon.' at '.$notification->public_entered_sref.' on '.$date;
          $comment .= ($notification->generated_by==='data_cleaner_identification_difficulty') 
            ? ' as being of a species for which identification is not always trivial. <br/><em>'
            : '. The following information was given: <br/><em>';
        }
        elseif ($notification->verified_on>$last_run_date && $notification->record_status!=='I' && $notification->record_status!=='T' && $notification->record_status!=='C') {
          if ($notification->record_owner==='t')
            $comment = 'Your record of '.$notification->taxon.' at '.$notification->public_entered_sref.' on '.$date.' was examined by an expert.<br/>"';
          else
            $comment = 'A record of '.$notification->taxon.' at '.$notification->public_entered_sref.' on '.$date.' which you\'d previously commented on was examined by an expert.<br/>"';
        }
        elseif ($notification->record_owner==='t')
          $comment = 'A comment was added to your record of '.$notification->taxon.' at '.$notification->public_entered_sref.' on '.$date.'.<br/>"';          
        else
          $comment = 'A reply was added to the record of '.$notification->taxon.' at '.$notification->public_entered_sref.' on '.$date.' which you\'ve previously commented on.<br/>"';          
        $comment .= $notification->comment;
        if ($notification->auto_generated==='t') {
          // a difficult ID record is not necessarily important...
          $thing = ($notification->generated_by==='data_cleaner_identification_difficulty') ? 'identification' : 'important record';
          $comment .= "</em><br/>You may be contacted by an expert to confirm this $thing so if you can supply any more information or photographs it would be useful.";
        } else 
          $comment .= '"<br/>';
      }
      $theNotificationToInsert = array(
        'source' => 'Verifications and comments',
        'source_type' => $notification->source_type,
        'data' => json_encode(array(
            'username'=>$notification->username,'occurrence_id'=>$notification->id,'comment'=>$comment,
            'taxon'=>$notification->taxon,'date'=>$date,'entered_sref'=>$notification->public_entered_sref,
            'auto_generated'=>$notification->auto_generated, 'record_status'=>$notification->record_status, 'updated_on'=>$notification->updated_on
        )),
        'linked_id' => $notification->id,
        'user_id' => $notification->notify_user_id,
        // use digest mode the user selected for this notification, or their default if not specific
        'digest_mode' => 'N',
        'source_detail' => $notification->source_detail
      );
      $db->insert('notifications', $theNotificationToInsert);
    }
    echo count($notifications) . ' notifications generated<br/>';
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}

?>