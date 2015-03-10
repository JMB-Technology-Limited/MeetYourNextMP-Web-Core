<?php


namespace com\meetyournextmp\tasks;
use com\meetyournextmp\models\HumanPopItInfoModel;
use com\meetyournextmp\repositories\HumanRepository;


/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class ImportPopItHumansTask extends \BaseTask {


	public function getExtensionId()
	{
		return "com.meetyournextmp";
	}

	public function getTaskId()
	{
		return "ImportPopItHumans";
	}

	public function getShouldRunAutomaticallyNow() {
		return $this->getLastRunEndedAgoInSeconds() > 4*60*60; // TODO $config
	}

	/** @return Array Roady to be made into JSON */
	protected function run()
	{


		// This would be nice, but then we wouldn't delete candidates mistakenly marked as standing
		// $url = 'http://yournextmp.popit.mysociety.org/api/v0.1/search/persons?q=_exists_:standing_in.2015.post_id&page=';

		$url = 'https://yournextmp.popit.mysociety.org/api/v0.1/persons?page=';



		$page = 0;
		$morePages = true;

		$countryRepo = new \repositories\CountryRepository();
		$areaRepo = new \repositories\AreaRepository();
		$gb = $countryRepo->loadByTwoCharCode("GB");
		$siteRepo = new \repositories\SiteRepository();
		$site = $siteRepo->loadById($this->app['config']->singleSiteID); // TODO assumes single site!

		$areaMapItInfoRepo = new \com\meetyournextmp\repositories\AreaMapItInfoRepository();
		$humanPopItInfoRepo = new \com\meetyournextmp\repositories\HumanPopItInfoRepository();
		$humanRepo = new HumanRepository();
		$areaRepo = new \repositories\AreaRepository();

		while($morePages) {

			++$page;

			$this->logVerbose( "Getting page ".$page );

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url.$page);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Meet Your Next MP');
			$data = curl_exec($ch);
			$response = curl_getinfo( $ch );
			curl_close($ch);

			if ($response['http_code'] != 200) {
				return array('result'=>'error','errorHTTPCode'=>200);
			}

			$dataObj = json_decode($data);

			foreach($dataObj->result as $humanObj) {

				$this->logVerbose( "Human ".$humanObj->name);

				/** @var $humanPopItInfo HumanPopItInfoModel */
				$humanPopItInfo = $humanPopItInfoRepo->getByPopItID($humanObj->id);
				$human = null;

				$standing = false;
				if (isset($humanObj->standing_in)) {
					$standingIn = get_object_vars($humanObj->standing_in);
					$standingIn2015 = null;

					if (isset($standingIn['2015'])) {
						$standingIn2015 = get_object_vars($standingIn['2015']);
						$standing = true;
					}


					$partyMemberships = isset($humanObj->party_memberships) ? get_object_vars($humanObj->party_memberships) : array();
					$partyMemberships2015 = null;
					if (isset($partyMemberships['2015'])) {
						$partyMemberships2015 = get_object_vars($partyMemberships['2015']);
					}
				}

				if ($standing) {

					$facebook = null;
					$twitter = null;
					$imageURL = null;
					$imageProxyURL = null;

					if (isset($humanObj->links)) {
						foreach($humanObj->links as $linkData) {
							$linkDataArray = get_object_vars($linkData);
							if ($linkDataArray['note'] == 'facebook page') {
								$facebook  = $linkDataArray['url'];
							}
						}
					}
					if (isset($humanObj->contact_details)) {
						foreach($humanObj->contact_details as $linkData) {
							$linkDataArray = get_object_vars($linkData);
							if ($linkDataArray['type'] == 'twitter') {
								$twitter = $linkDataArray['value'];
							}
						}
					}
					if (isset($humanObj->images)) {
						foreach($humanObj->images as $imageData) {
							$imageDataArray = get_object_vars($imageData);
							$imageProxyURL = $imageDataArray['proxy_url'];
							$imageURL = $imageDataArray['url'];
						}
					}

					$this->logVerbose( " - Standing in 2015!");

					$description = "Standing in ".$standingIn2015['name']." for ".$partyMemberships2015['name']."\n\nFind out more at https://yournextmp.com/person/".$humanObj->id;

					$bits = explode('/', $standingIn2015['mapit_url']);
					$mapItID = $bits[4];

					if ($humanPopItInfo) {

						$this->logVerbose(" - Found existing record");

						$human = $humanRepo->loadById($humanPopItInfo->getHumanId());
						if ($human->getIsDeleted()) {
							$this->logVerbose(" - Undeleting existing Human record");
							$humanRepo->undelete($human);
						}
						if ($human->getTitle() != $humanObj->name ||
							$description != $human->getDescription() ||
							$human->getEmail() != $humanObj->email ||
							$human->getTwitter() != $twitter ||
							$human->getImageUrl() != $imageURL) {

							$this->logVerbose(" - Updating existing Human record");
							$human->setTitle($humanObj->name);
							$human->setDescription($description);
							$human->setEmail($humanObj->email);
							$human->setTwitter($twitter);
							$human->setImageUrl($imageURL);
							$humanRepo->edit($human);
						}

						$humanPopItInfo->setMapitId($mapItID);
						$humanPopItInfo->setName($humanObj->name);
						if (isset($humanObj->gender)) {
							$humanPopItInfo->setGenderFromString($humanObj->gender);
						}
						if (isset($humanObj->email)) {
							$humanPopItInfo->setEmail($humanObj->email);
						}
						$humanPopItInfo->setParty($partyMemberships2015['name']);
						if (isset($humanObj->birth_date)) {
							$humanPopItInfo->setBirthDate($humanObj->birth_date);
						}
						$humanPopItInfo->setFacebook($facebook);
						$humanPopItInfo->setTwitter($twitter);
						$humanPopItInfo->setImageUrl($imageURL);
						$humanPopItInfo->setImageProxyUrl($imageProxyURL);

						$humanPopItInfoRepo->edit($humanPopItInfo);

					} else {

						$this->logVerbose(" - Adding new record");

						$human = new \com\meetyournextmp\models\HumanModel();
						$human->setTitle($humanObj->name);
						$human->setDescription($description);
						$human->setEmail($humanObj->email);
						$human->setTwitter($twitter);
						$human->setImageUrl($imageURL);

						$humanRepo->create($human, $site);

						$humanPopItInfo = new \com\meetyournextmp\models\HumanPopItInfoModel();
						$humanPopItInfo->setHumanId($human->getId());
						$humanPopItInfo->setPopitId($humanObj->id);
						$humanPopItInfo->setMapitId($mapItID);
						$humanPopItInfo->setName($humanObj->name);
						if (isset($humanObj->gender)) {
							$humanPopItInfo->setGenderMaleFromString($humanObj->gender);
						}
						if (isset($humanObj->email)) {
							$humanPopItInfo->setEmail($humanObj->email);
						}
						$humanPopItInfo->setParty($partyMemberships2015['name']);
						if (isset($humanObj->birth_date)) {
							$humanPopItInfo->setBirthDate($humanObj->birth_date);
						}
						$humanPopItInfo->setFacebook($facebook);
						$humanPopItInfo->setTwitter($twitter);
						$humanPopItInfo->setImageUrl($imageURL);
						$humanPopItInfo->setImageProxyUrl($imageProxyURL);

						$humanPopItInfoRepo->create($humanPopItInfo);


					}

					$areaMapItInfo = $areaMapItInfoRepo->getByMapItID($humanPopItInfo->getMapitId());
					if ($areaMapItInfo) {
						$area = $areaRepo->loadById($areaMapItInfo->getAreaId());
						if ($area) {
							$humanRepo->addHumanToArea($human, $area);
						}
					}

				} else {
					$this->logVerbose( " - NOT Standing in 2015!" );

					if ($humanPopItInfo) {
						$human = $humanRepo->loadById($humanPopItInfo->getHumanId());
						if ($human && !$human->getIsDeleted()) {
							$this->logVerbose( " - Deleteing ");
							$humanRepo->delete($human);
						}
					}
				}

			}

			$morePages = $dataObj->has_more;

		}

		return array('result'=>'ok');

	}

	public function getResultDataAsString(\models\TaskLogModel $taskLogModel) {
		if ($taskLogModel->getIsResultDataHaveKey("result") && $taskLogModel->getResultDataValue("result") == "ok") {
			return "Ok";
		} else {
			return "Fail";
		}

	}


}


