<?php
use Symfony\Component\Routing;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$routes = new Routing\RouteCollection();

//$routes->add('hello', new Routing\Route('/hello/{name}', ['name'=>'World', '_controller'=>'render_template']));
//$routes->add('bye', new Routing\Route('/bye',['_controller'=>'render_template']));

$routes->add('leap_year', new Routing\Route('/is_leap_year/{year}', array(
  'year'=>NULL,
  '_controller'=>'Calendar\Controller\LeapYearController::indexAction',
)));

$routes->add('leap_year_test', new Routing\Route('/is_leap_year_test/{year}', array(
    'blank'=>'test',
    'year'=>NULL,
    '_controller'=>'Calendar\Controller\LeapYearController::testAction',
)));

return $routes;
