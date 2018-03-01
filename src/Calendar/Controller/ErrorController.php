<?php
/**
 * Created by PhpStorm.
 * User: rick
 * Date: 3/1/18
 * Time: 6:35 PM
 */

namespace Calendar\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Debug\Exception\FlattenException;

class ErrorController
{
  public function exceptionAction(FlattenException $exception)
  {
      $msg = 'Something went wrong ('.$exception->getMessage().')';
      return new Response($msg, $exception->getStatusCode());
  }
}