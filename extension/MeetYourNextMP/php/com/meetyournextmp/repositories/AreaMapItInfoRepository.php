<?php

namespace com\meetyournextmp\repositories;
use com\meetyournextmp\models\AreaMapItInfoModel;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class AreaMapItInfoRepository {

	public function getByCodeGSS($code) {
		global $DB;
		$stat = $DB->prepare("SELECT area_mapit_information.* FROM area_mapit_information ".
				" WHERE area_mapit_information.code_gss =:code ");
		$stat->execute(array( 'code'=> $code));
		if ($stat->rowCount() > 0) {
			$a = new AreaMapItInfoModel();
			$a->setFromDataBaseRow($stat->fetch());
			return $a;
		}
	}

	public function getByAreaID($id) {
		global $DB;
		$stat = $DB->prepare("SELECT area_mapit_information.* FROM area_mapit_information ".
				" WHERE area_mapit_information.area_id =:area_id ");
		$stat->execute(array( 'area_id'=> $id));
		if ($stat->rowCount() > 0) {
			$a = new AreaMapItInfoModel();
			$a->setFromDataBaseRow($stat->fetch());
			return $a;
		}
	}

	public function getByMapItID($id) {
		global $DB;
		$stat = $DB->prepare("SELECT area_mapit_information.* FROM area_mapit_information ".
				" WHERE area_mapit_information.mapit_id =:mapit_id ");
		$stat->execute(array( 'mapit_id'=> $id));
		if ($stat->rowCount() > 0) {
			$a = new AreaMapItInfoModel();
			$a->setFromDataBaseRow($stat->fetch());
			return $a;
		}
	}

	public function getByName($name) {
		global $DB;
		$stat = $DB->prepare("SELECT area_mapit_information.* FROM area_mapit_information ".
				" WHERE lower(area_mapit_information.name) =:name ");
		$stat->execute(array( 'name'=> trim(strtolower($name))));
		if ($stat->rowCount() > 0) {
			$a = new AreaMapItInfoModel();
			$a->setFromDataBaseRow($stat->fetch());
			return $a;
		}
	}


	public function create(AreaMapItInfoModel $areaMapItInfoModel) {
		global $DB;
		try {
			$DB->beginTransaction();


			$stat = $DB->prepare("INSERT INTO area_mapit_information (area_id, name, code_gss,code_unit_id, mapit_id) ".
				"VALUES (:area_id, :name, :code_gss, :code_unit_id, :mapit_id) RETURNING id");
			$stat->execute(array(
				'area_id'=>$areaMapItInfoModel->getAreaId(),
				'name'=>$areaMapItInfoModel->getName(),
				'code_gss'=>$areaMapItInfoModel->getCodeGss(),
				'code_unit_id'=>$areaMapItInfoModel->getCodeUnitId(),
				'mapit_id'=>$areaMapItInfoModel->getMapitId(),
			));

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}

	}

	public function edit(AreaMapItInfoModel $areaMapItInfoModel) {
		global $DB;
		try {
			$DB->beginTransaction();


			$stat = $DB->prepare("UPDATE area_mapit_information SET ".
				"area_id=:area_id, name=:name, code_gss=:code_gss, code_unit_id=:code_unit_id, mapit_id=:mapit_id  ".
				"WHERE id=:id");
			$stat->execute(array(
				'id'=>$areaMapItInfoModel->getId(),
				'area_id'=>$areaMapItInfoModel->getAreaId(),
				'name'=>$areaMapItInfoModel->getName(),
				'code_gss'=>$areaMapItInfoModel->getCodeGss(),
				'code_unit_id'=>$areaMapItInfoModel->getCodeUnitId(),
				'mapit_id'=>$areaMapItInfoModel->getMapitId(),
			));

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}

	}


}
