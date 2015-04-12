<?php


namespace repositories;

use models\EventModel;
use models\NewEventDraftModel;
use models\SiteModel;
use models\MediaModel;
use models\UserAccountModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class NewEventDraftRepository
{

	public function create(NewEventDraftModel $newEventDraftModel) {
		global $DB;


		try {
			$DB->beginTransaction();

			$stat = $DB->prepare("SELECT max(slug) AS c FROM new_event_draft_information WHERE site_id=:site_id");
			$stat->execute(array('site_id'=>$newEventDraftModel->getSiteId()));
			$data = $stat->fetch();
			$newEventDraftModel->setSlug($data['c'] + 1);

			$stat = $DB->prepare("INSERT INTO new_event_draft_information (site_id, details, user_account_id,created_at,updated_at,slug) ".
				"VALUES (:site_id, :details, :user_account_id, :created_at, :updated_at, :slug ) RETURNING id");
			$stat->execute(array(
				'site_id'=>$newEventDraftModel->getSiteId(),
				'slug'=>$newEventDraftModel->getSlug(),
				'details'=>json_encode($newEventDraftModel->getDetailsForDataBase()),
				'user_account_id'=>($newEventDraftModel->getUserAccountId() ? $newEventDraftModel->getUserAccountId() : null),
				'created_at'=>\TimeSource::getFormattedForDataBase(),
				'updated_at'=>\TimeSource::getFormattedForDataBase(),
			));
			$data = $stat->fetch();
			$newEventDraftModel->setId($data['id']);

			$stat = $DB->prepare("INSERT INTO new_event_draft_history (new_event_draft_id, details, user_account_id,created_at,not_duplicate_events_changed,event_id_changed,was_existing_event_id_changed) ".
				"VALUES (:new_event_draft_id, :details, :user_account_id,:created_at,:not_duplicate_events_changed,:event_id_changed,:was_existing_event_id_changed )");
			$stat->execute(array(
				'new_event_draft_id'=>$newEventDraftModel->getId(),
				'details'=>json_encode($newEventDraftModel->getDetailsForDataBase()),
				'user_account_id'=>($newEventDraftModel->getUserAccountId() ? $newEventDraftModel->getUserAccountId() : null),
				'created_at'=>\TimeSource::getFormattedForDataBase(),
				'not_duplicate_events_changed'=>-2,
				'event_id_changed'=>-2,
				'was_existing_event_id_changed'=>-2,
			));

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}

	}

	public function loadBySlugForSiteAndUser($slug, SiteModel $siteModel, UserAccountModel $userAccountModel) {
		global $DB;
		$stat = $DB->prepare("SELECT new_event_draft_information.*  FROM new_event_draft_information ".
			" WHERE new_event_draft_information.slug =:slug AND new_event_draft_information.site_id = :site_id AND new_event_draft_information.user_account_id = :user_id");
		$stat->execute(array( 'slug'=>$slug, 'site_id'=>$siteModel->getId(), 'user_id'=>$userAccountModel->getId() ));
		if ($stat->rowCount() > 0) {
			$event = new NewEventDraftModel();
			$event->setFromDataBaseRow($stat->fetch());
			return $event;
		}
	}

	public function saveProgress(NewEventDraftModel $newEventDraftModel) {
		global $DB;

		try {
			$DB->beginTransaction();

			$stat = $DB->prepare("UPDATE new_event_draft_information SET details=:details, updated_at=:updated_at WHERE id=:id");
			$stat->execute(array(
				'id'=>$newEventDraftModel->getId(),
				'details'=>json_encode($newEventDraftModel->getDetailsForDataBase()),
				'updated_at'=>\TimeSource::getFormattedForDataBase(),
			));

			$stat = $DB->prepare("INSERT INTO new_event_draft_history (new_event_draft_id, details, user_account_id,created_at,not_duplicate_events_changed,event_id_changed,was_existing_event_id_changed) ".
				"VALUES (:new_event_draft_id, :details, :user_account_id,:created_at,-2,-2,-2 )");
			$stat->execute(array(
				'new_event_draft_id'=>$newEventDraftModel->getId(),
				'details'=>json_encode($newEventDraftModel->getDetailsForDataBase()),
				'user_account_id'=>($newEventDraftModel->getUserAccountId() ? $newEventDraftModel->getUserAccountId() : null),
				'created_at'=>\TimeSource::getFormattedForDataBase(),
			));

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}

	public function saveNotDuplicateEvents(NewEventDraftModel $newEventDraftModel) {
		global $DB;

		try {
			$DB->beginTransaction();

			$stat = $DB->prepare("UPDATE new_event_draft_information SET not_duplicate_events=:not_duplicate_events, updated_at=:updated_at WHERE id=:id");
			$stat->execute(array(
				'id'=>$newEventDraftModel->getId(),
				'not_duplicate_events'=>json_encode($newEventDraftModel->getNotDuplicateEventsForDatabase()),
				'updated_at'=>\TimeSource::getFormattedForDataBase(),
			));

			$stat = $DB->prepare("INSERT INTO new_event_draft_history (new_event_draft_id, not_duplicate_events, user_account_id,created_at,details_changed,event_id_changed,was_existing_event_id_changed) ".
				"VALUES (:new_event_draft_id, :not_duplicate_events, :user_account_id,:created_at,-2,-2,-2 )");
			$stat->execute(array(
				'new_event_draft_id'=>$newEventDraftModel->getId(),
				'not_duplicate_events'=>json_encode($newEventDraftModel->getNotDuplicateEventsForDatabase()),
				'user_account_id'=>($newEventDraftModel->getUserAccountId() ? $newEventDraftModel->getUserAccountId() : null),
				'created_at'=>\TimeSource::getFormattedForDataBase(),
			));

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}

	public function markIsDuplicateOf(NewEventDraftModel $newEventDraftModel, EventModel $eventModel) {
		global $DB;

		try {
			$DB->beginTransaction();

			$stat = $DB->prepare("UPDATE new_event_draft_information SET was_existing_event_id=:was_existing_event_id, updated_at=:updated_at WHERE id=:id");
			$stat->execute(array(
				'id'=>$newEventDraftModel->getId(),
				'was_existing_event_id'=>$eventModel->getId(),
				'updated_at'=>\TimeSource::getFormattedForDataBase(),
			));


			$stat = $DB->prepare("INSERT INTO new_event_draft_history (new_event_draft_id, was_existing_event_id, user_account_id,created_at,details_changed,event_id_changed,not_duplicate_events_changed) ".
				"VALUES (:new_event_draft_id, :was_existing_event_id, :user_account_id,:created_at,-2,-2,-2 )");
			$stat->execute(array(
				'new_event_draft_id'=>$newEventDraftModel->getId(),
				'was_existing_event_id'=>$eventModel->getId(),
				'user_account_id'=>($newEventDraftModel->getUserAccountId() ? $newEventDraftModel->getUserAccountId() : null),
				'created_at'=>\TimeSource::getFormattedForDataBase(),
			));


			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}


	//
	//
	// There is also code in EventRepositry Create
	//
	//

}