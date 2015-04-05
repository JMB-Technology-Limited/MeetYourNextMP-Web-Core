<?php


namespace repositories;

use models\CountryModel;
use models\EventCustomFieldDefinitionModel;
use models\SiteModel;
use models\UserAccountModel;


/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class EventCustomFieldDefinitionRepository
{

	public function loadBySiteIDAndID($siteID, $id) {
		global $DB;
		$stat = $DB->prepare("SELECT event_custom_field_definition_information.* FROM event_custom_field_definition_information WHERE site_id =:sid AND id =:id");
		$stat->execute(array( 'sid'=>$siteID, 'id'=>$id ));
		if ($stat->rowCount() > 0) {
			$ecfd = new EventCustomFieldDefinitionModel();
			$ecfd->setFromDataBaseRow($stat->fetch());
			return $ecfd;
		}
	}


	public function create(EventCustomFieldDefinitionModel $eventCustomFieldDefinitionModel, UserAccountModel $userAccountModel = null) {
		global $DB;
		try {
			$DB->beginTransaction();

			$stat = $DB->prepare("INSERT INTO event_custom_field_definition_information (site_id, key, extension_id,type,label,created_at) ".
				"VALUES (:site_id, :key, :extension_id,:type,:label,:created_at) RETURNING id");
			$stat->execute(array(
				'site_id'=>$eventCustomFieldDefinitionModel->getSiteId(),
				'key'=>$eventCustomFieldDefinitionModel->getKey(),
				'extension_id'=>$eventCustomFieldDefinitionModel->getExtensionId(),
				'type'=>$eventCustomFieldDefinitionModel->getType(),
				'label'=>substr($eventCustomFieldDefinitionModel->getLabel(),0,VARCHAR_COLUMN_LENGTH_USED),
				'created_at'=>\TimeSource::getFormattedForDataBase(),
			));
			$data = $stat->fetch();
			$eventCustomFieldDefinitionModel->setId($data['id']);

			$stat = $DB->prepare("INSERT INTO event_custom_field_definition_history (event_custom_field_definition_id, key, extension_id,type,label,created_at,user_account_id) ".
				"VALUES (:event_custom_field_definition_id, :key, :extension_id,:type,:label,:created_at,:user_account_id)");
			$stat->execute(array(
				'event_custom_field_definition_id'=>$eventCustomFieldDefinitionModel->getId(),
				'key'=>$eventCustomFieldDefinitionModel->getKey(),
				'extension_id'=>$eventCustomFieldDefinitionModel->getExtensionId(),
				'type'=>$eventCustomFieldDefinitionModel->getType(),
				'label'=>substr($eventCustomFieldDefinitionModel->getLabel(),0,VARCHAR_COLUMN_LENGTH_USED),
				'created_at'=>\TimeSource::getFormattedForDataBase(),
				'user_account_id'=>($userAccountModel ? $userAccountModel->getId() : null),
			));

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}

		$this->updateSiteCache($eventCustomFieldDefinitionModel->getSiteId());

	}

	public function editLabel(EventCustomFieldDefinitionModel $model, UserAccountModel $userAccountModel = null) {
		global $DB;
		try {
			$DB->beginTransaction();

			$stat = $DB->prepare("UPDATE event_custom_field_definition_information SET label=:label WHERE id=:id");
			$stat->execute(array(
				'label'=>substr($model->getLabel(),0,VARCHAR_COLUMN_LENGTH_USED),
				'id'=>$model->getId(),
			));

			$stat = $DB->prepare("INSERT INTO event_custom_field_definition_history (event_custom_field_definition_id, key_changed, extension_id_changed,type_changed,label,is_active_changed,created_at,user_account_id) ".
				"VALUES (:event_custom_field_definition_id, -2, -2,-2,:label,-2,:created_at,:user_account_id)");
			$stat->execute(array(
				'event_custom_field_definition_id'=>$model->getId(),
				'label'=>substr($model->getLabel(),0,VARCHAR_COLUMN_LENGTH_USED),
				'created_at'=>\TimeSource::getFormattedForDataBase(),
				'user_account_id'=>($userAccountModel ? $userAccountModel->getId() : null),
			));

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}

		$this->updateSiteCache($model->getSiteId());
	}

	public function activate(EventCustomFieldDefinitionModel $model, UserAccountModel $userAccountModel = null) {
		global $DB;
		try {
			$DB->beginTransaction();

			$stat = $DB->prepare("UPDATE event_custom_field_definition_information SET is_active='1' WHERE id=:id");
			$stat->execute(array(
				'id'=>$model->getId(),
			));

			$stat = $DB->prepare("INSERT INTO event_custom_field_definition_history (event_custom_field_definition_id, key_changed, extension_id_changed,type_changed,label_changed,is_active,created_at,user_account_id) ".
				"VALUES (:event_custom_field_definition_id, -2, -2,-2,-2,'1',:created_at,:user_account_id)");
			$stat->execute(array(
				'event_custom_field_definition_id'=>$model->getId(),
				'created_at'=>\TimeSource::getFormattedForDataBase(),
				'user_account_id'=>($userAccountModel ? $userAccountModel->getId() : null),
			));

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}

		$this->updateSiteCache($model->getSiteId());
	}

	public function deactivate(EventCustomFieldDefinitionModel $model, UserAccountModel $userAccountModel = null) {
		global $DB;
		try {
			$DB->beginTransaction();

			$stat = $DB->prepare("UPDATE event_custom_field_definition_information SET is_active='0' WHERE id=:id");
			$stat->execute(array(
				'id'=>$model->getId(),
			));

			$stat = $DB->prepare("INSERT INTO event_custom_field_definition_history (event_custom_field_definition_id, key_changed, extension_id_changed,type_changed,label_changed,is_active,created_at,user_account_id) ".
				"VALUES (:event_custom_field_definition_id, -2, -2,-2,-2,'0',:created_at,:user_account_id)");
			$stat->execute(array(
				'event_custom_field_definition_id'=>$model->getId(),
				'created_at'=>\TimeSource::getFormattedForDataBase(),
				'user_account_id'=>($userAccountModel ? $userAccountModel->getId() : null),
			));

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}

		$this->updateSiteCache($model->getSiteId());
	}


	public function updateSiteCache($site) {
		global $DB;


		$stat = $DB->prepare("SELECT * FROM event_custom_field_definition_information WHERE site_id=:site_id ORDER BY id ASC");
		$stat->execute(array('site_id'=>($site instanceof SiteModel ? $site->getId() : $site)));

		$out = array();
		while($data = $stat->fetch()) {
			$ecfd = new EventCustomFieldDefinitionModel();
			$ecfd->setFromDataBaseRow($data);
			$out[] = array(
				'id'=>$ecfd->getId(),
				'extension_id'=>$ecfd->getExtensionId(),
				'type'=>$ecfd->getType(),
				'key'=>$ecfd->getKey(),
				'label'=>$ecfd->getLabel(),
				'is_active'=>$ecfd->getIsActive(),
			);
		}

		$stat = $DB->prepare("UPDATE site_information SET cached_event_custom_field_definitions=:cached_event_custom_field_definitions WHERE id=:id");
		$stat->execute(array(
			'id'=> ($site instanceof SiteModel ? $site->getId() : $site),
			'cached_event_custom_field_definitions'=>json_encode($out)
		));
	}

}