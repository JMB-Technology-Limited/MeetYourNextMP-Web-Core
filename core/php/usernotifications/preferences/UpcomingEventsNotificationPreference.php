<?php


namespace usernotifications\preferences;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class UpcomingEventsNotificationPreference extends \BaseUserNotificationPreference {
	
	public function getLabel() { return 'Send Emails of upcoming events'; }

	public function getUserNotificationPreferenceType() { return 'UpcomingEvents'; }
	
}

