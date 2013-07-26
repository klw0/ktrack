<?php

namespace Tracker\StatsBundle\Collector;

use Tracker\CampaignBundle\Entity\Campaign;
use Doctrine\ORM\EntityManager;

/**
 * This class gathers stats
 */
class StatCollector
{
  private $em;

  /**
   * Constructify
   */
  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

  /**
   * ==================================================================
   * Stat methods
   * ==================================================================
   */

  /**
   * Returns day to date revenue (today's revenue).
   */
  public function getDayToDateRevenue()
  {
    // Start date is the beginning of today
    $startDateTime = new \DateTime();
    $startDateTime->setTime(0, 0);

    return $this->getRevenue($startDateTime, new \DateTime());
  }

  /**
   * Returns month to date revenue.
   */
  public function getMonthToDateRevenue()
  {
    // Start date is the beginning of the month
    $startDateTime = new \DateTime();
    $startDateTime->setDate(idate('Y'), idate('m'), 1);
    $startDateTime->setTime(0, 0);

    return $this->getRevenue($startDateTime, new \DateTime());
  }

  /**
   * Returns year to date revenue.
   */
  public function getYearToDateRevenue()
  {
    // Start date is the beginning of the year
    $startDateTime = new \DateTime();
    $startDateTime->setDate(idate('Y'), 1, 1);
    $startDateTime->setTime(0, 0);

    return $this->getRevenue($startDateTime, new \DateTime());
  }

  /**
   * Returns revenue from |$startDateTime| to |$endDateTime|
   */
  private function getRevenue($startDateTime, $endDateTime)
  {
    $qb = $this->em->createQueryBuilder()
      ->select('SUM(cv.revenue) as revenue')
      ->from('TrackerTrackingBundle:Conversion', 'cv');

    // Filter by date
    $qb = $this->addDateFilteringToQueryBuilder($startDateTime, $endDateTime, $qb, 'cv.createdAt');

    $result = $qb->getQuery()->getSingleResult();
    return $result['revenue'];
  }

  /**
   * Returns stats for all of the targets/views of the campaign
   */
  public function getTargetStats(Campaign $campaign, \DateTime $startDateTime, \DateTime $endDateTime, $groupByLandingPage, $groupByOffer, $groupByExtraData)
  {
    $selectFields = array(
      'v.targetName as targetName',

      // Get the count of the active targets for this campaign named |v.targetName|.  If there is at least one in the result set, then we know the target is active.
      // The reason we need to do this is because there may be targets with the same name in the same campaign, but with different creative identifiers.
      '(SELECT COUNT(t.active) FROM TrackerTargetBundle:Target t WHERE t.name = v.targetName AND t.campaign = :campaign_id AND t.active = TRUE) as isTargetActive',

      'COUNT(DISTINCT v.id) as views',
      'COUNT(DISTINCT cl.id) as clicks',
      'COUNT(DISTINCT cv.id) as conversions',
      'SUM(cv.revenue) as revenue',
      'SUM(v.cost) as cost',
      'AVG(v.cost) as averageCostPerView'
    );

    $qb = $this->em->createQueryBuilder()
      ->select($selectFields)
      ->from('TrackerTrackingBundle:View', 'v')
      ->leftJoin('v.clicks', 'cl')
      ->leftJoin('cl.conversions', 'cv')
      ->where('v.campaign = :campaign_id')
      ->setParameter('campaign_id', $campaign)
      ->groupBy('v.targetName');

    // Should we group by landing pages as well as targets?
    if ($groupByLandingPage)
    {
      // Override the existing select fields
      $selectFields[] = 'lp.name as landingPageName';
      $qb->select($selectFields);

      $qb->leftJoin('v.landingPage', 'lp');
      $qb->addGroupBy('lp.name');
    }

    // Should we group by offers as well as targets?
    if ($groupByOffer)
    {
      // Override the existing select fields
      $selectFields[] = 'o.name as offerName';
      $qb->select($selectFields);

      $qb->leftJoin('cl.offer', 'o');
      $qb->addGroupBy('o.name');
    }

    // Should we also group by the extra data?
    foreach (range(1, 5) as $i)
    {
      if ($groupByExtraData[$i])
      {
        $field = "v.extraData$i";
        $selectFields[] = "$field as extraData$i";

        $qb->addGroupBy($field);
      }

      // Override the existing select fields
      $qb->select($selectFields);
    }

    // Filter by date
    $qb = $this->addDateFilteringToQueryBuilder($startDateTime, $endDateTime, $qb, 'v.createdAt');

    return $qb->getQuery()->getResult();
  }

