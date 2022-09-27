<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\View\Model\ViewModel;
use User\Form\MedicineAddForm;
use User\Model\UserModel;
use User\UserData\UserData;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\I18n\Translator\Translator;

class AddController extends AbstractActionController
{
    private $userModel;

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
        $translator = new Translator();
		$translator->addTranslationFile("gettext", __DIR__."/../../language/{$_COOKIE["langue"]}.mo");


        setcookie("langue",$_COOKIE["langue"]);

        #Create a form where we will add data and filter to validate the form
        $addForm = new MedicineAddForm();
        $request = $this->getRequest();
        

        if($request->isPost()){

            #Get the picture selected
            $file = $_FILES['photo'];

            # Get the data for the form
            $formData = $request->getPost()->toArray();
            $formData["photo"] = $request->getFiles()->toArray()["photo"];

            #Set the filter and data
            $addForm->setInputFilter($this->userModel->getAddFormFilter());
            $addForm->setData($formData);

            # Get the info of the user
            $user = $this->userModel->fetchAccountByEmail($this->identity());

            #Initialize the medicament list if empty or get it if not empty
            if ($user['medicament'] == null){ 
                $user['medicament'] = ['liste' => []];
            }
            else{
                $user['medicament'] = json_decode($user['medicament'],true);
            }


            if($addForm->isValid()){
                # Upload the file selected in our directory
                $file_name = $file['name'];
                $file_tmp = $file['tmp_name'];
                $file_size = $file['size'];
                $file_error = $file['error'];

                $file_ext = explode('.', $file_name);
                $this->console_log($file_ext);

                $allowed = array('png','jpg','jpeg','svg');
                
                if ($file_error === 0) {
                    if ($file_size <= 2097152) {
                        if (!in_array($file_ext[count($file_ext)-1], $allowed))
                        {
                            $this->flashMessenger()->addErrorMessage($translator->translate('Veuillez choisir une image'));
                            return $this->redirect()->refresh();
                        }
        
                        $file_name_new = $file['name'];
                        $file_destination = __DIR__."\..\img\\${file_name}";
        
                        if (move_uploaded_file($file_tmp, $file_destination)) {
                            echo $file_destination;
                        }
                    }
                }

                try{
                    $data = $addForm->getData();

                    # Delete the submit button from data
                    unset($data["add_medicine"]);

                    # Only keep the name of our photo
                    $data["photo"] = $data["photo"]["name"];

                    # Add our new medicine
                    array_push($user['medicament']['liste'],$data);
                                   
                    $this->userModel->updateMedList($user['user_id'], $user['medicament']);

                    $this->flashMessenger()->addSuccessMessage($translator->translate('Médicament ajouté'));
                    return $this->redirect()->toRoute('profile');

                } catch(RuntimeException $exception){
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
	    			return $this->redirect()->refresh();
                }
            }
            $this->flashMessenger()->addErrorMessage('Echec...');
        }
        return (new ViewModel(['form' => $addForm]))->setTemplate('user/auth/addmedicine') ;
	}

    public function deleteAction()
    {
        $translator = new Translator();
		$translator->addTranslationFile("gettext", __DIR__."/../../language/{$_COOKIE["langue"]}.mo");

        # Get the url to get the index parameter
        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $url_components = parse_url($actual_link);
        $p = preg_split("#/#",$url_components["path"]);
        $index = intval($p[2]);
        

        $user = $this->userModel->fetchAccountByEmail($this->identity());

        $user['medicament'] = json_decode(stripcslashes(trim($user['medicament'],'"')),true);

        # Delete the drug at the corresponding index 
        array_splice($user['medicament']["liste"],$index,1);

        try{
            $this->userModel->updateMedList($user['user_id'], $user['medicament']);
            $this->flashMessenger()->addSuccessMessage($translator->translate('Médicament supprimé'));

            # Get the updated info of the user
            $userData = new UserData();
            $data = $this->userModel->fetchAccountByEmail($this->identity());
            $userData->__set('nom',$data['nom']);
            $userData->__set('prenom',$data['prenom']);
            $userData->__set('medicaments',$data['medicament']);
            return $this->redirect()->toRoute('profile');

        } catch(RuntimeException $exception){
            $this->flashMessenger()->addErrorMessage($exception->getMessage());
            $this->flashMessenger()->addErrorMessage('Echec...');
            return $this->redirect()->refresh();
        }


        return (new ViewModel(['user' => $user]))->setTemplate('user/auth/profile') ;
    }

    public function editAction()
	{
        $addForm = new MedicineAddForm();
        $request = $this->getRequest();

        setcookie("langue",$_COOKIE["langue"]);

        $translator = new Translator();
		$translator->addTranslationFile("gettext", __DIR__."/../../language/{$_COOKIE["langue"]}.mo");

        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $url_components = parse_url($actual_link);
        $p = preg_split("#/#",$url_components["path"]);
        $index = intval($p[2]);

        $user = $this->userModel->fetchAccountByEmail($this->identity());
        $user['medicament'] = json_decode(stripcslashes(trim($user['medicament'],'"')),true);

        $addForm->setData($user['medicament']['liste'][$index]);

        if($request->isPost()){
            #Get the picture selected
            $file = $_FILES['photo'];

            # Get the data for the form
            $formData = $request->getPost()->toArray();
            $formData["photo"] = $request->getFiles()->toArray()["photo"];
            
            $addForm->setInputFilter($this->userModel->getAddFormFilter());
            $addForm->setData($formData);


            if($addForm->isValid()){
                #Upload the file selected in our directory
                // File Properties
                $file_name = $file['name'];
                $file_tmp = $file['tmp_name'];
                $file_size = $file['size'];
                $file_error = $file['error'];

                if ($file_error === 0) {
                    if ($file_size <= 2097152) {
        
                        $file_name_new = $file['name'];
                        $file_destination = __DIR__."\..\img\\${file_name}";
        
                        if (move_uploaded_file($file_tmp, $file_destination)) {
                            echo $file_destination;
                        }
                    }
                }

                try{
                    $data = $addForm->getData();
                    # Only keep the name of our photo
                    $data["photo"] = $data["photo"]["name"];

                    $user['medicament']['liste'][$index] = $data ;
                    unset($user['medicament']['liste'][$index]["add_medicine"]);
 
                    $this->userModel->updateMedList($user['user_id'], $user['medicament']);
                    $this->flashMessenger()->addSuccessMessage($translator->translate('Médicament modifié'));
                    return $this->redirect()->toRoute('profile');

                } catch(RuntimeException $exception){
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
	    			return $this->redirect()->refresh();
                }
            }
            $this->flashMessenger()->addErrorMessage('Echec...');
        }
        return (new ViewModel(['form' => $addForm]))->setTemplate('user/auth/addmedicine') ;
	}
}