<?php
// framework/front.php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Simplex;
/*
function render_template($request) {
  extract($request->attributes->all(),EXTR_SKIP);
  ob_start();
  include sprintf(__DIR__.'/../src/pages/%s.php', $_route);
  return new Response(ob_get_clean());
}
*/


$request = Request::createFromGlobals();
$dispatcher = new EventDispatcher();
//$dispatcher->addListener('response', [new Simplex\GoogleListener(),'onResponse']);
//$dispatcher->addListener('response', [new Simplex\ContentLengthListener(),'onResponse'], -255);
$dispatcher->addSubscriber(new Simplex\ContentLengthListener());
$dispatcher->addSubscriber(new Simplex\GoogleListener());

$routes = include __DIR__.'/../src/app.php';
$context = new Routing\RequestContext();
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

$controllerResolver = new HttpKernel\Controller\ControllerResolver();
$argumentResolver = new HttpKernel\Controller\ArgumentResolver();

$framework = new Simplex\Framework($dispatcher, $matcher, $controllerResolver, $argumentResolver);

$response = $framework->handle($request);

$response->send();
