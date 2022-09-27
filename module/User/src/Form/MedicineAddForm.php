<?php

declare(strict_types=1);

namespace User\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\I18n\Translator\Translator;


class MedicineAddForm extends Form
{
	public function __construct($name = null)
	{
		parent::__construct('new_medicine');
		$this->setAttribute('method', 'post');

		$translator = new Translator();
		$translator->addTranslationFile("gettext", __DIR__."/../../language/{$_COOKIE["langue"]}.mo");

		$this->add([
			'type' => Element\File::class,
			'name' => 'photo',
			'options' => [
				'label' => $translator->translate('Photo')
			],
		]);

		$this->add([
			'type' => Element\Text::class,
			'name' => 'classe',
			'options' => [
				'label' => $translator->translate('Classe du médicament')
			]
		]);

        $this->add([
			'type' => Element\Text::class,
			'name' => 'dci',
			'options' => [
				'label' => $translator->translate('DCI')
			]
		]);

        $this->add([
			'type' => Element\Text::class,
			'name' => 'nom',
			'options' => [
				'label' => $translator->translate('Nom')
			]
		]);

        $this->add([
			'type' => Element\Text::class,
			'name' => 'voie',
			'options' => [
				'label' => $translator->translate('Voie d\'administration')
			]
		]);

        $this->add([
			'type' => Element\Text::class,
			'name' => 'dosage',
			'options' => [
				'label' => $translator->translate('Dosage')
			],
		]);

        $this->add([
			'type' => Element\Text::class,
			'name' => 'unite',
			'options' => [
				'label' => $translator->translate('Unité')
			]
		]);


		$this->add([
			'type' => Element\Submit::class,
			'name' => 'add_medicine',
			'attributes' => [
				'value' => 'Ajouter',
				'class' => 'btn btn-primary'
			]
		]);
	}
}