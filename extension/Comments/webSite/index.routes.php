<?php


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */


$permissionCommentsChangeRequired = function(Request $request, Application $app) {
	global $CONFIG;
	if (!$app['currentUserPermissions']->hasPermission("org.openacalendar.comments","COMMENTS_CHANGE")) {
		if ($app['currentUser']) {
			return $app->abort(403); // TODO
		} else {
			return new RedirectResponse($CONFIG->getWebIndexDomainSecure().'/you/login');
		}
	}
};



// ####################### Events

$app->match('/event/{slug}/new/comment', "org\openacalendar\comments\site\controllers\EventController::newComment")
		->assert('slug', FRIENDLY_SLUG_REGEX)
		->before($permissionCommentsChangeRequired)
		->before($canChangeSite);

// ####################### Areas

$app->match('/area/{slug}/comments', "org\openacalendar\comments\site\controllers\AreaController::comments")
		->assert('slug', FRIENDLY_SLUG_REGEX);


$app->match('/area/{slug}/comment', "org\openacalendar\comments\site\controllers\AreaController::comments")
		->assert('slug', FRIENDLY_SLUG_REGEX);


$app->match('/area/{slug}/new/comment', "org\openacalendar\comments\site\controllers\AreaController::newComment")
		->assert('slug', FRIENDLY_SLUG_REGEX)
		->before($permissionCommentsChangeRequired)
		->before($canChangeSite);

// ####################### Admin

$app->match('/admin/comments/', "org\openacalendar\comments\site\controllers\AdminController::index")
		->before($permissionCalendarAdministratorRequired)
		->before($canChangeSite);

$app->match('/admin/comments/event/{slug}/', "org\openacalendar\comments\site\controllers\AdminEventController::index")
		->before($permissionCalendarAdministratorRequired)
		->before($canChangeSite);

$app->match('/admin/comments/area/{slug}/', "org\openacalendar\comments\site\controllers\AdminAreaController::index")
		->before($permissionCalendarAdministratorRequired)
		->before($canChangeSite);

$app->match('/admin/comment/', "org\openacalendar\comments\site\controllers\AdminController::index")
		->before($permissionCalendarAdministratorRequired)
		->before($canChangeSite);

$app->match('/admin/comment/event/{slug}/', "org\openacalendar\comments\site\controllers\AdminEventController::index")
		->before($permissionCalendarAdministratorRequired)
		->before($canChangeSite);

$app->match('/admin/comments/area/{slug}/', "org\openacalendar\comments\site\controllers\AdminAreaController::index")
		->before($permissionCalendarAdministratorRequired)
		->before($canChangeSite);
