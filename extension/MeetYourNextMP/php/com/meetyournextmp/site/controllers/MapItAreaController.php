<?php

namespace com\meetyournextmp\site\controllers;

use com\meetyournextmp\PostcodeParser;
use com\meetyournextmp\repositories\AreaMapItInfoRepository;
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
class MapItAreaController  {

	function postcode(Request $request, Application $app) {
		global $CONFIG;

		$postcode = $request->query->get("postcode");
		$urlExtra = '';
		if ($request->query->get("page") == 'candidates') {
			$urlExtra = '/humans';
		};

		$postcodeParser = new PostcodeParser($postcode);
		if (!$postcodeParser->isValid()) {
			$this->logPostcodeSearch($app, $postcode, false, array("Parser says not valid"));
			$app['flashmessages']->addMessage('That does not look like a valid postcode! Did you enter a full postcode?');
			return $app->redirect('/');
		}


		$memcachedConnection = null;

		if ($CONFIG->memcachedServer) {
			$memcachedConnection = new \Memcached();
			$memcachedConnection->addServer($app['config']->memcachedServer, $app['config']->memcachedPort);
			$url = $memcachedConnection->get($postcodeParser->getCanonical());
			if ($url) {
				$this->logPostcodeSearch($app, $postcode, true, array("From Cache"));
				return $app->redirect($url.$urlExtra);
			}
		}

		$url = "http://mapit.mysociety.org/postcode/".urlencode($postcode);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Meet Your Next MP');
		$data = curl_exec($ch);
		$response = curl_getinfo( $ch );
		curl_close($ch);

		if ($response['http_code'] != 200) {
			$this->logPostcodeSearch($app, $postcode, false, array("not 200 response from API"));
			$app['flashmessages']->addMessage('Sorry, we had a problem with the postcode API! Did you enter a full postcode?');
			return $app->redirect('/');
		}

		$dataObj = json_decode($data);

		$mapItId = $dataObj->shortcuts->WMC;


		if (!$mapItId) {
			$this->logPostcodeSearch($app, $postcode, false, array("No mapidid"));
			$app['flashmessages']->addMessage('Sorry, we did not find your seat in the data!');
			return $app->redirect('/');
		}

		$repo = new AreaMapItInfoRepository();
		$areaMapIdInfo = $repo->getByMapItID($mapItId);

		if (!$areaMapIdInfo) {
			$this->logPostcodeSearch($app, $postcode, false, array("no areaid"));
			$app['flashmessages']->addMessage('Sorry, we did not find your seat in the database!');
			return $app->redirect('/');
		}

		$repo = new AreaRepository();
		$area = $repo->loadById($areaMapIdInfo->getAreaId());

		if (!$area) {
			$this->logPostcodeSearch($app, $postcode, false, array("no area"));
			$app['flashmessages']->addMessage('Sorry, we did not find your area in the database!');
			return $app->redirect('/');
		}

		if ($memcachedConnection) {
			$memcachedConnection->set($postcodeParser->getCanonical(), '/area/'.$area->getSlugForUrl(), 60*60*24*30);
		}

		$this->logPostcodeSearch($app, $postcode, true, array("Cached"));
		return $app->redirect('/area/'.$area->getSlugForUrl().$urlExtra);

	}

	protected function logPostcodeSearch(Application $app, $search, $result, $rest) {
		if (isset($app['config']->logFileMYNMPPostCodeSearch) && $app['config']->logFileMYNMPPostCodeSearch) {
			$handle = fopen($app['config']->logFileMYNMPPostCodeSearch, "a");
			$now = \TimeSource::getDateTime();
			$data = array_merge(array($now->format("c"), $search, $result ? "YES":"NO"), $rest);
			fputcsv($handle, $data);
			fclose($handle);
		}
	}

	protected function findAreaForLinkToSeat(Request $request, Application $app) {
		$repo = new AreaMapItInfoRepository();
		$areaRepo = new AreaRepository();

		if ($request->query->has("mapitid")) {
			$areaInfo = $repo->getByMapItID($request->query->get("mapitid"));
			if ($areaInfo) {
				$area = $areaRepo->loadById($areaInfo->getAreaId());
				if ($area) {
					return $area;
				}
			}
		}

		if ($request->query->has("gssid")) {
			$areaInfo = $repo->getByCodeGSS($request->query->get("gssid"));
			if ($areaInfo) {
				$area = $areaRepo->loadById($areaInfo->getAreaId());
				if ($area) {
					return $area;
				}
			}
		}


		if ($request->query->has("title")) {
			$areaInfo = $repo->getByName($request->query->get("title"));
			if ($areaInfo) {
				$area = $areaRepo->loadById($areaInfo->getAreaId());
				if ($area) {
					return $area;
				}
			}
		}

	}


	function linkToSeatHTML(Request $request, Application $app) {
		global $CONFIG;

		$area = $this->findAreaForLinkToSeat($request, $app);

		if ($area) {
			return $app->redirect('/area/'.$area->getSlugForUrl());
		} else {
			$app->abort(404, "Does not exist.");
		}


	}

}
