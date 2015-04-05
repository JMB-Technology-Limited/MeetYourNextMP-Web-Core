<?php

namespace com\meetyournextmp\models;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class HumanModel  {


	protected $id;
	protected $site_id;
	protected $slug;
	protected $title;
	protected $description;
	protected $is_deleted;
	protected $twitter;
	protected $party;
	protected $email;
	protected $image_url;
	protected $url;
	protected $is_duplicate_of_id;

	public function setFromDataBaseRow($data) {
		$this->id = $data['id'];
		$this->site_id = $data['site_id'];
		$this->slug = $data['slug'];
		$this->title = $data['title'];
		$this->description = $data['description'];
		$this->is_deleted = $data['is_deleted'];
		$this->twitter = $data['twitter'];
		$this->party = $data['party'];
		$this->email = $data['email'];
		$this->image_url = $data['image_url'];
		$this->url = $data['url'];
		$this->is_duplicate_of_id = $data['is_duplicate_of_id'];
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getSiteId() {
		return $this->site_id;
	}

	public function setSiteId($site_id) {
		$this->site_id = $site_id;
	}

	public function getSlug() {
		return $this->slug;
	}

	public function getSlugForUrl() {
		$unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
			'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
			'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
			'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
			'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y',
			'Ğ'=>'G', 'İ'=>'I', 'Ş'=>'S', 'ğ'=>'g', 'ı'=>'i', 'ş'=>'s', 'ü'=>'u',
			'ă'=>'a', 'Ă'=>'A', 'ș'=>'s', 'Ș'=>'S', 'ț'=>'t', 'Ț'=>'T'
		);
		$extraSlug = strtr( trim($this->title), $unwanted_array );
		$extraSlug = preg_replace("/[^a-zA-Z0-9\-]+/", "", str_replace(" ", "-",strtolower($extraSlug)));
		// Do it twice to get ---'s turned to -'s to.
		$extraSlug = str_replace("--", "-", $extraSlug);
		$extraSlug = str_replace("--", "-", $extraSlug);
		return $this->slug.($extraSlug?"-".$extraSlug:'');
	}

	public function setSlug($slug) {
		$this->slug = $slug;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		$this->title = $title;
	}


	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
	}


	public function getIsDeleted() {
		return $this->is_deleted;
	}

	public function setIsDeleted($is_deleted) {
		$this->is_deleted = $is_deleted;
	}

	/**
	 * @param mixed $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param mixed $image_url
	 */
	public function setImageUrl($image_url)
	{
		$this->image_url = $image_url;
	}

	/**
	 * @return mixed
	 */
	public function getImageUrl()
	{
		return $this->image_url;
	}

	/**
	 * @param mixed $twitter
	 */
	public function setTwitter($twitter)
	{
		$this->twitter = $twitter;
	}

	/**
	 * @return mixed
	 */
	public function getTwitter()
	{
		return $this->twitter;
	}

	/**
	 * @param mixed $is_duplicate_of_id
	 */
	public function setIsDuplicateOfId($is_duplicate_of_id)
	{
		$this->is_duplicate_of_id = $is_duplicate_of_id;
	}

	/**
	 * @return mixed
	 */
	public function getIsDuplicateOfId()
	{
		return $this->is_duplicate_of_id;
	}

	/**
	 * @return mixed
	 */
	public function getParty()
	{
		return $this->party;
	}

	/**
	 * @param mixed $party
	 */
	public function setParty($party)
	{
		$this->party = $party;
	}

	/**
	 * @return mixed
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param mixed $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}





}
