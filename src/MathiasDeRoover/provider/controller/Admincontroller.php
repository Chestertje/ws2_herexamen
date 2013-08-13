<?php

namespace MathiasDeRoover\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;

class AdminController implements ControllerProviderInterface {

	public function connect(Application $app) {

		//@note $app['controllers_factory'] is a factory that returns a new instance of ControllerCollection when used.
		//@see http://silex.sensiolabs.org/doc/organizing_controllers.html
		$controllers = $app['controllers_factory'];

		// Bind sub-routes
		$controllers->get('/', array($this, 'admin'));
                
                $controllers->get('/browse', array($this, 'browse'))->method('GET|POST')->bind('adm.browse')->before(array($this, 'checkLogin'));
		$controllers->match('/add', array($this, 'add'))->method('GET|POST')->bind('adm.add')->before(array($this, 'checkLogin'));
                $controllers->match('/delete', array($this, 'delete'))->method('GET|POST')->bind('adm.delete')->before(array($this, 'checkLogin'));
                $controllers->match('/edit', array($this, 'edit'))->method('GET|POST')->bind('adm.edit')->before(array($this, 'checkLogin'));
                $controllers->match('/profile', array($this, 'profile'))->method('GET|POST')->bind('adm.profile')->before(array($this, 'checkLogin'));
		return $controllers;

	}
        public function checkLogin(Request $request, Application $app) {
		if (!$app['session']->get('username')) {
			return $app->redirect($app['url_generator']->generate('auth.login'));
		}
	}
        public function admin(Application $app){
            return $app->redirect($app['url_generator']->generate('adm.browse'));
        }
        public function browse(Application $app) {
            if($app['request']->get('filter') && $app['request']->get('filter') == 'false'){
                
                $app['session']->remove('filter');
                return $app->redirect($app['url_generator']->generate('adm.browse'));
            }
            $isFiltered = false;
            $where = [];
            $like = [];
            $username = $app['session']->get('username');
            $id = $app['session']->get('company_id');
            $pagenum = (($app['request']->get('page')== 0||$app['request']->get('page')== null) && !is_numeric($app['request']->get('page'))) ? 1 : $app['request']->get('page');
            $filter = $app['session']->get('filter');
            $offset = (empty($filter['offset'])) ? 3 : $filter['offset'];
            $offsetOptions = array(1=>'2',2=>'5',3=>'10',4=>'20',5=>'40');
            
            $provincesArr = $app['admin']->getProvinces();
            $provinces = array();
            foreach ($provincesArr as $province) {
                $provinces[$province['Provincie_id']] = $province['Provincie'];
                //array_push($provinces, $province['Provincie']);
            }
            $structureTypeArr = $app['admin']->getStructureType();
            $structureTypes = array();
            foreach ($structureTypeArr as $structure) {
               $structureTypes[$structure['Vastgoedtype_id']] = $structure['Type'];
            }
            $statusArr = $app['admin']->getStatuses();
            $statuses = array();
            foreach ($statusArr as $status) {
               $statuses[$status['Status_id']]= $status['Status'];
            }
            $buildingTypeArr = $app['admin']->getBuildingType();
            $buildingTypes = array();
            foreach ($buildingTypeArr as $type) {
                $buildingTypes[$type['Bebouwingtype_id']] =$type['Type'];
            }
            $filterForm = $app['form.factory']->createNamed('filterForm')
            ->add('provincie', 'choice', array(
                'data' => $filter['vastgoed.Provincie_id'],
                'empty_value' => '',
                'choices' => $provinces,
                 'required' => false
            ))->add('vastgoed_type', 'choice', array(
                'data' => $filter['vastgoed.Vastgoedtype_id'],
                'empty_value' => '',
                'choices' => $structureTypes,
                'required' => false
            ))->add('status', 'choice', array(
                'data' => $filter['vastgoed.Status_id'],
                'empty_value' => '',
                'choices' => $statuses,
                'required' => false
            ))->add('offset', 'choice', array(
                'label' => 'Items/page',
                'data' => $offset,
                'choices' => $offsetOptions,
                'empty_value' => false,
                'required' => false
            ))->add('straat', 'text', array(
                'data' => $filter['vastgoed.Straat'],
                'required' => false,
                'constraints' => array(new Assert\Regex(array('pattern' => "/^[a-z\d\-_\s]+$/i",'match' => true,'message' => 'Enkel letters en cijfers')))
            ));
            if ('POST' == $app['request']->getMethod()) {
            $filterForm->bind($app['request']);

                if ($filterForm->isValid()) {
                    $data = $filterForm->getData();
                    //Array ( [title] => title [category] => 0 [contract_type] => 0 [start_date] => DateTime Object ( [date] => 2012-01-01 00:00:00 [timezone_type] => 3 [timezone] => UTC ) [end_date] => DateTime Object ( [date] => 2012-01-01 00:00:00 [timezone_type] => 3 [timezone] => UTC ) [description] => azeaz )
                    $where = [
                        "vastgoed.Provincie_id" => $data['provincie'],
                        "vastgoed.Vastgoedtype_id" => $data['vastgoed_type'],
                        "vastgoed.Status_id" => $data["status"]
                    ];
                    $like = [
                        "vastgoed.Straat" => $app->escape($data['straat'])
                    ];
                    $session = [
                        "vastgoed.Provincie_id" => $data['provincie'],
                        "vastgoed.Vastgoedtype_id" => $data['vastgoed_type'],
                        "vastgoed.Status_id" => $data["status"],
                        "offset" => $data['offset'],
                        "vastgoed.Straat" => $app->escape($data['straat'])
                    ];
                    $app['session']->set('filter', $session);
                    
                    return $app->redirect($app['url_generator']->generate('adm.browse'));
                }
            }
            elseif ($app['session']->get('filter')){

                $filter = [
                        "vastgoed.Provincie_id" => $app['session']->get('filter')['vastgoed.Provincie_id'],
                        "vastgoed.Vastgoedtype_id" => $app['session']->get('filter')['vastgoed.Vastgoedtype_id'],
                        "vastgoed.Status_id" => $app['session']->get('filter')["vastgoed.Status_id"],
                        
                    ];
                $like = [
                    "vastgoed.Straat" => $app['session']->get('filter')["vastgoed.Straat"]
                ];
                $limit = $this->paging($app, $filter, $like, $app['request']->get('page'), $offsetOptions[$offset], $id );
                $items = $app['admin']->filter($id, $filter,$like, $limit);
                return $app['twig']->render('admin/browse.twig', array('username' => $username,'items' => $items,'pages' => $this->last, 'page' => $pagenum , 'filterForm' => $filterForm->createView()));


            }
            
            $limit = $this->paging($app,$where, $like, $pagenum,$offsetOptions[$offset], $id);
            $items = $app['admin']->filter($id, $where, $like, $limit);
            return $app['twig']->render('admin/browse.twig', array('username' => $username, 'items' => $items, 'pages' => $this->last, 'page' => $pagenum, 'filterForm' => $filterForm->createView()));
        }
        var $last;
        public function paging(Application $app, $array, $like, $pagenum=1, $page_rows=10, $id){
            if (!(isset($pagenum)))
            {
                $pagenum = 1;
            } 


            $rows = $app['admin']->count($id ,$array, $like)['count'];
            
            //$rows = $temp['count'];
            //This tells us the page number of our last page 
            $this->last = ceil($rows/$page_rows); 
            
            //print_r('aantal paginas:'.$this->last);
            //this makes sure the page number isn't below one, or more than our maximum pages 
            if ($pagenum > $this->last) 
            { 
            $pagenum = $this->last; 
            }
            //print_r('    pagenum'.$pagenum);
            if ($pagenum < 1) 
            { 
            $pagenum = 1; 
            } 
            
            //This sets the range to display in our query 
            return $max = 'limit ' .($pagenum - 1) * $page_rows .',' .$page_rows; 
        }
        public function profile(Application $app){
            $company = $app['admin']->findCompanyByID($app['session']->get('company_id'));
            $provincesArr = $app['admin']->getProvinces();
                    $provinces = array();
                    foreach ($provincesArr as $province) {
                        $provinces[$province['Provincie_id']] = $province['Provincie'];
                        //array_push($provinces, $province['Provincie']);
                    }
            $settingsForm = $app['form.factory']->createNamed('settingsForm')
                ->add('naam_makelaar', 'text', array(
                    'data' => $company['Bedrijf_naam'], 
                    'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3)))
                ))
                ->add('contact_email', 'text', array(
                        'data' => $company['Contact_email'],
                        'constraints' => array(new Assert\Email(array('message' => 'The input "{{ value }}" is not a valid email.')), new Assert\NotBlank(), new Assert\Length(array('min' => 5)))
                ))->add('voornaam', 'text', array(
                    'data' => $company['Voornaam'],
                    'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3)), new Assert\Length(array('max' => 30)))
                ))->add('achternaam', 'text', array(
                    'data' => $company['Achternaam'],
                    'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3)), new Assert\Length(array('max' => 35)))
                ))->add('locatie', 'text', array(
                    'required' => false,
                    'data' => $company['Locatie'],
                    'constraints' => array(new Assert\Length(array('min' => 3)))
                ))->add('straat', 'text', array(
                    'data' => $company['Straat'],
                    'required' => false
                ))->add('provincie', 'choice', array(
                    'empty_value' => 'Kies een provincie',
                    'choices' => $provinces,
                    'required' => false,
                    'data' => $company['Provincie_id']
                ))->add('beschrijving', 'text', array(
                    'data' => $company['Beschrijving'],
                    'required' => false
                ))->add('telefoon_nr', 'text', array(
                    'data' => $company['Telefoon_nr'],
                    'required' => false,
                    'constraints' => array(new Assert\Regex(array('pattern' => "^(0\d\/\d{3}\.\d{2}\.\d{2}|0\d{2}\/\d{2}\.\d{2}\.\d{2})$^",'match' => true,'message' => 'formaat moet gelijk zijn aan: (x)xx/xxx.xx.xx')))
                ))->add('logo', 'file', array(
                    'required' => false,
                    'constraints' => array(new Assert\File(array('maxSize' => '2M', 'uploadIniSizeErrorMessage'=> 'The file is too large. Allowed maximum size is 2MB')))
                ));  
            if ('POST' == $app['request']->getMethod()) {
			$settingsForm->bind($app['request']);
                           $error = false; 
			if ($settingsForm->isValid()) {
				$data = $settingsForm->getData();
                                $files = $app['request']->files->get($settingsForm->getName());
                                if (isset($files['logo'])) {
                                    
                                    if (('.jpg' == substr($files['logo']->getClientOriginalName(), -4)) || ('.JPG' == substr($files['logo']->getClientOriginalName(), -4))|| ('.jpeg' == substr($files['logo']->getClientOriginalName(), -5))) {
                                        
                                            $files['logo']->move($app['company.base_path'], $app['session']->get('company_id') . '.jpg');
                                        
					// Define the new name (files are named sequentially)
					
					//$di = new \DirectoryIterator($app['company.base_path']);
					// Move it to its new location

                                    } else {
					$settingsForm->get('logo')->addError(new \Symfony\Component\Form\FormError('Only .jpg allowed'));
                                        $error = true;
                                    }
                                    
				} 
                                
                                $emailRepresentative = strtolower($data['contact_email']);
                                $id = $app['session']->get('company_id');
                                $name = $app['admin']->findCompanyByName($data['naam_makelaar']);
                                $contact_email = $app['admin']->findContactEmail($emailRepresentative);
                                
                                if($id){
                                    if($name && $data['naam_makelaar'] != $company['Bedrijf_naam']){
                                        $settingsForm->get('company_name')->addError(new \Symfony\Component\Form\FormError('company name already in use'));
                                        $error = true;
                                    }
                                    if($contact_email && $data['contact_email']!= $company['Contact_email']){
                                        
                                        $settingsForm->get('email_representative')->addError(new \Symfony\Component\Form\FormError('E-mail already in use'));
                                       $error = true;
                                    }
                                    
                                    if(!$error){
                                        
                                        $arraycomp = [
                                            "Bedrijf_naam" => $data['naam_makelaar'],
                                            "Contact_email" => $data['contact_email'],
                                            "Voornaam" => $data['voornaam'],
                                            "Achternaam" => $data['achternaam'],
                                            "Provincie_id" => $data['provincie'],
                                            "Logo" => (empty($data['logo'])) ? $company['Logo'] : $id.'.jpg' ,
                                            "locatie" => $data['locatie'],
                                            'Beschrijving' => $data['beschrijving'],
                                            'Telefoon_nr' => $data['telefoon_nr'],
                                            'Straat' => $data['straat'],
                                        ];
                                        //werkt niet als antwerpen geslecteerd is. geen idee waarom
                                        //$array = array_diff($arraycomp, $company);
                                        
                                        //print_r($arraycomp);
                                        
                                        if($arraycomp){
                                            $app['admin']->updateCompany($id,$arraycomp);
                                            
                                        }
                                        if($arraycomp['Voornaam'] != $app['session']->get('username')){
                                            $app['session']->set('username', $arraycomp['Voornaam']);
                                        }
                                        return $app->redirect($app['url_generator']->generate('adm.profile').'?profile=true');
                                    }
                                }
                                
			}
		}
                
            return $app['twig']->render('admin/profile.twig', array('url' => $company['Logo'] ,'username'=> $app['session']->get('username') ,'settingsForm' => $settingsForm->createView()));
        }
        public function edit(Application $app){ 
            $id = $app['request']->get('id');
            if($id && is_numeric($id)){
                $vastgoed = $app['admin']->getVastgoed($id,$app['session']->get('company_id'));
                if($vastgoed){                    
                    $provincesArr = $app['admin']->getProvinces();
                    $provinces = array();
                    foreach ($provincesArr as $province) {
                        $provinces[$province['Provincie_id']] = $province['Provincie'];
                        //array_push($provinces, $province['Provincie']);
                    }
                    $structureTypeArr = $app['admin']->getStructureType();
                    $structureTypes = array();
                    foreach ($structureTypeArr as $structure) {
                       $structureTypes[$structure['Vastgoedtype_id']] = $structure['Type'];
                    }
                    $statusArr = $app['admin']->getStatuses();
                    $statuses = array();
                    foreach ($statusArr as $status) {
                       $statuses[$status['Status_id']]= $status['Status'];
                    }
                    $buildingTypeArr = $app['admin']->getBuildingType();
                    $buildingTypes = array();
                    foreach ($buildingTypeArr as $type) {
                        $buildingTypes[$type['Bebouwingtype_id']] =$type['Type'];
                    }          
                    for ($i = date('Y'); $i >= 1800; $i--) {
                        $bouwjaar[$i] = $i;
                    }
                    
                    //$images = glob('uploads/'.$vastgoed['Vastgoed_id'].'/*.*');
                    $images = array();
                    if(file_exists("uploads/".$id. '/')){
                        $imagesArr = scandir("uploads/".$id. '/', 1);
                        
                        foreach($imagesArr as $image ){
                            if(('.jpg' == substr($image, -4))||('.JPG' == substr($image, -4))||('.jpeg' == substr($image, -5))){
                                array_push($images, $image);

                            }
                        }
                    }
                    
                    $editForm = $app['form.factory']->createNamed('editForm')
                        ->add('plaats', 'text', array(
                            'data' => $vastgoed['Locatie'],
                            'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3)))
                        ))->add('straat', 'text', array(
                            'data' => $vastgoed['Straat'],
                            'required' => true,
                            'constraints' => array(new Assert\NotBlank())
                        ))->add('provincie', 'choice', array(
                            'data' => $vastgoed['Provincie_id'],
                            'choices' => $provinces,
                            'constraints' => array(new Assert\NotBlank())
                        ))->add('prijs', 'number', array(
                            'data' => $vastgoed['Prijs'],
                            'required' => true,
                            'constraints' => array(new Assert\NotBlank())
                        ))->add('bebouwing_type', 'choice', array(
                            'choices' => $buildingTypes ,
                            'data' => $vastgoed['Bebouwingtype_id'],                            
                            'constraints' => array(new Assert\NotBlank())
                        ))->add('vastgoed_type', 'choice', array(
                            'data' => $vastgoed['Vastgoedtype_id'],
                            'choices' => $structureTypes, 
                            'required' => false,
                            'constraints' => array(new Assert\NotBlank())
                        ))->add('status_vastgoed', 'choice', array(
                            'data' => $vastgoed['Status_id'],
                            'choices' => $statuses,
                            'constraints' => array(new Assert\NotBlank())
                        ))->add('bouwjaar', 'choice', array(
                            'choices' => $bouwjaar,
                            'required' => false,
                            'data' => $vastgoed['Bouwjaar'],
                            'empty_value' => false,
                            'constraints' => array(new Assert\NotBlank())
                        ))->add('aantal_slaapkamers', 'number', array(
                            'data' => $vastgoed['Aantal_slaapkamers'],
                            'required' => false
                        ))->add('beschrijving', 'text', array(
                            'data' => $vastgoed['Beschrijving'],
                            'required' => false
                        ))->add('aantal_kamers', 'number', array(
                            'data' => $vastgoed['Aantal_kamers'],
                            'required' => false
                        ))->add('oppervlakte', 'number', array(
                            'data' => $vastgoed['Oppervlakte'],
                            'required' => false,
                        ))->add('garage', 'choice', array(
                            'choices' => array(1 => "ja",0 => "neen"),
                            'data' => $vastgoed['Garage'],
                            'expanded' => true,
                            'required' => false,
                            'empty_value' => false,
                            'constraints' => array(new Assert\NotBlank())
                        ))->add('files', 'file', array(
                                'label' => "Choose 1 - 5 pictures",
                                "attr" => array(
                                                "multiple" => "multiple",
                                                "name" => "files[]",
                                                ),
                        ))->add('fotos', 'file', array(
                    'label' => "Foto's",
                    'required' => false,
                    "attr" => array("accept" => "image/jpeg"),
                    'constraints' => array(new Assert\File(array('maxSize' => '2M', 'uploadIniSizeErrorMessage'=> 'The file is too large. Allowed maximum size is 2MB')))
                ))->add('delete_images', 'choice', array(
                    'choices'   => $images,
                    'multiple'  => true,
                    'expanded' => true
                ));
                    if ('POST' == $app['request']->getMethod() && $vastgoed) {
                                $editForm->bind($app['request']);

                                if ($editForm->isValid()) {
                                        $data = $editForm->getData();                                
                                    
                                    $arrayMain = [
                                        "Prijs" => $data['prijs'],
                                        "Locatie" => $data['plaats'],
                                        "Provincie_id" => $data['provincie'],
                                        "Straat" => $data['straat'],
                                        "Vastgoedtype_id" => $data['vastgoed_type'],
                                        "Bebouwingtype_id" => $data['bebouwing_type'],
                                        "Aantal_slaapkamers" => $data['aantal_slaapkamers'],
                                        "Status_id" => $data['status_vastgoed'],
                                        "Aantal_kamers" => $data['aantal_kamers'],
                                        "Oppervlakte" => $data['oppervlakte'],
                                        "Garage" => $data['garage'],
                                        "Bouwjaar" => $data['bouwjaar'],
                                        "Beschrijving" => $data['beschrijving']
                                    ];
                                    
                                    if($data['delete_images'] != null){
                                         foreach ($data['delete_images'] as $image) {
                                            if(file_exists('uploads/'. $id . '/' .$images[$image])){
                                                 unlink('uploads/'. $id . '/' . $images[$image]);
                                                
                                            }
                                             
                                         }
                                    }
                                        
                                    foreach ($_FILES["filesToUpload"]["error"] as $key => $error) {
                                        
                                        
                                        if ($error == UPLOAD_ERR_OK) {
                                            $name = $_FILES["filesToUpload"]["name"][$key];
                                            echo $name;
                                            
                                            if (!file_exists('uploads/'.$vastgoed['Vastgoed_id'])) {
                                                mkdir('uploads/'.$vastgoed['Vastgoed_id'], 0777, true);
                                            }
                                            
                                            if (('.jpg' == substr($name, -4))||('.JPG' == substr($name, -4))||('.jpeg' == substr($name, -5))) {
                                                move_uploaded_file( $_FILES["filesToUpload"]["tmp_name"][$key], "uploads/" . $vastgoed['Vastgoed_id'] .'/'. $_FILES['filesToUpload']['name'][$key]);
                                                echo "uploads/" . $vastgoed['Vastgoed_id'] .'/'. $_FILES['filesToUpload']['name'][$key];
                                                
                                            }
                                        }
                                    }
                                        $app['admin']->updateVastgoed($id,$arrayMain);
                                        return $app->redirect($app['url_generator']->generate('adm.browse').'?update=true');
                                }
                        }

                    return $app['twig']->render('admin/edit.twig', array('username'=> $app['session']->get('username'),'vastgoed_id'=>$id, 'images' => $images ,'editForm' => $editForm->createView()));
                }
            }
            return $app->redirect($app['url_generator']->generate('adm.browse'));
        }
        public function delete(Application $app){ 
            $id = $app['request']->get('id');
            if($id && is_numeric($id)){
                $company_id = $app['session']->get('company_id');
                $vastgoed = $app['admin']->getVastgoed($id,$company_id);
                
                $deleteForm = $app['form.factory']->createNamed('deleteForm');
                if($app['request']->get('js')==='true'){
                if($id && $vastgoed){
                    $app['admin']->deleteVastgoed($id);
                }
                else{
                    header('HTTP/1.1 500 Internal Server Booboo');
                    header('Content-Type: application/json');
                    die('ERROR');
                }
            }
            if(!$vastgoed){
                return $app->redirect($app['url_generator']->generate('adm.browse').'?delete=false');
            }
            
            if ('POST' == $app['request']->getMethod()&& $vastgoed) {
                            $deleteForm->bind($app['request']);

                            if ($deleteForm->isValid()) {
                                if($id && $vastgoed){
                                    $app['admin']->deleteVastgoed($id);
                                }
                                return $app->redirect($app['url_generator']->generate('adm.browse').'?delete=true');
                            }
                }
                
                return $app['twig']->render('admin/delete.twig', array('username'=> $app['session']->get('username') ,'deleteForm' => $deleteForm->createView()));
            }
            
            return $app->redirect($app['url_generator']->generate('adm.browse').'?delete=false');
        }

