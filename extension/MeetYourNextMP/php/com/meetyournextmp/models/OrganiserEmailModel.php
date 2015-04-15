<?php

namespace com\meetyournextmp\models;
use com\meetyournextmp\repositories\builders\EventRepositoryBuilder;
use com\meetyournextmp\repositories\builders\HumanRepositoryBuilder;
use models\AreaModel;
use models\EventModel;
use repositories\AreaRepository;
use repositories\VenueRepository;
use Silex\Application;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class OrganiserEmailModel  {

	public static function getEmailFrom($email) {
		$emailBits = explode(" ",trim($email));
		if ($emailBits[0] && filter_var($emailBits[0], FILTER_VALIDATE_EMAIL)) {
			return $emailBits[0];
		}
	}

	protected $id;
	protected $event_id;
	protected $email;
	protected $subject;
	protected $body_text;
	protected $body_html;
	protected $created_at;
	protected $sent_at;


	public function setFromDataBaseRow($data) {
		$this->id = $data['id'];
		$this->event_id = $data['event_id'];
		$this->email = $data['email'];
		$this->subject = $data['subject'];
		$this->body_text = $data['body_text'];
		$this->body_html = $data['body_html'];
		$this->created_at = $data['created_at'];
		$this->sent_at = $data['sent_at'];

	}


	public function setFromAppAndEventAndEmail(Application $app, EventModel $eventModel, $email) {

		$this->event_id = $eventModel->getId();
		$this->email = $email;
		$this->subject = "Can we check the details of your event?";

		$venueRepo = new VenueRepository();
		$venue = null;
		if ($eventModel->getVenueId()) {
			$venue = $venueRepo->loadById($eventModel->getVenueId());
		}

		$area = null;
		$areaRepo = new AreaRepository();
		if ($eventModel->getAreaId()) {
			$area = $areaRepo->loadById($eventModel->getAreaId());
		} else if ($venue && $venue->getAreaId()) {
			$area = $areaRepo->loadById($venue->getAreaId());
		}

		$hrb = new HumanRepositoryBuilder();
		$hrb->setHumansForEvent($eventModel);
		$hrb->setIncludeDeleted(false);
		$humans = $hrb->fetchAll();

		$this->body_html = $app['twig']->render('email/organiserEmail.html.twig', array(
			'event'=>$eventModel,
			'email'=>$this->email,
			'venue'=>$venue,
			'area'=>$area,
			'humans'=>$humans,
		));
		if ($app['config']->isDebug) file_put_contents('/tmp/organiserEmail.html', $this->body_html);

		$this->body_text = $app['twig']->render('email/organiserEmail.txt.twig', array(
			'event'=>$eventModel,
			'email'=>$this->email,
			'venue'=>$venue,
			'area'=>$area,
			'humans'=>$humans,
		));
		if ($app['config']->isDebug) file_put_contents('/tmp/organiserEmail.txt', $this->body_text);

		$this->created_at = $app['timesource']->getDateTime();


	}


	public function  send(Application $app) {



			$message = \Swift_Message::newInstance();
			$message->setSubject($this->subject);
			$message->setFrom(array($app['config']->emailFrom => $app['config']->emailFromName));
			$message->setTo( $this->email);
			$message->setBody($this->body_text);
			$message->addPart($this->body_html,'text/html');


			if (!$app['config']->isDebug) {
				$app['mailer']->send($message);
			}
	}

	/**
	 * @param mixed $body_html
	 */
	public function setBodyHtml($body_html)
	{
		$this->body_html = $body_html;
	}

	/**
	 * @return mixed
	 */
	public function getBodyHtml()
	{
		return $this->body_html;
	}

	/**
	 * @param mixed $body_text
	 */
	public function setBodyText($body_text)
	{
		$this->body_text = $body_text;
	}

	/**
	 * @return mixed
	 */
	public function getBodyText()
	{
		return $this->body_text;
	}

	/**
	 * @param mixed $created_at
	 */
	public function setCreatedAt($created_at)
	{
		$this->created_at = $created_at;
	}

	/**
	 * @return mixed
	 */
	public function getCreatedAt()
	{
		return $this->created_at;
	}

	/**
	 * @param mixed $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param mixed $event_id
	 */
	public function setEventId($event_id)
	{
		$this->event_id = $event_id;
	}

	/**
	 * @return mixed
	 */
	public function getEventId()
	{
		return $this->event_id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $sent_at
	 */
	public function setSentAt($sent_at)
	{
		$this->sent_at = $sent_at;
	}

	/**
	 * @return mixed
	 */
	public function getSentAt()
	{
		return $this->sent_at;
	}

	/**
	 * @param mixed $subject
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;
	}

	/**
	 * @return mixed
	 */
	public function getSubject()
	{
		return $this->subject;
	}






}
