<?php
// framework/front.php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;
use Simplex\StringResponseListener;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;

//include the service container
$container = include __DIR__.'/../src/container.php';

//configure to use StringResponseListener::class
$container->register('listener.string_response', StringResponseListener::class);
$container->getDefinition('dispatcher')
    ->addMethodCall('addSubscriber', array(new Reference('listener.string_response')));

//set a generic parameter
//$container->setParameter('debug', true);

//configure a ResponseListener to encode to utf-8
$container->register('listener.response', ResponseListener::class)
    ->setArguments(array('%charset%'));
$container->setParameter('charset','UTF-8');

//import the routes into a parameter
$container->setParameter('routes', include __DIR__.'/../src/app.php');

//gets the request object from php globals
if(!isset($argv[0])) {
    $request = Request::createFromGlobals();
}
//or use the cli
else {
    $request = Request::create(isset($argv[1]) ? $argv[1]:'/is_leap_year/2012');
}

//main request handling function in the front controller
$response = $container->get('framework')->handle($request);

if(isset($argv[0])) var_dump($response);

//send the response the client
$response->send();
