<?php

namespace com\meetyournextmp\models;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class AreaMapItInfoModel  {

	protected $id;
	protected $area_id;
	protected $name;
	// Really for future apps, should be one code field with JSON.
	protected $code_gss;
	protected $code_unit_id;
	protected $mapit_id;

	public function setFromDataBaseRow($data) {
		$this->id = $data['id'];
		$this->area_id = $data['area_id'];
		$this->name = $data['name'];
		$this->code_gss = $data['code_gss'];
		$this->code_unit_id = $data['code_unit_id'];
		$this->mapit_id = $data['mapit_id'];

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
	 * @param mixed $code_gss
	 */
	public function setCodeGss($code_gss)
	{
		$this->code_gss = $code_gss;
	}

	/**
	 * @return mixed
	 */
	public function getCodeGss()
	{
		return $this->code_gss;
	}

	/**
	 * @param mixed $code_unit_id
	 */
	public function setCodeUnitId($code_unit_id)
	{
		$this->code_unit_id = $code_unit_id;
	}

	/**
	 * @return mixed
	 */
	public function getCodeUnitId()
	{
		return $this->code_unit_id;
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


}
