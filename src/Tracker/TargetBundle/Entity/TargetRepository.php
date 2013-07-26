<?php

namespace Tracker\TargetBundle\Entity;

use Tracker\CampaignBundle\Entity\Campaign;

use Doctrine\ORM\EntityRepository;

/**
 * TargetRepository
 */
class TargetRepository extends EntityRepository
{
  /**
   * Returns the cost of a target for the specific campaign
   */
  public function getTargetCost($targetName, Campaign $campaign)
  {
    // If the campaign's traffic source does not have a service to interact with the traffic source,
    // then it will not have any local target data, so use the campaign's estimated CPV.
    if (!$campaign->getTrafficSource()->hasService())
      return $campaign->getEstimatedCostPerView();

    /*
     * A campaign may have multiple targets with the same name in different creatives at the traffic source level.
     * Because of this, we need to find all of the targets with the given target name for this campaign and do something with
     * their costs.  There are two options that make sense - either take the average of the costs, or return the highest cost.
     * Returning the highest cost may yield the most accurate stats, since a higher cost target at a PPV network will get more
     * traffic than a lower cost target, so this is what we will do.
     */
    $query = $this->createQueryBuilder('t')
      ->where('t.name = :target_name')
      ->setParameter('target_name', $targetName)
      ->andWhere('t.campaign = :campaign_id')
      ->setParameter('campaign_id', $campaign)
      ->orderBy('t.currentBid', 'DESC')
      ->getQuery();

    $targets = $query->getResult();
    if (count($targets) > 0)
    {
      // Return the bid price on the target with the highest cost (index 0 since it's sorted)
      $target = $targets[0];
      return $target->getCurrentBid();
    }
    else
      return $campaign->getEstimatedCostPerView();
  }
}