        public function add(Application $app) {
            $provincesArr = $app['admin']->getProvinces();
            $provinces = array();
            foreach ($provincesArr as $province) {
                $provinces[$province['Provincie_id']] = $province['Provincie'];
                //array_push($provinces, $province['Provincie']);
            }
            $structureTypeArr = $app['admin']->getStructureType();
            $structureTypes = array();
            foreach ($structureTypeArr as $structure) {
               $structureTypes[$structure['Vastgoedtype_id']] = $structure['Type'];
            }
            $statusArr = $app['admin']->getStatuses();
            $statuses = array();
            foreach ($statusArr as $status) {
               $statuses[$status['Status_id']]= $status['Status'];
            }
            $buildingTypeArr = $app['admin']->getBuildingType();
            $buildingTypes = array();
            foreach ($buildingTypeArr as $type) {
                $buildingTypes[$type['Bebouwingtype_id']] =$type['Type'];
            }
            
            
            /*print_r($buildingTypes);
            print_r($statuses);
            print_r($structureTypes);
            print_r($provinces);*/
            for ($i = date('Y'); $i >= 1800; $i--) {
                $bouwjaar[$i] = $i;
            }
                    
            $addForm = $app['form.factory']->createNamed('addForm')
                ->add('plaats', 'text', array(
                    'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3)))
                ))->add('straat', 'text', array(
                    'required' => true,
                    'constraints' => array(new Assert\NotBlank())
                ))->add('provincie', 'choice', array(
                    'choices' => $provinces,
                    'constraints' => array(new Assert\NotBlank())
                ))->add('prijs', 'number', array(
                    'required' => true,
                    'constraints' => array(new Assert\NotBlank())
                ))->add('bebouwing_type', 'choice', array(
                    'choices' => $buildingTypes,
                    'data' => 3,
                    'required' => false,
                    'constraints' => array(new Assert\NotBlank())
                ))->add('vastgoed_type', 'choice', array(
                    'choices' => $structureTypes, 
                    'constraints' => array(new Assert\NotBlank())
                ))->add('status_vastgoed', 'choice', array(
                    'choices' => $statuses,
                    'constraints' => array(new Assert\NotBlank())
                ))->add('bouwjaar', 'choice', array(
                    'choices' => $bouwjaar,
                    'required' => false,
                    'empty_value' => false,
                    'constraints' => array(new Assert\NotBlank())
                ))->add('aantal_slaapkamers', 'number', array(
                    'required' => false
                ))->add('beschrijving', 'text', array(
                    'required' => false
                ))->add('aantal_kamers', 'number', array(
                    'required' => false
                ))->add('oppervlakte', 'number', array(
                    'required' => false,
                ))->add('garage', 'choice', array(
                    'choices' => array(1 => "ja",0 => "neen"),
                    'data' => 1,
                    'expanded' => true,
                    'required' => false,
                    'empty_value' => false,
                    'constraints' => array(new Assert\NotBlank())
                ))->add('fotos', 'file', array(
                    'label' => "Foto's",
                    'required' => false,
                    "attr" => array("accept" => "image/jpeg"),
                    'constraints' => array(new Assert\File(array('maxSize' => '2M', 'uploadIniSizeErrorMessage'=> 'The file is too large. Allowed maximum size is 2MB')))
                ));
            
            if ('POST' == $app['request']->getMethod()) {
			$addForm->bind($app['request']);

			if ($addForm->isValid()) {
				$data = $addForm->getData();
                                //$files = $data["files"];
                                //print_r($data);
                                //Array ( [title] => title [category] => 0 [contract_type] => 0 [start_date] => DateTime Object ( [date] => 2012-01-01 00:00:00 [timezone_type] => 3 [timezone] => UTC ) [end_date] => DateTime Object ( [date] => 2012-01-01 00:00:00 [timezone_type] => 3 [timezone] => UTC ) [description] => azeaz )
                                $arrayMain = [
                                    "Makelaar_id" => $app['session']->get('company_id'),
                                    "Prijs" => $data['prijs'],
                                    "Locatie" => $data['plaats'],
                                    "Provincie_id" => $data['provincie'],
                                    "Straat" => $data['straat'],
                                    "Vastgoedtype_id" => $data['vastgoed_type'],
                                    "Bebouwingtype_id" => $data['bebouwing_type'],
                                    "Aantal_slaapkamers" => $data['aantal_slaapkamers'],
                                    "Status_id" => $data['status_vastgoed'],
                                    "Aantal_kamers" => $data['aantal_kamers'],
                                    "Oppervlakte" => $data['oppervlakte'],
                                    "Garage" => $data['garage'],
                                    "Bouwjaar" => $data['bouwjaar'],
                                    "Beschrijving" => $data['beschrijving']
                                ];
                                
                                                            
                                $app['admin']->insertVastgoed($arrayMain);
                                //$files = $app['request']->files->get($addForm->getName());
                                foreach ($_FILES["filesToUpload"]["error"] as $key => $error) {
                                    if ($error == UPLOAD_ERR_OK) {
                                        $name = $_FILES["filesToUpload"]["name"][$key];
                                        if (!file_exists('uploads/'.$app['admin']->getLastInsert($app['session']->get('company_id'))['Vastgoed_id'])) {
                                            mkdir('uploads/'.$app['admin']->getLastInsert($app['session']->get('company_id'))['Vastgoed_id'], 0777, true);
                                        }
                                        if (('.jpg' == substr($name, -4))||('.JPG' == substr($name, -4))||('.jpeg' == substr($name, -5))) {
                                        move_uploaded_file( $_FILES["filesToUpload"]["tmp_name"][$key], "uploads/".$app['admin']->getLastInsert($app['session']->get('company_id'))['Vastgoed_id'] .'/'. $_FILES['filesToUpload']['name'][$key]);
                                        }
                                    }
                                }
				return $app->redirect($app['url_generator']->generate('adm.browse').'?add=true');
			}
		}
            
            return $app['twig']->render('admin/add.twig', array('username'=> $app['session']->get('username') ,'addForm' => $addForm->createView()));
        }

}