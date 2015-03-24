<?php

namespace com\meetyournextmp\siteapi1\controllers;

use api1exportbuilders\EventListCSVBuilder;
use com\meetyournextmp\repositories\AreaMapItInfoRepository;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use repositories\AreaRepository;
use api1exportbuilders\EventListICalBuilder;
use api1exportbuilders\EventListJSONBuilder;
use api1exportbuilders\EventListJSONPBuilder;
use api1exportbuilders\EventListATOMBeforeBuilder;
use api1exportbuilders\EventListATOMCreateBuilder;


/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class MapItIDController extends \siteapi1\controllers\AreaController {
	
	
	protected $parameters = array();
	
	protected function build($slug, Request $request, Application $app) {
		$this->parameters = array();

		if (strpos($slug,"-") > 0) {
			$slugBits = explode("-", $slug, 2);
			$slug = $slugBits[0];
		}

		$ampir = new AreaMapItInfoRepository();
		$this->parameters['areaMapItInfo'] = $ampir->getByMapItID($slug);
		if (!$this->parameters['areaMapItInfo']) {
			return false;
		}

		$ar = new AreaRepository();
		$this->parameters['area'] = $ar->loadById($this->parameters['areaMapItInfo']->getAreaId());
		if (!$this->parameters['area']) {
			return false;
		}
		
		return true;
	}

	
}


