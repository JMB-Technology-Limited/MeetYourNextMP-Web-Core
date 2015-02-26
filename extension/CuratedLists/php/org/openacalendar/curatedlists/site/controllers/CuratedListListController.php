<?php

namespace org\openacalendar\curatedlists\site\controllers;


use Silex\Application;
use org\openacalendar\curatedlists\repositories\builders\CuratedListRepositoryBuilder;

/**
 *
 * @package org.openacalendar.curatedlists
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk> 
 */
class CuratedListListController {

	
	function index(Application $app) {

		$grb = new CuratedListRepositoryBuilder();
		$grb->setSite($app['currentSite']);
		$grb->setIncludeDeleted(false);

		$lists = $grb->fetchAll();

		return $app['twig']->render('site/curatedlistlist/index.html.twig', array(
				'curatedlists'=>$lists,
			));
				
	}

}