  /**
   * Returns stats for all of the landing pages of the campaign
   */
  public function getLandingPageStats(Campaign $campaign, \DateTime $startDateTime, \DateTime $endDateTime, $groupByOffer)
  {
    $selectFields = array(
      'lp.name as landingPageName',
      'COUNT(DISTINCT v.id) as views',
      'COUNT(DISTINCT cl.id) as clicks',
      'COUNT(DISTINCT cv.id) as conversions',
      'SUM(cv.revenue) as revenue',
      'SUM(v.cost) as cost',
    );

    $qb = $this->em->createQueryBuilder()
      ->select($selectFields)
      ->from('TrackerCampaignBundle:LandingPage', 'lp')
      ->leftJoin('lp.views', 'v')
      ->leftJoin('v.clicks', 'cl')
      ->leftJoin('cl.conversions', 'cv')
      ->where('lp.campaign = :campaign_id')
      ->setParameter('campaign_id', $campaign)
      ->groupBy('lp.name');

    // Should we group by offers as well as targets?
    if ($groupByOffer)
    {
      // Override the existing select fields
      $selectFields[] = 'o.name as offerName';
      $qb->select($selectFields);

      $qb->leftJoin('cl.offer', 'o');
      $qb->addGroupBy('o.name');
    }

    // Filter by date
    $qb = $this->addDateFilteringToQueryBuilder($startDateTime, $endDateTime, $qb, 'v.createdAt');

    return $qb->getQuery()->getResult();
  }

  /**
   * Returns stats for all of the offers of the campaign
   */
  public function getOfferStats(Campaign $campaign, \DateTime $startDateTime, \DateTime $endDateTime)
  {
    $selectFields = array(
      'o.name as offerName',
      'COUNT(DISTINCT cl.id) as visitors',
      'COUNT(DISTINCT cv.id) as conversions',
      'SUM(cv.revenue) as revenue',
      'SUM(v.cost) as cost',
    );

    $qb = $this->em->createQueryBuilder()
      ->select($selectFields)
      ->from('TrackerCampaignBundle:Offer', 'o')
      ->join('o.offerGroup', 'og')
      ->leftJoin('o.clicks', 'cl')
      ->leftJoin('cl.conversions', 'cv')
      ->leftJoin('cl.view', 'v')
      ->where('og.campaign = :campaign_id')
      ->setParameter('campaign_id', $campaign)
      ->groupBy('o.name');

    // Filter by date
    $qb = $this->addDateFilteringToQueryBuilder($startDateTime, $endDateTime, $qb, 'v.createdAt');

    return $qb->getQuery()->getResult();
  }

  /**
   * Returns overall stats for the campaign
   */
  public function getCampaignStats(Campaign $campaign, \DateTime $startDateTime, \DateTime $endDateTime)
  {
    $selectFields = array(
      'c.name as campaignName',
      'COUNT(DISTINCT v.id) as views',
      'COUNT(DISTINCT cl.id) as clicks',
      'COUNT(DISTINCT cv.id) as conversions',
      'SUM(cv.revenue) as revenue',
      'SUM(v.cost) as cost',
    );

    $qb = $this->em->createQueryBuilder()
      ->select($selectFields)
      ->from('TrackerCampaignBundle:Campaign', 'c')
      ->leftJoin('c.views', 'v')
      ->leftJoin('v.clicks', 'cl')
      ->leftJoin('cl.conversions', 'cv')
      ->where('c.id = :campaign_id')
      ->setParameter('campaign_id', $campaign)
      ->groupBy('c.name');

    // Filter by date
    $qb = $this->addDateFilteringToQueryBuilder($startDateTime, $endDateTime, $qb, 'v.createdAt');

    return $qb->getQuery()->getResult();
  }

  /**
   * Returns hourly stats for a campaign.
   */
  public function getCampaignHourlyStats(Campaign $campaign, \DateTime $startDateTime, \DateTime $endDateTime)
  {
    $format = 'h:i A';

    $keys = array('views', 'clicks', 'conversions', 'revenue', 'cost');
    $hourlyStats = array();
    foreach (range(0, 23) as $hour)
    {
      foreach ($keys as $key)
        $hourlyStats[$hour][$key] = 0;
    }

    // Iterate through each day and hour, and sum the stats.  This is slow, but at the time Doctrine does not support things
    // like GROUP BY hour.  Could go native, but it feels wrong for some reason.
    for ($date = clone $startDateTime; $date < $endDateTime; $date->add(new \DateInterval('P1D')))
    {
      foreach (range(0, 23) as $hour)
      {
        $hourStart = clone $date;
        $hourStart->setTime($hour, 0, 0);
        $hourEnd = clone $date;
        $hourEnd->setTime($hour, 59, 59);

        $hourStats = $this->getCampaignStats($campaign, $hourStart, $hourEnd);
        foreach ($keys as $key)
          $hourlyStats[$hour][$key] += (!empty($hourStats)) ? $hourStats[0][$key] : 0;

        $hourlyStats[$hour]['period'] = $hourStart->format($format) . ' to ' . $hourEnd->format($format);
      }
    }

    return $hourlyStats;
  }


  /**
   * Adds a clause to the query builder object to only accept dates between |$startDateTime| and |$endDateTime|
   */
  private function addDateFilteringToQueryBuilder(\DateTime $startDateTime, \DateTime $endDateTime, $qb, $field)
  {
    $format = 'Y-m-d H:i:s';

    if ($startDateTime !== null && $endDateTime !== null)
      $qb->andWhere($qb->expr()->between($field, "'" . $startDateTime->format($format) . "'", "'" . $endDateTime->format($format) . "'"));

    return $qb;
  }
}
