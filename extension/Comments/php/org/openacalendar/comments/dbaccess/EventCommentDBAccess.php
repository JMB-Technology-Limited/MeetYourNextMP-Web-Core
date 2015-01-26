<?php


namespace org\openacalendar\comments\dbaccess;

use models\UserAccountModel;
use org\openacalendar\comments\models\EventCommentModel;
use sysadmin\controllers\API2Application;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class EventCommentDBAccess {

	/** @var  \PDO */
	protected $db;

	/** @var  \TimeSource */
	protected $timesource;


	function __construct($db, $timesource)
	{
		$this->db = $db;
		$this->timesource = $timesource;
	}

	protected $possibleFields = array('title','comment','is_deleted','is_closed_by_admin');


	public function update(EventCommentModel $eventcomment, $fields, UserAccountModel $user = null ) {
		$alreadyInTransaction = $this->db->inTransaction();

		// Make Information Data
		$fieldsSQL1 = array();
		$fieldsParams1 = array( 'id'=>$eventcomment->getId() );
		foreach($fields as $field) {
			$fieldsSQL1[] = " ".$field."=:".$field." ";
			if ($field == 'title') {
				$fieldsParams1['title'] = substr($eventcomment->getTitle(),0,VARCHAR_COLUMN_LENGTH_USED);
			} else if ($field == 'comment') {
				$fieldsParams1['comment'] = $eventcomment->getDescription();
			} else if ($field == 'is_deleted') {
				$fieldsParams1['is_deleted'] = ($eventcomment->getIsDeleted()?1:0);
			} else if ($field == 'is_closed_by_admin') {
				$fieldsParams1['is_closed_by_admin'] = ($eventcomment->getIsClosedByAdmin()?1:0);
			}
		}

		// Make History Data
		$fieldsSQL2 = array('event_comment_id','user_account_id','created_at','approved_at');
		$fieldsSQLParams2 = array(':event_comment_id',':user_account_id',':created_at',':approved_at');
		$fieldsParams2 = array(
			'event_comment_id'=>$eventcomment->getId(),
			'user_account_id'=>($user ? $user->getId() : null),
			'created_at'=>$this->timesource->getFormattedForDataBase(),
			'approved_at'=>$this->timesource->getFormattedForDataBase(),
		);
		foreach($this->possibleFields as $field) {
			if (in_array($field, $fields) || $field == 'title') {
				$fieldsSQL2[] = " ".$field." ";
				$fieldsSQLParams2[] = " :".$field." ";
				if ($field == 'title') {
					$fieldsParams2['title'] = substr($eventcomment->getTitle(),0,VARCHAR_COLUMN_LENGTH_USED);
				} else if ($field == 'comment') {
					$fieldsParams2['comment'] = $eventcomment->getDescription();
				} else if ($field == 'is_deleted') {
					$fieldsParams2['is_deleted'] = ($eventcomment->getIsDeleted()?1:0);
				} else if ($field == 'is_closed_by_admin') {
					$fieldsParams2['is_closed_by_admin'] = ($eventcomment->getIsClosedByAdmin()?1:0);
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
			$stat = $this->db->prepare("UPDATE event_comment_information  SET ".implode(",", $fieldsSQL1)." WHERE id=:id");
			$stat->execute($fieldsParams1);

			// History SQL
			$stat = $this->db->prepare("INSERT INTO event_comment_history (".implode(",",$fieldsSQL2).") VALUES (".implode(",",$fieldsSQLParams2).")");
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
