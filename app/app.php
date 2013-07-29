<?php

// Bootstrap
require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Dummy Data
require __DIR__ . DIRECTORY_SEPARATOR . 'data.php';

$app->error(function (\Exception $e, $code) use ($app) {
	if ($code == 404) {
		return $app['twig']->render('errors/404.twig', array('error' => $e->getMessage()));
	} else {
		return 'Shenanigans! Something went horribly wrong // ' . $e->getMessage();
	}
});
$pages = array(
        'internships/about' => 'about'
    );
    foreach ($pages as $route => $view) {
        $app->get($route, function () use ($app, $view) {
            return $app['twig']->render('static/' . $view . '.twig');
        })->bind('int.'.$view);
    }

$app->get('/', function(Silex\Application $app) {
        //return '<p><a href="' . $app['request']->getBaseUrl(). '/company/login">login</a></p><p><a href="' . $app['request']->getBaseUrl(). '/internships">internships</a></p>';
         return $app->redirect($app['url_generator']->generate('auth.login'));
        
});

// Mount our ControllerProviders
$app->mount('/auth', new MathiasDeRoover\Provider\Controller\AuthController());
$app->mount('/admin', new MathiasDeRoover\Provider\Controller\AdminController());