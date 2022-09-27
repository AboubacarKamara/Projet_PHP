<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;


class LogoutController extends AbstractActionController
{
	public function indexAction()
	{
		setcookie("langue",$_COOKIE["langue"]);
		$auth = new AuthenticationService();
		if($auth->hasIdentity()) {
			#Disconnect the current user
			$auth->clearIdentity();
		}

		return $this->redirect()->toRoute('login');
	}
}