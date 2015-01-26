<?php

namespace org\openacalendar\comments\site\controllers;

use org\openacalendar\comments\models\EventCommentModel;
use org\openacalendar\comments\repositories\EventCommentRepository;
use org\openacalendar\comments\site\forms\NewCommentForm;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class AdminController {

	function index(Request $request, Application $app) {

		return $app['twig']->render('site/commentsadmin/index.html.twig', array());

	}
}
