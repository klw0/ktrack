<?php

namespace Tracker\StatsBundle\Controller;

use Tracker\StatsBundle\Collector\StatCollector;
use Tracker\StatsBundle\Form\CampaignStatsFilterType;
use Tracker\StatsBundle\Form\DaypartStatsFilterType;
use Tracker\StatsBundle\Form\ConvertingSubIdType;
use Tracker\TrackingBundle\Entity\Click;
use Tracker\TrackingBundle\Entity\Conversion;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Stats controller.
 *
 * @Route("/stats")
 */
class StatsController extends Controller
{
  /**
   * Gets the stats for a campaign
   *
   * @Route("/", defaults={"campaignId" = null}, name="stats")
   * @Route("/campaign/{campaignId}", requirements={"campaignId" = "\d+"}, name="stats_campaign")
   * @Template()
   */
  public function campaignAction($campaignId)
  {
    $em = $this->getDoctrine()->getEntityManager();

    $campaign = $em->getRepository('TrackerCampaignBundle:Campaign')->find($campaignId);
    if (!$campaign)
    {
      // No campaign was specified ($campaignId was null), so get the first campaign created
      $campaigns = $em->getRepository('TrackerCampaignBundle:Campaign')->findAll();
      $campaign = $campaigns[0];
    }

    // Set some default options for the form
    $startDate = new \DateTime();
    $startTime = array('hour' => 0, 'minute' => 0, 'second' => 0);
    $startDateTime = clone $startDate;
    $startDateTime->setTime($startTime['hour'], $startTime['minute'], $startTime['second']);

    $endDate = new \DateTime();
    $endTime = array('hour' => 23, 'minute' => 59, 'second' => 59);
    $endDateTime = clone $endDate;
    $endDateTime->setTime($endTime['hour'], $endTime['minute'], $endTime['second']);

    $filterFormData = array(
      'campaigns' => $campaign,
      'startDate' => $startDate,
      'startTime' => $startTime,
      'endDate' => $endDate,
      'endTime' => $endTime,
      'targetGroupByLandingPage' => false,
      'targetGroupByOffer' => false,
      'targetGroupByExtraData' => false,
      'landingPageGroupByOffer' => false,
    );

    // Don't forget about the extra data fields!
    $groupByExtraData = array();
    foreach (range(1, 5) as $i)
    {
      $filterFormData['targetGroupByExtraData' . $i] = false;
      $groupByExtraData[$i] = false;
    }

    // Create the form
    $activeCampaignIds = $em->getRepository('TrackerCampaignBundle:Campaign')->getActiveCampaignIds();
    $filterForm = $this->createForm(new CampaignStatsFilterType($activeCampaignIds), $filterFormData);

    // Create the stat collector
    $statCollector = new StatCollector($em);

    return array(
      'filter_form'         => $filterForm->createView(),
      'target_stats'        => $statCollector->getTargetStats($campaign, $startDateTime, $endDateTime, $filterFormData['targetGroupByLandingPage'], $filterFormData['targetGroupByOffer'], $groupByExtraData),
      'landing_page_stats'  => $statCollector->getLandingPageStats($campaign, $startDateTime, $endDateTime, $filterFormData['landingPageGroupByOffer']),
      'offer_stats'         => $statCollector->getOfferStats($campaign, $startDateTime, $endDateTime),
      'campaign_stats'      => $statCollector->getCampaignStats($campaign, $startDateTime, $endDateTime),
    );
  }

  /**
   * Returns the rendered stats after an ajax request
   *
   * @Route("/campaign/update", name="stats_campaign_update")
   * @Method("POST")
   */
  public function campaignStatsAjaxAction(Request $request)
  {
    if (!$request->isXmlHttpRequest())
      throw new \Exception("That wasn't an ajax request, bitch.");

    // Bind the form
    $filterForm = $this->createForm(new CampaignStatsFilterType());
    $filterForm->bindRequest($request);
    $data = $filterForm->getData();

    // Extract some data
    $campaign = $data['campaigns'];
    $startTime = $data['startTime'];
    $startDateTime = $data['startDate']->setTime($startTime['hour'], $startTime['minute'], $startTime['second']);
    $endTime = $data['endTime'];
    $endDateTime = $data['endDate']->setTime($endTime['hour'], $endTime['minute'], $endTime['second']);

    // Extract the extra data options
    $groupByExtraData = array();
    foreach (range(1, 5) as $i)
      $groupByExtraData[$i] = $data['targetGroupByExtraData' . $i];

    // Get the stats and render them
    $statCollector = new StatCollector($this->getDoctrine()->getEntityManager());

    return $this->render('TrackerStatsBundle:Stats:stats.html.twig', array(
      'target_stats'        => $statCollector->getTargetStats($campaign, $startDateTime, $endDateTime, $data['targetGroupByLandingPage'], $data['targetGroupByOffer'], $groupByExtraData),
      'landing_page_stats'  => $statCollector->getLandingPageStats($campaign, $startDateTime, $endDateTime, $data['landingPageGroupByOffer']),
      'offer_stats'         => $statCollector->getOfferStats($campaign, $startDateTime, $endDateTime),
      'campaign_stats'      => $statCollector->getCampaignStats($campaign, $startDateTime, $endDateTime),
    ));
  }

