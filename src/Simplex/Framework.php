<?php
// example.com/src/Simplex/Framework.php
namespace Simplex;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Routing;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;
use Symfony\Component\HttpFoundation\RequestStack;


class Framework extends HttpKernel\HttpKernel
{

    public function __construct($routes)
    {
        $context = new Routing\RequestContext();
        $matcher = new Routing\Matcher\UrlMatcher($routes, $context);
        $requestStack = new RequestStack();

        $controllerResolver = new HttpKernel\Controller\ControllerResolver();
        $argumentResolver = new HttpKernel\Controller\ArgumentResolver();

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher, $requestStack));
        $dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));
        $dispatcher->addSubscriber(new HttpKernel\EventListener\StreamedResponseListener());
        $dispatcher->addSubscriber(new StringResponseListener());

        parent::__construct($dispatcher, $controllerResolver, $requestStack, $argumentResolver);
    }
}