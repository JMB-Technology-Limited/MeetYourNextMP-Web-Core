<?php

namespace org\openacalendar\comments\repositories;

use models\AreaModel;
use models\AreaHistoryModel;
use org\openacalendar\comments\models\AreaCommentHistoryModel;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class AreaCommentHistoryRepository {

	
	public function ensureChangedFlagsAreSet(AreaCommentHistoryModel $areaCommentHistory) {
		global $DB;
		
		// do we already have them?
		if (!$areaCommentHistory->isAnyChangeFlagsUnknown()) return;
		
		// load last.
		$stat = $DB->prepare("SELECT * FROM area_comment_history WHERE area_comment_id = :id AND created_at < :at ".
				"ORDER BY created_at DESC");
		$stat->execute(array('id'=>$areaCommentHistory->getId(),'at'=>$areaCommentHistory->getCreatedAt()->format("Y-m-d H:i:s")));
		
		
		if ($stat->rowCount() == 0) {
			$areaCommentHistory->setChangedFlagsFromNothing();
		} else {
			while($areaCommentHistory->isAnyChangeFlagsUnknown() && $lastHistoryData = $stat->fetch()) {
				$lastHistory = new AreaCommentHistoryModel();
				$lastHistory->setFromDataBaseRow($lastHistoryData);
				$areaCommentHistory->setChangedFlagsFromLast($lastHistory);
			}
		}

		// Save back to DB
		$sqlFields = array();
		$sqlParams = array(
			'id'=>$areaCommentHistory->getId(),
			'created_at'=>$areaCommentHistory->getCreatedAt()->format("Y-m-d H:i:s"),
			'is_new'=>$areaCommentHistory->getIsNew()?1:0,
		);

		if ($areaCommentHistory->getTitleChangedKnown()) {
			$sqlFields[] = " title_changed = :title_changed ";
			$sqlParams['title_changed'] = $areaCommentHistory->getTitleChanged() ? 1 : -1;
		}
		if ($areaCommentHistory->getCommentChangedKnown()) {
			$sqlFields[] = " comment_changed = :comment_changed ";
			$sqlParams['comment_changed'] = $areaCommentHistory->getCommentChanged() ? 1 : -1;
		}
		if ($areaCommentHistory->getIsDeletedChangedKnown()) {
			$sqlFields[] = " is_deleted_changed = :is_deleted_changed ";
			$sqlParams['is_deleted_changed'] = $areaCommentHistory->getIsDeletedChanged() ? 1 : -1;
		}
		if ($areaCommentHistory->getIsClosedByAdminChangedKnown()) {
			$sqlFields[] = " is_closed_by_admin_changed = :is_closed_by_admin_changed ";
			$sqlParams['is_closed_by_admin_changed'] = $areaCommentHistory->getIsClosedByAdminChanged() ? 1 : -1;
		}

		$statUpdate = $DB->prepare("UPDATE area_comment_history SET ".
			" is_new = :is_new, ".
			implode(" , ",$sqlFields).
			" WHERE area_comment_id = :id AND created_at = :created_at");
		$statUpdate->execute($sqlParams);
	}
	
}