  /**
   * Get daypart stats.
   *
   * @Route("/daypart", defaults={"campaignId" = null}, name="stats_daypart")
   * @Route("/daypart/campaign/{campaignId}", requirements={"campaignId" = "\d+"}, name="stats_daypart_campaign")
   * @Template()
   */
  public function daypartAction($campaignId)
  {
    $em = $this->getDoctrine()->getEntityManager();

    $campaign = $em->getRepository('TrackerCampaignBundle:Campaign')->find($campaignId);
    if (!$campaign)
    {
      // No campaign was specified ($campaignId was null), so get the first campaign created
      $campaigns = $em->getRepository('TrackerCampaignBundle:Campaign')->findAll();
      $campaign = $campaigns[0];
    }

    // Set some default options for the form
    $startDateTime = new \DateTime();
    $startDateTime->setTime(0, 0, 0);
    $endDateTime = new \DateTime();
    $endDateTime->setTime(23, 59, 59);

    $filterFormData = array(
      'campaigns' => $campaign,
      'startDate' => $startDateTime,
      'endDate' => $endDateTime,
    );

    // Create the filter form
    $activeCampaignIds = $em->getRepository('TrackerCampaignBundle:Campaign')->getActiveCampaignIds();
    $filterForm = $this->createForm(new DaypartStatsFilterType($activeCampaignIds), $filterFormData);

    // Create the stat collector
    $statCollector = new StatCollector($em);

    return array(
      'filter_form'  => $filterForm->createView(),
      'hourly_stats' => $statCollector->getCampaignHourlyStats($campaign, $startDateTime, $endDateTime),
    );
  }

  /**
   * Returns the rendered daypart stats after an ajax request
   *
   * @Route("/daypart/update", name="stats_daypart_update")
   * @Method("POST")
   */
  public function daypartStatsAjaxAction(Request $request)
  {
    if (!$request->isXmlHttpRequest())
      throw new \Exception("That wasn't an ajax request, bitch.");

    // Bind the form
    $filterForm = $this->createForm(new DaypartStatsFilterType());
    $filterForm->bindRequest($request);
    $data = $filterForm->getData();

    // Extract some data
    $campaign = $data['campaigns'];
    $startDateTime = $data['startDate']->setTime(0, 0, 0);
    $endDateTime = $data['endDate']->setTime(23, 59, 59);

    // Get the stats and render them
    $statCollector = new StatCollector($this->getDoctrine()->getEntityManager());

    return $this->render('TrackerStatsBundle:Stats:daypartStats.html.twig', array(
      'stats' => $statCollector->getCampaignHourlyStats($campaign, $startDateTime, $endDateTime),
    ));
  }

  /**
   * Form handling for manual sub id conversion tracking.
   *
   * @Route("/add-converting-subids", name="add_converting_subids")
   * @Template()
   */
  public function addConvertingSubIdsAction(Request $request)
  {
    $form = $this->createForm(new ConvertingSubIdType());

    // Did the form submit?
    if ($request->getMethod() == 'POST')
    {
      $form->bindRequest($request);

      if ($form->isValid())
      {
        $data = $form->getData();
        $em = $this->getDoctrine()->getEntityManager();
        $badSubIds = array();

        // Add conversions for each of the sub ids
        foreach ($data['subIds'] as $subId)
        {
          $click = $em->getRepository('TrackerTrackingBundle:Click')->find(Click::decodeKey($subId));

          // If the click doesn't exist, then we got a bad sub id
          if (!$click)
          {
            $badSubIds[] = $subId;
            continue;
          }

          // Should we override the existing conversions?
          if ($data['overrideExistingConversions'])
          {
            $click->getConversions()->clear();
            $em->persist($click);
          }

          // Create a new conversion
          $conversion = new Conversion();
          $conversion->setRevenue($click->getOffer()->getPayout());
          $conversion->setClick($click);

          $em->persist($conversion);
          $em->flush();
        }
      }

      // Were there any problems?
      if (empty($badSubIds))
        $this->get('session')->setFlash('success', '<strong>Cool, Daddy-O!</strong> The conversions were added successfully.');
      else
        $this->get('session')->setFlash('error', '<strong>¡Problemo!</strong> The following sub ids could not be found: ' . implode($badSubIds, ', '));

      // Display the bad sub ids in the form (and clear out the good ones)
      $form->setData(array('subIds' => $badSubIds));
    }

    return array(
      'form' => $form->createView(),
    );
  }

  /**
   * Returns revenues used in the navigation bar
   *
   * @Route("/navbar-revenue", name="stats_navbar_revenue")
   * @Template()
   */
  public function navbarRevenueAction()
  {
    $statCollector = new StatCollector($this->getDoctrine()->getEntityManager());

    return array(
      'dtd_revenue' => $statCollector->getDayToDateRevenue(),
      'mtd_revenue' => $statCollector->getMonthToDateRevenue(),
      'ytd_revenue' => $statCollector->getYearToDateRevenue(),
    );
  }
}
