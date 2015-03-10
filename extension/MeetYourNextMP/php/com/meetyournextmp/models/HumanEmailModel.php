<?php

namespace com\meetyournextmp\models;
use com\meetyournextmp\repositories\builders\EventRepositoryBuilder;
use models\AreaModel;
use Silex\Application;

/**
 *
 * @package com.meetyournextmp
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class HumanEmailModel  {

	protected $id;
	protected $human_id;
	protected $email;
	protected $subject;
	protected $body_text;
	protected $body_html;
	protected $created_at;
	protected $sent_at;


	public function setFromDataBaseRow($data) {
		$this->id = $data['id'];
		$this->human_id = $data['human_id'];
		$this->email = $data['email'];
		$this->subject = $data['subject'];
		$this->body_text = $data['body_text'];
		$this->body_html = $data['body_html'];
		$this->created_at = $data['created_at'];
		$this->sent_at = $data['sent_at'];

	}


	public function setFromAppAndHumanAndArea(Application $app, HumanModel $humanModel,  AreaModel $areaModel) {

		$this->human_id = $humanModel->getId();
		$this->email = $humanModel->getEmail();
		$this->subject = "Can you tell voters which husting events you are attending?";

		$eventRepoBuilder = new EventRepositoryBuilder();
		$eventRepoBuilder->setIncludeDeleted(false);
		$eventRepoBuilder->setIncludeCancelled(false);
		$eventRepoBuilder->setAfterNow();
		$eventRepoBuilder->setArea($areaModel);
		$events = $eventRepoBuilder->fetchAll();

		$this->body_html = $app['twig']->render('email/humanEmail.html.twig', array(
			'human'=>$humanModel,
			'area'=>$areaModel,
			'events'=>$events,
			'email'=>$this->email,
		));
		if ($app['config']->isDebug) file_put_contents('/tmp/humanEmail.html', $this->body_html);

		$this->body_text = $app['twig']->render('email/humanEmail.txt.twig', array(
			'human'=>$humanModel,
			'area'=>$areaModel,
			'events'=>$events,
			'email'=>$this->email,
		));
		if ($app['config']->isDebug) file_put_contents('/tmp/humanEmail.txt', $this->body_text);

		$this->created_at = $app['timesource']->getDateTime();


	}


	public function  send(Application $app) {



			$message = \Swift_Message::newInstance();
			$message->setSubject($this->subject);
			$message->setFrom(array($app['config']->emailFrom => $app['config']->emailFromName));
			$message->setTo($this->email);
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
	 * @param mixed $human_id
	 */
	public function setHumanId($human_id)
	{
		$this->human_id = $human_id;
	}

	/**
	 * @return mixed
	 */
	public function getHumanId()
	{
		return $this->human_id;
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
