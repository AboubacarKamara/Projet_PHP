<?php

declare(strict_types=1);

namespace User\Model;


use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\Adapter\Adapter;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\InputFilter;
use Laminas\Validator;
use Laminas\I18n;
use Laminas\Filter;
use User\UserData\UserData;
use Laminas\I18n\Translator\Translator;

class UserModel extends AbstractTableGateway
{
    protected $adapter;
	protected $table = 'users';  

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
		$this->initialize();
	}

    public function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }


	/*
	   Get the user information by his email
	   Return an key-value array with the value of the database field
	*/ 
	public function fetchAccountByEmail(string $email)
	{
		$sqlQuery = $this->sql->select()
			->where(['email' => $email]);
		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute()->current();
		
		if(!$handler) {
			return null;
		}

		return $handler;
	}

	/*
	   Update the medicament list for the user by id and taking a new list in parameters
	   Used for Modify and Delete drug 
	*/
	public function updateMedList(int $id, array $liste_med){
		$sqlQuery = $this->sql
			->update()
			->set(['medicament'=> json_encode($liste_med)])
			->where(['user_id' => $id]);
		$sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler = $sqlStmt->execute()->current();
	}

	/*
		Create account following a form data 
	*/
    public function saveAccount(array $data)
    {
        $values = [
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'birthdate' => $data['date'],
            'email' => $data['email'],
            'password' => (new Bcrypt())->create($data['mdp'])
        ];

        $sqlQuery = $this->sql->insert()->values($values);
        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

	/*
	   Create Filter for the validation of the Login form
	*/
	public function getLoginFormFilter()
	{
		$inputFilter = new InputFilter\InputFilter();
		$factory = new InputFilter\Factory();
		$translator = new Translator();
		$translator->addTranslationFile("gettext", __DIR__."/../../language/{$_COOKIE["langue"]}.mo");
		
 
		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'email',
					'filters' => [
						# Trim and Lower our string
						['name' => Filter\StringTrim::class],
						['name' => Filter\StringToLower::class]
					],
					'validators' => [
						# Verify the field is not empty
						['name' => Validator\NotEmpty::class,
							'options' => [
								'messages' => [
									Validator\NotEmpty::IS_EMPTY => $translator->translate('Ce champ ne peut pas être vide'),
								],
							],
						],
					],
				]
			)
		);

		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'mdp',
					'required' => true,
					'filters' => [
						['name' => Filter\StringTrim::class],
					],
					'validators' => [
						['name' => Validator\NotEmpty::class,
							'options' => [
								'messages' => [
									Validator\NotEmpty::IS_EMPTY => $translator->translate('Ce champ ne peut pas être vide'),
								],
							],
						],
					]
				]
			)
		);

		return $inputFilter;
	}

	/*
	   Create Filter for the validation of the account creation form
	*/
    public function getCreateFormFilter()
	{
		$inputFilter = new InputFilter\InputFilter();
		$factory = new InputFilter\Factory();
		$translator = new Translator();
		$translator->addTranslationFile("gettext", __DIR__."/../../language/{$_COOKIE["langue"]}.mo");

		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'nom',
					'required' => true,
					'filters' => [
						['name' => Filter\StringTrim::class],
						['name' => I18n\Filter\Alnum::class], 
					],
					'validators' => [
						['name' => Validator\NotEmpty::class,
							'options' => [
								'messages' => [
									Validator\NotEmpty::IS_EMPTY => $translator->translate('Ce champ ne peut pas être vide'),
								],
							],
						],
						['name' => I18n\Validator\Alnum::class,
							'options' => [
								'messages' => [
									I18n\Validator\Alnum::NOT_ALNUM => $translator->translate('Le nom ne doit contenir que des chiffres et des lettres'),
								],
							],
						],
					],
				]
			)
		);

        $inputFilter->add(
			$factory->createInput(
				[
					'name' => 'prenom',
					'required' => true,
					'filters' => [
						['name' => Filter\StringTrim::class],
						['name' => I18n\Filter\Alnum::class], 
					],
					'validators' => [
						['name' => Validator\NotEmpty::class,
							'options' => [
								'messages' => [
									Validator\NotEmpty::IS_EMPTY => $translator->translate('Ce champ ne peut pas être vide'),
								],
							],
						],
						['name' => I18n\Validator\Alnum::class,
							'options' => [
								'messages' => [
									I18n\Validator\Alnum::NOT_ALNUM => $translator->translate('Le nom ne doit contenir que des chiffres et des lettres') ,
								],
							],
						],
					],
				]
			)
		);


		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'email',
					'required' => true,
					'filters' => [
						['name' => Filter\StripTags::class],
						['name' => Filter\StringTrim::class], 
						['name' => Filter\StringToLower::class]
					],
					'validators' => [
						['name' => Validator\NotEmpty::class,
							'options' => [
								'messages' => [
									Validator\NotEmpty::IS_EMPTY => $translator->translate('Ce champ ne peut pas être vide'),
								],
							],
						],
						['name' => Validator\EmailAddress::class],
						['name' => Validator\Db\NoRecordExists::class,
							'options' => [
								'table' => $this->table,
								'field' => 'email',
								'adapter' => $this->adapter,
							],
						],
					],
				]
			)
		);
	

		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'mdp',
					'required' => true,
					'filters' => [
						['name' => Filter\StringTrim::class],
					],
					'validators' => [
						['name' => Validator\NotEmpty::class,
							'options' => [
								'messages' => [
									Validator\NotEmpty::IS_EMPTY => $translator->translate('Ce champ ne peut pas être vide'),
								],
							],
						],
						['name' => Validator\StringLength::class,
							'options' => [
								'min' => 7,
								'messages' => [
									Validator\StringLength::TOO_SHORT => $translator->translate('Le mot de passe doit contenir au moins 7 caractères'),
								],
							],
						],
					],
				]
			)
		);

		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'confirm_mdp',
					'required' => true,
					'filters' => [
						['name' => Filter\StringTrim::class],
					],
					'validators' => [
						['name' => Validator\NotEmpty::class,
							'options' => [
								'messages' => [
									Validator\NotEmpty::IS_EMPTY => $translator->translate('Ce champ ne peut pas être vide'),
								],
							],
						], 
						['name' => Validator\Identical::class,
							'options' => [
								'token' => 'mdp',
								'messages' => [
									Validator\Identical::NOT_SAME => $translator->translate('Les mots de passe ne correspondent pas'),
								],
							],
						],
					],
				]
			)
		);

		return $inputFilter;
	}

	/*
	   Create Filter for the validation of the addidtion of a new drug form
	*/
	public function getAddFormFilter()
	{
		$inputFilter = new InputFilter\InputFilter();
		$factory = new InputFilter\Factory();
		$translator = new Translator();
		$translator->addTranslationFile("gettext", __DIR__."/../../language/{$_COOKIE["langue"]}.mo");
		
		$fileInput = new InputFilter\FileInput('photo');
		$fileInput->setRequired(true);
		$inputFilter->add($fileInput);


		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'classe',
					'required' => true,
					'filters' => [
						['name' => Filter\StringTrim::class],
					],
					'validators' => [
						['name' => Validator\NotEmpty::class,
							'options' => [
								'messages' => [
									Validator\NotEmpty::IS_EMPTY => $translator->translate('Ce champ ne peut pas être vide'),
								],
							],
						],
					]
				]
			)
		);

		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'dci',
					'required' => true,
					'filters' => [
						['name' => Filter\StringTrim::class],
					],
					'validators' => [
						['name' => Validator\NotEmpty::class,
							'options' => [
								'messages' => [
									Validator\NotEmpty::IS_EMPTY => $translator->translate('Ce champ ne peut pas être vide'),
								],
							],
						],
					]
				]
			)
		);

		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'nom',
					'required' => true,
					'filters' => [
						['name' => Filter\StringTrim::class],
					],
					'validators' => [
						['name' => Validator\NotEmpty::class,
							'options' => [
								'messages' => [
									Validator\NotEmpty::IS_EMPTY => $translator->translate('Ce champ ne peut pas être vide'),
								],
							],
						],
					]
				]
			)
		);

		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'voie',
					'required' => true,
					'filters' => [
						['name' => Filter\StringTrim::class],
					],
					'validators' => [
						['name' => Validator\NotEmpty::class,
							'options' => [
								'messages' => [
									Validator\NotEmpty::IS_EMPTY => $translator->translate('Ce champ ne peut pas être vide'),
								],
							],
						],
					]
				]
			)
		);

		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'dosage',
					'required' => true,
					'filters' => [
						['name' => Filter\StringTrim::class],
					],
					'validators' => [
						['name' => Validator\NotEmpty::class,
							'options' => [
								'messages' => [
									Validator\NotEmpty::IS_EMPTY => $translator->translate('Ce champ ne peut pas être vide'),
								],
							],
						],
					]
				]
			)
		);

		$inputFilter->add(
			$factory->createInput(
				[
					'name' => 'unite',
					'required' => true,
					'filters' => [
						['name' => Filter\StringTrim::class],
					],
					'validators' => [
						['name' => Validator\NotEmpty::class,
							'options' => [
								'messages' => [
									Validator\NotEmpty::IS_EMPTY => $translator->translate('Ce champ ne peut pas être vide'),
								],
							],
						],
					]
				]
			)
		);

		return $inputFilter;
	}
 }
