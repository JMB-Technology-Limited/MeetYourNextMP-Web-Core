<?php


namespace dbaccess;

use models\UserAccountModel;
use models\ImportURLModel;
use sysadmin\controllers\API2Application;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class ImportURLDBAccess {

	/** @var  \PDO */
	protected $db;

	/** @var  \TimeSource */
	protected $timesource;

	/** @var \UserAgent */
	protected $useragent;

	function __construct($db, $timesource, $useragent)
	{
		$this->db = $db;
		$this->timesource = $timesource;
		$this->useragent = $useragent;
	}


	protected $possibleFields = array('country_id','area_id','title','is_enabled','expired_at','group_id','is_manual_events_creation');

	public function update(ImportURLModel $importURL, $fields, UserAccountModel $user = null ) {
		$alreadyInTransaction = $this->db->inTransaction();

		// Make Information Data
		$fieldsSQL1 = array();
		$fieldsParams1 = array( 'id'=>$importURL->getId() );
		foreach($fields as $field) {
			$fieldsSQL1[] = " ".$field."=:".$field." ";
			if ($field == 'title') {
				$fieldsParams1['title'] = substr($importURL->getTitle(),0,VARCHAR_COLUMN_LENGTH_USED);
			} else if ($field == 'area_id') {
				$fieldsParams1['area_id'] = $importURL->getAreaId();
			} else if ($field == 'is_enabled') {
				$fieldsParams1['is_enabled'] = $importURL->getIsEnabled() ? 1 : 0;
			} else if ($field == 'is_manual_events_creation') {
				$fieldsParams1['is_manual_events_creation'] = $importURL->getIsManualEventsCreation() ? 1 : 0;
			} else if ($field == 'country_id') {
				$fieldsParams1['country_id'] = $importURL->getCountryId();
			} else if ($field == 'expired_at') {
				$fieldsParams1['expired_at'] = $importURL->getExpiredAt() ? $importURL->getExpiredAt()->format("Y-m-d H:i:s") : null;
			} else if ($field == 'group_id') {
				$fieldsParams1['group_id'] = ($importURL->getGroupId());
			}
		}

		// Make History Data
		$fieldsSQL2 = array('import_url_id','user_account_id','created_at','approved_at');
		$fieldsSQLParams2 = array(':import_url_id',':user_account_id',':created_at',':approved_at');
		$fieldsParams2 = array(
			'import_url_id'=>$importURL->getId(),
			'user_account_id'=>($user ? $user->getId() : null),
			'created_at'=>$this->timesource->getFormattedForDataBase(),
			'approved_at'=>$this->timesource->getFormattedForDataBase(),
		);
		foreach($this->possibleFields as $field) {
			if (in_array($field, $fields) || $field == 'title') {
				$fieldsSQL2[] = " ".$field." ";
				$fieldsSQLParams2[] = " :".$field." ";
				if ($field == 'title') {
					$fieldsParams2['title'] = substr($importURL->getTitle(),0,VARCHAR_COLUMN_LENGTH_USED);
				} else if ($field == 'area_id') {
					$fieldsParams2['area_id'] = $importURL->getAreaId();
				} else if ($field == 'country_id') {
					$fieldsParams2['country_id'] = $importURL->getCountryId();
				} else if ($field == 'group_id') {
					$fieldsParams2['group_id'] = $importURL->getGroupId();
				} else if ($field == 'expired_at') {
					$fieldsParams2['expired_at'] = $importURL->getExpiredAt() ? $importURL->getExpiredAt()->format("Y-m-d H:i:s") : null;
				} else if ($field == 'is_enabled') {
					$fieldsParams2['is_enabled'] = ($importURL->getIsEnabled()?1:0);
				} else if ($field == 'is_manual_events_creation') {
					$fieldsParams2['is_manual_events_creation'] = ($importURL->getIsManualEventsCreation()?1:0);
				}
				$fieldsSQL2[] = " ".$field."_changed ";
				$fieldsSQLParams2[] = " 0 ";
			} else {
				$fieldsSQL2[] = " ".$field."_changed ";
				$fieldsSQLParams2[] = " -2 ";
			}
		}



		try {
			if (!$alreadyInTransaction) {
				$this->db->beginTransaction();
			}

			// Information SQL
			$stat = $this->db->prepare("UPDATE import_url_information  SET ".implode(",", $fieldsSQL1)." WHERE id=:id");
			$stat->execute($fieldsParams1);

			// History SQL
			$stat = $this->db->prepare("INSERT INTO import_url_history (".implode(",",$fieldsSQL2).") VALUES (".implode(",",$fieldsSQLParams2).")");
			$stat->execute($fieldsParams2);

			if (!$alreadyInTransaction) {
				$this->db->commit();
			}
		} catch (Exception $e) {
			if (!$alreadyInTransaction) {
				$this->db->rollBack();
			}
			throw $e;
		}

	}


} 
