<?php

declare(strict_types=1);

namespace User\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\I18n\Translator\Translator;



class LoginForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct('sign_in');
		$this->setAttribute('method', 'post');
		
		$translator = new Translator();
		$translator->addTranslationFile("gettext", __DIR__."/../../language/{$_COOKIE["langue"]}.mo");

		$this->add([
			'type' => Element\Email::class,
			'name' => 'email',
			'options' => [
				'label' => $translator->translate("Email"),
			]
		]);

		$this->add([
			'type' => Element\Password::class,
			'name' => 'mdp',
			'options' => [
				'label' => $translator->translate("Mot de passe")
			]
		]);

		# submit button
		$this->add([
			'type' => Element\Submit::class,
			'name' => 'connexion',
			'attributes' => [
				'value' => "Se connecter",
				'class' => 'btn btn-primary'
			]
		]);
	}

}
