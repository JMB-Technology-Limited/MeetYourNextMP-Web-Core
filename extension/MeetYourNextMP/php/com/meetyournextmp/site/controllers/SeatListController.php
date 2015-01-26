<?php

namespace com\meetyournextmp\site\controllers;

use com\meetyournextmp\repositories\builders\AreaRepositoryBuilder;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class SeatListController  {

	function index(Request $request, Application $app) {


		$freeTextSearch = $request->query->get("freeTextSearch");

		$arb = new AreaRepositoryBuilder();
		$arb->setIsMapItAreaOnly(true);
		$arb->setIncludeDeleted(false);
		$arb->setLimit(1000);
		if ($freeTextSearch) {
			$arb->setFreeTextSearch($freeTextSearch);
		}

		return $app['twig']->render('site/seatlist/index.html.twig', array(
			'areas'=>$arb->fetchAll(),
			'freeTextSearch'=>$freeTextSearch,
		));

	}

}
