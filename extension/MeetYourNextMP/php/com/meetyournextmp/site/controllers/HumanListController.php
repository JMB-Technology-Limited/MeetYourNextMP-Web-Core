<?php

namespace com\meetyournextmp\site\controllers;

use com\meetyournextmp\repositories\builders\HumanRepositoryBuilder;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;


/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class HumanListController {
	
	
	function index(Application $app, Request $request) {

		if ($request->query->get('search')) {
			$trb = new HumanRepositoryBuilder();
			$trb->setSite($app['currentSite']);
			$trb->setIncludeDeleted(false);
			$trb->setFreeTextsearch($request->query->get('search'));
			return $app['twig']->render('site/humanlist/index.html.twig', array(
				'humans'=>$trb->fetchAll(),
				'search'=>$request->query->get('search'),
			));
		} else {
			return $app['twig']->render('site/humanlist/index.html.twig', array(
				'humans'=>array(),
				'search'=>null,
			));
		}

	}
	
		
	
}


