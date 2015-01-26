<?php

namespace org\openacalendar\comments\site\forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;

/**
 *
 * @package org.openacalendar.comments
 * @license CLOSED SOURCE
 * @copyright (c) 2013-2015, JMB Technology Limited, http://jmbtechnology.co.uk/
 * @author James Baster <james@jarofgreen.co.uk>
 */

class NewCommentForm extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {

		$builder->add('title', 'text', array(
			'label'=>'Title',
			'required'=>false,
			'max_length'=>VARCHAR_COLUMN_LENGTH_USED,
			'attr' => array('autofocus' => 'autofocus')
		));
		$builder->add('comment', 'textarea', array(
			'label'=>'Comment',
			'required'=>false
		));

		/** @var \closure $myExtraFieldValidator **/
		$myExtraFieldValidator = function(FormEvent $event){
			$form = $event->getForm();
			$myExtraFieldTitle = $form->get('title')->getData();
			$myExtraFieldComment = $form->get('comment')->getData();

			if (!trim($myExtraFieldTitle) && !trim($myExtraFieldComment)) {
				$form['comment']->addError(new FormError("You must write something!"));
			}
		};

		// adding the validator to the FormBuilderInterface
		$builder->addEventListener(FormEvents::POST_BIND, $myExtraFieldValidator);

	}

	public function getName() {
		return 'NewCommentForm';
	}

	public function getDefaultOptions(array $options) {
		return array(
		);
	}

}
