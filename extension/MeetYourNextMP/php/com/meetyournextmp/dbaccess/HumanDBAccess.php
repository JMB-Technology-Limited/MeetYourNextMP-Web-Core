<?php

namespace com\meetyournextmp\dbaccess;


use com\meetyournextmp\models\HumanModel;
use models\UserAccountModel;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class HumanDBAccess {

	/** @var  \PDO */
	protected $db;

	/** @var  \TimeSource */
	protected $timesource;


	function __construct($db, $timesource)
	{
		$this->db = $db;
		$this->timesource = $timesource;
	}

	protected $possibleFields = array('title','description','is_deleted','email','twitter','image_url','is_duplicate_of_id');


	public function update(HumanModel $human, $fields, UserAccountModel $user = null ) {
		$alreadyInTransaction = $this->db->inTransaction();

		// Make Information Data
		$fieldsSQL1 = array();
		$fieldsParams1 = array( 'id'=>$human->getId() );
		foreach($fields as $field) {
			$fieldsSQL1[] = " ".$field."=:".$field." ";
			if ($field == 'title') {
				$fieldsParams1['title'] = substr($human->getTitle(),0,VARCHAR_COLUMN_LENGTH_USED);
			} else if ($field == 'description') {
				$fieldsParams1['description'] = $human->getDescription();
			} else if ($field == 'email') {
				$fieldsParams1['email'] = $human->getEmail();
			} else if ($field == 'twitter') {
				$fieldsParams1['twitter'] = $human->getTwitter();
			} else if ($field == 'image_url') {
				$fieldsParams1['image_url'] = $human->getImageUrl();
			} else if ($field == 'is_deleted') {
				$fieldsParams1['is_deleted'] = ($human->getIsDeleted()?1:0);
			} else if ($field == 'is_duplicate_of_id') {
				$fieldsParams1['is_duplicate_of_id'] = $human->getIsDuplicateOfId();
			}
		}

		// Make History Data
		$fieldsSQL2 = array('human_id','user_account_id','created_at','approved_at');
		$fieldsSQLParams2 = array(':human_id',':user_account_id',':created_at',':approved_at');
		$fieldsParams2 = array(
			'human_id'=>$human->getId(),
			'user_account_id'=>($user ? $user->getId() : null),
			'created_at'=>$this->timesource->getFormattedForDataBase(),
			'approved_at'=>$this->timesource->getFormattedForDataBase(),
		);
		foreach($this->possibleFields as $field) {
			if (in_array($field, $fields) || $field == 'title') {
				$fieldsSQL2[] = " ".$field." ";
				$fieldsSQLParams2[] = " :".$field." ";
				if ($field == 'title') {
					$fieldsParams2['title'] = substr($human->getTitle(),0,VARCHAR_COLUMN_LENGTH_USED);
				} else if ($field == 'description') {
					$fieldsParams2['description'] = $human->getDescription();
				} else if ($field == 'image_url') {
					$fieldsParams2['image_url'] = $human->getImageUrl();
				} else if ($field == 'email') {
					$fieldsParams2['email'] = $human->getEmail();
				} else if ($field == 'twitter') {
					$fieldsParams2['twitter'] = $human->getTwitter();
				} else if ($field == 'is_deleted') {
					$fieldsParams2['is_deleted'] = ($human->getIsDeleted()?1:0);
				} else if ($field == 'is_duplicate_of_id') {
					$fieldsParams2['is_duplicate_of_id'] = $human->getIsDuplicateOfId();
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
			$stat = $this->db->prepare("UPDATE human_information  SET ".implode(",", $fieldsSQL1)." WHERE id=:id");
			$stat->execute($fieldsParams1);

			// History SQL
			$stat = $this->db->prepare("INSERT INTO human_history (".implode(",",$fieldsSQL2).") VALUES (".implode(",",$fieldsSQLParams2).")");
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
