<?php
// framework/front.php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel;
use Symfony\Component\Routing;

//use Simplex;

//gets the request object from php globals
if(!isset($argv[0])) {
    $request = Request::createFromGlobals();
}
//or use the cli
else {
    $request = Request::create(isset($argv[1]) ? $argv[1]:'/is_leap_year/2012');
}

$requestStack = new RequestStack();

//include the routes
$routes = include __DIR__.'/../src/app.php';
//context and matcher objects required for matching routes
$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

//resolves routes to their controllers
$controllerResolver = new HttpKernel\Controller\ControllerResolver();
//gets the arguments from the passed route
$argumentResolver = new HttpKernel\Controller\ArgumentResolver();


//event dispatcher object allows binding of events (required (addListener) or not (addSubscriber))
$dispatcher = new EventDispatcher();
//$dispatcher->addSubscriber(new Simplex\ContentLengthListener());
//$dispatcher->addSubscriber(new Simplex\GoogleListener());
$dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher, $requestStack));

$dispatcher->addSubscriber(new HttpKernel\EventListener\ExceptionListener(
    'Calendar\Controller\ErrorController::exceptionAction'
));
$dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));
$dispatcher->addSubscriber(new HttpKernel\EventListener\StreamedResponseListener());
$dispatcher->addSubscriber(new Simplex\StringResponseListener());

//instatiate the framework
$framework = new Simplex\Framework($dispatcher, $controllerResolver, $requestStack, $argumentResolver);
/*
$framework = new HttpKernel\HttpCache\HttpCache(
    $framework,
    new HttpKernel\HttpCache\Store(__DIR__.'/../cache'),
    new HttpKernel\HttpCache\Esi(),
    array('debug'=>true)
);
*/
//main request handling function in the front controller
$response = $framework->handle($request);

if(isset($argv[0])) var_dump($response);

//send the response the client
$response->send();
