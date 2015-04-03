<?php

namespace com\meetyournextmp\repositories;

use com\meetyournextmp\models\HumanPopItInfoModel;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class HumanPopItInfoRepository {


	/** @return HumanPopItInfoModel */
	public function getByPopItID($id) {
		global $DB;
		$stat = $DB->prepare("SELECT human_popit_information.* FROM human_popit_information ".
				" WHERE human_popit_information.popit_id =:popit_id ");
		$stat->execute(array( 'popit_id'=> $id));
		if ($stat->rowCount() > 0) {
			$p = new HumanPopItInfoModel();
			$p->setFromDataBaseRow($stat->fetch());
			return $p;
		}
	}
	/** @return HumanPopItInfoModel */
	public function getByHumanID($id) {
		global $DB;
		$stat = $DB->prepare("SELECT human_popit_information.* FROM human_popit_information ".
				" WHERE human_popit_information.human_id =:human_id ");
		$stat->execute(array( 'human_id'=> $id));
		if ($stat->rowCount() > 0) {
			$p = new HumanPopItInfoModel();
			$p->setFromDataBaseRow($stat->fetch());
			return $p;
		}
	}

	public function create(HumanPopItInfoModel $humanPopItInfoModel) {
		global $DB;
		try {
			$DB->beginTransaction();


			$stat = $DB->prepare("INSERT INTO human_popit_information (human_id,popit_id,name,mapit_id,gender_female,gender_male,email,party,birth_date,facebook,twitter,image_url,image_proxy_url) ".
				"VALUES (:human_id,:popit_id,:name,:mapit_id,:gender_female,:gender_male,:email,:party,:birth_date,:facebook,:twitter,:image_url,:image_proxy_url) RETURNING id");
			$stat->execute(array(
				'human_id'=>$humanPopItInfoModel->getHumanId(),
				'popit_id'=>$humanPopItInfoModel->getPopitId(),
				'name'=>$humanPopItInfoModel->getName(),
				'mapit_id'=>$humanPopItInfoModel->getMapitId(),
				'gender_female'=>$humanPopItInfoModel->getGenderFemale() ? 1 : 0,
				'gender_male'=>$humanPopItInfoModel->getGenderMale() ? 1 : 0,
				'email'=>$humanPopItInfoModel->getEmail(),
				'party'=>$humanPopItInfoModel->getParty(),
				'birth_date'=>$humanPopItInfoModel->getBirthDate(),
				'facebook'=>$humanPopItInfoModel->getFacebook(),
				'twitter'=>$humanPopItInfoModel->getTwitter(),
				'image_url'=>$humanPopItInfoModel->getImageUrl(),
				'image_proxy_url'=>$humanPopItInfoModel->getImageProxyUrl(),
			));

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}

	}

	public function edit(HumanPopItInfoModel $humanPopItInfoModel) {
		global $DB;
		try {
			$DB->beginTransaction();


			$stat = $DB->prepare("UPDATE human_popit_information SET ".
				" name=:name,  mapit_id=:mapit_id , gender_female=:gender_female, gender_male=:gender_male, email=:email,  ".
				"  party=:party, birth_date=:birth_date,facebook=:facebook, twitter=:twitter, image_url=:image_url, image_proxy_url=:image_proxy_url  ".
				"WHERE id=:id");
			$stat->execute(array(
				'id'=>$humanPopItInfoModel->getId(),
				'mapit_id'=>$humanPopItInfoModel->getMapitId(),
				'name'=>$humanPopItInfoModel->getName(),
				'gender_female'=>$humanPopItInfoModel->getGenderFemale() ? 1 : 0,
				'gender_male'=>$humanPopItInfoModel->getGenderMale() ? 1 : 0,
				'email'=>$humanPopItInfoModel->getEmail(),
				'party'=>$humanPopItInfoModel->getParty(),
				'birth_date'=>$humanPopItInfoModel->getBirthDate(),
				'facebook'=>$humanPopItInfoModel->getFacebook(),
				'twitter'=>$humanPopItInfoModel->getTwitter(),
				'image_url'=>$humanPopItInfoModel->getImageUrl(),
				'image_proxy_url'=>$humanPopItInfoModel->getImageProxyUrl(),
			));

			$DB->commit();
		} catch (Exception $e) {
			$DB->rollBack();
		}

	}


}
