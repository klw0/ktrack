<?php

namespace Tracker\CampaignBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CampaignRepository
 */
class CampaignRepository extends EntityRepository
{
  /**
   * Returns an array of ids of the active campaigns
   */
  public function getActiveCampaignIds()
  {
    $activeCampaigns = $this->findBy(array('active' => true));
    $activeIds = array();

    foreach ($activeCampaigns as $campaign)
      $activeIds[] = $campaign->getId();

    return $activeIds;
  }
}
