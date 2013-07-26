<?php

namespace Tracker\TrafficSourceBundle\Service;

use Tracker\CampaignBundle\Entity\Campaign;
use Tracker\TargetBundle\Entity\Target;
use Tracker\TrafficSourceBundle\Wrapper\TrafficSourceWrapperInterface;

use Symfony\Bundle\DoctrineBundle\Registry as Doctrine;

/**
 * Abstract base class for all traffic source services.
 */
abstract class TrafficSourceService
{
  protected $em;
  protected $wrapper;

  public function __construct(Doctrine $doctrine)
	{
    $this->em = $doctrine->getEntityManager();
  }

  /**
   * Updates the local target cache with data retrieved from the traffic source.
   */
  public function updateTargetCache(Campaign $campaign)
  {
    // Get target details for all of the creatives that this campaign has
    $targetDetails = array();
    foreach ($campaign->getCreativeIdentifiers() as $creativeId)
      $targetDetails = array_merge($targetDetails, $this->wrapper->getTargetDetailsForCreative($creativeId));

    foreach ($targetDetails as $targetDetail)
    {
      // Find a target by its name, campaign and creative identifier.  Name and creative identifier are probably enough, but whatever.
      $target = $this->em->getRepository('TrackerTargetBundle:Target')->findOneBy(array('name' => $targetDetail['name'], 'campaign' => $campaign->getId(), 'creativeIdentifier' => $targetDetail['creativeIdentifier']));
      if (!$target)
      {
        // The target doesn't exist in our cache so create a new one
        $target = new Target();
        $target->setName($targetDetail['name']);
        $target->setCampaign($campaign);
        $target->setCreativeIdentifier($targetDetail['creativeIdentifier']);
      }

      // Update the cached target
      $target->setCurrentBid($targetDetail['currentBid']);
      $target->setActive($targetDetail['active']);
      $this->em->persist($target);
    }

    $this->em->flush();
  }

  /**
   * Sets the API key to use when authenticating.
   */
  public function setApiKey($key)
  {
    $this->wrapper->setApiKey($key);
  }

  /**
   * Sets the username and password to use when authenticating.
   */
  public function setUsernameAndPassword($username, $password)
  {
    $this->wrapper->setUsernameAndPassword($username, $password);
  }
}
