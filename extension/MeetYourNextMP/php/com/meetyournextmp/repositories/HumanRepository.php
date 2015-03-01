<?php


namespace com\meetyournextmp\repositories;

use com\meetyournextmp\dbaccess\HumanDBAccess;
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
class HumanRepository {

	/** @var  \dbaccess\HumanDBAccess */
	protected $humanDBAccess;

	function __construct()
	{
		global $DB, $USERAGENT;
		$this->humanDBAccess = new HumanDBAccess($DB, new \TimeSource(), $USERAGENT);
	}

	
	public function create(HumanModel $human, SiteModel $site, UserAccountModel $creator=null) {
		global $DB;
		try {
			$DB->beginTransaction();

			$stat = $DB->prepare("SELECT max(slug) AS c FROM human_information WHERE site_id=:site_id");
			$stat->execute(array('site_id'=>$site->getId()));
			$data = $stat->fetch();
			$human->setSlug($data['c'] + 1);
			
			$stat = $DB->prepare("INSERT INTO human_information (site_id, slug, title,description,created_at,approved_at, is_deleted, email, twitter, image_url) ".
					"VALUES (:site_id, :slug, :title, :description, :created_at,:approved_at, '0', :email, :twitter, :image_url) RETURNING id");
			$stat->execute(array(
					'site_id'=>$site->getId(), 
					'slug'=>$human->getSlug(),
					'title'=>substr($human->getTitle(),0,VARCHAR_COLUMN_LENGTH_USED),
					'twitter'=>substr($human->getTwitter(),0,VARCHAR_COLUMN_LENGTH_USED),
					'email'=>substr($human->getEmail(),0,VARCHAR_COLUMN_LENGTH_USED),
					'image_url'=>$human->getImageUrl(),
					'description'=>$human->getDescription(),
					'created_at'=>\TimeSource::getFormattedForDataBase(),
					'approved_at'=>\TimeSource::getFormattedForDataBase(),
				));
			$data = $stat->fetch();
			$human->setId($data['id']);
			
			$stat = $DB->prepare("INSERT INTO human_history (human_id, title, description, user_account_id  , created_at, approved_at, is_new, is_deleted, email, twitter, image_url) VALUES ".
					"(:human_id, :title, :description, :user_account_id  , :created_at, :approved_at, '1', '0', :email, :twitter, :image_url)");
			$stat->execute(array(
					'human_id'=>$human->getId(),
					'title'=>substr($human->getTitle(),0,VARCHAR_COLUMN_LENGTH_USED),
					'twitter'=>substr($human->getTwitter(),0,VARCHAR_COLUMN_LENGTH_USED),
					'email'=>substr($human->getEmail(),0,VARCHAR_COLUMN_LENGTH_USED),
					'image_url'=>$human->getImageUrl(),
					'description'=>$human->getDescription(),
					'user_account_id'=>($creator?$creator->getId():null),
					'created_at'=>\TimeSource::getFormattedForDataBase(),
					'approved_at'=>\TimeSource::getFormattedForDataBase(),
				));
						
			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}
	
	
	public function loadBySlug(SiteModel $site, $slug) {
		global $DB;
		$stat = $DB->prepare("SELECT human_information.* FROM human_information WHERE slug =:slug AND site_id =:sid");
		$stat->execute(array( 'sid'=>$site->getId(), 'slug'=>$slug ));
		if ($stat->rowCount() > 0) {
			$human = new HumanModel();
			$human->setFromDataBaseRow($stat->fetch());
			return $human;
		}
	}
	
	
	
	public function loadById($id) {
		global $DB;
		$stat = $DB->prepare("SELECT human_information.* FROM human_information WHERE id = :id");
		$stat->execute(array( 'id'=>$id ));
		if ($stat->rowCount() > 0) {
			$human = new HumanModel();
			$human->setFromDataBaseRow($stat->fetch());
			return $human;
		}
	}
	
	
	
	public function edit(HumanModel $human, UserAccountModel $user = null) {
		global $DB;
		if ($human->getIsDeleted()) {
			throw new \Exception("Can't edit deleted human!");
		}
		try {
			$DB->beginTransaction();

			$fields = array('title','description','is_deleted','email','twitter','image_url');
			$human->setIsDeleted(false);
			$this->humanDBAccess->update($human, $fields, $user);
			
			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}
	
	
	public function delete(HumanModel $human, UserAccountModel $user = null) {
		global $DB;
		try {
			$DB->beginTransaction();

			$human->setIsDeleted(true);
			$this->humanDBAccess->update($human, array('is_deleted'), $user);
			
			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}


	public function undelete(HumanModel $human, UserAccountModel $user = null) {
		global $DB;
		try {
			$DB->beginTransaction();

			$human->setIsDeleted(false);
			$this->humanDBAccess->update($human, array('is_deleted'), $user);

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}

	
	public function addHumanToEvent(HumanModel $human, EventModel $event, UserAccountModel $user=null) {
		global $DB;
		
		// check event not already in list
		$stat = $DB->prepare("SELECT * FROM event_has_human WHERE human_id=:human_id AND ".
				" event_id=:event_id AND removed_at IS NULL ");
		$stat->execute(array(
			'human_id'=>$human->getId(),
			'event_id'=>$event->getId(),
		));
		if ($stat->rowCount() > 0) {
			return;
		}
			
		// Add!
		$stat = $DB->prepare("INSERT INTO event_has_human (human_id,event_id,added_by_user_account_id,added_at,addition_approved_at) ".
				"VALUES (:human_id,:event_id,:added_by_user_account_id,:added_at,:addition_approved_at)");
		$stat->execute(array(
			'human_id'=>$human->getId(),
			'event_id'=>$event->getId(),
			'added_by_user_account_id'=>($user?$user->getId():null),
			'added_at'=>  \TimeSource::getFormattedForDataBase(),
			'addition_approved_at'=>  \TimeSource::getFormattedForDataBase(),
		));
		
	}

	
	
	
	public function removeHumanFromEvent(HumanModel $human, EventModel $event, UserAccountModel $user=null) {
		global $DB;

		
		$stat = $DB->prepare("UPDATE event_has_human SET removed_by_user_account_id=:removed_by_user_account_id,".
				" removed_at=:removed_at, removal_approved_at=:removal_approved_at WHERE ".
				" event_id=:event_id AND human_id=:human_id AND removed_at IS NULL ");
		$stat->execute(array(
				'event_id'=>$event->getId(),
				'human_id'=>$human->getId(),
				'removed_at'=>  \TimeSource::getFormattedForDataBase(),
				'removal_approved_at'=>  \TimeSource::getFormattedForDataBase(),
				'removed_by_user_account_id'=>($user?$user->getId():null),
		));
	}

	
	public function addHumanToArea(HumanModel $human, AreaModel $area, UserAccountModel $user=null) {
		global $DB;
		
		// check area not already in list
		$stat = $DB->prepare("SELECT * FROM human_in_area WHERE human_id=:human_id AND ".
				" area_id=:area_id AND removed_at IS NULL ");
		$stat->execute(array(
			'human_id'=>$human->getId(),
			'area_id'=>$area->getId(),
		));
		if ($stat->rowCount() > 0) {
			return;
		}
			
		// Add!
		$stat = $DB->prepare("INSERT INTO human_in_area (human_id,area_id,added_by_user_account_id,added_at,addition_approved_at) ".
				"VALUES (:human_id,:area_id,:added_by_user_account_id,:added_at,:addition_approved_at)");
		$stat->execute(array(
			'human_id'=>$human->getId(),
			'area_id'=>$area->getId(),
			'added_by_user_account_id'=>($user?$user->getId():null),
			'added_at'=>  \TimeSource::getFormattedForDataBase(),
			'addition_approved_at'=>  \TimeSource::getFormattedForDataBase(),
		));
		
	}

	
	
	
	public function removeHumanFromArea(HumanModel $human, AreaModel $area, UserAccountModel $user=null) {
		global $DB;

		
		$stat = $DB->prepare("UPDATE human_in_area SET removed_by_user_account_id=:removed_by_user_account_id,".
				" removed_at=:removed_at, removal_approved_at=:removal_approved_at WHERE ".
				" area_id=:area_id AND human_id=:human_id AND removed_at IS NULL ");
		$stat->execute(array(
				'area_id'=>$area->getId(),
				'human_id'=>$human->getId(),
				'removed_at'=>  \TimeSource::getFormattedForDataBase(),
				'removal_approved_at'=>  \TimeSource::getFormattedForDataBase(),
				'removed_by_user_account_id'=>($user?$user->getId():null),
		));
	}

				
}

