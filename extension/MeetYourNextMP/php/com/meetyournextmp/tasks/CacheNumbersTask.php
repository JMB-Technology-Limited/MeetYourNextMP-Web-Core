<?php

namespace com\meetyournextmp\tasks;
use com\meetyournextmp\models\HumanPopItInfoModel;
use com\meetyournextmp\repositories\builders\EventRepositoryBuilder;
use com\meetyournextmp\repositories\HumanRepository;
use repositories\AreaRepository;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class CacheNumbersTask extends \BaseTask {


	public function getExtensionId()
	{
		return "com.meetyournextmp";
	}

	public function getTaskId()
	{
		return "CacheNumbersTask";
	}

	public function getShouldRunAutomaticallyNow() {
		return $this->getLastRunEndedAgoInSeconds() > 4*60*60; // TODO $config
	}

	protected function run()
	{

		$siteRepo = new \repositories\SiteRepository();
		$site = $siteRepo->loadById($this->app['config']->singleSiteID); // TODO assumes single site!

		$out = array();

		$erb = new EventRepositoryBuilder();
		$erb->setIncludeDeleted(false);
		$erb->setIncludeCancelled(false);
		$erb->setSite($site);
		$out['countEventsTotal'] = count($erb->fetchAll());

		$erb = new EventRepositoryBuilder();
		$erb->setIncludeDeleted(false);
		$erb->setIncludeCancelled(false);
		$erb->setSite($site);
		$erb->setBefore($this->app['timesource']->getDateTime());
		$out['countEventsBeforeNow'] = count($erb->fetchAll());

		$erb = new EventRepositoryBuilder();
		$erb->setIncludeDeleted(false);
		$erb->setIncludeCancelled(false);
		$erb->setSite($site);
		$erb->setAfterNow();
		$out['countEventsAfterNow'] = count($erb->fetchAll());

		$arb = new \com\meetyournextmp\repositories\builders\AreaRepositoryBuilder();
		$arb->setIsMapItAreaOnly(true);
		$arb->setIncludeDeleted(false);
		$arb->setIncludeAreasWithNoEventsOnly(true);
		$arb->setLimit(800);
		$out['countSeatsWithNoEvents'] = count($arb->fetchAll());

		$areaRepo = new AreaRepository();

		foreach(array(3=>'countEventsInScotland',1=>'countEventsInEngland',2=>'countEventsInWales',4=>'countEventsInNIreland',712=>'countEventsInGreaterLondon') as $areaSlug=>$key) {
			$erb = new EventRepositoryBuilder();
			$erb->setIncludeDeleted(false);
			$erb->setIncludeCancelled(false);
			$erb->setSite($site);
			$erb->setArea($areaRepo->loadBySlug($site, $areaSlug));
			$out[$key] = count($erb->fetchAll());
		}

		file_put_contents(APP_ROOT_DIR.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'numbers.json', json_encode($out));



		return array('result'=>'ok');
	}

}

