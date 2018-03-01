<?php
// framework/front.php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Simplex;

//gets the request object from php globals
$request = Request::createFromGlobals();
//even dispatcher object allows binding of events (required (addListener) or not (addSubscriber))
$dispatcher = new EventDispatcher();
//$dispatcher->addListener('response', [new Simplex\GoogleListener(),'onResponse']);
//$dispatcher->addListener('response', [new Simplex\ContentLengthListener(),'onResponse'], -255);
$dispatcher->addSubscriber(new Simplex\ContentLengthListener());
$dispatcher->addSubscriber(new Simplex\GoogleListener());

//include the routes
$routes = include __DIR__.'/../src/app.php';
//context and matcher objects required for matching routes
$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);
//resolves routes to their controllers
$controllerResolver = new HttpKernel\Controller\ControllerResolver();
//gets the arguments from the passed route
$argumentResolver = new HttpKernel\Controller\ArgumentResolver();

//instatiate the framework
$framework = new Simplex\Framework($dispatcher, $matcher, $controllerResolver, $argumentResolver);

//main request handling function in the front controller
$response = $framework->handle($request);

//send the response the client
$response->send();
