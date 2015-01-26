<?php

namespace org\openacalendar\comments\models;
use models\AreaModel;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class AreaCommentModel {

	protected $id;
	protected $area_id;
	protected $slug;
	protected $title;
	protected $comment;
	protected $user_account_id;
	protected $is_deleted = false;
	protected $is_closed_by_admin = false;
	protected $created_at;

	/** @var  AreaModel */
	protected $area;

	protected $user_account_username;

	public function setFromDataBaseRow($data) {
		$this->id = $data['id'];
		$this->area_id = $data['area_id'];
		$this->slug = $data['slug'];
		$this->title = $data['title'];
		$this->comment = $data['comment'];
		$this->user_account_id = $data['user_account_id'];
		if (isset($data['user_account_username'])) {
			$this->user_account_username = $data['user_account_username'];
		}
		$this->is_deleted = $data['is_deleted'];
		$this->is_closed_by_admin = $data['is_closed_by_admin'];
		$utc = new \DateTimeZone("UTC");
		$this->created_at = new \DateTime($data['created_at'], $utc);
		if (isset($data['area_information_slug']) && $data['area_information_slug']) {
			$this->area = new AreaModel();
			$this->area->setTitle($data['area_information_title']);
			$this->area->setSlug($data['area_information_slug']);
		}
	}

	/**
	 * @param mixed $comment
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;
	}

	/**
	 * @return mixed
	 */
	public function getComment()
	{
		return $this->comment;
	}

	/**
	 * @param mixed $area_id
	 */
	public function setAreaId($area_id)
	{
		$this->area_id = $area_id;
	}

	/**
	 * @return mixed
	 */
	public function getAreaId()
	{
		return $this->area_id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param boolean $is_deleted
	 */
	public function setIsDeleted($is_deleted)
	{
		$this->is_deleted = $is_deleted;
	}

	/**
	 * @return boolean
	 */
	public function getIsDeleted()
	{
		return $this->is_deleted;
	}

	/**
	 * @param boolean $is_closed_by_admin
	 */
	public function setIsClosedByAdmin($is_closed_by_admin)
	{
		$this->is_closed_by_admin = $is_closed_by_admin;
	}

	/**
	 * @return boolean
	 */
	public function getIsClosedByAdmin()
	{
		return $this->is_closed_by_admin;
	}

	/**
	 * @param mixed $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param mixed $user_account_id
	 */
	public function setUserAccountId($user_account_id)
	{
		$this->user_account_id = $user_account_id;
	}

	/**
	 * @return mixed
	 */
	public function getUserAccountId()
	{
		return $this->user_account_id;
	}

	/**
	 * @param mixed $slug
	 */
	public function setSlug($slug)
	{
		$this->slug = $slug;
	}

	/**
	 * @return mixed
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	public function getCreatedAt() {
		return $this->created_at;
	}

	public function getCreatedAtTimeStamp() {
		return $this->created_at->getTimestamp();
	}

	/**
	 * @return mixed
	 */
	public function getUserAccountUsername()
	{
		return $this->user_account_username;
	}

	/**
	 * @return \models\AreaModel
	 */
	public function getArea()
	{
		return $this->area;
	}





}
