<?php

declare(strict_types=1);

namespace User\Controller;

use RuntimeException;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Form\CreateForm;
use User\Model\UserModel;
use Laminas\I18n\Translator\Translator;

class AuthController extends AbstractActionController
{

    private $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function createAction()
    {
        # Get the translator
        $translator = new Translator();
		$translator->addTranslationFile("gettext", __DIR__."/../../language/{$_COOKIE["langue"]}.mo");

        # Check if the user is not already logged
        $auth = new AuthenticationService();
        if($auth->hasIdentity()){
            return $this->redirect()->toRoute('profile');
        }

        setcookie("langue",$_COOKIE["langue"]);

        $createForm = new CreateForm();
        $request = $this->getRequest();

        if($request->isPost()){
            $formData = $request->getPost()->toArray();
            $createForm->setInputFilter($this->userModel->getCreateFormFilter());
            $createForm->setData($formData);

            if($createForm->isValid()){
                try{
                    $data = $createForm->getData();
                    
                    $this->userModel->saveAccount($data);
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Compte crée avec succès. Vous pouvez maintenant vous connecter'));
                    return $this->redirect()->toRoute('login');

                } catch(RuntimeException $exception){
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
	    			return $this->redirect()->refresh();
                }
            }
            $this->flashMessenger()->addErrorMessage('Echec...');
        }

        return new ViewModel(['form' => $createForm]);
    }
}
