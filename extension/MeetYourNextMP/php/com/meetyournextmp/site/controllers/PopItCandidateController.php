<?php

namespace com\meetyournextmp\site\controllers;

use com\meetyournextmp\PostcodeParser;
use com\meetyournextmp\repositories\AreaMapItInfoRepository;
use com\meetyournextmp\repositories\HumanPopItInfoRepository;
use com\meetyournextmp\repositories\HumanRepository;
use repositories\AreaRepository;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class PopItCandidateController  {


	protected function findHumanForLinkToCandidate(Request $request, Application $app) {
		$repo = new HumanPopItInfoRepository();
		$humanRepo = new HumanRepository();

		if ($request->query->has("ynmpid")) {
			$humanInfo = $repo->getByPopItID($request->query->get("ynmpid"));
			if ($humanInfo) {
				$human = $humanRepo->loadById($humanInfo->getHumanId());
				if ($human) {
					return $human;
				}
			}
		}


	}


	function linkToCandidateHTML(Request $request, Application $app) {
		global $CONFIG;

		$human = $this->findHumanForLinkToCandidate($request, $app);

		if ($human) {
			return $app->redirect('/human/'.$human->getSlugForUrl());
		} else {
			$app->abort(404, "Does not exist.");
		}

	}

}
