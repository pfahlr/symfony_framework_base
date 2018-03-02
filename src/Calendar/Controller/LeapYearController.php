<?php
namespace Calendar\Controller;

use Calendar\Model\LeapYear;

class LeapYearController
{
  public function indexAction($year)
  {
    $leapYear = new LeapYear();
    if($leapYear->is_leap_year($year)) {
      $response = 'Yes, this is a leap year CACHE:'.rand();
    }
    else {
      $response = new Response('No, not a leap year CACHE:'.rand());
    }

    return $response;
  }

  public function testAction($blank, $year) {
      $content = "This is some other content";
      return $content;
  }
}
