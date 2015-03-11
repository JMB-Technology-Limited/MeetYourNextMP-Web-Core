<?php

namespace com\meetyournextmp\site\controllers;

use com\meetyournextmp\repositories\builders\HumanRepositoryBuilder;
use com\meetyournextmp\repositories\HumanRepository;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class EventController extends \site\controllers\EventController {

	function editHumans($slug, Request $request, Application $app) {		
		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Event does not exist.");
		}
		
		$humanRepo = new HumanRepository();
			
		if ('POST' == $request->getMethod() && $request->request->get('CSFRToken') == $app['websession']->getCSFRToken()) {
			
			if ($request->request->get('addHuman')) {

				$data = $request->request->get('addHuman');
				if (!is_array($data)) {
					$data = array($data);
				}
				foreach($data as $slug) {
					$human = $humanRepo->loadBySlug($app['currentSite'], $slug);
					if ($human) {
						$humanRepo->addHumanToEvent($human, $this->parameters['event'], $app['currentUser']);
					}
				}

			} elseif ($request->request->get('removeHuman')) {
				$human = $humanRepo->loadBySlug($app['currentSite'], $request->request->get('removeHuman'));
				if ($human) {
					$humanRepo->removeHumanFromEvent($human, $this->parameters['event'], $app['currentUser']);
				}
				
			}


			if ($request->request->get("returnTo") == 'event') {
				return $app->redirect("/event/". $this->parameters['event']->getSlugforURL());
			}

		}
		
		$trb = new HumanRepositoryBuilder();
		$trb->setSite($app['currentSite']);
		$trb->setIncludeDeleted(false);
		$trb->setHumansForEvent($this->parameters['event']);
		$this->parameters['humans'] = $trb->fetchAll();

		$this->parameters['filterLimitToArea'] = $request->query->get('limitToArea') != 'no';
		$this->parameters['filterFreeTextSearch'] = $request->query->get('freeTextSearch');

		$trb = new HumanRepositoryBuilder();
		$trb->setSite($app['currentSite']);
		$trb->setIncludeDeleted(false);
		$trb->setHumansNotForEvent($this->parameters['event']);
		if ($this->parameters['area'] && $this->parameters['filterLimitToArea']) {
			$trb->setArea($this->parameters['area']);
		}
		if ($this->parameters['filterFreeTextSearch']) {
			$trb->setFreeTextsearch($this->parameters['filterFreeTextSearch']);
		}
		$trb->setLimit(200);

		$this->parameters['humansToAdd'] = $trb->fetchAll();

		return $app['twig']->render('site/event/edit.humans.html.twig', $this->parameters);
	}
	
	
}

