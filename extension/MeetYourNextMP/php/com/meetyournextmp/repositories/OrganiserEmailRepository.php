<?php


namespace com\meetyournextmp\repositories;

use com\meetyournextmp\dbaccess\HumanDBAccess;
use com\meetyournextmp\models\HumanEmailModel;
use com\meetyournextmp\models\HumanModel;
use com\meetyournextmp\models\OrganiserEmailModel;
use models\AreaModel;
use models\SiteModel;
use models\EventModel;
use models\UserAccountModel;

/**
 *
 * @package Core
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class OrganiserEmailRepository {


	public function create(OrganiserEmailModel $organiserEmailModel) {
		global $DB;

		$stat = $DB->prepare("INSERT INTO organiser_email ".
			" (event_id,email,subject,body_text,body_html,created_at) ".
			" VALUES (:event_id,:email,:subject,:body_text,:body_html,:created_at) ".
			" RETURNING id");

		$stat->execute(array(
			'event_id'=>$organiserEmailModel->getEventId(),
			'email'=>$organiserEmailModel->getEmail(),
			'subject'=>$organiserEmailModel->getSubject(),
			'body_text'=>$organiserEmailModel->getBodyText(),
			'body_html'=>$organiserEmailModel->getBodyHtml(),
			'created_at'=>\TimeSource::getFormattedForDataBase(),
		));
		$data = $stat->fetch();
		$organiserEmailModel->setId($data['id']);
	}


	public function markSent(OrganiserEmailModel $organiserEmailModel) {
		global $DB;

		$stat = $DB->prepare("UPDATE organiser_email SET sent_at=:sent_at WHERE id=:id");
		$stat->execute(array(
			'id'=>$organiserEmailModel->getId(),
			'sent_at'=>\TimeSource::getFormattedForDataBase(),
		));

	}

	public function hasBeenSentToEvent(EventModel $eventModel) {
		global $DB;

		$stat = $DB->prepare("SELECT id FROM organiser_email WHERE event_id = :id");
		$stat->execute(array('id'=>$eventModel->getId()));
		return $stat->rowCount() > 0;
	}

}
