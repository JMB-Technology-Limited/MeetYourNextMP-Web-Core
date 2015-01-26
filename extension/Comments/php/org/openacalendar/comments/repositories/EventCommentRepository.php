<?php

namespace org\openacalendar\comments\repositories;


use models\EventModel;
use models\UserAccountModel;
use org\openacalendar\comments\dbaccess\EventCommentDBAccess;
use org\openacalendar\comments\models\EventCommentModel;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class EventCommentRepository {


	/** @var  EventCommentDBAccess */
	protected $eventCommentDBAccess;

	function __construct()
	{
		global $DB, $USERAGENT;
		$this->eventCommentDBAccess = new EventCommentDBAccess($DB, new \TimeSource(), $USERAGENT);
	}


	public function getByEventAndSlug(EventModel $event, $slug) {
		global $DB;
		$stat = $DB->prepare("SELECT event_comment_information.* FROM event_comment_information ".
				" WHERE event_comment_information.event_id =:event_id AND event_comment_information.slug = :slug ");
		$stat->execute(array( 'event_id'=> $event->getId(), 'slug'=> $slug));
		if ($stat->rowCount() > 0) {
			$a = new EventCommentModel();
			$a->setFromDataBaseRow($stat->fetch());
			return $a;
		}
	}

	public function create(EventCommentModel $eventComment, EventModel $event, UserAccountModel $creator) {
		global $DB;
		try {
			$DB->beginTransaction();

			$stat = $DB->prepare("SELECT max(slug) AS c FROM event_comment_information WHERE event_id=:event_id");
			$stat->execute(array('event_id'=>$event->getId()));
			$data = $stat->fetch();
			$eventComment->setSlug($data['c'] + 1);

			$stat = $DB->prepare("INSERT INTO event_comment_information (event_id, slug, title, comment, user_account_id, is_deleted, created_at, approved_at) ".
					"VALUES (:event_id, :slug, :title, :comment, :user_account_id, '0', :created_at, :approved_at) RETURNING id");
			$stat->execute(array(
					'event_id'=>$event->getId(),
					'slug'=>$eventComment->getSlug(),
					'title'=>substr($eventComment->getTitle(),0,VARCHAR_COLUMN_LENGTH_USED),
					'comment'=>$eventComment->getComment(),
					'user_account_id'=> $creator->getId(),
					'created_at'=>\TimeSource::getFormattedForDataBase(),
					'approved_at'=>\TimeSource::getFormattedForDataBase(),
				));
			$data = $stat->fetch();
			$eventComment->setId($data['id']);

			$stat = $DB->prepare("INSERT INTO event_comment_history (event_comment_id, title, comment, user_account_id, is_deleted, created_at, approved_at, is_new) VALUES ".
					"(:event_comment_id, :title, :comment, :user_account_id, '0', :created_at, :approved_at, '1')");
			$stat->execute(array(
					'event_comment_id'=>$eventComment->getId(),
					'title'=>substr($eventComment->getTitle(),0,VARCHAR_COLUMN_LENGTH_USED),
					'comment'=>$eventComment->getComment(),
					'user_account_id'=> $creator->getId(),
					'created_at'=>\TimeSource::getFormattedForDataBase(),
					'approved_at'=>\TimeSource::getFormattedForDataBase(),
				));

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}



	public function delete(EventCommentModel $eventCommentModel, UserAccountModel $user) {
		global $DB;
		try {
			$DB->beginTransaction();

			$eventCommentModel->setIsDeleted(true);
			$this->eventCommentDBAccess->update($eventCommentModel, array('is_deleted'), $user);

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}


	public function undelete(EventCommentModel $eventCommentModel, UserAccountModel $user) {
		global $DB;
		try {
			$DB->beginTransaction();

			$eventCommentModel->setIsDeleted(false);
			$this->eventCommentDBAccess->update($eventCommentModel, array('is_deleted'), $user);

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}

	public function closeByAdmin(EventCommentModel $eventCommentModel, UserAccountModel $user) {
		global $DB;
		try {
			$DB->beginTransaction();

			$eventCommentModel->setIsClosedByAdmin(true);
			$this->eventCommentDBAccess->update($eventCommentModel, array('is_closed_by_admin'), $user);

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}


	public function uncloseByAdmin(EventCommentModel $eventCommentModel, UserAccountModel $user) {
		global $DB;
		try {
			$DB->beginTransaction();

			$eventCommentModel->setIsClosedByAdmin(false);
			$this->eventCommentDBAccess->update($eventCommentModel, array('is_closed_by_admin'), $user);

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}

}
