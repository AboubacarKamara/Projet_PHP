<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Laminas\Authentication\AuthenticationService;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Adapter\Adapter;
use Laminas\Authentication\Result;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\SessionManager;
use Laminas\View\Model\ViewModel;
use User\Form\LoginForm;
use User\Model\UserModel;
use User\UserData\UserData;
use Laminas\I18n\Translator\Translator;



class LoginController extends AbstractActionController
{
    private $adapter;
    private $userModel;

    public function __construct(Adapter $adapter, UserModel $userModel){
        $this->adapter = $adapter;
        $this->userModel = $userModel;
    }

	public function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }

    public function indexAction()
    {   
		$translator = new Translator();
		$translator->addTranslationFile("gettext", __DIR__."/../../language/{$_COOKIE["langue"]}.mo");


		$auth = new AuthenticationService();
        if($auth->hasIdentity()){
            return $this->redirect()->toRoute('profile');
        }

		setcookie("langue",$_COOKIE["langue"]);

		$loginForm = new LoginForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$loginForm->setInputFilter($this->userModel->getLoginFormFilter());
			$loginForm->setData($formData);

			if($loginForm->isValid()) {
				$authAdapter = new CredentialTreatmentAdapter($this->adapter);
				$authAdapter->setTableName($this->userModel->getTable())
				            ->setIdentityColumn('email')
				            ->setCredentialColumn('password')
				            ->getDbSelect();

				$data = $loginForm->getData();

				# Set the identify to find the user on the other pages
				$authAdapter->setIdentity($data['email']);

				# Password crypter
				$hash = new Bcrypt();
				$info = $this->userModel->fetchAccountByEmail($data['email']);

				# Verify if the form password hash and db password hash correspond
				if($hash->verify($data['mdp'], $info['password'])) {
					$authAdapter->setCredential($info['password']);
				} else {
					$authAdapter->setCredential(''); # To handle errors
				}

				$authResult = $auth->authenticate($authAdapter);

				switch ($authResult->getCode()) {
					# If the connexion worked
					case Result::SUCCESS:
						return $this->redirect()->toRoute(
							'profile'
						);
						break;		
					# Else...
					default:
						$this->flashMessenger()->addErrorMessage($translator->translate('Echec de la connexion.'));
						return $this->redirect()->refresh(); # refresh the page to show error
						break;
				}
            }
        }
		return (new ViewModel(['form' => $loginForm]))->setTemplate('user/auth/login');
    }
}