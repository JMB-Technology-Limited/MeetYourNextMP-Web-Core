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
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk> 
 */

class AreaEditForm extends \BaseFormWithEditComment {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		parent::buildForm($builder, $options);

		$builder->add('title', 'text', array(
			'label'=>'Title',
			'required'=>true, 
			'max_length'=>VARCHAR_COLUMN_LENGTH_USED, 
			'attr' => array('autofocus' => 'autofocus')
		));
		$builder->add('description', 'textarea', array(
			'label'=>'Description',
			'required'=>false
		));
		
	}
	
	public function getName() {
		return 'AreaEditForm';
	}
	
	public function getDefaultOptions(array $options) {
		return array(
		);
	}
	
}


