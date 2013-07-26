<?php

namespace Tracker\TrafficSourceBundle\Service;

use Tracker\CampaignBundle\Entity\Campaign;
use Tracker\TrafficSourceBundle\Service\TrafficSourceService;
use Tracker\TrafficSourceBundle\Wrapper\Trafficvance as TrafficvanceWrapper;

use Symfony\Bundle\DoctrineBundle\Registry as Doctrine;

/**
 * This service interacts with Trafficvance's API.
 */
class Trafficvance extends TrafficSourceService
{
  public function __construct(Doctrine $doctrine)
	{
    parent::__construct($doctrine);

    // Use the correct wrapper
    $this->wrapper = new TrafficvanceWrapper();
  }
}
