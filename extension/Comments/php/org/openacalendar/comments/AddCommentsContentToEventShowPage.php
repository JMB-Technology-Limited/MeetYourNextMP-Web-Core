<?php

namespace org\openacalendar\comments;

use org\openacalendar\comments\repositories\builders\EventCommentRepositoryBuilder;
use Silex\Application;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class AddCommentsContentToEventShowPage extends  \BaseAddContentToEventShowPage {

	/** @var  Application */
	protected $app;


	protected $parameters;

	function __construct($parameters, Application $app)
	{
		$this->parameters = $parameters;
		$this->app = $app;


		$app['currentUserActions']->set("org.openacalendar.comments","eventNewComment",
			$app['currentUserPermissions']->hasPermission("org.openacalendar.comments","COMMENTS_CHANGE")
			&& !$this->parameters['event']->getIsDeleted()
			&& !$this->parameters['event']->getIsCancelled());
	}


	public function  getParameters()
	{

		$ecrb = new EventCommentRepositoryBuilder();
		$ecrb->setIncludeDeleted(false);
		$ecrb->setIncludeClosedByAdmin(false);
		$ecrb->setEvent($this->parameters['event']);
		$ecrb->setLimit(1000);
		return array('eventComments' => $ecrb->fetchAll() );

	}


	public function getTemplatesAtEnd() {
		return array('site/event/show.comments.html.twig');
	}
}
