<?php


namespace org\openacalendar\comments\models;

use InterfaceHistoryModel;
use models\EventModel;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class EventCommentHistoryModel extends EventCommentModel implements InterfaceHistoryModel {
	
	protected $created_at; 
	protected $user_account_id;
	protected $user_account_username;

	/** @var  EventModel */
	protected $event;

	protected $is_currently_deleted = 0;

	protected $title_changed = 0;
	protected $comment_changed = 0;
	protected $user_account_id_changed = 0;
	protected $is_deleted_changed = 0;
	protected $is_closed_by_admin_changed = 0;


	protected $is_new = 0;


	public function setFromDataBaseRow($data) {
		$this->id = $data['event_comment_id'];
		$this->slug = isset($data['event_slug']) ? $data['event_slug'] : null;

		$this->title = $data['title'];
		$this->comment = $data['comment'];
		$this->is_deleted = $data['is_deleted'];
		$this->is_closed_by_admin = $data['is_closed_by_admin'];

		$utc = new \DateTimeZone("UTC");
		$this->created_at = new \DateTime($data['created_at'], $utc);
		$this->user_account_id = isset($data['user_account_id']) ? $data['user_account_id'] : null;
		$this->user_account_username = isset($data['user_account_username']) ? $data['user_account_username'] : null;

		$this->title_changed  = $data['title_changed'];
		$this->comment_changed  = $data['comment_changed'];
		$this->is_deleted_changed  = $data['is_deleted_changed'];
		$this->is_closed_by_admin_changed  = $data['is_closed_by_admin_changed'];

		$this->is_new = isset($data['is_new']) ? $data['is_new'] : 0;

		$this->is_currently_deleted = isset($data['is_currently_deleted']) ? $data['is_currently_deleted'] : 0;

		if (isset($data['event_information_slug']) && $data['event_information_slug']) {
			$this->event = new EventModel();
			$this->event->setSummary($data['event_information_summary']);
			$this->event->setSlug($data['event_information_slug']);
		}
	}
	
		
	public function getCreatedAt() {
		return $this->created_at;
	}

	public function getCreatedAtTimeStamp() {
		return $this->created_at->getTimestamp();
	}

	public function getUserAccountId() {
		return $this->user_account_id;
	}

	public function getUserAccountUsername() {
		return $this->user_account_username;
	}
	
	public function isAnyChangeFlagsUnknown() {
		return $this->title_changed == 0 || $this->comment_changed == 0 || $this->is_deleted_changed == 0 ||
				$this->is_closed_by_admin_changed == 0;
	}
	
	public function setChangedFlagsFromNothing() {
		$this->title_changed = $this->title ? 1 : -1;
		$this->comment_changed = $this->comment ? 1 : -1;
		$this->is_closed_by_admin_changed = $this->is_closed_by_admin_changed ? 1 : -1;
		$this->is_deleted_changed = $this->is_deleted ? 1 : -1;
		$this->is_new = 1;
	}

	public function setChangedFlagsFromLast(EventCommentHistoryModel $last) {
		if ($this->title_changed == 0 && $last->title_changed != -2) {
			$this->title_changed  = ($this->title  != $last->title  )? 1 : -1;
		}
		if ($this->comment_changed == 0 && $last->comment_changed != -2) {
			$this->comment_changed  = ($this->comment  != $last->comment  )? 1 : -1;
		}
		if ($this->is_deleted_changed == 0 && $last->is_deleted_changed != -2) {
			$this->is_deleted_changed  = ($this->is_deleted  != $last->is_deleted  )? 1 : -1;
		}
		if ($this->is_closed_by_admin_changed == 0 && $last->is_closed_by_admin_changed != -2) {
			$this->is_closed_by_admin_changed = ($this->is_closed_by_admin != $last->is_closed_by_admin) ? 1 : -1;
		}
		$this->is_new = 0;
	}

	public function getTitleChanged() {
		return ($this->title_changed > -1);
	}

	public function getTitleChangedKnown() {
		return ($this->title_changed > -2);
	}

	public function getCommentChanged() {
		return ($this->comment_changed > -1);
	}

	public function getCommentChangedKnown() {
		return ($this->comment_changed > -2);
	}

	public function getIsDeletedChanged() {
		return ($this->is_deleted_changed > -1);
	}

	public function getIsDeletedChangedKnown() {
		return ($this->is_deleted_changed > -2);
	}

	public function getIsClosedByAdminChanged() {
		return ($this->is_closed_by_admin_changed > -1);
	}

	public function getIsClosedByAdminChangedKnown() {
		return ($this->is_closed_by_admin_changed > -2);
	}

	public function getIsNew() {
		return ($this->is_new == 1);
	}

	public function getSiteEmailTemplate() {
		return '/email/common/eventCommentHistoryItem.html.twig';
	}

	public function getSiteWebTemplate() {
		return  '/site/common/eventCommentHistoryItem.html.twig';
	}

	/**
	 * @return \models\EventModel
	 */
	public function getEvent()
	{
		return $this->event;
	}

	/**
	 * @return int
	 */
	public function getIsCurrentlyDeleted()
	{
		return $this->is_currently_deleted;
	}


	/** @return boolean */
	public function isEqualTo(\InterfaceHistoryModel $otherHistoryModel) {
		return $otherHistoryModel instanceof $this &&
		$otherHistoryModel->getCreatedAtTimeStamp() == $this->getCreatedAtTimeStamp() &&
		$otherHistoryModel->getId() == $this->getId();
	}


}

