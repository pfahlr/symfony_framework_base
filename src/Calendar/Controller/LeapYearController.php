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
      $response = new Response('Yes, this is a leap year CACHE:'.rand());
    }
    else {
      $response = new Response('No, not a leap year CACHE:'.rand());
    }

    $date = date_create_from_format('Y-m-d H:i:s', '2005-10-15 10:00:00');

    $response->setCache([
        'public'        => true,
        'etag'          => 'abcde',
        'last_modified' => $date,
        'max_age'       => 10,
        's_maxage'      => 10,
    ]);

    //$response->setTtl(10);
    return $response;
  }

  public function testAction($blank, $year) {
      $content = "
      This is some other content
      <esi:include src=\"abcde\" />
      Some footer content
      ";

      return new Response($content);

  }
}
