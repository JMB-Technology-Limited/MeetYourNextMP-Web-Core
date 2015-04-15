<?php

namespace com\meetyournextmp\tasks;
use com\meetyournextmp\models\HumanPopItInfoModel;
use com\meetyournextmp\repositories\AreaMapItInfoRepository;
use com\meetyournextmp\repositories\builders\AreaRepositoryBuilder;
use com\meetyournextmp\repositories\builders\EventRepositoryBuilder;
use com\meetyournextmp\repositories\builders\HumanRepositoryBuilder;
use com\meetyournextmp\repositories\HumanPopItInfoRepository;
use com\meetyournextmp\repositories\HumanRepository;
use models\AreaModel;
use models\CountryModel;
use models\EventModel;
use models\VenueModel;
use reports\SeriesOfValueByTimeReport;
use repositories\AreaRepository;
use repositories\CountryRepository;
use repositories\VenueRepository;
use Silex\Application;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class MadeDataDumpYNMPReadTask extends \BaseTask {


	protected $localTimeZone;

	public function getExtensionId()
	{
		return "com.meetyournextmp";
	}

	public function getTaskId()
	{
		return "MadeDataDumpYNMPReadTask";
	}

	public function getShouldRunAutomaticallyNow() {
		return $this->getLastRunEndedAgoInSeconds() > 1*60*60;
	}

	protected function run()
	{

		$this->localTimeZone = new \DateTimeZone("Europe/London");

		$siteRepo = new \repositories\SiteRepository();
		$site = $siteRepo->loadById($this->app['config']->singleSiteID); // TODO assumes single site!

		$areaRepository = new AreaRepository();

		$venueRepository = new VenueRepository();

		$countryRepository = new CountryRepository();
		$countries = array();

		$humanPopItRepository = new HumanPopItInfoRepository();

		$areaMapItRepo = new AreaMapItInfoRepository();

		$out = array('data'=>array(), 'areasPastEvents'=>array());

		$erb = new EventRepositoryBuilder();
		$erb->setSite($site);
		$erb->setIncludeDeleted(true);
		$erb->setIncludeCancelled(true);
		$erb->setAfterNow();

		foreach($erb->fetchAll() as $event) {

			$venue = null;
			$country = null;
			$area = null;
			$humans = array();

			if ($event->getCountryId()) {
				if (!isset($countries[$event->getCountryId()])) {
					$countries[$event->getCountryId()] = $countryRepository->loadById($event->getCountryId());
				}
				$country = $countries[$event->getCountryId()];
			}

			if ($event->getVenueId()) {
				$venue = $venueRepository->loadById($event->getVenueId());
			}

			if ($event->getAreaId()) {
				$area = $areaRepository->loadById($event->getAreaId());
			} else if ($venue && $venue->getAreaId()) {
				$area = $areaRepository->loadById($venue->getAreaId());
			}

			$thisOut = $this->addEvent($event, $venue, $area, $country);

			$thisOut['humans'] = array();

			$mapitids = array();

			if ($area) {
				$areamapit = $areaMapItRepo->getByAreaID($area->getId());
				if ($areamapit) {
					$mapitids[] = $areamapit->getMapitId();
				}
			}

			$hrb = new HumanRepositoryBuilder();
			$hrb->setHumansForEvent($event);
			foreach($hrb->fetchAll() as $human) {
				$popit = $humanPopItRepository->getByHumanID($human->getId());

				$thisOut['humans'][] = array(
					'popit_id'=>$popit->getPopitId()
				);

				$arb = new AreaRepositoryBuilder();
				$arb->setIncludeDeleted(false);
				$arb->setHuman($human);
				foreach($arb->fetchAll() as $areaForHuman) {
					if (!$area || $area->getId() != $areaForHuman->getId()) {
						$areamapit = $areaMapItRepo->getByAreaID($areaForHuman->getId());
						if ($areamapit) {
							$mapitids[] = $areamapit->getMapitId();
						}
					}
				}

			}

			$thisOut['mapitids'] = array_values(array_unique($mapitids));

			$out['data'][] = $thisOut;


		}

		$arb = new \com\meetyournextmp\repositories\builders\AreaRepositoryBuilder();
		$arb->setLimit(1000);
		$arb->setIncludeDeleted(false);
		$arb->setIsMapItAreaOnly(true);
		foreach($arb->fetchAll() as $area) {

			$erb = new EventRepositoryBuilder();
			$erb->setIncludeDeleted(false);
			$erb->setIncludeCancelled(false);
			$erb->setArea($area);
			$erb->setBeforeNow();

			$areamapit = $areaMapItRepo->getByAreaID($area->getId());

			$out['areasPastEvents'][$areamapit->getMapitId()] = $erb->fetchCount();

		}


		file_put_contents(APP_ROOT_DIR.DIRECTORY_SEPARATOR.'webSingleSite'.DIRECTORY_SEPARATOR.'datadump'.DIRECTORY_SEPARATOR.'ynmpread.json', json_encode($out));


		return array('result'=>'ok');
	}



	public function addEvent(EventModel $event,  VenueModel $venue = null,
							 AreaModel $area = null, CountryModel $country = null) {
		global $CONFIG;

		$out = array(
			'slug'=>$event->getSlug(),
			'slugforurl'=>$event->getSlugForUrl(),
			'summary'=> $event->getSummary(),
			'summaryDisplay'=> $event->getSummaryDisplay(),
			'description'=> ($event->getDescription()?$event->getDescription():''),
			'deleted'=> (boolean)$event->getIsDeleted(),
			'cancelled'=> (boolean)$event->getIsCancelled(),
			'is_physical'=> (boolean)$event->getIsPhysical(),
			'is_virtual'=> (boolean)$event->getIsVirtual(),
		);

		$out['siteurl'] = $CONFIG->isSingleSiteMode ?
			'http://'.$CONFIG->webSiteDomain.'/event/'.$event->getSlugForUrl() :
			'http://'.($this->site?$this->site->getSlug():$event->getSiteSlug()).".".$CONFIG->webSiteDomain.'/event/'.$event->getSlugForUrl();
		$out['url'] = $event->getUrl() && filter_var($event->getUrl(), FILTER_VALIDATE_URL) ? $event->getUrl() : $out['siteurl'];
		$out['ticket_url'] = $event->getTicketUrl() && filter_var($event->getTicketUrl(), FILTER_VALIDATE_URL) ? $event->getTicketUrl() : null;
		$out['timezone'] = $event->getTimezone();

		$startLocal = clone $event->getStartAt();
		$startLocal->setTimeZone($this->localTimeZone);
		$startTimeZone = clone $event->getStartAt();
		$startTimeZone->setTimeZone(new \DateTimeZone($event->getTimezone()));
		$out['start'] = array(
			'timestamp'=>$event->getStartAt()->getTimestamp(),
			'rfc2882utc'=>$event->getStartAt()->format('r'),
			'rfc2882local'=>$startLocal->format('r'),
			'displaylocal'=>$startLocal->format('D j M Y h:ia'),
			'yearlocal'=>$startLocal->format('Y'),
			'monthlocal'=>$startLocal->format('n'),
			'daylocal'=>$startLocal->format('j'),
			'hourlocal'=>$startLocal->format('G'),
			'minutelocal'=>$startLocal->format('i'),
			'rfc2882timezone'=>$startTimeZone->format('r'),
			'displaytimezone'=>$startTimeZone->format('D j M Y h:ia'),
			'yeartimezone'=>$startTimeZone->format('Y'),
			'monthtimezone'=>$startTimeZone->format('n'),
			'daytimezone'=>$startTimeZone->format('j'),
			'hourtimezone'=>$startTimeZone->format('G'),
			'minutetimezone'=>$startTimeZone->format('i'),

		);


		$endLocal = clone $event->getEndAt();
		$endLocal->setTimeZone($this->localTimeZone);
		$endTimeZone = clone $event->getEndAt();
		$endTimeZone->setTimeZone(new \DateTimeZone($event->getTimezone()));
		$out['end'] = array(
			'timestamp'=>$event->getEndAt()->getTimestamp(),
			'rfc2882utc'=>$event->getEndAt()->format('r'),
			'rfc2882local'=>$endLocal->format('r'),
			'displaylocal'=>$endLocal->format('D j M Y h:ia'),
			'yearlocal'=>$endLocal->format('Y'),
			'monthlocal'=>$endLocal->format('n'),
			'daylocal'=>$endLocal->format('j'),
			'hourlocal'=>$endLocal->format('G'),
			'minutelocal'=>$endLocal->format('i'),
			'rfc2882timezone'=>$endTimeZone->format('r'),
			'displaytimezone'=>$endTimeZone->format('D j M Y h:ia'),
			'yeartimezone'=>$endTimeZone->format('Y'),
			'monthtimezone'=>$endTimeZone->format('n'),
			'daytimezone'=>$endTimeZone->format('j'),
			'hourtimezone'=>$endTimeZone->format('G'),
			'minutetimezone'=>$endTimeZone->format('i'),
		);


		if ($venue) {
			$out['venue'] = array(
				'slug'=>$venue->getSlug(),
				'title'=>$venue->getTitle(),
				'description'=>$venue->getDescription(),
				'address'=>$venue->getAddress(),
				'addresscode'=>$venue->getAddressCode(),
				'lat'=>$venue->getLat(),
				'lng'=>$venue->getLng(),
			);
		}

		if ($area) {
			$out['areas'] = array(array(
				'slug'=>$area->getSlug(),
				'title'=>$area->getTitle(),
			));
		}

		if ($country) {
			$out['country'] = array(
				'title'=>$country->getTitle(),
			);
		}

		return $out;
	}


}


