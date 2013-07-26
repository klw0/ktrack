<?php

namespace Tracker\TrafficSourceBundle\Service;

use Tracker\CampaignBundle\Entity\Campaign;
use Tracker\TrafficSourceBundle\Service\TrafficSourceService;
use Tracker\TrafficSourceBundle\Wrapper\LeadImpact as LeadImpactWrapper;

use Symfony\Bundle\DoctrineBundle\Registry as Doctrine;

/**
 * This service interacts with LeadImpact's site.
 */
class LeadImpact extends TrafficSourceService
{
  public function __construct(Doctrine $doctrine)
	{
    parent::__construct($doctrine);

    // Use the correct wrapper
    $this->wrapper = new LeadImpactWrapper();
  }
}
