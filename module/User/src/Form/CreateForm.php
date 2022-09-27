<?php

declare(strict_types=1);

namespace User\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\I18n\Translator\Translator;


class CreateForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct('new_account');

		# Setting the submit method to post
		$this->setAttribute('method', 'post');

		$translator = new Translator();
		$translator->addTranslationFile("gettext", __DIR__."/../../language/{$_COOKIE["langue"]}.mo");


		# Add the Last Name Field
		$this->add([
			'type' => Element\Text::class,
			'name' => 'nom',
			'options' => [
				'label' => $translator->translate('Nom')
			],
		]);

		# Add the First Name Field
		$this->add([
			'type' => Element\Text::class,
			'name' => 'prenom',
			'options' => [
				'label' => $translator->translate('Prenom')
			]
		]);

		# Add the Email Field
		$this->add([
			'type' => Element\Email::class,
			'name' => 'email',
			'options' => [
				'label' => $translator->translate('Email')
			]
		]);

		# Add the Date Select (small issue -> cant change the language of the month selector)
		$this->add([
			'type' => Element\DateSelect::class,
			'name' => 'date',
			'options' => [
				'label' => $translator->translate('Date de naissance'),
				'create_empty_option' => true,
				'render_delimiters' => false
			],
		]);

		# Add the Password Field
		$this->add([
			'type' => Element\Password::class,
			'name' => 'mdp',
			'options' => [
				'label' => $translator->translate('Mot de passe')
			],
		]);

		# Add the Password verification Field
		$this->add([
			'type' => Element\Password::class,
			'name' => 'confirm_mdp',
			'options' => [
				'label' => $translator->translate('Verification mot de passe')
			],
		]);

		# Add Submit button
		$this->add([
			'type' => Element\Submit::class,
			'name' => 'creer_compte',
			'attributes' => [
				'value' => 'S\'inscrire',
				'class' => 'btn btn-primary'
			]
		]);
	}
}

