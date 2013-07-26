<?php

namespace Tracker\SecurityBundle\Authorization\Voter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class IpVoter implements VoterInterface
{
  private $container;

  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
  }

  public function supportsAttribute($attribute)
  {
    // we won't check against a user attribute, so we return true
    return true;
  }

  public function supportsClass($class)
  {
    // our voter supports all type of token classes, so we return true
    return true;
  }

  public function vote(TokenInterface $token, $object, array $attributes)
  {
    $request = $this->container->get('request');

    $doctrine = $this->container->get('doctrine');
    $ip = $doctrine->getRepository('TrackerSecurityBundle:BlacklistedIp')
      ->findOneByIp($request->getClientIp());

    if ($ip)
      return VoterInterface::ACCESS_DENIED;

    return VoterInterface::ACCESS_ABSTAIN;
  }
}
