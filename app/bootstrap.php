<?php

// Require Composer Autoloader
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Create new Silex App
$app = new Silex\Application();

$app->register(new Knp\Provider\RepositoryServiceProvider(), array(
	'repository.repositories' => array(
		'auth' => 'MathiasDeRoover\\Repository\\auth',
		'admin' => 'MathiasDeRoover\\Repository\\admin',
	)
));

// Use Twig — @note: Be sure to install Twig via Composer first!
$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__ .  DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'views'
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__ . 'config.php'));
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
	'translator.messages' => array(),
));
// App Configuration
$app['debug'] = true;
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	'db.options' => $app['db.options']
));