<?php

namespace com\meetyournextmp\site\controllers;

use com\meetyournextmp\repositories\builders\AreaRepositoryBuilder;
use com\meetyournextmp\repositories\builders\EventRepositoryBuilder;
use com\meetyournextmp\repositories\HumanRepository;
use repositories\builders\filterparams\EventFilterParams;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class HumanController {
	
	
	protected $parameters = array();
	
	protected function build($slug, Request $request, Application $app) {
		$this->parameters = array('currentUserWatchesGroup'=>false);
		
		if (strpos($slug, "-")) {
			$slug = array_shift(explode("-", $slug, 2));
		}
		
		$tr = new HumanRepository();
		$this->parameters['human'] = $tr->loadBySlug($app['currentSite'], $slug);
		if (!$this->parameters['human']) {
			return false;
		}
		
		
		return true;
	}
	
	function show($slug, Request $request, Application $app) {
		
		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Human does not exist.");
		}
		
		$this->parameters['eventListFilterParams'] = new EventFilterParams( new EventRepositoryBuilder() );
		$this->parameters['eventListFilterParams']->set($_GET);
		$this->parameters['eventListFilterParams']->getEventRepositoryBuilder()->setSite($app['currentSite']);
		$this->parameters['eventListFilterParams']->getEventRepositoryBuilder()->setHuman($this->parameters['human']);
		$this->parameters['eventListFilterParams']->getEventRepositoryBuilder()->setIncludeAreaInformation(true);
		$this->parameters['eventListFilterParams']->getEventRepositoryBuilder()->setIncludeVenueInformation(true);
		if ($app['currentUser']) {
			$this->parameters['eventListFilterParams']->getEventRepositoryBuilder()->setUserAccount($app['currentUser'], true);
		}

		$this->parameters['events'] = $this->parameters['eventListFilterParams']->getEventRepositoryBuilder()->fetchAll();

		$arb = new AreaRepositoryBuilder();
		$arb->setIncludeDeleted(false);
		$arb->setHuman($this->parameters['human']);
		$this->parameters['areas'] = $arb->fetchAll();

		return $app['twig']->render('site/human/show.html.twig', $this->parameters);
	}
	
	
		
}



