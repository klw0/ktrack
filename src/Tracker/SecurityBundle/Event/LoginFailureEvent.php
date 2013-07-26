<?php

namespace Tracker\SecurityBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;

class LoginFailureEvent extends Event
{
  private $request;

  public function __construct(Request $request)
  {
    $this->request = $request;
  }

  public function getRequest()
  {
    return $this->request;
  }
}
