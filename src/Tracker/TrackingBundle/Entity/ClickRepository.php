<?php

namespace Tracker\TrackingBundle\Entity;

use Tracker\TrackingBundle\Entity\View;
use Tracker\CampaignBundle\Entity\OfferGroup;

use Doctrine\ORM\EntityRepository;

/**
 * ClickRepository
 */
class ClickRepository extends EntityRepository
{
  /**
   * Returns a click that exists for the given view and offer group
   */
  public function findOneByViewAndOfferGroup(View $view, OfferGroup $offerGroup)
  {
    $query = $this->createQueryBuilder('c')
      ->where('c.view = :view_id')
      ->setParameter('view_id', $view->getId())
      ->innerJoin('c.offer', 'o', 'WITH', 'o.offerGroup = :offer_group_id')
      ->setParameter('offer_group_id', $offerGroup->getId())
      ->setMaxResults(1)
      ->getQuery();

    try {
      return $query->getSingleResult();
    } catch (\Doctrine\ORM\NoResultException $e) {
      return null;
    }
  }
}
