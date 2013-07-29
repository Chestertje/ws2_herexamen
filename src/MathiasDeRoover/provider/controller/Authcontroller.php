<?php

namespace MathiasDeRoover\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;

class AuthController implements ControllerProviderInterface {

	public function connect(Application $app) {

		//@note $app['controllers_factory'] is a factory that returns a new instance of ControllerCollection when used.
		//@see http://silex.sensiolabs.org/doc/organizing_controllers.html
		$controllers = $app['controllers_factory'];

		// Bind sub-routes
		$controllers->get('/', array($this, 'auth'));
                $controllers->get('/logout', array($this, 'logout'))->bind('auth.logout');
		$controllers->match('/login', array($this, 'login'))->method('GET|POST|')->bind('auth.login');
                $controllers->match('/register', array($this, 'register'))->method('GET|POST')->bind('auth.register');
               
		return $controllers;

	}
        
        public function auth(Application $app){
            return $app->redirect($app['url_generator']->generate('auth.login'));
        }
        
        public function logout(Application $app) {
            if($app['session']->get('username')){
                echo $app['session']->get('username');
                $app['session']->invalidate();
            }
            return $app->redirect($app['url_generator']->generate('auth.login'));
        }

	public function login(Application $app) {
            
         if($app['session']->get('username')){
             return $app->redirect($app['url_generator']->generate('adm.browse'));
            //return 'logged in   <a href="'.$app['url_generator']->generate('auth.logout').'">test</a>';                             
         }
            $loginform = $app['form.factory']->createNamed('loginform')
                ->add('contact_email', 'text', array(
                    'constraints' => array(new Assert\Email(array('message' => 'The input "{{ value }}" is not a valid email.')), new Assert\NotBlank(), new Assert\Length(array('min' => 5)))
                ))
                ->add('password', 'password');
            if ('POST' == $app['request']->getMethod()) {
                $loginform->bind($app['request']);

                if ($loginform->isValid()) {
                        $data = $loginform->getData();
                        $contact_email = strtolower($data['contact_email']);

                        $id = $app['auth']->getUser($contact_email);
                        if($id){
                            $password = $app['auth']->getPassword($contact_email);
                            if($password['Paswoord'] == $data['password']){
                                $app['session']->set('username', $id['Voornaam']);
                                $app['session']->set('company_id', $id['Makelaar_id']);
                                
                                return $app->redirect($app['url_generator']->generate('adm.browse'));
                            }else {
					$loginform->get('password')->addError(new \Symfony\Component\Form\FormError('De combinatie gebruikersnaam - paswoord is niet correct'));
				}
                        }
                        else {
                            $loginform->get('password')->addError(new \Symfony\Component\Form\FormError('De combinatie gebruikersnaam - paswoord is niet correct'));
                        }
                                
                                
                }
            }
            return $app['twig']->render('auth/login.twig', array('loginform' => $loginform->createView()));
		
	}
        
	public function register(Application $app) {
            if ($app['session']->get('username')) {
                return $app->redirect($app['url_generator']->generate('adm.browse'));
            }

            $registerForm = $app['form.factory']->createNamed('registerForm')
                ->add('company_name', 'text', array(
                    'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3)))
                ))
                ->add('contact_email', 'text', array(
                        'constraints' => array(new Assert\Email(array('message' => 'The input "{{ value }}" is not a valid email.')), new Assert\NotBlank(), new Assert\Length(array('min' => 5)))
                ))->add('firstname', 'text', array(
                    'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3)), new Assert\Length(array('max' => 30)))
                ))->add('lastname', 'text', array(
                    'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 3)), new Assert\Length(array('max' => 35)))
                ))
                ->add('password', 'repeated', array(
                    'type' => 'password',
                    'first_name' => 'Password',
                    'second_name' => 'Password_confirmation',
                    'invalid_message' => 'Passwords are not the same',
                    'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 6)),new Assert\Regex(array('pattern' => "/^\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*$/",'match' => true,'message' => 'password must contain at least 1 number,1 lower case letter and 1 upper case letter')))
                    //^\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*$
                ));
            if ('POST' == $app['request']->getMethod()) {
			$registerForm->bind($app['request']);

			if ($registerForm->isValid()) {
				$data = $registerForm->getData();
                                $contact_email = strtolower($data['contact_email']);
                                $id = $app['auth']->getUser($contact_email);
                                $name = $app['auth']->checkCompanyName($data['company_name']);
                                if(!$id && !$name){
                                    $array = [
                                        "Bedrijf_naam" => $data['company_name'],
                                        "Contact_email" => $contact_email,
                                        "Voornaam" => $data['firstname'],
                                        "Achternaam" => $data['lastname'],
                                        "Paswoord" => $data['password']
                                    ];
                                    $app['auth']->addCompany($array);
                                    $id=$app['auth']->getUser($contact_email);
                                    $app['session']->set('username', $data['firstname']);
                                    $app['session']->set('company_id', $id['Makelaar_id']);
                                    return $app->redirect($app['url_generator']->generate('auth.login'));
                                }
                                else if($id){
                                    $registerForm->get('contact_email')->addError(new \Symfony\Component\Form\FormError('Email adres is al in gebruik'));
                                }
                                else if($name){
                                    $registerForm->get('company_name')->addError(new \Symfony\Component\Form\FormError('Bedrijf naam is al in gebruik'));
                                }
                                
				
				//$app['links']->insert($data);

				
			}
		}
            return $app['twig']->render('auth/register.twig', array('registerForm' => $registerForm->createView()));
	}

}