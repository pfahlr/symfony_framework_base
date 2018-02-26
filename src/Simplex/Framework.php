<?php
namespace Simplex;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;


class Framework {

  private $dispatcher;
  private $matcher;
  private $controllerResolver;
  private $argumentResolver;

  public function __construct(
    EventDispatcher $dispatcher,
    UrlMatcherInterface $matcher,
    ControllerResolverInterface $controllerResolver,
    ArgumentResolverInterface $argumentResolver)
  {
    $this->dispatcher = $dispatcher;
    $this->matcher = $matcher;
    $this->controllerResolver = $controllerResolver;
    $this->argumentResolver = $argumentResolver;
  }

  public function handle(Request $request)
  {
    $this->matcher->getContext()->fromRequest($request);
    try {
      $request->attributes->add($this->matcher->match($request->getPathInfo()));
      $controller = $this->controllerResolver->getController($request);
      $arguments = $this->argumentResolver->getArguments($request, $controller);

      $response = call_user_func_array($controller, $arguments);
    }
    catch (ResourceNotFoundException $e) {
      $response = new Response('Not Found', 404);
    }
    catch (\RuntimeException $e) {
      $response = new Response('An error occured', 500);
    }

    // dispatch a response event
    $this->dispatcher->dispatch('response', new ResponseEvent($response, $request));
    return $response;
  }
}