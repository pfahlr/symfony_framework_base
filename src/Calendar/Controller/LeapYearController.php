<?php
namespace Calendar\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Calendar\Model\LeapYear;

class LeapYearController
{
  public function indexAction($year)
  {
    $leapYear = new LeapYear();
    if($leapYear->is_leap_year($year)) {
      return new Response('Yes, this is a leap year');
    }
    return new Response('No, not a leap year');
  }
  public function testAction($blank, $year) {}
}
