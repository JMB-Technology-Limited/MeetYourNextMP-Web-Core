<?php

namespace com\meetyournextmp;
use com\meetyournextmp\import\ImportCBTIElectionsHandler;
use com\meetyournextmp\reports\valuereports\NonDeletedNonCancelledEventsStartAtReport;
use com\meetyournextmp\repositories\AreaMapItInfoRepository;
use models\VenueModel;
use repositories\AreaRepository;
use Silex\Application;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class ExtensionMeetYourNextMP extends \BaseExtension {

	function __construct(Application $app)
	{
		parent::__construct($app);
	}


	public function getId() {
		return 'com.meetyournextmp';
	}

	public function getTitle() {
		return "Meet Your Next MP";
	}

	public function getDescription() {
		return "Meet Your Next MP";
	}



	public function getAddContentToEventShowPages($parameters) {
		return array(
			new AddHumansContentToEventShowPage($parameters, $this->app),
		);
	}


	public function addDetailsToVenue(VenueModel $venue) {

		if ($venue->getAddressCode() && !$venue->getAreaId()) {
			$area = $this->getAreaForPostCode(new PostcodeParser($venue->getAddressCode()));
			if ($area) {
				$venue->setAreaId($area->getId());
			}
		}

	}


	public function getAreaForPostCode(PostcodeParser $postcodeParser) {
		global $CONFIG;
		if ($postcodeParser->isValid()) {

			$memcachedConnection = null;


			$areaRepo = new AreaRepository();

			if ($CONFIG->memcachedServer) {
				$memcachedConnection = new \Memcached();
				$memcachedConnection->addServer($CONFIG->memcachedServer, $CONFIG->memcachedPort);
				$url = $memcachedConnection->get($postcodeParser->getCanonical());
				if ($url) {
					$urlBits = explode("/",$url);
					$urlBitsBits = explode("-", $urlBits[2]);
					$area = $areaRepo->loadBySlug($this->app['currentSite'], $urlBitsBits[0]);
					if ($area) {
						return $area;
					}
				}
			}

			$url = "http://mapit.mysociety.org/postcode/".urlencode($postcodeParser->getCanonical());

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Meet Your Next MP');
			$data = curl_exec($ch);
			$response = curl_getinfo( $ch );
			curl_close($ch);

			if ($response['http_code'] != 200) {
				return;
			}

			$dataObj = json_decode($data);

			$mapItId = $dataObj->shortcuts->WMC;


			if (!$mapItId) {
				return;
			}

			$repo = new AreaMapItInfoRepository();
			$areaMapIdInfo = $repo->getByMapItID($mapItId);

			if (!$areaMapIdInfo) {
				return;
			}

			$area = $areaRepo->loadById($areaMapIdInfo->getAreaId());

			if (!$area) {
				return;
			}

			if ($memcachedConnection) {
				$memcachedConnection->set($postcodeParser->getCanonical(), '/area/'.$area->getSlugForUrl(), 60*60*24*30);
			}

			return $area;

		}
	}

	public function getTasks() {
		return array(
			new \com\meetyournextmp\tasks\ImportPopItHumansTask($this->app),
			new \com\meetyournextmp\tasks\CacheNumbersTask($this->app),
			new \com\meetyournextmp\tasks\MadeDataDumpYNMPReadTask($this->app),
		);
	}


	public function getValueReports() {
		return array(
			new NonDeletedNonCancelledEventsStartAtReport(),
		);
	}

	public function getImportURLHandlers() {
		return array(
			new ImportCBTIElectionsHandler(),
		);
	}
}
