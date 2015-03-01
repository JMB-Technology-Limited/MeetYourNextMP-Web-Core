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
		$this->parameters['humans'] = $trb->fetchAll();

		$tweet = "";
		foreach($this->parameters['humans'] as $human) {
			if ($human->getTwitter()) {
				$tweet .= '@'. $human->getTwitter().' ';
			}
		}

		$tweet .= 'Could you add your #ge2015 events to '.
			'https://'.$app['config']->webIndexDomain.'/area/'.$this->parameters['area']->getSlugForURL();

		if ($tweet) {

			$url = 'https://twitter.com/intent/tweet?text='.urlencode($tweet);
			return $app->redirect($url);

		} else {

			// TODO better?
			return $app['twig']->render('site/area/humans.html.twig', $this->parameters);

		}



	}


}
