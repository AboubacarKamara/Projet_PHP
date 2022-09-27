<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Authentication\AuthenticationService;
use User\UserData\UserData;
use User\Model\UserModel;


class ProfileController extends AbstractActionController
{

    public function __construct(UserModel $userModel)
    {
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

        $auth = new AuthenticationService();
        # If User disconnect then try to go to previous page redirect to login page since he's not connected anymore
		if(!$auth->hasIdentity()) {
			return $this->redirect()->toRoute('login');
		}
        setcookie("langue",$_COOKIE["langue"]);

        # Get the user Data we need to display
        $user = new UserData();
        $data = $this->userModel->fetchAccountByEmail($this->identity());

        $user->__set('nom',$data['nom']);
        $user->__set('prenom',$data['prenom']);
        $user->__set('medicaments',$data['medicament']);

        return (new ViewModel(['user' => $user]))->setTemplate('user/auth/profile');
    }

    
}