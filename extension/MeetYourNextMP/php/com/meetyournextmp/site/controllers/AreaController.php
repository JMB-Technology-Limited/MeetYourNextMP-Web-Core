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
class AreaController extends \site\controllers\AreaController {


	function humans($slug, Request $request, Application $app) {

		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Area does not exist.");
		}

		$trb = new HumanRepositoryBuilder();
		$trb->setSite($app['currentSite']);
		$trb->setIncludeDeleted(false);
		$trb->setArea($this->parameters['area']);
		$trb->setLimit(200);
		$this->parameters['humans'] = $trb->fetchAll();

		return $app['twig']->render('site/area/humans.html.twig', $this->parameters);
	}

	function tweetHumans($slug, Request $request, Application $app) {

		if (!$this->build($slug, $request, $app)) {
			$app->abort(404, "Area does not exist.");
		}

		$trb = new HumanRepositoryBuilder();
		$trb->setSite($app['currentSite']);
		$trb->setIncludeDeleted(false);
		$trb->setArea($this->parameters['area']);
		$trb->setLimit(200);
		$this->parameters['humans'] = array();
		foreach($trb->fetchAll() as $human) {
			if ($human->getTwitter()) {
				$this->parameters['humans'][] = $human;
			}
		}

		// =================================== If no Candidates
		if (!$this->parameters['humans']) {
			return $app['twig']->render('site/area/tweetToCandidates.noHumans.html.twig', $this->parameters);
		}

		// =================================== If Tweeting

		if ($request->request->has("action") and $request->request->has("action") == 'tweet') {

			$humanSlugs = $request->request->get('human');

			$foundHumans = false;
			$tweet = ".";

			if (is_array($humanSlugs)) {
				foreach ($this->parameters['humans'] as $human) {
					if (in_array($human->getSlug(), $humanSlugs)) {
						$tweet .= '@' . $human->getTwitter() . ' ';
						$foundHumans = true;
					}
				}
			}

			if ($foundHumans) {
				$tweet .= 'Could you add your #ge2015 events to '.
					'https://'.$app['config']->webIndexDomain.'/area/'.$this->parameters['area']->getSlugForURL();

				$url = 'https://twitter.com/intent/tweet?text='.urlencode($tweet);
				return $app->redirect($url);
			}
		}



		// =================================== Show Form


		return $app['twig']->render('site/area/tweetToCandidates.html.twig', $this->parameters);






	}


}
