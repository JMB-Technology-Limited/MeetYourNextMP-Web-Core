<?php

namespace com\meetyournextmp\site\controllers;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class HelpController extends \site\controllers\AreaController {

	protected $parameters  = array();

	function index(Request $request, Application $app) {
		return $app['twig']->render('site/help/index.html.twig', $this->parameters);
	}

	function indexWho(Request $request, Application $app) {
		$erb = new \com\meetyournextmp\repositories\builders\EventRepositoryBuilder();
		$erb->setIncludeNoHumansOnly(true);
		$erb->setAfterNow();
		$erb->setIncludeDeleted(false);
		$erb->setIncludeCancelled(false);
		$erb->setLimit(50);
		$this->parameters['events'] = $erb->fetchAll();

		return $app['twig']->render('site/help/who.html.twig', $this->parameters);
	}

	function indexNoEventsSeat(Request $request, Application $app) {

		$arb = new \com\meetyournextmp\repositories\builders\AreaRepositoryBuilder();
		$arb->setIsMapItAreaOnly(true);
		$arb->setIncludeDeleted(false);
		$arb->setIncludeAreasWithNoEventsOnly(true);
		$arb->setLimit(800);
		$this->parameters['areas'] = $arb->fetchAll();


		return $app['twig']->render('site/help/noevents.seats.html.twig', $this->parameters);
	}
}
