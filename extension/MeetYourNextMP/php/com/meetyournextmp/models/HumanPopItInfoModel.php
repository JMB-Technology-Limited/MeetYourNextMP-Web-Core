<?php

namespace com\meetyournextmp\models;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class HumanPopItInfoModel  {

	protected $id;
	protected $human_id;
	protected $popit_id;
	protected $mapit_id;
	protected $name;

	protected $gender_female;
	protected $gender_male;
	protected $email;
	protected $party;
	protected $birth_date;
	protected $facebook;
	protected $twitter;

	protected $image_url;
	protected $image_proxy_url;

	public function setFromDataBaseRow($data) {
		$this->id = $data['id'];
		$this->human_id = $data['human_id'];
		$this->popit_id = $data['popit_id'];
		$this->mapit_id = $data['mapit_id'];
		$this->name = $data['name'];
		$this->gender_female = $data['gender_female'];
		$this->gender_male = $data['gender_male'];
		$this->email = $data['email'];
		$this->party = $data['party'];
		$this->birth_date = $data['birth_date'];
		$this->facebook = $data['facebook'];
		$this->twitter = $data['twitter'];
		$this->image_proxy_url = $data['image_proxy_url'];
		$this->image_url = $data['image_url'];
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
	 * @param mixed $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param mixed $human_id
	 */
	public function setHumanId($human_id)
	{
		$this->human_id = $human_id;
	}

	/**
	 * @return mixed
	 */
	public function getHumanId()
	{
		return $this->human_id;
	}

	/**
	 * @param mixed $popit_id
	 */
	public function setPopitId($popit_id)
	{
		$this->popit_id = $popit_id;
	}

	/**
	 * @return mixed
	 */
	public function getPopitId()
	{
		return $this->popit_id;
	}

	/**
	 * @param mixed $mapit_id
	 */
	public function setMapitId($mapit_id)
	{
		$this->mapit_id = $mapit_id;
	}

	/**
	 * @return mixed
	 */
	public function getMapitId()
	{
		return $this->mapit_id;
	}

	/**
	 * @param mixed $birth_date
	 */
	public function setBirthDate($birth_date)
	{
		$this->birth_date = $birth_date;
	}

	/**
	 * @return mixed
	 */
	public function getBirthDate()
	{
		return $this->birth_date;
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
	 * @param mixed $facebook
	 */
	public function setFacebook($facebook)
	{
		$this->facebook = $facebook;
	}

	/**
	 * @return mixed
	 */
	public function getFacebook()
	{
		return $this->facebook;
	}

	/**
	 * @param mixed $gender_female
	 */
	public function setGenderFemale($gender_female)
	{
		$this->gender_female = $gender_female;
	}

	/**
	 * @return mixed
	 */
	public function getGenderFemale()
	{
		return $this->gender_female;
	}

	/**
	 * @param mixed $gender_male
	 */
	public function setGenderMale($gender_male)
	{
		$this->gender_male = $gender_male;
	}

	/**
	 * @return mixed
	 */
	public function getGenderMale()
	{
		return $this->gender_male;
	}

	/**
	 * @return mixed
	 */
	public function setGenderMaleFromString($in)
	{
		$in = strtolower(trim($in));
		if ($in == 'male') {
			$this->gender_male = true;
			$this->gender_female = false;
		} else if ($in == "female") {
			$this->gender_male = false;
			$this->gender_female = true;
		} else {
			$this->gender_male = false;
			$this->gender_female = false;
		}
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
	public function getParty()
	{
		return $this->party;
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
	 * @param mixed $image_proxy_url
	 */
	public function setImageProxyUrl($image_proxy_url)
	{
		$this->image_proxy_url = $image_proxy_url;
	}

	/**
	 * @return mixed
	 */
	public function getImageProxyUrl()
	{
		return $this->image_proxy_url;
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






}
