<?php

namespace com\meetyournextmp\tasks;
use com\meetyournextmp\models\HumanPopItInfoModel;
use com\meetyournextmp\repositories\builders\EventRepositoryBuilder;
use com\meetyournextmp\repositories\HumanRepository;

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

		file_put_contents(APP_ROOT_DIR.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'numbers.json', json_encode($out));



		return array('result'=>'ok');
	}

}

