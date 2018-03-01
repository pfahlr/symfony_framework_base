<?php
namespace Simplex;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;


class Framework implements HttpKernelInterface {

  private $dispatcher;
  private $matcher;
  private $controllerResolver;
  private $argumentResolver;

  //called from front.php (not dependency injected).
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

  public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
  {
    //getter for context member of matcher which is associated with request member, see call in front.php
    $this->matcher->getContext()->fromRequest($request);

    try {
      //use the matcher to split the parameters from the url
      $request->attributes->add($this->matcher->match($request->getPathInfo()));
      //use the controller resolver to get the controller out of the request
      $controller = $this->controllerResolver->getController($request);
      //get the arguments from the request in context of the controller
      $arguments = $this->argumentResolver->getArguments($request, $controller);
      //call the controller function the builds the response
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

/*
 * requiring further study:
 *  -why does argument resolver need the controller?
 *  -why does attributes->add() need the matcher
 */

