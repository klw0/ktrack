<?php

namespace Tracker\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Tracker\SecurityBundle\SecurityEvents;
use Tracker\SecurityBundle\Event\LoginFailureEvent;

class SecurityController extends Controller
{
  /**
   * @Route("/login", name="login")
   * @Template
   */
  public function loginAction()
  {
    $error = null;
    $request = $this->get('request');

    // Did a login error occur on this request?
    if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR) || $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR))
    {
      $error = "Fuck You.";

      // Dispatch a login failure event, which the LoginListener will log for us
      $event = new LoginFailureEvent($request);
      $dispatcher = $this->get('event_dispatcher');
      $dispatcher->dispatch(SecurityEvents::onLoginFailure, $event);
    }

    return array(
      'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
      'error'         => $error
    );
  }

  /**
   * @Route("/login_check", name="login_check")
   */
  public function loginCheckAction()
  {
    // The security layer will intercept this request
  }

  /**
   * @Route("/logout", name="logout")
   */
  public function logoutAction()
  {
    // The security layer will intercept this request
  }

  /**
   * @Route("/security", name="security")
   * @Template
   */
  public function indexAction()
  {
    // Get all of the login attempts
    $loginAttempts = $this->getDoctrine()
      ->getRepository('TrackerSecurityBundle:LoginAttempt')
      ->findAll();

    // Get the blacklisted IPs
    $blacklistedIps = $this->getDoctrine()
      ->getRepository('TrackerSecurityBundle:BlacklistedIp')
      ->findAll();

    // Get the known IPs
    $knownIps = $this->getDoctrine()
      ->getRepository('TrackerSecurityBundle:KnownIp')
      ->findAll();

    return array(
      'login_attempts'  => $loginAttempts,
      'blacklisted_ips' => $blacklistedIps,
      'known_ips'       => $knownIps
    );
  }
}
