<?php


namespace com\meetyournextmp\repositories;

use com\meetyournextmp\dbaccess\HumanDBAccess;
use com\meetyournextmp\models\HumanTweetModel;
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
class HumanTweetRepository {


	public function create(HumanTweetModel $humanTweetModel) {
		global $DB;

		$stat = $DB->prepare("INSERT INTO human_tweet ".
			" (human_id,text,created_at) ".
			" VALUES (:human_id,:text,:created_at) ".
			" RETURNING id");

		$stat->execute(array(
			'human_id'=>$humanTweetModel->getHumanId(),
			'text'=>$humanTweetModel->getText(),
			'created_at'=>\TimeSource::getFormattedForDataBase(),
		));
		$data = $stat->fetch();
		$humanTweetModel->setId($data['id']);
	}


	public function markSent(HumanTweetModel $humanTweetModel) {
		global $DB;

		$stat = $DB->prepare("UPDATE human_tweet SET sent_at=:sent_at WHERE id=:id");
		$stat->execute(array(
			'id'=>$humanTweetModel->getId(),
			'sent_at'=>\TimeSource::getFormattedForDataBase(),
		));

	}

}

