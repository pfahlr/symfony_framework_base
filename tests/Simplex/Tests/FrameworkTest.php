<?php
// example.com/tests/Simplex/Tests/FrameworkTest.php
namespace Simplex\Tests;

use Calendar\Controller\LeapYearController;
use Calendar\Model\LeapYear;
use PHPUnit\Framework\TestCase;
use Simplex\ContentLengthListener;
use Simplex\StringResponseListener;
use Simplex\Framework;
use Simplex\GoogleListener;
use Simplex\ResponseEvent;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
//use Symfony\Component\Routing\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;


class FrameworkTest extends TestCase
{

    public function testLeapYear() {
        $leapYear = new LeapYearController();
        $this->assertContains('Yes', $leapYear->indexAction(2012)->getContent());
        $this->assertContains('No', $leapYear->indexAction(2013)->getContent());
        $this->assertEquals(
            substr($leapYear->indexAction(date('Y'))->getContent(),0,7),
            substr($leapYear->indexAction(null)->getContent(),0,7)
        );

        $this->assertContains('This is some other content', $leapYear->testAction('',''));

        $leapYear = new LeapYear();
        $this->assertEquals(true, $leapYear->is_leap_year(2012));
        $this->assertEquals(false, $leapYear->is_leap_year(2013));
    }

    public function testGoogleListener() {

        $request = new Request();
        $response = new Response();

        $responseEvent = new ResponseEvent($response, $request);

        $googleListener = new GoogleListener();

        $googleListener->onResponse($responseEvent);

        $this->assertArrayHasKey('response', $googleListener->getSubscribedEvents());

        $this->assertContains('GA CODE', $response->getContent());
    }

    public function testContentLengthListener(){

        $request = new Request();
        $response = new Response('123');

        $responseEvent = new ResponseEvent($response, $request);

        $contentLengthListener = new ContentLengthListener();

        $contentLengthListener->onResponse($responseEvent);

        $this->assertArrayHasKey('response',$contentLengthListener->getSubscribedEvents());

        $this->assertEquals('3', $response->headers->get('Content-Length'));

    }

    public function testStringResponseListener() {
        $event = $this->createMock(\Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent::class);

        $event
            ->expects($this->once())
            ->method('getControllerResult')
            ->will($this->returnValue('Lorem Ipsum'));

        $strResponseListener = new StringResponseListener();
        $strResponseListener->onView($event);

        $this->assertArrayHasKey('kernel.view',$strResponseListener->getSubscribedEvents());

    }

    /*
    public function testNotFoundHandling()
    {
        $framework = $this->getFrameworkForException(new ResourceNotFoundException());

        $response = $framework->handle(new Request());

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testErrorHandling()
    {
        $framework = $this->getFrameworkForException(new \RuntimeException());
        $response = $framework->handle(new Request());
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testControllerResponse()
    {
        $matcher = $this->createMock(Routing\Matcher\UrlMatcherInterface::class);
        // use getMock() on PHPUnit 5.3 or below
        // $matcher = $this->getMock(Routing\Matcher\UrlMatcherInterface::class);

        $matcher
            ->expects($this->once())
            ->method('match')
            ->will($this->returnValue(array(
                '_route' => 'foo',
                'name' => 'Fabien',
                '_controller' => function ($name) {
                    return new Response('Hello '.$name);
                }
            )))
        ;
        $matcher
            ->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($this->createMock(Routing\RequestContext::class)))
        ;
        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();
        $eventDispatcher = $this->createMock(EventDispatcher::class);
        $framework = new Framework($eventDispatcher, $matcher, $controllerResolver, $argumentResolver);

        $response = $framework->handle(new Request());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Hello Fabien', $response->getContent());
    }

    private function getFrameworkForException($exception)
    {
        $matcher = $this->createMock(Routing\Matcher\UrlMatcherInterface::class);
        // use getMock() on PHPUnit 5.3 or below
        // $matcher = $this->getMock(Routing\Matcher\UrlMatcherInterface::class);
        $matcher
            ->expects($this->once())
            ->method('match')
            ->will($this->throwException($exception))
        ;
        $matcher
            ->expects($this->once())
            ->method('getContext')
            ->will($this->returnValue($this->createMock(Routing\RequestContext::class)))
        ;
        $controllerResolver = $this->createMock(ControllerResolverInterface::class);
        $argumentResolver = $this->createMock(ArgumentResolverInterface::class);
        $eventDispatcher = $this->createMock(EventDispatcher::class);
        return new Framework($eventDispatcher, $matcher, $controllerResolver, $argumentResolver);
    }
    */
}
