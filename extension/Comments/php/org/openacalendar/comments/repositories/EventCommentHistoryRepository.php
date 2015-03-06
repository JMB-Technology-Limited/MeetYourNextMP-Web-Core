<?php

namespace org\openacalendar\comments\repositories;

use models\EventModel;
use models\EventHistoryModel;
use org\openacalendar\comments\models\EventCommentHistoryModel;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class EventCommentHistoryRepository {

	
	public function ensureChangedFlagsAreSet(EventCommentHistoryModel $eventCommentHistory) {
		global $DB;
		
		// do we already have them?
		if (!$eventCommentHistory->isAnyChangeFlagsUnknown()) return;
		
		// load last.
		$stat = $DB->prepare("SELECT * FROM event_comment_history WHERE event_comment_id = :id AND created_at < :at ".
				"ORDER BY created_at DESC");
		$stat->execute(array('id'=>$eventCommentHistory->getId(),'at'=>$eventCommentHistory->getCreatedAt()->format("Y-m-d H:i:s")));
		
		
		if ($stat->rowCount() == 0) {
			$eventCommentHistory->setChangedFlagsFromNothing();
		} else {
			while($eventCommentHistory->isAnyChangeFlagsUnknown() && $lastHistoryData = $stat->fetch()) {
				$lastHistory = new EventCommentHistoryModel();
				$lastHistory->setFromDataBaseRow($lastHistoryData);
				$eventCommentHistory->setChangedFlagsFromLast($lastHistory);
			}
		}

		// Save back to DB
		$sqlFields = array();
		$sqlParams = array(
			'id'=>$eventCommentHistory->getId(),
			'created_at'=>$eventCommentHistory->getCreatedAt()->format("Y-m-d H:i:s"),
			'is_new'=>$eventCommentHistory->getIsNew()?1:0,
		);

		if ($eventCommentHistory->getTitleChangedKnown()) {
			$sqlFields[] = " title_changed = :title_changed ";
			$sqlParams['title_changed'] = $eventCommentHistory->getTitleChanged() ? 1 : -1;
		}
		if ($eventCommentHistory->getCommentChangedKnown()) {
			$sqlFields[] = " comment_changed = :comment_changed ";
			$sqlParams['comment_changed'] = $eventCommentHistory->getCommentChanged() ? 1 : -1;
		}
		if ($eventCommentHistory->getIsDeletedChangedKnown()) {
			$sqlFields[] = " is_deleted_changed = :is_deleted_changed ";
			$sqlParams['is_deleted_changed'] = $eventCommentHistory->getIsDeletedChanged() ? 1 : -1;
		}
		if ($eventCommentHistory->getIsClosedByAdminChangedKnown()) {
			$sqlFields[] = " is_closed_by_admin_changed = :is_closed_by_admin_changed ";
			$sqlParams['is_closed_by_admin_changed'] = $eventCommentHistory->getIsClosedByAdminChanged() ? 1 : -1;
		}

		$statUpdate = $DB->prepare("UPDATE event_comment_history SET ".
			" is_new = :is_new, ".
			implode(" , ",$sqlFields).
			" WHERE event_comment_id = :id AND created_at = :created_at");
		$statUpdate->execute($sqlParams);
	}
	
}


