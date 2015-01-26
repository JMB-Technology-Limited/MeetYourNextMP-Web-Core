<?php

namespace com\meetyournextmp;

use com\meetyournextmp\repositories\builders\HumanRepositoryBuilder;
use Silex\Application;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */


class AddHumansContentToEventShowPage extends \BaseAddContentToEventShowPage {


	/** @var  Application */
	protected $app;


	protected $parameters;

	function __construct($parameters, Application $app)
	{
		$this->parameters = $parameters;
		$this->app = $app;

		$app['currentUserActions']->set("org.openacalendar","eventEditHumans",
			$app['currentUserActions']->has("org.openacalendar","eventEditDetails"));
	}


	public function  getParameters()
	{

		$out = array();


		$trb = new HumanRepositoryBuilder();
		$trb->setSite($this->app['currentSite']);
		$trb->setIncludeDeleted(false);
		$trb->setHumansForEvent($this->parameters['event']);
		$out['humans'] = $trb->fetchAll();


		if (count($out['humans']) == 0
			&& $this->app['currentUserActions']->has("org.openacalendar","eventEditDetails") &&
			$this->parameters['area']) {

				$trb = new HumanRepositoryBuilder();
				$trb->setSite($this->app['currentSite']);
				$trb->setIncludeDeleted(false);
				$trb->setArea($this->parameters['area']);
				$trb->setLimit(100);
				$out['humansToAdd'] = $trb->fetchAll();

		} else {

			$out['humansToAdd'] = array();
		}

		return $out;;

	}


	public function getTemplatesAfterDetails() {
		return array('site/event/show.humans.html.twig');
	}


}
