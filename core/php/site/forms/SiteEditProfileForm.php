<?php

namespace site\forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;


/**
 *
 * @package Core
 * @link http://ican.openacalendar.org/ OpenACalendar Open Source Software
 * @license http://ican.openacalendar.org/license.html 3-clause BSD
 * @copyright (c) 2013-2014, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */
class SiteEditProfileForm extends AbstractType{

	/** @var Config ***/
	protected $config;
	
	function __construct(\Config $config) {
		$this->config = $config;
	}

	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		
		$builder->add('title', 'text', array(
			'label'=>'Title',
			'required'=>false, 
			'max_length'=>VARCHAR_COLUMN_LENGTH_USED, 
			'attr' => array('autofocus' => 'autofocus')
		));
		
		$builder->add('description_text', 'textarea', array(
			'label'=>'Description',
			'required'=>false
		));
		
		$builder->add('footer_text', 'textarea', array(
			'label'=>'Footer Text',
			'required'=>false
		));
		
		if ($this->config->isFileStore()) {
			$builder->add('logo', 'file', array(
				"mapped" => false, 
				'label'=>'Upload new Logo',
				'required'=>false
			));
		}
	}
	
	public function getName() {
		return 'SiteEditProfileForm';
	}
	
	public function getDefaultOptions(array $options) {
		return array(
		);
	}
	
}