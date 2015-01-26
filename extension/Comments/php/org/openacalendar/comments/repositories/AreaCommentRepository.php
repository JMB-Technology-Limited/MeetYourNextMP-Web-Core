<?php

namespace org\openacalendar\comments\repositories;


use models\AreaModel;
use models\UserAccountModel;
use org\openacalendar\comments\dbaccess\AreaCommentDBAccess;
use org\openacalendar\comments\models\AreaCommentModel;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class AreaCommentRepository {


	/** @var  AreaCommentDBAccess */
	protected $areaCommentDBAccess;

	function __construct()
	{
		global $DB, $USERAGENT;
		$this->areaCommentDBAccess = new AreaCommentDBAccess($DB, new \TimeSource(), $USERAGENT);
	}


	public function getByAreaAndSlug(AreaModel $area, $slug) {
		global $DB;
		$stat = $DB->prepare("SELECT area_comment_information.* FROM area_comment_information ".
				" WHERE area_comment_information.area_id =:area_id AND area_comment_information.slug = :slug ");
		$stat->execute(array( 'area_id'=> $area->getId(), 'slug'=> $slug));
		if ($stat->rowCount() > 0) {
			$a = new AreaCommentModel();
			$a->setFromDataBaseRow($stat->fetch());
			return $a;
		}
	}

	public function create(AreaCommentModel $areaComment, AreaModel $area, UserAccountModel $creator) {
		global $DB;
		try {
			$DB->beginTransaction();

			$stat = $DB->prepare("SELECT max(slug) AS c FROM area_comment_information WHERE area_id=:area_id");
			$stat->execute(array('area_id'=>$area->getId()));
			$data = $stat->fetch();
			$areaComment->setSlug($data['c'] + 1);

			$stat = $DB->prepare("INSERT INTO area_comment_information (area_id, slug, title, comment, user_account_id, is_deleted, created_at, approved_at) ".
					"VALUES (:area_id, :slug, :title, :comment, :user_account_id, '0', :created_at, :approved_at) RETURNING id");
			$stat->execute(array(
					'area_id'=>$area->getId(),
					'slug'=>$areaComment->getSlug(),
					'title'=>substr($areaComment->getTitle(),0,VARCHAR_COLUMN_LENGTH_USED),
					'comment'=>$areaComment->getComment(),
					'user_account_id'=> $creator->getId(),
					'created_at'=>\TimeSource::getFormattedForDataBase(),
					'approved_at'=>\TimeSource::getFormattedForDataBase(),
				));
			$data = $stat->fetch();
			$areaComment->setId($data['id']);

			$stat = $DB->prepare("INSERT INTO area_comment_history (area_comment_id, title, comment, user_account_id, is_deleted, created_at, approved_at, is_new) VALUES ".
					"(:area_comment_id, :title, :comment, :user_account_id, '0', :created_at, :approved_at, '1')");
			$stat->execute(array(
					'area_comment_id'=>$areaComment->getId(),
					'title'=>substr($areaComment->getTitle(),0,VARCHAR_COLUMN_LENGTH_USED),
					'comment'=>$areaComment->getComment(),
					'user_account_id'=> $creator->getId(),
					'created_at'=>\TimeSource::getFormattedForDataBase(),
					'approved_at'=>\TimeSource::getFormattedForDataBase(),
				));

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}



	public function delete(AreaCommentModel $areaCommentModel, UserAccountModel $user) {
		global $DB;
		try {
			$DB->beginTransaction();

			$areaCommentModel->setIsDeleted(true);
			$this->areaCommentDBAccess->update($areaCommentModel, array('is_deleted'), $user);

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}


	public function undelete(AreaCommentModel $areaCommentModel, UserAccountModel $user) {
		global $DB;
		try {
			$DB->beginTransaction();

			$areaCommentModel->setIsDeleted(false);
			$this->areaCommentDBAccess->update($areaCommentModel, array('is_deleted'), $user);

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}

	public function closeByAdmin(AreaCommentModel $areaCommentModel, UserAccountModel $user) {
		global $DB;
		try {
			$DB->beginTransaction();

			$areaCommentModel->setIsClosedByAdmin(true);
			$this->areaCommentDBAccess->update($areaCommentModel, array('is_closed_by_admin'), $user);

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}


	public function uncloseByAdmin(AreaCommentModel $areaCommentModel, UserAccountModel $user) {
		global $DB;
		try {
			$DB->beginTransaction();

			$areaCommentModel->setIsClosedByAdmin(false);
			$this->areaCommentDBAccess->update($areaCommentModel, array('is_closed_by_admin'), $user);

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}
	}

}
