<?php


namespace org\openacalendar\comments\userpermissions;


/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class CommentsChangeUserPermission extends \BaseUserPermission {

	public function getUserPermissionExtensionID() {
		return 'org.openacalendar.comments';
	}

	public function getUserPermissionKey() { return 'COMMENTS_CHANGE'; }

	public function isForSite() { return true; }

	public function requiresUser() { return true; }

	public function requiresEditorUser() { return true; }

	public function getParentPermissionsIDs() {
		return array(
			array('org.openacalendar','CALENDAR_CHANGE'),
		);
	}
}
