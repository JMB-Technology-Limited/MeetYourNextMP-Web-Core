<?php

use models\SiteModel;
use models\VenueModel;
use models\UserAccountModel;
use Silex\Application;

/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
abstract class BaseExtension {

	/** @var  Application */
	protected $app;

	function __construct(Application $app) {
		$this->app = $app;
	}


	public abstract function getId();
	
	
	public abstract function getTitle();
	
	public function getDescription() {
		return null;
	}



	public function beforeVenueSave(VenueModel $venue, UserAccountModel $user) {
		
	}



	public function addDetailsToVenue(VenueModel $venue) {

	}

	/**
	 * @param SiteModel $site
	 * @param UserAccountModel $owner User who owns site. This is usually, but may not be, the user who performed the action.
	 */
	public function afterSiteCreate(SiteModel $site, UserAccountModel $owner) {

	}
	
	public function getUserNotificationTypes() {
		return array();
	}

	public function getUserPermissions() {
		return array();
	}

	public function getUserPermission($key) {
		return null;
	}
	
	public function getUserNotificationType($type) {
		return null;
	}
	
	public function getUserNotificationPreferenceTypes() {
		return array();
	}
	
	public function getUserNotificationPreference($type) {
		return null;
	}
	
	public function getAppConfigurationDefinitions() {
		return array();
	}
	
	
	public function getAppConfigurationDefinition($key) {
		foreach($this->getAppConfigurationDefinitions() as $def) {
			if ($def->getKey() == $key) {
				return $def;
			}
		}
	}
	
	public function getImportURLHandlers() {
		return array();
	}

	/**
	 * This should be called on code update, config change or extension activated/deactivated
	 */
	public function clearCache() {   }

	/**
	 * Called to get template variables.
	 *
	 * Note this in only called in specific situations; when the system is about to send an email.
	 *
	 * @param \models\SiteModel $siteModel
	 * @return array
	 */
	public function getTemplateVariables(\models\SiteModel $siteModel = null) {
		return array();
	}

	/**
	 * @return array SysAdminLink return array of SysAdminLink new array('url'=>, 'title'=>)
	 */
	public function getSysAdminLinks() {
		return array();
	}

	public function getAddContentToEventShowPages($parameters) {
		return array();
	}

	/**
	 * @return array BaseTask
	 */
	public function getTasks() {
		return array();
	}


	/**
	 * @return array BaseUserWatchesNotifyContent
	 */
	public function getUserNotifyContents(SiteModel $site, UserAccountModel $userAccountModel) {
		return array();
	}

	/** @return InterfaceNewsFeedModel */
	public function getNewsFeedModel($interfaceHistoryModel) { // @TODO InterfaceHistoryModel type!!!!!!
		return null;
	}

	public function getHistoryRepositoryBuilderData(\repositories\builders\config\HistoryRepositoryBuilderConfig $historyRepositoryBuilderConfig) {
		return array();
	}


	public function makeSureHistoriesAreCorrect($interfaceHistoryModel) {  // @TODO InterfaceHistoryModel type!!!!!!

	}


	public function getSiteFeatures(\models\SiteModel $siteModel = null) {
		return array();
	}

	/** @return InterfaceEventCustomFieldType */
	public function getEventCustomFieldByType($type) {

	}

}

