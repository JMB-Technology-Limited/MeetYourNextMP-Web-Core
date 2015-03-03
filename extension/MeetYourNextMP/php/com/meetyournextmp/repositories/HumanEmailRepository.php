<?php


namespace com\meetyournextmp\repositories;

use com\meetyournextmp\dbaccess\HumanDBAccess;
use com\meetyournextmp\models\HumanEmailModel;
use com\meetyournextmp\models\HumanModel;
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
class HumanEmailRepository {


	public function create(HumanEmailModel $humanEmailModel) {
		global $DB;

		$stat = $DB->prepare("INSERT INTO human_email ".
			" (human_id,email,subject,body_text,body_html,created_at) ".
			" VALUES (:human_id,:email,:subject,:body_text,:body_html,:created_at) ".
			" RETURNING id");

		$stat->execute(array(
			'human_id'=>$humanEmailModel->getHumanId(),
			'email'=>$humanEmailModel->getEmail(),
			'subject'=>$humanEmailModel->getSubject(),
			'body_text'=>$humanEmailModel->getBodyText(),
			'body_html'=>$humanEmailModel->getBodyHtml(),
			'created_at'=>\TimeSource::getFormattedForDataBase(),
		));
		$data = $stat->fetch();
		$humanEmailModel->setId($data['id']);
	}


	public function markSent(HumanEmailModel $humanEmailModel) {
		global $DB;

		$stat = $DB->prepare("UPDATE human_email SET sent_at=:sent_at WHERE id=:id");
		$stat->execute(array(
			'id'=>$humanEmailModel->getId(),
			'sent_at'=>\TimeSource::getFormattedForDataBase(),
		));

	}

}
