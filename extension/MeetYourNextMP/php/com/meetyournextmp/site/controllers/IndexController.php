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
class IndexController {


	function formedia(Application $app) {

		$trb = new HumanRepositoryBuilder();
		$trb->setSite($app['currentSite']);
		$trb->setIncludeDeleted(false);
		$humans = $trb->fetchAll();

		return $app['twig']->render('site/index/formedia.html.twig', array(
			'humans'=>$humans,
		));
	}



}


