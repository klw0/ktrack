<?php

namespace Tracker\SecurityBundle\Listener;

use Tracker\SecurityBundle\Entity\LoginAttempt;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\DoctrineBundle\Registry as Doctrine;

/**
 * Listens for login success and login failure events and logs them to the database
 */
class LoginListener
{
  private $em;

  public function __construct(Doctrine $doctrine)
	{
    $this->em = $doctrine->getEntityManager();
  }

  public function onLoginSuccess(Event $event)
  {
    $request = $event->getRequest();
    $username = $request->request->get('_username');
    $this->createLoginAttempt($username, true, $request);
  }

  public function onLoginFailure(Event $event)
  {
    $request = $event->getRequest();
    $username = $request->getSession()->get(SecurityContext::LAST_USERNAME);
    $this->createLoginAttempt($username, false, $request);
  }

  private function createLoginAttempt($username, $successful, Request $request)
  {
    $loginAttempt = new LoginAttempt();
    $loginAttempt->setUsername($username);
    $loginAttempt->setSuccessful($successful);
    $loginAttempt->setIp($request->getClientIp());
    $loginAttempt->setUserAgent($request->headers->get('user-agent'));

    $this->em->persist($loginAttempt);
    $this->em->flush();
  }
}
